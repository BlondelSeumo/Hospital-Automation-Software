package com.qdocs.smarthospital24.patient;

import android.app.ProgressDialog;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
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
import com.qdocs.smarthospital24.adapters.PatientIpdDischargeAdapter;
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

public class PatientIpdPatientLists extends BaseActivity {

    RecyclerView pathologyListView;
    ArrayList<String> patient_idList = new ArrayList<String>();
    ArrayList<String> bedList = new ArrayList<String>();
    ArrayList<String> patientNameList = new ArrayList<String>();
    ArrayList<String> ipd_nolist = new ArrayList<String>();
    ArrayList<String> ipdidlist = new ArrayList<String>();
    ArrayList<String> genderlist = new ArrayList<String>();
    ArrayList<String> mobilenolist = new ArrayList<String>();
    ArrayList<String> consultantlist = new ArrayList<String>();
    ArrayList<String> credit_limitlist = new ArrayList<String>();
    ArrayList<String> dischargedlist = new ArrayList<String>();
    ArrayList<String> paymentlist = new ArrayList<String>();
    ArrayList<String> chargeslist = new ArrayList<String>();
    ArrayList<String> duelist = new ArrayList<String>();
    PatientIpdDischargeAdapter adapter;
    public String defaultDateFormat,currency;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_ipd_discharge_activity, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.ipd_Patient));
        pathologyListView = (RecyclerView) findViewById(R.id.patientdischarge_listview);
        adapter = new PatientIpdDischargeAdapter(PatientIpdPatientLists.this,bedList,
                patientNameList,ipd_nolist,genderlist,mobilenolist, consultantlist,credit_limitlist,paymentlist,chargeslist,dischargedlist,ipdidlist);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        pathologyListView.setLayoutManager(mLayoutManager);
        pathologyListView.setItemAnimator(new DefaultItemAnimator());
        pathologyListView.setAdapter(adapter);
        if(Utility.isConnectingToInternet(getApplicationContext())){
            params.put("patient_id", Utility.getSharedPreferences(getApplicationContext(), Constants.patient_id));
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
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.patientipdlistUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        float duecharge=0,charge=0;
                        float payment= 0;

                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                patientNameList .add(dataArray.getJSONObject(i).getString("patient_name"));
                                bedList.add(dataArray.getJSONObject(i).getString("bed_name")+"-"+dataArray.getJSONObject(i).getString("bedgroup_name")+"-"+dataArray.getJSONObject(i).getString("floor_name"));
                                ipd_nolist.add(dataArray.getJSONObject(i).getString("id"));
                                ipdidlist.add(dataArray.getJSONObject(i).getString("id"));
                                genderlist.add(dataArray.getJSONObject(i).getString("gender"));
                                mobilenolist.add(dataArray.getJSONObject(i).getString("mobileno"));
                                consultantlist.add(dataArray.getJSONObject(i).getString("name")+" "+dataArray.getJSONObject(i).getString("surname")+"("+dataArray.getJSONObject(i).getString("employee_id")+")");
                                credit_limitlist.add(dataArray.getJSONObject(i).getString("id"));
                                dischargedlist.add(dataArray.getJSONObject(i).getString("discharged"));
                                paymentlist.add(dataArray.getJSONObject(i).getString("payment"));
                                chargeslist.add(dataArray.getJSONObject(i).getString("charges"));

                            }
                            adapter.notifyDataSetChanged();
                        } else {
                            Toast.makeText(getApplicationContext(),getApplicationContext().getString(R.string.noData), Toast.LENGTH_SHORT).show();
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
                Toast.makeText(PatientIpdPatientLists.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientIpdPatientLists.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}
