package com.qdocs.smarthospital24.fragments;

import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.qdocs.smarthospital24.PatientAddAppoinment;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.DashboardAppointmentBottomsheet;
import com.qdocs.smarthospital24.adapters.DashboardBottomsheet;
import com.qdocs.smarthospital24.adapters.DashboardHolidaysheet;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.CustomCalendar;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.List;
import java.util.Map;

import static android.widget.Toast.makeText;

public class DashboardCalender extends Fragment implements CustomCalendar.RobotoCalendarListener {

    private CustomCalendar robotoCalendarView;
    RelativeLayout mainLay;
    Calendar calendar;
    String monthNo="";
    public int currentMonth=0, month = 0;
    JSONArray eventDetailsArray = new JSONArray();
    JSONArray appointmentArray = new JSONArray();
    JSONArray holidayArray = new JSONArray();
    SimpleDateFormat dateFormatter = new SimpleDateFormat("yyyy-MM-dd");
    public List<String> taskDateList= new ArrayList<String>();
    public List<String> taskholidayDateList= new ArrayList<String>();
    public List<String> appointDateList= new ArrayList<String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    TextView appointment_nodata ;
    TextView event_nodata ;
    RecyclerView taskListView ;
    RecyclerView appointListView;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.fragment_dashboard_calender, container, false);
        calendar = Calendar.getInstance();
        currentMonth = calendar.getTime().getMonth()+1;
        monthNo = String.valueOf(currentMonth);

        robotoCalendarView = (CustomCalendar) mainView.findViewById(R.id.robotoCalendarPicker);
        mainLay = (RelativeLayout) mainView.findViewById(R.id.attendance_mainLay);

        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMinimum(Calendar.DAY_OF_MONTH));
        Date begining = calendar.getTime();
        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMaximum(Calendar.DAY_OF_MONTH));
        Date end = calendar.getTime();
        params.put("date_from", dateFormatter.format(begining));
        params.put("date_to", dateFormatter.format(end));
        params.put("user_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.userId));
        params.put("patient_id",Utility.getSharedPreferences(getActivity(), Constants.patient_id));
        JSONObject obj=new JSONObject(params);
        Log.e("params ", obj.toString());
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }


        robotoCalendarView.setRobotoCalendarListener(this);
        robotoCalendarView.setShortWeekDays(false);
        robotoCalendarView.showDateTitle(true);
        robotoCalendarView.updateView();

        return mainView;
    }

    private void markDate() {
        try {
            if (taskDateList.size() != 0) {
                for (int a = 0; a < taskDateList.size(); a++) {
                    calendar.set(Calendar.DAY_OF_MONTH, Integer.parseInt(getDayFromDate(taskDateList.get(a))));
                    robotoCalendarView.markCircleImage1(calendar);
                    Date date = calendar.getTime();
                    String formattedDate = new SimpleDateFormat("yyyy-MM-dd").format(date);

                }
            }
        }catch(Exception e){
            Log.e("Mark date Exception 6", e.toString());
        }
        try {
            if (appointDateList.size() != 0) {
                for (int a = 0; a < appointDateList.size(); a++) {
                    calendar.set(Calendar.DAY_OF_MONTH, Integer.parseInt(getDayFromDate(appointDateList.get(a))));
                    robotoCalendarView.markCircleImage4(calendar);
                    Date date = calendar.getTime();
                    String formattedDate = new SimpleDateFormat("yyyy-MM-dd").format(date);

                }
            }
        }catch(Exception e){
            Log.e("Mark date Exception 7", e.toString());
        }

        try {
            if (taskholidayDateList.size() != 0) {
                for (int a = 0; a < taskholidayDateList.size(); a++) {
                    calendar.set(Calendar.DAY_OF_MONTH, Integer.parseInt(getDayFromDate(taskholidayDateList.get(a))));
                    robotoCalendarView.markCircleImage2(calendar);
                    Date date = calendar.getTime();
                    String formattedDate = new SimpleDateFormat("yyyy-MM-dd").format(date);

                }
            }
        }catch(Exception e){
            Log.e("Mark date Exception 7", e.toString());
        }

    }

    @Override
    public void onDayClick(Calendar daySelectedCalendar) {
        Date date=daySelectedCalendar.getTime();
        DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        String formattedDate=dateFormat.format(date);
        showEventDetail(formattedDate);
    }

    private void showEventDetail(final String dateSelected) {

        Log.e("Details Array ", eventDetailsArray.toString());
        ArrayList<String> eventIdList = new ArrayList<>();
        ArrayList<String> eventTitleList = new ArrayList<>();
        ArrayList<String> eventDateList = new ArrayList<>();
        ArrayList<String> eventStatusList = new ArrayList<>();
        ArrayList<String> eventTypeList = new ArrayList<>();
        ArrayList<String> eventDescList = new ArrayList<>();

        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");

        try {
            for (int i=0; i<eventDetailsArray.length(); i++) {
                String eventDateStr = eventDetailsArray.getJSONObject(i).getString("events_lists");
                List<String> eventDate = Arrays.asList(eventDateStr.split("\\s*,\\s*"));

                for (int j = 0; j<eventDate.size(); j++) {
                    if(eventDate.get(j).equals(dateSelected)) {

                        eventIdList.add(eventDetailsArray.getJSONObject(i).getString("id"));
                        eventTitleList.add(eventDetailsArray.getJSONObject(i).getString("event_title"));
                        eventTypeList.add(eventDetailsArray.getJSONObject(i).getString("event_type"));
                        eventDescList.add(eventDetailsArray.getJSONObject(i).getString("event_description"));

                        String startDate = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, eventDetailsArray.getJSONObject(i).getString("start_date"));
                        String endDate = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, eventDetailsArray.getJSONObject(i).getString("end_date"));

                        if(eventDetailsArray.getJSONObject(i).getString("event_type").equals("task")) {
                            eventDateList.add(startDate);
                        } else {
                            eventDateList.add(startDate + " - " + endDate);
                        }
                        eventStatusList.add(eventDetailsArray.getJSONObject(i).getString("is_active"));
                    }
                }

                Log.e("Date List", eventDate.toString());

            }
        }catch (Exception e) {
            Log.e("ERROR", e.toString());
        }

        Log.e("Details Array ", appointmentArray.toString());
        ArrayList<String> appointIdList = new ArrayList<>();
        ArrayList<String> appointment_statusList = new ArrayList<>();
        ArrayList<String> appointDateList = new ArrayList<>();
        ArrayList<String> appointment_noList = new ArrayList<>();
        ArrayList<String> appointment_messageList = new ArrayList<>();
        ArrayList<String> appointment_doctorlist = new ArrayList<>();


        try {
            for (int i=0; i<appointmentArray.length(); i++) {
                String eventDateStr = appointmentArray.getJSONObject(i).getString("date_list");
                List<String> eventDate = Arrays.asList(eventDateStr.split("\\s*,\\s*"));

                for (int j = 0; j<eventDate.size(); j++) {
                    if(eventDate.get(j).equals(dateSelected)) {

                        appointIdList.add(appointmentArray.getJSONObject(i).getString("id"));
                        appointment_statusList.add(appointmentArray.getJSONObject(i).getString("appointment_status"));
                        appointment_noList.add(appointmentArray.getJSONObject(i).getString("id"));
                        appointment_messageList.add(appointmentArray.getJSONObject(i).getString("message"));
                        appointment_doctorlist.add(appointmentArray.getJSONObject(i).getString("name")+" "+appointmentArray.getJSONObject(i).getString("surname"));

                        String date = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, appointmentArray.getJSONObject(i).getString("date"));
                       // String endDate = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, eventDetailsArray.getJSONObject(i).getString("end_date"));

                        appointDateList.add(date);

                    }
                }

                Log.e("Date List", eventDate.toString());

            }
        }catch (Exception e) {
            Log.e("ERROR", e.toString());
        }


        Log.e("holidayArray ", holidayArray.toString());
        ArrayList<String> holidayIdList = new ArrayList<>();
        ArrayList<String> holidayTitleList = new ArrayList<>();
        ArrayList<String> holidayTypeList = new ArrayList<>();
        ArrayList<String> holidayDescList = new ArrayList<>();
        ArrayList<String> holidayDateList = new ArrayList<>();
        ArrayList<String> holiday_doctorlist = new ArrayList<>();

        try {
            for (int i=0; i<holidayArray.length(); i++) {
                String holidayDateStr = holidayArray.getJSONObject(i).getString("date_list");
                List<String> holidayDate = Arrays.asList(holidayDateStr.split("\\s*,\\s*"));

                for (int j = 0; j<holidayDate.size(); j++) {
                    if(holidayDate.get(j).equals(dateSelected)) {

                        holidayIdList.add(holidayArray.getJSONObject(i).getString("id"));
                        holidayTitleList.add(holidayArray.getJSONObject(i).getString("title"));
                        holidayTypeList.add(holidayArray.getJSONObject(i).getString("holiday_type"));
                        holidayDescList.add(holidayArray.getJSONObject(i).getString("description"));

                        String startDate = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, holidayArray.getJSONObject(i).getString("from_date"));
                        String endDate = Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat, holidayArray.getJSONObject(i).getString("to_date"));
                        holidayDateList.add(startDate + " - " + endDate);
                       /* if(holidayArray.getJSONObject(i).getString("event_type").equals("task")) {
                            eventDateList.add(startDate);
                        } else {
                            eventDateList.add(startDate + " - " + endDate);
                        }*/
                        //ventStatusList.add(holidayArray.getJSONObject(i).getString("is_active"));
                    }
                }

                Log.e("Date List", holidayDate.toString());

            }
        }catch (Exception e) {
            Log.e("ERROR", e.toString());
        }
        Log.e("EVENT TITLE", eventTitleList.toString());

            System.out.println("Details Array"+ eventDetailsArray.toString());
            System.out.println("Appoint Details Array"+ appointmentArray.toString());
            System.out.println("Holiday Details Array"+ holidayArray.toString());

            View view = getActivity().getLayoutInflater().inflate(R.layout.fragment_dashboard_bottom_sheet, null);
            view.setMinimumHeight(500);

            TextView headerTV = view.findViewById(R.id.dashboard_bottomSheet_header);
            ImageView crossBtn = view.findViewById(R.id.dashboard_bottomSheet_crossBtn);
            LinearLayout addappointBtn = view.findViewById(R.id.adapter_patient_addappointBtn);
            RecyclerView appointListView = view.findViewById(R.id.dashboard_bottomSheet_appoinments_listview);
            RecyclerView taskListView = view.findViewById(R.id.dashboard_bottomSheet_task_listview);
            RecyclerView holidayListView = view.findViewById(R.id.dashboard_bottomSheet_holiday_listview);
            TextView appointment_nodata = view.findViewById(R.id.appointment_nodata);
            TextView event_nodata = view.findViewById(R.id.event_nodata);
            TextView holiday_nodata = view.findViewById(R.id.holiday_nodata);

            headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.primaryColour)));
            headerTV.setText(getActivity().getString(R.string.appointtaskAndEvents));

            DashboardBottomsheet adapter = new DashboardBottomsheet(getActivity(), eventIdList, eventTitleList,
                    eventStatusList, eventDateList, eventTypeList, eventDescList );
            RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
            taskListView.setLayoutManager(mLayoutManager);
            taskListView.setItemAnimator(new DefaultItemAnimator());
            taskListView.setAdapter(adapter);

            DashboardAppointmentBottomsheet appointadapter = new DashboardAppointmentBottomsheet(getActivity(), appointIdList, appointment_statusList,
                    appointment_noList,appointDateList, appointment_messageList,appointment_doctorlist);
            RecyclerView.LayoutManager mLayoutManager1 = new LinearLayoutManager(getActivity().getApplicationContext());
            appointListView.setLayoutManager(mLayoutManager1);
            appointListView.setItemAnimator(new DefaultItemAnimator());
            appointListView.setAdapter(appointadapter);

             DashboardHolidaysheet holidaysheet = new DashboardHolidaysheet(getActivity(), holidayIdList, holidayTitleList,
                     holidayTypeList,holidayDescList, holidayDateList);
             RecyclerView.LayoutManager hLayoutManager1 = new LinearLayoutManager(getActivity().getApplicationContext());
             holidayListView.setLayoutManager(hLayoutManager1);
             holidayListView.setItemAnimator(new DefaultItemAnimator());
             holidayListView.setAdapter(holidaysheet);


        final BottomSheetDialog dialog = new BottomSheetDialog(getActivity());
            dialog.setContentView(view);
            BottomSheetBehavior mBehavior = BottomSheetBehavior.from((View) view.getParent());
            mBehavior.setPeekHeight(800);
            dialog.show();
            crossBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    dialog.dismiss();
                }
            });

        addappointBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(getActivity(), PatientAddAppoinment.class);
                intent.putExtra("formattedDate", dateSelected);
                startActivity(intent);
                dialog.dismiss();
            }
        });
    }

    @Override
    public void onDayLongClick(Calendar daySelectedCalendar) { }

    @Override
    public void onRightButtonClick() {
        month++;
        calendar.add(Calendar.MONTH, 1);
        currentMonth = calendar.getTime().getMonth()+1;
        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMinimum(Calendar.DAY_OF_MONTH));
        Date begining = calendar.getTime();
        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMaximum(Calendar.DAY_OF_MONTH));
        Date end = calendar.getTime();
        params.put("date_from", dateFormatter.format(begining));
        params.put("date_to", dateFormatter.format(end));
        params.put("user_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.userId));
        params.put("patient_id",Utility.getSharedPreferences(getActivity(), Constants.patient_id));
        JSONObject obj=new JSONObject(params);
        Log.e("params ", obj.toString());
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }


    }

    @Override
    public void onLeftButtonClick() {
        month--;
        calendar.add(Calendar.MONTH, -1);
        currentMonth = calendar.getTime().getMonth()+1;
        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMinimum(Calendar.DAY_OF_MONTH));
        Date begining = calendar.getTime();
        calendar.set(Calendar.DAY_OF_MONTH, calendar.getActualMaximum(Calendar.DAY_OF_MONTH));
        Date end = calendar.getTime();
        params.put("date_from", dateFormatter.format(begining));
        params.put("date_to", dateFormatter.format(end));
        params.put("user_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.userId));
        params.put("patient_id",Utility.getSharedPreferences(getActivity(), Constants.patient_id));
        JSONObject obj=new JSONObject(params);
        Log.e("params ", obj.toString());
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }
    }

    private void getDataFromApi(String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(getActivity());
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+ Constants.getDashboardUrl;
        System.out.println("dashboard url---"+url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result calender", result);
                        System.out.println("dashboard url---"+result);

                        JSONObject object = new JSONObject(result);
                        String success = "200";
                        if (success.equals("200")) {
                            String dateList = object.getString("date_lists");
                            String holiday_date_list = object.getString("holiday_date_list");
                            String appointment_date_List = object.getString("appointment_date_list");
                            eventDetailsArray = object.getJSONArray("public_events");
                            appointmentArray = object.getJSONArray("appointment");
                            holidayArray = object.getJSONArray("holiday");

                            taskDateList = Arrays.asList(dateList.split("\\s*,\\s*"));
                            appointDateList = Arrays.asList(appointment_date_List.split("\\s*,\\s*"));
                            taskholidayDateList = Arrays.asList(holiday_date_list.split("\\s*,\\s*"));
                            markDate();
                        } else {
                            Toast.makeText(getActivity().getApplicationContext(), object.getString("errorMsg"), Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(getActivity(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getActivity().getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getActivity().getApplicationContext(), "accessToken"));
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }

            @Override
            public byte[] getBody() throws AuthFailureError {
                try {
                    return requestBody == null ? null : requestBody.getBytes("utf-8");
                } catch (UnsupportedEncodingException uee) {
                    VolleyLog.wtf("Unsupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");
                    return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity());
        requestQueue.add(stringRequest);
    }
    public String getDayFromDate(String date) {

        SimpleDateFormat input = new SimpleDateFormat("yyyy-MM-dd");
        SimpleDateFormat output = new SimpleDateFormat("dd");
        try {
            Date myDate = input.parse(date);
            String newDate = output.format(myDate);
            return newDate;
        } catch (ParseException e) {
            e.printStackTrace();
            return "";
        }
    }
}
