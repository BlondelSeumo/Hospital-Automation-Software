package com.qdocs.smarthospital24.fragments;

import android.content.Intent;
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
import com.qdocs.smarthospital24.adapters.PatientIpdPaymentAdapter;
import com.qdocs.smarthospital24.patient.IPDPayment;
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

public class PatientIPDPaymentFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    RecyclerView recyclerView;
    TextView total_payment;
    ArrayList<String> payment_modeList = new ArrayList<String>();
    ArrayList<String> pnoteList = new ArrayList<String>();
    ArrayList<String> transactionIDList = new ArrayList<String>();
    ArrayList<String> cheque_noList = new ArrayList<String>();
    ArrayList<String> attachmentList = new ArrayList<String>();
    ArrayList<String> cheque_dateList = new ArrayList<String>();
    ArrayList<String> dateList = new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> paid_amountList = new ArrayList<String>();
    PatientIpdPaymentAdapter adapter;
    LinearLayout paymentBtn;
    LinearLayout nodata_layout,data_layout;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdno,ipdid="";
    String payment="";
    public PatientIPDPaymentFragment(String ipdno) {
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

        View mainView = inflater.inflate(R.layout.fragment_ipd_payment, container, false);
        recyclerView = (RecyclerView) mainView.findViewById(R.id.payment_listview);
        total_payment = (TextView) mainView.findViewById(R.id.total_payment);
        paymentBtn = (LinearLayout) mainView.findViewById(R.id.paymentBtn);
        nodata_layout =mainView.findViewById(R.id.nodata_layout);
        data_layout =mainView.findViewById(R.id.data_layout);
        adapter = new PatientIpdPaymentAdapter(getActivity(),payment_modeList,dateList,
                pnoteList,paid_amountList,cheque_noList,cheque_dateList,transactionIDList,attachmentList);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);
        recyclerView.setHasFixedSize(true);

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
                        if (obj.get("result") instanceof JSONObject) {
                            final JSONObject dataArray = obj.getJSONObject("result");
                            paymentBtn.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View view) {
                                    try {
                                        Intent intent=new Intent(getActivity(), IPDPayment.class);
                                        intent.putExtra("Id",dataArray.getString("ipdid"));
                                        startActivity(intent);
                                    } catch (JSONException e) {
                                        e.printStackTrace();
                                    }
                                }
                            });
                        }

                        final JSONArray paymentdataArray = obj.getJSONArray("payments");

                        dateList.clear();
                        payment_modeList.clear();
                        pnoteList.clear();
                        paid_amountList.clear();
                        transactionIDList.clear();
                        cheque_dateList.clear();
                        cheque_noList.clear();
                        attachmentList.clear();

                        final String currency = Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.currency);
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "dateFormat");
                        if(paymentdataArray.length() != 0) {
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);
                                float sum = 0;

                            for(int i = 0; i < paymentdataArray.length(); i++) {
                                dateList.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,paymentdataArray.getJSONObject(i).getString("payment_date")));
                                payment_modeList.add(paymentdataArray.getJSONObject(i).getString("payment_mode"));
                                cheque_noList.add(paymentdataArray.getJSONObject(i).getString("cheque_no"));
                                cheque_dateList.add(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,paymentdataArray.getJSONObject(i).getString("cheque_date")));
                                pnoteList.add(paymentdataArray.getJSONObject(i).getString("note"));
                                attachmentList.add(paymentdataArray.getJSONObject(i).getString("attachment"));
                                transactionIDList.add("TRID"+paymentdataArray.getJSONObject(i).getString("id"));
                                payment=paymentdataArray.getJSONObject(i).getString("ipd_id");
                                paid_amountList.add(paymentdataArray.getJSONObject(i).getString("amount"));

                                sum += Float.parseFloat(paymentdataArray.getJSONObject(i).getString("amount"));
                                total_payment.setText("Total: " +currency+  String.format("%.2f", sum));
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
        },new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(getActivity().getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
         }){
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