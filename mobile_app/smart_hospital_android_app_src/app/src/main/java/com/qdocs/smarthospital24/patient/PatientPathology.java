package com.qdocs.smarthospital24.patient;

import android.app.ProgressDialog;
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
import com.qdocs.smarthospital24.adapters.PatientPathologyAdapter;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.PathologyModel;
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

public class PatientPathology extends BaseActivity {

    RecyclerView pathologyListView;
    LinearLayout nodata_layout;
    ArrayList<PathologyModel> pathology_detail_list = new ArrayList<>();
    PatientPathologyAdapter adapter;
    SwipeRefreshLayout pullToRefresh;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDateFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_pathology_activity, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.Pathology));
        pathologyListView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);

        adapter = new PatientPathologyAdapter(PatientPathology.this, pathology_detail_list,null);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        pathologyListView.setLayoutManager(mLayoutManager);
        pathologyListView.setItemAnimator(new DefaultItemAnimator());
        pathologyListView.setAdapter(adapter);
        pullToRefresh = findViewById(R.id.pullToRefresh);

        pullToRefresh.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                pullToRefresh.setRefreshing(true);
                    loaddata(); //your code
            }
        });
        loaddata();
    }

    public void loaddata(){
        if(Utility.isConnectingToInternet(getApplicationContext())){
            params.put("patient_id", Utility.getSharedPreferences(getApplicationContext(), Constants.patient_id));
            JSONObject obj=new JSONObject(params);
            Log.e("params ", obj.toString());
            getDataFromApi(obj.toString());//Api Call
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

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getPathologyDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                pullToRefresh.setRefreshing(false);
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        pathology_detail_list.clear();
                        if(dataArray.length() != 0) {
                            pathology_detail_list.clear();
                            for(int i = 0; i < dataArray.length(); i++) {
                                String defaultDateTimeFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
                                PathologyModel pathologyModel = new PathologyModel();

                                pathologyModel.setId(dataArray.getJSONObject(i).getString("id"));
                                pathologyModel.setPatient_name(dataArray.getJSONObject(i).getString("patient_name"));
                                pathologyModel.setDate(Utility.parseDate("yyyy-MM-dd hh:mm:ss", defaultDateTimeFormat,dataArray.getJSONObject(i).getString("date")));
                                pathologyModel.setDoctor_name(dataArray.getJSONObject(i).getString("doctor_name"));
                                pathologyModel.setPaid_amount(dataArray.getJSONObject(i).getString("paid_amount"));
                                pathologyModel.setNet_amount(dataArray.getJSONObject(i).getString("net_amount"));
                                pathologyModel.setNote(dataArray.getJSONObject(i).getString("note"));
                                pathologyModel.setTpa(dataArray.getJSONObject(i).getString("organisation_name"));
                                pathologyModel.setTpa_id(dataArray.getJSONObject(i).getString("insurance_id"));
                                pathologyModel.setTpa_validity(dataArray.getJSONObject(i).getString("insurance_validity"));
                                pathologyModel.setCase_reference_id(dataArray.getJSONObject(i).getString("case_reference_id"));
                                JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                                ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    CustomFieldModel customFieldModel = new CustomFieldModel();
                                    customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                    customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                    customArrayList.add(customFieldModel);
                                }
                                pathologyModel.setCustomfield(customArrayList);
                                pathology_detail_list.add(pathologyModel);
                            }
                            adapter.notifyDataSetChanged();
                        } else {
                            pullToRefresh.setVisibility(View.GONE);
                            nodata_layout.setVisibility(View.VISIBLE);
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
                Toast.makeText(PatientPathology.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientPathology.this);//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }
}


