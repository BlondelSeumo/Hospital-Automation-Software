package com.qdocs.smarthospital24.patient;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.Toast;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.PatientAddAppoinment;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.PatientAppoinmentListAdapter;
import com.qdocs.smarthospital24.model.AppointmentModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;
import static android.widget.Toast.makeText;

public class PatientAppoinmentList extends BaseActivity {

    RecyclerView recyclerView;
    LinearLayout nodata_layout;
    PatientAppoinmentListAdapter adapter;
    SwipeRefreshLayout pullToRefresh;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDateFormat,defaultDatetimeFormat, currency;
    ArrayList<AppointmentModel> appointment_detail = new ArrayList<>();
    FrameLayout add_appointment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_appoinment, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        loaddata();

        titleTV.setText(getApplicationContext().getString(R.string.MyAppointments));
        add_appointment = (FrameLayout) findViewById(R.id.add_appointment);
        add_appointment.setVisibility(View.VISIBLE);
        recyclerView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);
        pullToRefresh = (SwipeRefreshLayout) findViewById(R.id.pullToRefresh);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        adapter = new PatientAppoinmentListAdapter(PatientAppoinmentList.this,appointment_detail,null);
        recyclerView.setAdapter(adapter);

        pullToRefresh.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                pullToRefresh.setRefreshing(true);
                loaddata();
            }
        });

        add_appointment.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(PatientAppoinmentList.this, PatientAddAppoinment.class);
                SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd");
                String date = df.format(Calendar.getInstance().getTime());
                intent.putExtra("formattedDate", date);
                startActivity(intent);
            }
        });
    }

    public  void  loaddata(){
        if(Utility.isConnectingToInternet(getApplicationContext())){
            params.put("patient_id", Utility.getSharedPreferences(getApplicationContext(), "patient_id"));
            JSONObject obj=new JSONObject(params);
            Log.e("params ", obj.toString());
            getDataFromApi(obj.toString());
        }else{
            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }
    }


    private void getDataFromApi (String bodyParams) {
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getAppointmentUrl;

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
                pullToRefresh.setRefreshing(false);
                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        JSONArray dataArray = object.getJSONArray("result");
                        appointment_detail.clear();
                        if(dataArray.length() != 0){
                             for(int i=0; i<dataArray.length(); i++) {
                                 AppointmentModel appointmentModel=new AppointmentModel();
                                 appointmentModel.setId(dataArray.getJSONObject(i).getString("id"));
                                 appointmentModel.setDate(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                                 appointmentModel.setName(dataArray.getJSONObject(i).getString("name"));
                                 appointmentModel.setSurname(dataArray.getJSONObject(i).getString("surname"));
                                 appointmentModel.setMessage(dataArray.getJSONObject(i).getString("message"));
                                 appointmentModel.setEmployee_id(dataArray.getJSONObject(i).getString("employee_id"));
                                 appointmentModel.setAppointment_status(dataArray.getJSONObject(i).getString("appointment_status"));
                                 appointmentModel.setSpecialist_name(dataArray.getJSONObject(i).getString("specialist_name"));
                                 appointmentModel.setPriority(dataArray.getJSONObject(i).getString("priorityname"));
                                 appointmentModel.setAppointment_serial_no(dataArray.getJSONObject(i).getString("appointment_serial_no"));
                                 appointmentModel.setLive_consult(dataArray.getJSONObject(i).getString("live_consult"));
                                 appointmentModel.setSource(dataArray.getJSONObject(i).getString("source"));
                                 JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                                 ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                 for(int j = 0; j < customArray.length(); j++) {
                                     CustomFieldModel customFieldModel = new CustomFieldModel();
                                     customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                     customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                     customArrayList.add(customFieldModel);
                                 }
                                 appointmentModel.setCustomfield(customArrayList);
                                 appointment_detail.add(appointmentModel);
                             }
                             adapter.notifyDataSetChanged();
                        }else{
                            pullToRefresh.setVisibility(View.GONE);
                            nodata_layout.setVisibility(View.VISIBLE);
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientAppoinmentList.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
                    VolleyLog.wtf("Unsupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");
                    return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAppoinmentList.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}
