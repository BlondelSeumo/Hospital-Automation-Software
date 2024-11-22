package com.qdocs.smarthospital24.fragments;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;
import androidx.fragment.app.Fragment;
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
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientIPDBillFragment extends Fragment  implements SwipeRefreshLayout.OnRefreshListener{

    ArrayList<String> dateList = new ArrayList<String>();
    ArrayList<String> payment_modeList = new ArrayList<String>();
    ArrayList<String> pnoteList = new ArrayList<String>();
    ArrayList<String> paid_amountList = new ArrayList<String>();
    SwipeRefreshLayout pullToRefresh;
    TextView totalcharge,totalpayment,grosstotal,discount,othercharges,tax,netamount;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdno;

    public PatientIPDBillFragment(String ipdno) {
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

        View mainView = inflater.inflate(R.layout.fragment_ipd_bill, container, false);

        pullToRefresh =mainView.findViewById(R.id.pullToRefresh);
        pullToRefresh.setOnRefreshListener(this);
        totalcharge = (TextView) mainView.findViewById(R.id.totalcharge);
        totalpayment = (TextView) mainView.findViewById(R.id.totalpayment);
        grosstotal = (TextView) mainView.findViewById(R.id.grosstotal);
        discount = (TextView) mainView.findViewById(R.id.discount);
        othercharges = (TextView) mainView.findViewById(R.id.othercharges);
        tax = (TextView) mainView.findViewById(R.id.tax);
        netamount = (TextView) mainView.findViewById(R.id.netamount);

        pullToRefresh.post(new Runnable() {
           @Override
           public void run() {
               pullToRefresh.setRefreshing(true);
               loadData();
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

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.patientipddetailsUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                pullToRefresh.setRefreshing(false);
                if (result != null) {
                    String sum;
                    try {
                        Log.e("Result", result);
                        JSONObject obj2 = new JSONObject(result);
                        JSONObject billArray = obj2.getJSONObject("billdetails");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "dateFormat");
                        final String currency = Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.currency);
                        JSONObject totalArray = obj2.getJSONObject("totalcharges");
                        JSONObject totalpaidArray = obj2.getJSONObject("totalpaid");

                        float charge= Float.valueOf(totalArray.getString("charge"));
                        float payment=Float.valueOf(totalpaidArray.getString("paid_amount"));

                        sum=String.valueOf(charge-payment);

                            discount.setText(currency+billArray.getString("discount"));
                            othercharges.setText(currency+billArray.getString("other_charge"));
                            tax.setText(currency+billArray.getString("tax"));
                            if(billArray.getString("payment_status").equals("paid")){
                                netamount.setText(currency+billArray.getString("net_amount"));
                                grosstotal.setText(currency+billArray.getString("gross_total"));
                            }else{
                                grosstotal.setText(currency+sum);
                                netamount.setText(currency+sum);
                            }
                            totalcharge.setText(currency+String.valueOf(charge));
                            totalpayment.setText(currency+totalpaidArray.getString("paid_amount"));

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
                    VolleyLog.wtf("Unsupported Encoding while trying to get the bytes of %s using %s", requestBody,"utf-8");
                    return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity().getApplicationContext()); //Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}