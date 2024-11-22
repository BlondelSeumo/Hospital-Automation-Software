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
import com.qdocs.smarthospital24.adapters.PatientOpdTreatmentHistoryAdapter;
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

public class PatientOPDTreatHistoryFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    RecyclerView recyclerView;
    ArrayList<String> opd_nolist = new ArrayList<String>();
    ArrayList<String> patient_unique_id = new ArrayList<String>();
    ArrayList<String> consultantList = new ArrayList<String>();
    ArrayList<String> symptomslist = new ArrayList<String>();
    ArrayList<String> refferencelist = new ArrayList<String>();
    ArrayList<String> visitidlist = new ArrayList<String>();
    ArrayList<String> casereference_idlist = new ArrayList<String>();
    ArrayList<String> header_notelist = new ArrayList<String>();
    ArrayList<String> footer_notelist = new ArrayList<String>();
    ArrayList<String> dateList = new ArrayList<String>();
    ArrayList<String> prescriptionlist = new ArrayList<String>();

    PatientOpdTreatmentHistoryAdapter adapter;
    LinearLayout nodata_layout,data_layout;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDatetimeFormat, currency;

    public PatientOPDTreatHistoryFragment() {
        // Required empty public constructor
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
            Log.e("params", obj.toString());
            getDataFromApi(obj.toString());
        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.fragment_list, container, false);
        recyclerView = (RecyclerView) mainView.findViewById(R.id.patientOpd_listview);
        adapter = new PatientOpdTreatmentHistoryAdapter(getActivity(), opd_nolist,consultantList,
                refferencelist,symptomslist,dateList,visitidlist,prescriptionlist,casereference_idlist);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);
        nodata_layout =mainView.findViewById(R.id.nodata_layout);
        data_layout =mainView.findViewById(R.id.data_layout);
        defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);
        loadData();
        return mainView;
    }

    @Override
    public void onRefresh() {
        loadData();
    }

    private void getDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(getActivity());
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.getOPDDetailsUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("treatmenthistory");

                            opd_nolist.clear();
                            consultantList.clear();
                            refferencelist.clear();
                            dateList.clear();
                            symptomslist.clear();
                            visitidlist.clear();
                            header_notelist.clear();
                            footer_notelist.clear();
                            prescriptionlist.clear();
                            casereference_idlist.clear();

                        if(dataArray.length() != 0) {
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);

                            for(int i = 0; i < dataArray.length(); i++) {
                                opd_nolist.add(dataArray.getJSONObject(i).getString("opd_id"));
                                consultantList.add(dataArray.getJSONObject(i).getString("name") + " " + dataArray.getJSONObject(i).getString("surname"));
                                dateList.add(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("appointment_date")));
                                refferencelist.add(dataArray.getJSONObject(i).getString("refference"));
                                symptomslist.add(dataArray.getJSONObject(i).getString("symptoms"));
                                visitidlist.add(dataArray.getJSONObject(i).getString("visit_id"));
                                casereference_idlist.add(dataArray.getJSONObject(i).getString("case_reference_id"));
                                prescriptionlist.add(dataArray.getJSONObject(i).getString("prescription"));
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
                    pd.dismiss();
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