package com.qdocs.smarthospital24.fragments;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.fragment.app.Fragment;
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
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.PatientIpdChargeAdapter;
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

public class PatientIPDChargeFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener {

    RecyclerView recyclerView;
    TextView total_charge;
    ArrayList<String> charge_typeList = new ArrayList<String>();
    ArrayList<String> charge_cateList = new ArrayList<String>();
    ArrayList<String> dateList = new ArrayList<String>();
    ArrayList<String> standard_chargeList = new ArrayList<String>();
    ArrayList<String> amountList = new ArrayList<String>();
    ArrayList<String> totalchargelist = new ArrayList<String>();
    ArrayList<String> apply_chargeList = new ArrayList<String>();
    ArrayList<String> nameList = new ArrayList<String>();
    ArrayList<String> taxList = new ArrayList<String>();
    ArrayList<String> discountList = new ArrayList<String>();
    ArrayList<String> qtyList = new ArrayList<String>();
    ArrayList<String> tpaList = new ArrayList<String>();
    PatientIpdChargeAdapter adapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdno;
    LinearLayout nodata_layout,data_layout;
    public PatientIPDChargeFragment(String ipdno) {
       this.ipdno=ipdno;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        loadData();
    }

    private void loadData() {
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            params.put("patient_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.patient_id));
            params.put("ipd_id",ipdno);
            JSONObject obj=new JSONObject(params);
            Log.e("params ", obj.toString());
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }

    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.fragment_ipd_charge, container, false);
        recyclerView = (RecyclerView) mainView.findViewById(R.id.charge_listview);
        total_charge = (TextView) mainView.findViewById(R.id.total_charge);
        nodata_layout =mainView.findViewById(R.id.nodata_layout);
        data_layout =mainView.findViewById(R.id.data_layout);
        adapter = new PatientIpdChargeAdapter(getActivity(), charge_typeList,dateList,
                charge_cateList,standard_chargeList,amountList,apply_chargeList,totalchargelist,nameList,taxList,qtyList,tpaList,discountList);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);
        loadData();
        return mainView;
    }

    @Override
    public void onRefresh() {
        loadData();
    }

    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.patientipddetailsUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {

                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("charges");
                        dateList.clear();
                        apply_chargeList.clear();
                        nameList.clear();
                        taxList.clear();
                        qtyList.clear();
                        charge_typeList.clear();
                        charge_cateList.clear();
                        standard_chargeList.clear();
                        amountList.clear();
                        discountList.clear();
                        tpaList.clear();
                        float sum = 0;
                        final String currency = Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.currency);
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                        if(dataArray.length() != 0) {
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);
                            for(int i = 0; i < dataArray.length(); i++) {
                                dateList.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                                apply_chargeList.add(dataArray.getJSONObject(i).getString("apply_charge"));
                                nameList.add(dataArray.getJSONObject(i).getString("name")+"\n"+dataArray.getJSONObject(i).getString("note"));

                                Float discount_amount=Float.valueOf(String.valueOf((Double.parseDouble(dataArray.getJSONObject(i).getString("discount_percentage"))/100)*Double.parseDouble(dataArray.getJSONObject(i).getString("apply_charge"))));
                                Float tax_amount=Float.valueOf(String.valueOf((Double.parseDouble(dataArray.getJSONObject(i).getString("tax"))/100)*(Double.parseDouble(dataArray.getJSONObject(i).getString("apply_charge"))-Double.parseDouble(String.format("%.2f", discount_amount)))));
                                taxList.add( String.format("%.2f", tax_amount)+"("+dataArray.getJSONObject(i).getString("tax")+"%)");
                                System.out.println("tax_amount=="+String.format("%.2f", tax_amount));
                                discountList.add( String.format("%.2f", discount_amount)+"("+dataArray.getJSONObject(i).getString("discount_percentage")+"%)");
                                qtyList.add(dataArray.getJSONObject(i).getString("qty"));


                                tpaList.add(dataArray.getJSONObject(i).getString("tpa_charge"));
                                charge_typeList.add(dataArray.getJSONObject(i).getString("charge_type"));
                                charge_cateList.add(dataArray.getJSONObject(i).getString("charge_category_name"));
                                standard_chargeList.add(dataArray.getJSONObject(i).getString("standard_charge"));
                                amountList.add(dataArray.getJSONObject(i).getString("amount"));
                                String totalArray = dataArray.getJSONObject(i).getString("amount");
                                sum += Float.parseFloat(dataArray.getJSONObject(i).getString("amount"));
                                if(totalArray.length() != 0) {
                                    total_charge.setText("Total: " +currency+  String.format("%.2f", sum));
                                }

                            }

                            adapter.notifyDataSetChanged();
                        } else {
                            nodata_layout.setVisibility(View.VISIBLE);
                            data_layout.setVisibility(View.GONE);
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    Toast.makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(getActivity().getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getActivity().getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getActivity().getApplicationContext(), "accessToken"));
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
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity().getApplicationContext());//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }
}