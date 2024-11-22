package com.qdocs.smarthospital24.patient;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
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
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.PatientLiveConsultationAdapter;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientLiveConsulation extends BaseActivity {

    RecyclerView recyclerView;
    LinearLayout nodata_layout;
    ArrayList<String> consultantList = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> titlelist= new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> create_by_namelist = new ArrayList<String>();
    ArrayList<String> statuslist = new ArrayList<String>();
    ArrayList<String> join_urllist = new ArrayList<String>();
    ArrayList<String> patient_namelist = new ArrayList<String>();
    PatientLiveConsultationAdapter adapter;
    SwipeRefreshLayout pullToRefresh;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDatetimeFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_ambulance, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.liveconsult));
        recyclerView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        adapter = new PatientLiveConsultationAdapter(getApplicationContext(),datelist,titlelist,consultantList,create_by_namelist,statuslist,join_urllist,patient_namelist,idlist);
        recyclerView.setAdapter(adapter);

        pullToRefresh = findViewById(R.id.pullToRefresh);
        pullToRefresh.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                pullToRefresh.setRefreshing(true);
                loaddata();
            }
        });
        loaddata();
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
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getliveconsultUrl;
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
                        JSONArray dataArray = object.getJSONArray("liveconsult");

                        datelist.clear();
                        consultantList.clear();
                        titlelist.clear();
                        create_by_namelist.clear();
                        idlist.clear();
                        statuslist.clear();
                        patient_namelist.clear();
                        join_urllist.clear();

                        if(dataArray.length() != 0){
                          for(int i=0; i<dataArray.length(); i++) {
                              consultantList.add(dataArray.getJSONObject(i).getString("create_for_name") + " " + dataArray.getJSONObject(i).getString("create_for_surname")+ " (" + dataArray.getJSONObject(i).getString("create_for_role_name")+ ": " + dataArray.getJSONObject(i).getString("create_for_employee_id")+")");
                              datelist.add(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                              titlelist.add(dataArray.getJSONObject(i).getString("title"));
                              idlist.add(dataArray.getJSONObject(i).getString("id"));
                              create_by_namelist.add(dataArray.getJSONObject(i).getString("create_by_name")+ " " + dataArray.getJSONObject(i).getString("create_by_surname")+ " (" + dataArray.getJSONObject(i).getString("create_by_employee_id")+")");
                              patient_namelist.add(dataArray.getJSONObject(i).getString("patient_name"));
                              statuslist.add(dataArray.getJSONObject(i).getString("status"));
                              join_urllist.add(dataArray.getJSONObject(i).getString("join_url"));
                              System.out.println("titlelist=="+titlelist);
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
                Toast.makeText(PatientLiveConsulation.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientLiveConsulation.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}
