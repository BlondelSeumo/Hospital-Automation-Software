package com.qdocs.smarthospital24.fragments;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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
import com.qdocs.smarthospital24.adapters.PatientIPDMedicationAdapter;
import com.qdocs.smarthospital24.model.DoseModel;
import com.qdocs.smarthospital24.model.MedicationModel;
import com.qdocs.smarthospital24.model.MedicineModel;
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
import java.util.Objects;

import static android.widget.Toast.makeText;

public class PatientOPDMedicationFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    RecyclerView recyclerView;
    ArrayList<MedicationModel> medication_list = new ArrayList<>();
    ArrayList<String> instructionlist = new ArrayList<String>();
    private String opdid;
    PatientIPDMedicationAdapter adapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public String defaultDatetimeFormat,defaultDateFormat, currency;

    public PatientOPDMedicationFragment(String opdid) {
        this.opdid=opdid;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        loadData();
    }

    private void loadData() {
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            params.put("opd_id",opdid);
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
        recyclerView = (RecyclerView) mainView.findViewById(R.id.patientOpd_listview);
        adapter = new PatientIPDMedicationAdapter(getActivity(), medication_list,null);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity().getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(adapter);

        defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);

        return mainView;
    }

    @Override
    public void onRefresh() {
        loadData(); }

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
                        JSONArray dataArray = obj.getJSONArray("medication_detail");
                        medication_list.clear();

                        if(dataArray.length() != 0) {

                            for(int i = 0; i < dataArray.length(); i++) {

                                MedicationModel medicationModel = new MedicationModel();
                                medicationModel.setMedicine_date(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dataArray.getJSONObject(i).getString("medicine_date")));
                                medicationModel.setMedicine_day(dataArray.getJSONObject(i).getString("medicine_day"));
                                JSONArray medicineArray = dataArray.getJSONObject(i).getJSONArray("medicine");
                                ArrayList<MedicineModel> medicineslist = new ArrayList<>();
                                medicineslist.clear();
                                for(int j = 0; j < medicineArray.length(); j++) {
                                    MedicineModel medicineModel = new MedicineModel();
                                    medicineModel.setMedicine_name(medicineArray.getJSONObject(j).getString("medicine_name"));
                                    JSONArray doseArray = medicineArray.getJSONObject(j).getJSONArray("doses");
                                    ArrayList<DoseModel> doseList = new ArrayList<>();
                                    doseList.clear();
                                    for(int k = 0; k < doseArray.length(); k++) {
                                        DoseModel doseModel = new DoseModel();
                                        doseModel.setDate(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,doseArray.getJSONObject(k).getString("date")));
                                        doseModel.setTime(doseArray.getJSONObject(k).getString("time"));
                                        doseModel.setRemark(doseArray.getJSONObject(k).getString("remark"));
                                        doseModel.setMedicine_dosage(doseArray.getJSONObject(k).getString("medicine_dosage")+" "+doseArray.getJSONObject(k).getString("unit"));
                                        doseList.add(doseModel);
                                    }
                                    medicineModel.setDoses(doseList);
                                    medicineslist.add(medicineModel);
                                }
                                medicationModel.setMedicine(medicineslist);
                                medication_list.add(medicationModel);
                            }
                            adapter.notifyDataSetChanged();
                        } else { }

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
                Toast.makeText(Objects.requireNonNull(getActivity()).getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(getActivity().getApplicationContext());
        requestQueue.add(stringRequest);
    }
}