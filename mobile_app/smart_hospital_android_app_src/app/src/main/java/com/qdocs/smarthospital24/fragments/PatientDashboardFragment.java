package com.qdocs.smarthospital24.fragments;

import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.patient.PatientAppoinmentList;
import com.qdocs.smarthospital24.patient.PatientNotification;
import com.qdocs.smarthospital24.patient.PatientTasks;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;
import static android.widget.Toast.makeText;

public class PatientDashboardFragment extends Fragment {

    RelativeLayout notificationLayout, appointLayout, pendingTaskLayout;
    TextView notificationValue, appointmentValue, pendingTaskValue;
    CardView notificationCard, appointmentCard, pendingTaskCard;
    FrameLayout calenderFrame;
    public Map<String, String> headers = new HashMap<String, String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public PatientDashboardFragment() { }
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.patient_dashboard_fragment, container, false);
        notificationLayout = mainView.findViewById(R.id.dashboard_fragment_notificationView);
        appointLayout = mainView.findViewById(R.id.dashboard_fragment_appointView);
        pendingTaskLayout = mainView.findViewById(R.id.dashboard_fragment_pendingTaskView);
        notificationCard = mainView.findViewById(R.id.dashboard_fragment_notificationCard);
        appointmentCard = mainView.findViewById(R.id.dashboard_fragment_appointmentCard);
        pendingTaskCard = mainView.findViewById(R.id.dashboard_fragment_pendingTaskCard);
        notificationValue = mainView.findViewById(R.id.dashboard_fragment_notification_value);
        appointmentValue = mainView.findViewById(R.id.dashboard_fragment_appointment_value);
        pendingTaskValue = mainView.findViewById(R.id.dashboard_fragment_pendingTask_value);
        calenderFrame = mainView.findViewById(R.id.dashboardViewPager);

        notificationLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent asd = new Intent(getActivity().getApplicationContext(), PatientNotification.class);
                getActivity().startActivity(asd);
            }
        });
        appointLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent asd = new Intent(getActivity().getApplicationContext(), PatientAppoinmentList.class);
                getActivity().startActivity(asd);
            }
        });
        pendingTaskLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent asd = new Intent(getActivity().getApplicationContext(), PatientTasks.class);
                getActivity().startActivity(asd);
            }
        });
        Log.e("STATUS", "onCreateView");
        loadData();

        return mainView;
    }
    private void loadData() {
        decorate();
        loadFragment(new DashboardCalender());
        params.put("patient_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.patient_id));
        params.put("user_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.userId));
        params.put("date_from", getDateOfMonth(new Date(), "first"));
        params.put("date_to", getDateOfMonth(new Date(), "last"));
        JSONObject obj = new JSONObject(params);
        Log.e("params ", obj.toString());
        System.out.println("dashboard url==="+obj.toString());
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }



        try {
            JSONArray modulesArray = new JSONArray(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.modulesArray));

            if (modulesArray.length() != 0) {
                ArrayList<String> moduleCodeList = new ArrayList<String>();
                ArrayList<String> moduleStatusList = new ArrayList<String>();

                for (int i = 0; i < modulesArray.length(); i++) {
                    if (modulesArray.getJSONObject(i).getString("short_code").equals("my_appointments")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        appointmentCard.setVisibility(View.GONE);
                    }
                    if (modulesArray.getJSONObject(i).getString("short_code").equals("calendar_to_do_list")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        pendingTaskCard.setVisibility(View.GONE);
                        calenderFrame.setVisibility(View.GONE);
                    }
                }
            }
        } catch (JSONException e) {
            Log.d("Error", e.toString());
        }
    }
    private void getDataFromApi(String bodyParams) {
        Log.e("RESULT PARAMS", bodyParams);

        final ProgressDialog pd = new ProgressDialog(getActivity());
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl") + Constants.getDashboardUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        System.out.println("result==="+result);
                        JSONObject object = new JSONObject(result);
                        //TODO success
                        String success = "1"; //object.getString("success");
                        if (success.equals("1")) {
                            notificationValue.setText(object.getString("notifications_count"));
                            appointmentValue.setText(object.getString("appointmentcount"));
                            pendingTaskValue.setText(object.getString("incomplete_task"));

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
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity());//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }
    private void decorate() {
        notificationLayout.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.secondaryColour)));
        appointLayout.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.secondaryColour)));
        pendingTaskLayout.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.secondaryColour)));
    }
    private void loadFragment(Fragment fragment) {
        FragmentTransaction transaction = getActivity().getSupportFragmentManager().beginTransaction();
        transaction.replace(calenderFrame.getId(), fragment);
        transaction.commit();
    }
    public static String getDateOfMonth(Date date, String index) {
        Calendar cal = Calendar.getInstance();
        cal.setTime(date);
        if (index.equals("first")) {
            cal.set(Calendar.DAY_OF_MONTH, cal.getActualMinimum(Calendar.DAY_OF_MONTH));
        } else {
            cal.set(Calendar.DAY_OF_MONTH, cal.getActualMaximum(Calendar.DAY_OF_MONTH));
        }
        SimpleDateFormat dateFormatter = new SimpleDateFormat("yyyy-MM-dd");
        return dateFormatter.format(cal.getTime());
    }
}