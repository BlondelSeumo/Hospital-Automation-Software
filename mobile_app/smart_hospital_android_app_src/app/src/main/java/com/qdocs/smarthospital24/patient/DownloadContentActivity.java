package com.qdocs.smarthospital24.patient;

import static android.widget.Toast.makeText;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.Toast;

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
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.ShareContentListAdapter;
import com.qdocs.smarthospital24.model.DownloadContentModel;
import com.qdocs.smarthospital24.model.ShareContentModel;
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

public class DownloadContentActivity extends BaseActivity {
    public String defaultDateFormat,defaultDatetimeFormat, currency;
    RecyclerView recyclerView;
    LinearLayout nodata_layout;
    ShareContentListAdapter adapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    ArrayList<ShareContentModel> download_content = new ArrayList<>();
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_download_content, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        loaddata();

        titleTV.setText(getApplicationContext().getString(R.string.content));

        recyclerView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);

        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        adapter = new ShareContentListAdapter(DownloadContentActivity.this,download_content,null);
        recyclerView.setAdapter(adapter);
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
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getsharecontentlistUrl;

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
                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        JSONArray dataArray = object.getJSONArray("contentlist");
                        download_content.clear();
                        if(dataArray.length() != 0){
                            for(int i=0; i<dataArray.length(); i++) {
                                ShareContentModel shareContentModel=new ShareContentModel();
                                shareContentModel.setId(dataArray.getJSONObject(i).getString("id"));
                                shareContentModel.setShare_date(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dataArray.getJSONObject(i).getString("share_date")));
                                shareContentModel.setValid_upto(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dataArray.getJSONObject(i).getString("valid_upto")));
                                shareContentModel.setDate(dataArray.getJSONObject(i).getString("valid_upto"));
                                shareContentModel.setTitle(dataArray.getJSONObject(i).getString("title"));
                                shareContentModel.setSharedby(dataArray.getJSONObject(i).getString("name")+" "+dataArray.getJSONObject(i).getString("surname")+"("+dataArray.getJSONObject(i).getString("employee_id")+")");

                                JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("content");
                                ArrayList<DownloadContentModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    DownloadContentModel downloadContentModel = new DownloadContentModel();
                                    downloadContentModel.setReal_name(customArray.getJSONObject(j).getString("real_name"));
                                    downloadContentModel.setFile_type(customArray.getJSONObject(j).getString("file_type"));
                                    downloadContentModel.setImg_name(customArray.getJSONObject(j).getString("img_name"));
                                    downloadContentModel.setVid_url(customArray.getJSONObject(j).getString("vid_url"));
                                    downloadContentModel.setThumb_path(customArray.getJSONObject(j).getString("thumb_path"));
                                    downloadContentModel.setThumb_name(customArray.getJSONObject(j).getString("thumb_name"));
                                    customArrayList.add(downloadContentModel);
                                }
                                shareContentModel.setContent(customArrayList);
                                download_content.add(shareContentModel);
                            }
                            adapter.notifyDataSetChanged();
                        }else{

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
                Toast.makeText(DownloadContentActivity.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(DownloadContentActivity.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}