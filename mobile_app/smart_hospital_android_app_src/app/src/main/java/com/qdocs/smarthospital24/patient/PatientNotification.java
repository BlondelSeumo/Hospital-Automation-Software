package com.qdocs.smarthospital24.patient;

import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
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
import com.qdocs.smarthospital24.adapters.PatientNotificationAdapter;
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

public class PatientNotification extends BaseActivity {

    ListView notificationList;
    PatientNotificationAdapter adapter;
    ArrayList<String> noticeTitleId = new ArrayList<String>();
    ArrayList<String> noticeTitleList = new ArrayList<String>();
    ArrayList<String> noticeDateList = new ArrayList<String>();
    ArrayList<String> noticeDescList = new ArrayList<String>();
    ArrayList<String> readList = new ArrayList<String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    SwipeRefreshLayout pullToRefresh;
    public String defaultDatetimeFormat, currency;
    FrameLayout delete_all;
    LinearLayout nodata_layout;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_notification_activity, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.notification));
        notificationList = (ListView) findViewById(R.id.recyclerview);
        delete_all = (FrameLayout) findViewById(R.id.delete_all);
        pullToRefresh = (SwipeRefreshLayout) findViewById(R.id.pullToRefresh);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);

        adapter = new PatientNotificationAdapter(PatientNotification.this, noticeTitleId, noticeTitleList, noticeDateList, noticeDescList,readList);
        notificationList.setAdapter(adapter);
        loaddata();
        delete_all.setVisibility(View.VISIBLE);


        delete_all.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                AlertDialog.Builder builder = new AlertDialog.Builder(PatientNotification.this);
                builder.setCancelable(false);
                builder.setTitle(PatientNotification.this.getString(R.string.deleteNotification));
                builder.setMessage(R.string.deleteNotificationmsg);
                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        if(Utility.isConnectingToInternet(getApplicationContext())){
                            params.put("patient_id", Utility.getSharedPreferences(PatientNotification.this, "patient_id"));
                            JSONObject obj=new JSONObject(params);
                            Log.e("params ", obj.toString());
                            deleteNotification(obj.toString());

                        }else{
                            makeText(PatientNotification.this, R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                    }
                });
                builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        dialogInterface.dismiss();
                    }
                });
                builder.show();
            }
        });

        pullToRefresh.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                pullToRefresh.setRefreshing(true);
                loaddata();
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
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+ Constants.getNotificationsUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                pullToRefresh.setRefreshing(false);
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        JSONArray dataArray = object.getJSONArray("resp");
                        if(dataArray.length() != 0) {
                        noticeTitleId.clear();
                        noticeTitleList.clear();
                        noticeDateList.clear();
                        noticeDescList.clear();
                        System.out.println("length== "+dataArray.length());
                        Log.e("length", dataArray.length()+"..");
                            for(int i = 0; i < dataArray.length(); i++) {
                                noticeTitleId.add(dataArray.getJSONObject(i).getString("id"));
                                noticeTitleList.add(dataArray.getJSONObject(i).getString("notification_title"));
                                noticeDateList.add(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                                noticeDescList.add(dataArray.getJSONObject(i).getString("notification_desc"));
                                readList.add(dataArray.getJSONObject(i).getString("read"));
                            }
                            adapter.notifyDataSetChanged();
                    } else{
                            nodata_layout.setVisibility(View.VISIBLE);
                            pullToRefresh.setVisibility(View.GONE);

                        }
                    }catch (JSONException e) {
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
                Toast.makeText(PatientNotification.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
                Log.e("Headers", headers.toString());
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientNotification.this); //Creating a Request Queue
        requestQueue.add(stringRequest);   //Adding request to the queue
    }

    private void deleteNotification (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+ Constants.deletesystemnotifications;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        String status = obj.getString("status");
                        final String message = obj.getString("message");
                        if(status.equals("success")){
                            runOnUiThread(new Runnable(){
                                public void run() {
                                    pd.dismiss();
                                    loaddata();
                                    Toast.makeText(getApplicationContext(),message, Toast.LENGTH_SHORT).show();
                                }
                            });
                        }else{
                            runOnUiThread(new Runnable(){
                                public void run() {
                                    pd.dismiss();
                                    Toast.makeText(getApplicationContext(),"Success", Toast.LENGTH_SHORT).show();
                                }
                            });
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
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientNotification.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
                Log.e("Headers", headers.toString());
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientNotification.this); //Creating a Request Queue
        requestQueue.add(stringRequest);   //Adding request to the queue
    }
}
