package com.qdocs.smarthospital24.fragments;

import static android.widget.Toast.makeText;

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
import com.qdocs.smarthospital24.adapters.PatientIPDPostnatalHistoryAdapter;
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

public class PatientIPDPostnatalHistoryFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener {
    ArrayList<String> ipdidlist = new ArrayList<>();
    ArrayList<String> labor_timelist = new ArrayList<>();
    ArrayList<String> delivery_timelist = new ArrayList<>();
    ArrayList<String> routine_questionlist = new ArrayList<>();
    ArrayList<String> general_remarklist = new ArrayList<>();


    RecyclerView ListView;
    PatientIPDPostnatalHistoryAdapter adapter;
    public String defaultDateFormat, currency;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdno;
    public PatientIPDPostnatalHistoryFragment(String ipdno) {
        this.ipdno=ipdno;
    }
    LinearLayout nodata_layout,data_layout;
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

        View mainView = inflater.inflate(R.layout.fragment_list, container, false);
        ListView = (RecyclerView) mainView.findViewById(R.id.patientOpd_listview);
        nodata_layout =mainView.findViewById(R.id.nodata_layout);
        data_layout =mainView.findViewById(R.id.data_layout);
        adapter = new PatientIPDPostnatalHistoryAdapter(getActivity(), ipdidlist,labor_timelist,delivery_timelist,routine_questionlist,general_remarklist);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        ListView.setLayoutManager(mLayoutManager);
        ListView.setItemAnimator(new DefaultItemAnimator());
        ListView.setAdapter(adapter);
        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);
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
                        JSONArray dataArray = obj.getJSONArray("postnatal_history");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                        ipdidlist.clear();
                        labor_timelist.clear();
                        delivery_timelist.clear();
                        routine_questionlist.clear();
                        general_remarklist.clear();

                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                        if(dataArray.length() != 0) {
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);
                            for(int i = 0; i < dataArray.length(); i++) {
                                ipdidlist.add("IPDN"+dataArray.getJSONObject(i).getString("id"));
                                labor_timelist.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("labor_time")));
                                delivery_timelist.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("delivery_time")));
                                routine_questionlist.add(dataArray.getJSONObject(i).getString("routine_question"));
                                general_remarklist.add(dataArray.getJSONObject(i).getString("general_remark"));
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
        requestQueue.add(stringRequest);  //Adding request to the queue
    }
}