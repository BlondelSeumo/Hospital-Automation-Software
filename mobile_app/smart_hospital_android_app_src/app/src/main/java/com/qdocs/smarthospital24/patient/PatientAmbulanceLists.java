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
import com.qdocs.smarthospital24.adapters.AmbulanceListAdapter;
import com.qdocs.smarthospital24.model.AmbulanceModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
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

public class PatientAmbulanceLists extends BaseActivity {

    RecyclerView recyclerView;
    LinearLayout nodata_layout;
    AmbulanceListAdapter adapter;
    SwipeRefreshLayout pullToRefresh;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDatetimeFormat, currency;
    ArrayList<AmbulanceModel> ambulance_detail_list = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_ambulance, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.Ambulance));
        recyclerView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        adapter = new AmbulanceListAdapter(PatientAmbulanceLists.this,ambulance_detail_list,null);
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
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getAmbulanceDetailsUrl;

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
                        ambulance_detail_list.clear();

                        if(dataArray.length() != 0){

                           for(int i=0; i<dataArray.length(); i++) {

                               AmbulanceModel ambulanceModel = new AmbulanceModel();

                               ambulanceModel.setId(dataArray.getJSONObject(i).getString("id"));
                               ambulanceModel.setDriver(dataArray.getJSONObject(i).getString("driver"));
                               ambulanceModel.setDriver_contact(dataArray.getJSONObject(i).getString("driver_contact"));
                               ambulanceModel.setVehicle_model(dataArray.getJSONObject(i).getString("vehicle_model"));
                               ambulanceModel.setVehicle_no(dataArray.getJSONObject(i).getString("vehicle_no"));
                               ambulanceModel.setAmount(dataArray.getJSONObject(i).getString("amount"));
                               ambulanceModel.setNet_amount(dataArray.getJSONObject(i).getString("net_amount"));
                               ambulanceModel.setPaid_amount(dataArray.getJSONObject(i).getString("paid_amount"));
                               ambulanceModel.setCharge_name(dataArray.getJSONObject(i).getString("charge_name"));
                               ambulanceModel.setCharge_category_name(dataArray.getJSONObject(i).getString("charge_category_name"));
                               ambulanceModel.setNote(dataArray.getJSONObject(i).getString("note"));
                               ambulanceModel.setDiscount_percentage(dataArray.getJSONObject(i).getString("discount_percentage"));
                               ambulanceModel.setDiscount(dataArray.getJSONObject(i).getString("discount"));
                               ambulanceModel.setCase_reference_id(dataArray.getJSONObject(i).getString("case_reference_id"));

                               String amount=String.valueOf((Double.parseDouble(dataArray.getJSONObject(i).getString("tax_percentage"))/100)*Double.parseDouble(dataArray.getJSONObject(i).getString("amount")));
                               ambulanceModel.setTax_percentage(amount+"("+dataArray.getJSONObject(i).getString("tax_percentage")+"%)");



                               Float discount_amount=Float.valueOf(String.valueOf((Double.parseDouble(dataArray.getJSONObject(i).getString("discount_percentage"))/100)*Double.parseDouble(dataArray.getJSONObject(i).getString("amount"))));
                               Float tax_amount=Float.valueOf(String.valueOf((Double.parseDouble(dataArray.getJSONObject(i).getString("tax_percentage"))/100)*(Double.parseDouble(dataArray.getJSONObject(i).getString("amount"))-Double.parseDouble(String.format("%.2f", discount_amount)))));
                               ambulanceModel.setTax_percentage( String.format("%.2f", tax_amount)+"("+dataArray.getJSONObject(i).getString("tax_percentage")+"%)");
                               System.out.println("tax_amount=="+String.format("%.2f", tax_amount));
                              // discountList.add( String.format("%.2f", discount_amount)+"("+dataArray.getJSONObject(i).getString("discount_percentage")+"%)");






                               JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                               ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                               for(int j = 0; j < customArray.length(); j++) {
                                   CustomFieldModel customFieldModel = new CustomFieldModel();
                                   customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                   customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                   customArrayList.add(customFieldModel);
                               }

                               ambulanceModel.setCustomfield(customArrayList);
                               ambulance_detail_list.add(ambulanceModel);
                           }
                        recyclerView.setAdapter(adapter);
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
                Toast.makeText(PatientAmbulanceLists.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientAmbulanceLists.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}
