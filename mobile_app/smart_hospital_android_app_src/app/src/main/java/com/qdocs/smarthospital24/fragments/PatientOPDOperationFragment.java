package com.qdocs.smarthospital24.fragments;

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

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.PatientOTAdapter;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.IpdOperationModel;
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

public class PatientOPDOperationFragment extends Fragment {
    ArrayList<String> technicianlist = new ArrayList<>();
    ArrayList<String> dateList = new ArrayList<>();
    ArrayList<String> idlist = new ArrayList<>();
    ArrayList<String> categorylist = new ArrayList<>();
    ArrayList<String> referencelist = new ArrayList<>();
    ArrayList<String> operationlist = new ArrayList<>();
    RecyclerView pathologyListView;
    PatientOTAdapter adapter;
    public String defaultDateFormat, currency;
    LinearLayout nodata_layout;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String opdid;
    ArrayList<IpdOperationModel> ipd_operation_list=new ArrayList<>();
    public PatientOPDOperationFragment(String opdid) {
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

        View mainView = inflater.inflate(R.layout.patient_pathology_fragment, container, false);
        pathologyListView = (RecyclerView) mainView.findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) mainView.findViewById(R.id.nodata_layout);
        adapter = new PatientOTAdapter(getActivity(),ipd_operation_list,null);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        pathologyListView.setLayoutManager(mLayoutManager);
        pathologyListView.setItemAnimator(new DefaultItemAnimator());
        pathologyListView.setAdapter(adapter);

        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);
        return mainView;
    }

    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.getOPDVisitDetailsUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {

                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject patientdetailsArray = obj.getJSONObject("patientdetails");
                        JSONObject patientArray = patientdetailsArray.getJSONObject("patient");

                        JSONArray dataArray = obj.getJSONArray("ot_detail");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "datetimeFormat");
                       ipd_operation_list.clear();
                        String defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");

                        if(dataArray.length() != 0) {

                            for(int i = 0; i < dataArray.length(); i++) {
                                IpdOperationModel ipdOperationModel= new IpdOperationModel();

                                ipdOperationModel.setOt_technician(dataArray.getJSONObject(i).getString("ot_technician"));
                                ipdOperationModel.setCase_id( patientArray.getString("case_reference_id"));
                                ipdOperationModel.setDate(Utility.parseDate("yyyy-MM-dd HH:mm", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("date")));
                                ipdOperationModel.setCategory(dataArray.getJSONObject(i).getString("category"));
                                ipdOperationModel.setId(dataArray.getJSONObject(i).getString("id"));
                                ipdOperationModel.setOperation(dataArray.getJSONObject(i).getString("operation"));
                                JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                                ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    CustomFieldModel customFieldModel = new CustomFieldModel();
                                    customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                    customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                    customArrayList.add(customFieldModel);
                                }

                                ipdOperationModel.setCustomfield(customArrayList);
                                ipd_operation_list.add(ipdOperationModel);

                            }
                            adapter.notifyDataSetChanged();
                        } else {

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