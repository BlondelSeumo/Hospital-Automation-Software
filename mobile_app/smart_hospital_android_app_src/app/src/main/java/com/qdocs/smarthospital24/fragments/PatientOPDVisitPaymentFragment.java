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
import com.qdocs.smarthospital24.adapters.PatientOpdVisitPaymentAdapter;
import com.qdocs.smarthospital24.patient.OPDPayment;
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

public class PatientOPDVisitPaymentFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    RecyclerView recyclerView;
    TextView total_payment;
    ArrayList<String> payment_modeList = new ArrayList<String>();
    ArrayList<String> cheque_noList = new ArrayList<String>();
    ArrayList<String> attachmentList = new ArrayList<String>();
    ArrayList<String> cheque_dateList = new ArrayList<String>();
    ArrayList<String> transactionIdList = new ArrayList<String>();
    ArrayList<String> pnoteList = new ArrayList<String>();
    ArrayList<String> dateList = new ArrayList<String>();
    ArrayList<String> paid_amountList = new ArrayList<String>();
    ArrayList<String> opd_idList = new ArrayList<String>();

    PatientOpdVisitPaymentAdapter adapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    private String opdid;
    LinearLayout paymentBtn;

    public PatientOPDVisitPaymentFragment(String opdid) {
       this.opdid=opdid;
    }
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        loadData();
    }
    private void loadData() {
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            params.put("opd_id", opdid);
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

        View mainView = inflater.inflate(R.layout.fragment_opd_payment, container, false);
        recyclerView = (RecyclerView) mainView.findViewById(R.id.payment_listview);
        total_payment = (TextView) mainView.findViewById(R.id.total_payment);
        adapter = new PatientOpdVisitPaymentAdapter(getActivity(), payment_modeList,dateList,
                pnoteList,paid_amountList,cheque_noList,cheque_dateList,transactionIdList,attachmentList);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);

        paymentBtn =mainView.findViewById(R.id.paymentBtn);
        loadData();
        paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(getActivity(), OPDPayment.class);
               intent.putExtra("Id",opdid);
                startActivity(intent);
            }
        });
        return mainView;
    }
    @Override
    public void onRefresh() {
        loadData();
    }
    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.getOPDVisitDetailsUrl;
        System.out.println("url=="+url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {

                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray paymentdataArray = obj.getJSONArray("payment_detail");

                        dateList.clear();
                        payment_modeList.clear();
                        pnoteList.clear();
                        paid_amountList.clear();
                        opd_idList.clear();
                        cheque_dateList.clear();
                        cheque_noList.clear();
                        attachmentList.clear();

                        final String currency = Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.currency);
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "dateFormat");
                        if (paymentdataArray.length() != 0) {
                            float sum = 0;
                            for (int i = 0; i < paymentdataArray.length(); i++) {
                                dateList.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat, paymentdataArray.getJSONObject(i).getString("payment_date")));
                                payment_modeList.add(paymentdataArray.getJSONObject(i).getString("payment_mode"));
                                cheque_noList.add(paymentdataArray.getJSONObject(i).getString("cheque_no"));
                                attachmentList.add(paymentdataArray.getJSONObject(i).getString("attachment"));
                                cheque_dateList.add(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,paymentdataArray.getJSONObject(i).getString("cheque_date")));
                                pnoteList.add(paymentdataArray.getJSONObject(i).getString("note"));
                                paid_amountList.add(paymentdataArray.getJSONObject(i).getString("amount"));
                                opd_idList.add(paymentdataArray.getJSONObject(i).getString("id"));
                                transactionIdList.add("TRID"+paymentdataArray.getJSONObject(i).getString("id"));
                                sum += Float.parseFloat(paymentdataArray.getJSONObject(i).getString("amount"));
                                total_payment.setText("Total: " +currency+  String.format("%.2f", sum));
                            }

                            adapter.notifyDataSetChanged();
                        } else {
                            total_payment.setText("Total  " + currency + "0");
                            // Toast.makeText(getActivity().getApplicationContext(), getActivity().getApplicationContext().getString(R.string.noData), Toast.LENGTH_SHORT).show();
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
            public byte[] getBody () throws AuthFailureError {
                try {
                    return requestBody == null ? null : requestBody.getBytes("utf-8");
                } catch (UnsupportedEncodingException uee) {
                    VolleyLog.wtf("Unsupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");
                    return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity().getApplicationContext());//Creating  a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}