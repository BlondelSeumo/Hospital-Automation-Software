package com.qdocs.smarthospital24.patient;

import android.app.ProgressDialog;
import android.content.Intent;
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
import com.qdocs.smarthospital24.adapters.PatientBloodBankAdapter;
import com.qdocs.smarthospital24.model.BloodBankModel;
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

public class PatientBloodBank extends BaseActivity {
    RecyclerView recyclerView;
    LinearLayout nodata_layout;public Map<String, String> params = new Hashtable<String, String>();
    ArrayList<BloodBankModel> bloodbank_detail_list = new ArrayList<>();
    PatientBloodBankAdapter adapter;
    SwipeRefreshLayout pullToRefresh;

    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDateFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.patient_bloodbanklist, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);
        component_issue=findViewById(R.id.component_issue);
        component_issue.setVisibility(View.VISIBLE);
        titleTV.setText(getApplicationContext().getString(R.string.BloodBank));
        recyclerView = (RecyclerView) findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) findViewById(R.id.nodata_layout);

        adapter = new PatientBloodBankAdapter(PatientBloodBank.this, bloodbank_detail_list,null);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);
        pullToRefresh = findViewById(R.id.pullToRefresh);

        pullToRefresh.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                pullToRefresh.setRefreshing(true);
                    loaddata(); //your code
            }
        });
        loaddata();

        component_issue.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(PatientBloodBank.this,PatientComponentIssue.class);
                startActivity(intent);
            }
        });
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
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getbloodbankDetailsUrl;

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
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        JSONArray jsonArray = object.getJSONArray("bloodissue");
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
                        if(jsonArray.length()!=0) {
                            bloodbank_detail_list.clear();
                            for(int i=0; i<jsonArray.length(); i++) {

                                BloodBankModel bloodBankModel = new BloodBankModel();

                                bloodBankModel.setId(jsonArray.getJSONObject(i).getString("id"));
                                bloodBankModel.setTechnician(jsonArray.getJSONObject(i).getString("technician"));
                                bloodBankModel.setPatient_name(jsonArray.getJSONObject(i).getString("patient_name"));
                                bloodBankModel.setDate_of_issue(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,jsonArray.getJSONObject(i).getString("date_of_issue")));
                                bloodBankModel.setDonor_name(jsonArray.getJSONObject(i).getString("donor_name"));
                                bloodBankModel.setCase_reference_id(jsonArray.getJSONObject(i).getString("case_reference_id"));
                                bloodBankModel.setBag_no(jsonArray.getJSONObject(i).getString("bag_no")+"("+jsonArray.getJSONObject(i).getString("volume")+" "+jsonArray.getJSONObject(i).getString("unit")+")");
                                bloodBankModel.setBlood_group(jsonArray.getJSONObject(i).getString("blood_group"));
                                bloodBankModel.setOrganisation_name(jsonArray.getJSONObject(i).getString("organisation_name"));
                                bloodBankModel.setInsurance_validity(Utility.parseDate("yyyy-MM-dd", defaultDatetimeFormat,jsonArray.getJSONObject(i).getString("insurance_validity")));
                                bloodBankModel.setInsurance_id(jsonArray.getJSONObject(i).getString("insurance_id"));
                                bloodBankModel.setAmount(jsonArray.getJSONObject(i).getString("amount"));
                                bloodBankModel.setNet_amount(jsonArray.getJSONObject(i).getString("net_amount"));
                                bloodBankModel.setPaid_amount(jsonArray.getJSONObject(i).getString("paid_amount"));
                                bloodBankModel.setTax_percentage(jsonArray.getJSONObject(i).getString("tax_percentage"));
                                bloodBankModel.setDiscount_percentage(jsonArray.getJSONObject(i).getString("discount_percentage"));
                                Float dis_amount=Float.valueOf(String.valueOf((Double.parseDouble(jsonArray.getJSONObject(i).getString("discount_percentage"))/100)*Double.parseDouble(jsonArray.getJSONObject(i).getString("amount"))));
                                bloodBankModel.setDiscount_amount(String.format("%.2f", dis_amount));
                                Float tax_amount=Float.valueOf(String.valueOf((Double.parseDouble(jsonArray.getJSONObject(i).getString("tax_percentage"))/100)*(Double.parseDouble(jsonArray.getJSONObject(i).getString("amount"))-Double.parseDouble(String.format("%.2f", dis_amount)))));
                                bloodBankModel.setTax_amount(String.format("%.2f", tax_amount));
                              //  bloodBankModel.setTransaction_id(jsonArray.getJSONObject(i).getString("transaction_id"));
                                JSONArray customArray = jsonArray.getJSONObject(i).getJSONArray("customfield");
                                ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    CustomFieldModel customFieldModel = new CustomFieldModel();
                                    customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                    customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                    customArrayList.add(customFieldModel);
                                }

                                bloodBankModel.setCustomfield(customArrayList);
                                bloodbank_detail_list.add(bloodBankModel);

                            }

                            adapter.notifyDataSetChanged();
                            pd.dismiss();
                        } else{
                            pullToRefresh.setVisibility(View.GONE);
                            nodata_layout.setVisibility(View.VISIBLE);
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                        pd.dismiss();
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
                Toast.makeText(PatientBloodBank.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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

        RequestQueue requestQueue = Volley.newRequestQueue(PatientBloodBank.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}


