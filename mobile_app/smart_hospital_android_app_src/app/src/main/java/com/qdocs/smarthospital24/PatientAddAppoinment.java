package com.qdocs.smarthospital24;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.adapters.PatientCusomFiledAdapter;
import com.qdocs.smarthospital24.adapters.SlotsAdapter;
import com.qdocs.smarthospital24.patient.PatientAppoinmentList;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.Picasso;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Map;
import java.util.Set;
import static android.widget.Toast.makeText;

public class PatientAddAppoinment extends AppCompatActivity implements DataTransferInterface  {

    String receivingString="";
    EditText Dates,message;
    Spinner Doctor,Live,Specialist,Slot,Shift,Priority;
    String doctor_id="",text="no",specialist_id="",shift_id="",slot_id="",priorityid="1";
    String dates="";
    Button save_patient_appoinment;
    ArrayList<String>doctorlist=new ArrayList<String>();
    ArrayList<String>slotlist=new ArrayList<String>();
    ArrayList<String>specialistlist=new ArrayList<String>();
    ArrayList<String>shiftlist=new ArrayList<String>();
    ArrayList<String>livelist=new ArrayList<String>();
    ArrayList<String>liveidlist=new ArrayList<String>();
    ArrayList<String>prioritylist=new ArrayList<String>();
    ArrayList<String>priorityidlist=new ArrayList<String>();
    ArrayList<String>doctoridlist=new ArrayList<String>();
    ArrayList<String>shiftidlist=new ArrayList<String>();
    ArrayList<String>slotidlist=new ArrayList<String>();
    ArrayList<String>specialistidlist=new ArrayList<String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public Map<String, String> params = new Hashtable<String, String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> nameList = new ArrayList<String>();
    ArrayList<String> typeList = new ArrayList<String>();
    public ImageView backBtn,overlapImage;
    RelativeLayout layoutTop;
    public String defaultDateFormat, currency;
    public TextView titleTV;
    EditText time;
    protected FrameLayout mDrawerLayout, actionBar;
    PatientCusomFiledAdapter adapter;
    RecyclerView recyclerview;
     GridView bottom_timeslots;
     String clickedItem="";
     Dialog dialog;
    JSONArray custom_fields = new JSONArray();
    JSONArray finaldata = new JSONArray();
    GridView timeslots;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_appoinment_page);
        overridePendingTransition(R.anim.enter, R.anim.exit);
        backBtn = findViewById(R.id.actionBar_backBtn);
        mDrawerLayout = findViewById(R.id.container);
        actionBar = findViewById(R.id.actionBarSecondary);
        titleTV = findViewById(R.id.actionBar_title);
        layoutTop = findViewById(R.id.layoutTop);
        overlapImage = findViewById(R.id.overlapImage);
        timeslots = findViewById(R.id.timeslots);
        time = findViewById(R.id.time);
        time.setText("00:00");
        save_patient_appoinment = findViewById(R.id.save_patient_appoinment);
        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);
        decorate();
        Utility.setLocale(getApplicationContext(),Utility.getSharedPreferences(getApplicationContext(), Constants.langCode));

        backBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        titleTV.setText(getApplicationContext().getString(R.string.addAppoinments));
        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            receivingString = extras.getString("formattedDate");
           // System.out.println("formattedDate=="+receivingString);
            System.out.println("Date=="+Utility.parseDate("yyyy-MM-dd", defaultDateFormat,receivingString));


        } else { }

        params.put("belong_to","appointment");
        JSONObject customobj=new JSONObject(params);
        Log.e("params ", customobj.toString());
        getCustomFieldApi(customobj.toString());

        if(Utility.isConnectingToInternet(getApplicationContext())){
            getScannerDataFromApi();
        }else{
            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }

        specialistlist.add("Select");
        specialistidlist.add("0");
        doctorlist.add("Select");
        doctoridlist.add("0");
        shiftlist.add("Select");
        shiftidlist.add("0");
        slotlist.add("Select");
        slotidlist.add("0");

        Dates=findViewById(R.id.date);
      //  Dates.setText(receivingString);
        Dates.setText(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,receivingString));

        Calendar c = Calendar.getInstance();
        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm");
        String getCurrentDateTime = sdf.format(c.getTime());

        message=findViewById(R.id.message);

        recyclerview=findViewById(R.id.recyclerview);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerview.setLayoutManager(mLayoutManager);
        recyclerview.setItemAnimator(new DefaultItemAnimator());
        adapter = new PatientCusomFiledAdapter(PatientAddAppoinment.this,idlist,nameList,typeList,this);
        recyclerview.setAdapter(adapter);

        Doctor=findViewById(R.id.Doctor_spinner);
        Live=findViewById(R.id.Live_spinner);
        Specialist=findViewById(R.id.specialist_spinner);
        Slot=findViewById(R.id.Slot_spinner);
        Shift=findViewById(R.id.Shift_spinner);
        Priority=findViewById(R.id.Priority_spinner);

        Dates.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Calendar mcurrentDate = Calendar.getInstance();

                int mDay   = mcurrentDate.get(Calendar.DAY_OF_MONTH);
                int mMonth = mcurrentDate.get(Calendar.MONTH);
                int mYear  = mcurrentDate.get(Calendar.YEAR);
                DatePickerDialog datePickerDialog = new DatePickerDialog(PatientAddAppoinment.this, new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker datepicker, int selectedyear, int selectedmonth, int selectedday) {
                        Calendar newDate = Calendar.getInstance();
                        newDate.set(selectedyear, selectedmonth, selectedday);
                        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                        dates=sdf.format(newDate.getTime());
                        receivingString=dates;
                        SimpleDateFormat sdfdate = new SimpleDateFormat("yyyy-MM-dd");
                       // Dates.setText(sdfdate.format(newDate.getTime())) ;
                        System.out.println("Date=="+Utility.parseDate("yyyy-MM-dd", defaultDateFormat,sdfdate.format(newDate.getTime())));
                        Dates.setText(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,sdfdate.format(newDate.getTime())));

                    }
                }, mYear, mMonth, mDay);
                datePickerDialog.show();
            }
        });

        save_patient_appoinment.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                params.put("patientId", Utility.getSharedPreferences(getApplicationContext(), "patient_id"));

                if(doctor_id.equals(" ")|| doctor_id.equals("0")){
                    Toast.makeText(PatientAddAppoinment.this, "Please select Doctor!", Toast.LENGTH_SHORT).show();
                }else if(shift_id.equals("0")){
                    Toast.makeText(PatientAddAppoinment.this, "The Shift Field is required!", Toast.LENGTH_SHORT).show();
                }else if(slot_id.equals("0")){
                    Toast.makeText(PatientAddAppoinment.this, "The Slot Field is required!", Toast.LENGTH_SHORT).show();
                }else if(message.getText().toString().equals("")){
                    Toast.makeText(PatientAddAppoinment.this, "The Message Field is required!", Toast.LENGTH_SHORT).show();
                }else if(time.getText().toString().equals("00:00")){
                    Toast.makeText(PatientAddAppoinment.this, "The Time Field is required!", Toast.LENGTH_SHORT).show();
                }else{
                    if(Utility.isConnectingToInternet(getApplicationContext())){
                        for(int i=0;i<custom_fields.length();i++){
                            try {
                                custom_fields.getJSONObject(i);
                                finaldata.put(custom_fields.getJSONObject(i));
                                System.out.println("custom_fields final=="+finaldata);

                            } catch (JSONException e) {
                                e.printStackTrace();
                            }
                        }

                        Set<String> customdata=new HashSet<String>();
                        JSONArray tempArray=new JSONArray();
                        for(int i=0;i<finaldata.length();i++){
                            try {
                                String stationCode = finaldata.getJSONObject(i).getString("custom_field_id");
                                String fieldvalue = finaldata.getJSONObject(i).getString("field_value");
                                System.out.println("stationCode== " + stationCode);
                                if(customdata.contains(stationCode)){
                                    System.out.println("tempArraycontains== " + tempArray);
                                    for(int j=0;j<tempArray.length();j++){
                                        if(tempArray.getJSONObject(j).getString("custom_field_id").equals(stationCode)){
                                            tempArray.getJSONObject(j).put("field_value",fieldvalue);
                                        }
                                    }
                                    continue;
                                } else{
                                    customdata.add(stationCode);
                                    tempArray.put(finaldata.getJSONObject(i));
                                }
                            } catch (JSONException e) {

                                e.printStackTrace();
                            }
                        }


                        finaldata= tempArray; //assign temp to original
                        System.out.println("tempArray== " + tempArray);

                        params.put("date",receivingString);
                        params.put("patientId", Utility.getSharedPreferences(getApplicationContext(), "patient_id"));
                        params.put("time", clickedItem);
                        params.put("doctor", doctor_id);
                        params.put("message", message.getText().toString());
                        params.put("live_consult", text);
                        params.put("specialist", specialist_id);
                        params.put("patient_type", "old");
                        params.put("priority", priorityid);
                        params.put("global_shift", shift_id);

                        try {
                            JSONObject obj=new JSONObject(params);
                            obj.put("custom_fields",finaldata);
                            Log.e("params ", obj.toString());
                            System.out.println("Result Params=="+obj.toString());
                            getDataFromApi(obj.toString());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }else{
                        makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });

        ArrayAdapter<String> doctorAdapter = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, doctorlist);
        doctorAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Doctor.setAdapter(doctorAdapter);
        Doctor.setSelection(0);
        Doctor.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                params.clear();
                doctor_id = doctoridlist.get(i);
                params.put("doctor_id",doctor_id);
                JSONObject obj=new JSONObject(params);
                Log.e("params ", obj.toString());
                if(Utility.isConnectingToInternet(getApplicationContext())){
                    getShiftFromApi(obj.toString());
                }else{
                    makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        ArrayAdapter<String> dataAdapters = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, specialistlist);
        dataAdapters.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Specialist.setAdapter(dataAdapters);
        Specialist.setSelection(0);
        Specialist.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                   @Override
                   public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                       params.clear();
                       specialist_id = specialistidlist.get(i);
                       params.put("specialistID",specialist_id);
                       JSONObject obj=new JSONObject(params);
                       Log.e("params ", obj.toString());
                       if(Utility.isConnectingToInternet(getApplicationContext())){
                           getDoctorFromApi(obj.toString());
                       }else{
                           makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                       }
                       Doctor.setSelection(0);

                   }
                   @Override
                   public void onNothingSelected(AdapterView<?> adapterView) { }
               });


        ArrayAdapter<String> shiftAdapter = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, shiftlist);
        shiftAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Shift.setAdapter(shiftAdapter);
        Shift.setSelection(0);
        Shift.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                params.clear();
                shift_id = shiftidlist.get(i);
                params.put("date",receivingString);
                params.put("doctor_id",doctor_id);
                params.put("global_shift",shift_id);
                JSONObject obj=new JSONObject(params);
                Log.e("shift params ", obj.toString());
                System.out.println("shift params "+ obj.toString());
                if(Utility.isConnectingToInternet(getApplicationContext())){
                    getSlotFromApi(obj.toString());
                    final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    final String getCurrentDateTime = sdf.format(c.getTime());
                    final String getstartTime=receivingString;

                    if (getCurrentDateTime.compareTo(getstartTime) < 0) {

                        System.out.println("helloo current date");
                    }else{
                        System.out.println("hiii current date");

                    }
                }else{
                    makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
                Slot.setSelection(0);
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        ArrayAdapter<String> slotAdapter = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, slotlist);
        slotAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Slot.setAdapter(slotAdapter);
        Slot.setSelection(0);
        Slot.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {

                slot_id = slotidlist.get(i);

                if(!slot_id.equals("0")){
                    Calendar c = Calendar.getInstance();
                    final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                    final String getCurrentDateTime = sdf.format(c.getTime());
                    final String getstartTime= receivingString;
                    if (getCurrentDateTime.compareTo(getstartTime) > 0) {
                       /* Snackbar snackbar = Snackbar.make(recyclerview, "Appointment Time Is Expired",Snackbar.LENGTH_SHORT);
                        snackbar.show();*/
                        makeText(PatientAddAppoinment.this, "Appointment Time Is Expired", Toast.LENGTH_SHORT).show();

                        dialog = new Dialog(PatientAddAppoinment.this);
                        dialog.setContentView(R.layout.bottom_sheet);
                        dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
                        dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;

                        TextView headerTV = dialog.findViewById(R.id.patient_bottomSheet__header);
                        ImageView crossBtn = dialog.findViewById(R.id.patient_bottomSheet__crossBtn);
                        bottom_timeslots = dialog.findViewById(R.id.bottom_timeslots);
                        params.clear();
                        params.put("doctor_id", doctor_id);
                        params.put("shift", slot_id);
                        params.put("date", receivingString);
                        params.put("global_shift", shift_id);
                        JSONObject obj=new JSONObject(params);
                        Log.e("slots by shift params ", obj.toString());
                        System.out.println("slots by shift params "+obj.toString());
                        if(Utility.isConnectingToInternet(getApplicationContext())){
                            getslotbyshift(obj.toString());
                        }else{
                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                        headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
                        headerTV.setText(getString(R.string.time));
                        crossBtn.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {
                                dialog.dismiss();
                            }
                        });
                        dialog.show();

                        if(!time.getText().equals("")){
                            time.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View view) {
                                    dialog.show();
                                }
                            });
                        }
                    }else{

                        dialog = new Dialog(PatientAddAppoinment.this);
                        dialog.setContentView(R.layout.bottom_sheet);
                        dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
                        dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;

                        TextView headerTV = dialog.findViewById(R.id.patient_bottomSheet__header);
                        ImageView crossBtn = dialog.findViewById(R.id.patient_bottomSheet__crossBtn);
                        bottom_timeslots = dialog.findViewById(R.id.bottom_timeslots);
                        params.clear();
                        params.put("doctor_id", doctor_id);
                        params.put("shift", slot_id);
                        params.put("date", receivingString);
                        params.put("global_shift", shift_id);
                        JSONObject obj=new JSONObject(params);
                        Log.e("slots by shift params ", obj.toString());
                        System.out.println("slots by shift params "+obj.toString());
                        if(Utility.isConnectingToInternet(getApplicationContext())){
                            getslotbyshift(obj.toString());
                        }else{
                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                        headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
                        headerTV.setText(getString(R.string.time));
                        crossBtn.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {
                                dialog.dismiss();
                            }
                        });
                        dialog.show();

                        if(!time.getText().equals("")){
                            time.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View view) {
                                    dialog.show();
                                }
                            });
                        }
                    }

                }
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        livelist.add("No");
        livelist.add("Yes");
        liveidlist.add("no");
        liveidlist.add("yes");
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, livelist);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Live.setAdapter(dataAdapter);
        Live.setSelection(0);
        Live.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                 text = liveidlist.get(i);
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });

        prioritylist.add("Normal");
        prioritylist.add("Urgent");
        prioritylist.add("Very Urgent");
        prioritylist.add("Low");
        priorityidlist.add("1");
        priorityidlist.add("2");
        priorityidlist.add("3");
        priorityidlist.add("5");
        ArrayAdapter<String> priorityAdapter = new ArrayAdapter<String>(PatientAddAppoinment.this, android.R.layout.simple_spinner_item, prioritylist);
        priorityAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Priority.setAdapter(priorityAdapter);
        Priority.setSelection(0);
        Priority.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                priorityid = priorityidlist.get(i);
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    private void decorate() {
        Picasso.with(getApplicationContext()).load(Utility.getSharedPreferences(this, "userImage")).placeholder(R.drawable.placeholder_user).into(overlapImage);
        layoutTop.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        actionBar.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        save_patient_appoinment.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        }
    }

    private void getslotbyshift (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getslotbyshiftUrl;
        System.out.println("URL="+url);

        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        ArrayList<String> timelist=new ArrayList<String>();
                        ArrayList<String> filledlist=new ArrayList<String>();
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                timelist.add(dataArray.getJSONObject(i).getString("time"));
                                filledlist.add(dataArray.getJSONObject(i).getString("filled"));
                            }

                        }

                        bottom_timeslots.setVisibility(View.VISIBLE);
                        SlotsAdapter slotAdapter = new SlotsAdapter(PatientAddAppoinment.this, timelist,filledlist,bottom_timeslots);
                        bottom_timeslots.setAdapter(slotAdapter);
                        bottom_timeslots.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                            @Override
                            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                                clickedItem=(String) bottom_timeslots.getItemAtPosition(position);
                                time.setText(clickedItem);
                                /*clickedItem= clickedItem.replaceAll("\"", "");

                                clickedItem= clickedItem.replaceAll("AM", "");
                                clickedItem= clickedItem.replaceAll("PM", "");
                                clickedItem = clickedItem.trim();*/
                                dialog.dismiss();
                            }
                        });
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }
    private void getDataFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.addAppointmentUrl;
        Log.e("URL: ",url);
        Log.e("bodyParams ",bodyParams);
        Log.e("userId ", Utility.getSharedPreferences(getApplicationContext(), "userId"));
        Log.e("accessToken ", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                System.out.println("Result==="+result);
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);

                        JSONObject obj = new JSONObject(result);
                        System.out.println("obj==="+obj.toString());
                        String results = obj.getString("result");
                        Toast.makeText(PatientAddAppoinment.this, getApplicationContext().getString(R.string.successAppoinments), Toast.LENGTH_SHORT).show();
                        finish();
                        Intent intent=new Intent(PatientAddAppoinment.this, PatientAppoinmentList.class);
                        startActivity(intent);
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {

                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }
    private void getShiftFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.doctorshiftbyidUrl;

        Log.e("URL: ",url);
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        shiftlist.clear();
                        shiftidlist.clear();
                        shiftlist.add("Select");
                        shiftidlist.add("0");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                shiftlist.add(dataArray.getJSONObject(i).getString("name"));
                                shiftidlist.add(dataArray.getJSONObject(i).getString("id"));
                            }

                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }
    private void getSlotFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getslotUrl;

        Log.e("URL: ",url);
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");

                        slotlist.clear();
                        slotidlist.clear();
                        slotlist.add("Select");
                        slotidlist.add("0");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                slotlist.add(dataArray.getJSONObject(i).getString("start_time")+"-"+dataArray.getJSONObject(i).getString("end_time"));
                                slotidlist.add(dataArray.getJSONObject(i).getString("id"));

                            }
                        }

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }
    private void getScannerDataFromApi () {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getSpecialistfronttUrl;

        Log.e("URL: ",url);
        Log.e("userId ", Utility.getSharedPreferences(getApplicationContext(), "userId"));
        Log.e("accessToken ", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("specialist");

                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                specialistlist.add(dataArray.getJSONObject(i).getString("specialist_name"));
                                specialistidlist.add(dataArray.getJSONObject(i).getString("id"));
                            }

                    }
                } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getCustomFieldApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getcustomfieldsUrl;

        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("custom_fields");
                        if(dataArray.length() != 0){
                            for(int i=0; i<dataArray.length(); i++) {
                                JSONObject appoint = dataArray.getJSONObject(i);
                                idlist.add(appoint.getString("id"));
                                nameList.add(appoint.getString("name"));
                                typeList.add(appoint.getString("type"));
                            }
                            adapter.notifyDataSetChanged();
                        }else{

                        }

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getDoctorFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getDoctorfrontUrl;

        Log.e("URL: ",url);
        Log.e("userId ", Utility.getSharedPreferences(getApplicationContext(), "userId"));
        Log.e("accessToken ", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("doctors");
                        doctorlist.clear();
                        doctoridlist.clear();
                        doctorlist.add("Select");
                        doctoridlist.add("0");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                doctorlist.add(dataArray.getJSONObject(i).getString("name")+" "+dataArray.getJSONObject(i).getString("surname")+"("+dataArray.getJSONObject(i).getString("employee_id")+")");
                                doctoridlist.add(dataArray.getJSONObject(i).getString("id"));
                            }

                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    @Override
    public void onSetValues(JSONArray customfield_data) {
        custom_fields=customfield_data;
        System.out.println("custom_fields="+custom_fields);

    }
}
