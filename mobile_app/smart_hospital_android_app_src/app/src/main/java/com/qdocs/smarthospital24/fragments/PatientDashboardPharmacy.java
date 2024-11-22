package com.qdocs.smarthospital24.fragments;

import android.app.ProgressDialog;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
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
import com.qdocs.smarthospital24.adapters.PatientPharmacyAdapter;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.PharmacyModel;
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

public class PatientDashboardPharmacy extends Fragment implements SwipeRefreshLayout.OnRefreshListener {

    RecyclerView homeworkListView;
    public String defaultDateFormat, currency;
    LinearLayout nodata_layout;
    PatientPharmacyAdapter adapter;
    SwipeRefreshLayout pullToRefresh;
    ArrayList<PharmacyModel> pharmacy_detail_list = new ArrayList<>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public PatientDashboardPharmacy() {}

    @Override
    public void setUserVisibleHint(boolean isVisibleToUser) {
        super.setUserVisibleHint(isVisibleToUser);
        if (isVisibleToUser) {
            getFragmentManager().beginTransaction().detach(this).attach(this).commit();
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.patient_pharmacy_fragment, container, false);
        homeworkListView = (RecyclerView) mainView.findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) mainView.findViewById(R.id.nodata_layout);
        loadData();
        adapter = new PatientPharmacyAdapter(getActivity(),pharmacy_detail_list,null);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        homeworkListView.setLayoutManager(mLayoutManager);
        homeworkListView.setItemAnimator(new DefaultItemAnimator());
        homeworkListView.setAdapter(adapter);
        pullToRefresh =mainView.findViewById(R.id.pullToRefresh);
        pullToRefresh.setOnRefreshListener(this);
        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);


        return mainView;
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
    public void onRefresh() {
        pullToRefresh.setRefreshing(true);
        loadData(); }

    private void getDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(getActivity());
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getActivity(), "apiUrl")+Constants.getPharmacyDetailsUrl;
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
                        pharmacy_detail_list.clear();
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");
                        if(dataArray.length() != 0) {

                            for(int i = 0; i < dataArray.length(); i++) {
                                PharmacyModel pharmacyModel = new PharmacyModel();

                                pharmacyModel.setId(dataArray.getJSONObject(i).getString("id"));
                                pharmacyModel.setCustomer_name(dataArray.getJSONObject(i).getString("customer_name"));
                                pharmacyModel.setDate(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                                pharmacyModel.setDoctor_name(dataArray.getJSONObject(i).getString("doctor_name"));
                                pharmacyModel.setCollectedby(dataArray.getJSONObject(i).getString("name")+" "+dataArray.getJSONObject(i).getString("surname")+" ("+dataArray.getJSONObject(i).getString("employee_id")+")");
                                pharmacyModel.setTotal(dataArray.getJSONObject(i).getString("total"));
                                pharmacyModel.setDiscount(dataArray.getJSONObject(i).getString("discount"));
                                pharmacyModel.setDiscount_percentage(dataArray.getJSONObject(i).getString("discount_percentage"));
                                pharmacyModel.setCase_reference_id(dataArray.getJSONObject(i).getString("case_reference_id"));
                                pharmacyModel.setNet_amount(dataArray.getJSONObject(i).getString("net_amount"));
                                pharmacyModel.setTax(dataArray.getJSONObject(i).getString("tax"));
                                pharmacyModel.setPaid_amount(dataArray.getJSONObject(i).getString("paid_amount"));
                                pharmacyModel.setRefund_amount(dataArray.getJSONObject(i).getString("refund_amount"));
                                pharmacyModel.setNote(dataArray.getJSONObject(i).getString("note"));
                                JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                                ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    CustomFieldModel customFieldModel = new CustomFieldModel();
                                    customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                    customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                    customArrayList.add(customFieldModel);
                                }

                                pharmacyModel.setCustomfield(customArrayList);
                                pharmacy_detail_list.add(pharmacyModel);
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
                    Toast.makeText(getActivity(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(getActivity(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getActivity(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getActivity(), "accessToken"));
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
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity()); //Creating a Request Queue
        requestQueue.add(stringRequest);  //Adding request to the queue
    }

}
