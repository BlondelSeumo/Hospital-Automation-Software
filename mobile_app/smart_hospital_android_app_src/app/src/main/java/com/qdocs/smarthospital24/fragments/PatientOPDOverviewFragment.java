package com.qdocs.smarthospital24.fragments;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
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
import com.qdocs.smarthospital24.adapters.AllergyAdapter;
import com.qdocs.smarthospital24.adapters.CurrentVitalAdapter;
import com.qdocs.smarthospital24.adapters.FindingsAdapter;
import com.qdocs.smarthospital24.adapters.PatientDoctorAdapter;
import com.qdocs.smarthospital24.adapters.PatientIPDMedicationAdapter;
import com.qdocs.smarthospital24.adapters.SymptomsAdapter;
import com.qdocs.smarthospital24.model.MedicationModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.eazegraph.lib.charts.PieChart;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientOPDOverviewFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    ArrayList<MedicationModel> medication_list = new ArrayList<>();
    ArrayList<String> allergylist = new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> currentvitallist = new ArrayList<String>();
    ArrayList<String> reference_rangelist = new ArrayList<String>();
    ArrayList<String> unitlist = new ArrayList<String>();
    ArrayList<String> patient_rangelist = new ArrayList<String>();
    ArrayList<String> messure_datelist = new ArrayList<String>();
    ArrayList<String> patient_vital_idlist = new ArrayList<String>();
    ArrayList<String> symptomslist = new ArrayList<String>();
    ArrayList<String> doctorlist = new ArrayList<String>();
    ArrayList<String> imagelist = new ArrayList<String>();
    ArrayList<String> findingslist = new ArrayList<String>();
    private String ipdno;
    PatientIPDMedicationAdapter adapter;
    PatientDoctorAdapter doctoradapter;
    FindingsAdapter findingadapter;
    AllergyAdapter allergyadapter;
    CurrentVitalAdapter currentVitalAdapter;
    SymptomsAdapter symptomsadapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    PieChart pieChart;
    ImageView doctorimage;
    TextView bminame,bmivitalvalue;
    RecyclerView findings_recyclerview,doctor_recyclerview,currentvitals_recyclerview,allergy_recyclerview,symptoms_recyclerview;
    public String defaultDatetimeFormat,defaultDateFormat, currency;
    public PatientOPDOverviewFragment() {

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
            getCurrentVitalFromApi(obj.toString());

        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.opd_overview_list, container, false);

        bminame=mainView.findViewById(R.id.bminame);
        bmivitalvalue=mainView.findViewById(R.id.bmivitalvalue);


        currentvitals_recyclerview = mainView.findViewById(R.id.currentvitals_recyclerview);
        currentVitalAdapter = new CurrentVitalAdapter(getActivity(),idlist, currentvitallist,reference_rangelist,unitlist,patient_rangelist,messure_datelist,patient_vital_idlist);
        RecyclerView.LayoutManager cvLayoutManager = new LinearLayoutManager(getActivity());
        currentvitals_recyclerview.setLayoutManager(cvLayoutManager);
        currentvitals_recyclerview.setItemAnimator(new DefaultItemAnimator());
        currentvitals_recyclerview.setAdapter(currentVitalAdapter);

        allergy_recyclerview = mainView.findViewById(R.id.allergy_recyclerview);
        allergyadapter = new AllergyAdapter(getActivity(), allergylist);
        RecyclerView.LayoutManager aLayoutManager = new LinearLayoutManager(getActivity());
        allergy_recyclerview.setLayoutManager(aLayoutManager);
        allergy_recyclerview.setItemAnimator(new DefaultItemAnimator());
        allergy_recyclerview.setAdapter(allergyadapter);

        symptoms_recyclerview = mainView.findViewById(R.id.symptoms_recyclerview);
        symptomsadapter = new SymptomsAdapter(getActivity(), symptomslist);
        RecyclerView.LayoutManager sLayoutManager = new LinearLayoutManager(getActivity());
        symptoms_recyclerview.setLayoutManager(sLayoutManager);
        symptoms_recyclerview.setItemAnimator(new DefaultItemAnimator());
        symptoms_recyclerview.setAdapter(symptomsadapter);

        findings_recyclerview = mainView.findViewById(R.id.findings_recyclerview);
        findingadapter = new FindingsAdapter(getActivity(), findingslist);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity());
        findings_recyclerview.setLayoutManager(mLayoutManager);
        findings_recyclerview.setItemAnimator(new DefaultItemAnimator());
        findings_recyclerview.setAdapter(findingadapter);

        doctor_recyclerview = mainView.findViewById(R.id.doctor_recyclerview);
        doctoradapter = new PatientDoctorAdapter(getActivity(), doctorlist,imagelist);
        RecyclerView.LayoutManager LayoutManager = new LinearLayoutManager(getActivity());
        doctor_recyclerview.setLayoutManager(LayoutManager);
        doctor_recyclerview.setItemAnimator(new DefaultItemAnimator());
        doctor_recyclerview.setAdapter(doctoradapter);


        defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);
        loadData();
        return mainView;
    }

    @Override
    public void onRefresh() {
        loadData();
    }

    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.getOPDDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {

                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject dataArray = obj.getJSONObject("patientdetails");
                        JSONObject patientArray = dataArray.getJSONObject("patient");

                        allergylist.clear();
                        JSONArray allergyArray = patientArray.getJSONArray("allergy");
                        for(int i = 0; i < allergyArray.length(); i++) {
                            allergylist.add(allergyArray.getJSONObject(i).getString("known_allergies"));

                         }
                        allergyadapter.notifyDataSetChanged();

                        symptomslist.clear();
                        JSONArray symptomsArray = patientArray.getJSONArray("symptoms");
                        for(int i = 0; i < symptomsArray.length(); i++) {
                            symptomslist.add(symptomsArray.getJSONObject(i).getString("symptoms"));
                        }
                        symptomsadapter.notifyDataSetChanged();


                        doctorlist.clear();
                        imagelist.clear();
                        JSONArray doctorArray = patientArray.getJSONArray("doctor");
                        for(int i = 0; i < doctorArray.length(); i++) {
                            doctorlist.add(doctorArray.getJSONObject(i).getString("name")+" "+doctorArray.getJSONObject(i).getString("surname")+" ("+doctorArray.getJSONObject(i).getString("employee_id")+")");
                            imagelist.add(doctorArray.getJSONObject(i).getString("image"));
                        }
                        doctoradapter.notifyDataSetChanged();

                        findingslist.clear();
                        JSONArray findingsArray = patientArray.getJSONArray("findings");
                        for(int j = 0; j < findingsArray.length();j++) {
                            findingslist.add(findingsArray.getJSONObject(j).getString("finding_description"));
                        }
                        findingadapter.notifyDataSetChanged();


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
                Toast.makeText(requireActivity().getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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


    private void getCurrentVitalFromApi (String bodyParams) {

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getActivity().getApplicationContext(), "apiUrl")+Constants.getPatientCurrentVitalUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {

                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        idlist.clear();
                        currentvitallist.clear();
                        unitlist.clear();
                        patient_rangelist.clear();
                        messure_datelist.clear();
                        patient_vital_idlist.clear();
                        JSONArray dataArray = obj.getJSONArray("patient_vital");

                        for(int i = 0; i < dataArray.length(); i++) {
                            idlist.add(dataArray.getJSONObject(i).getString("id"));
                            currentvitallist.add(dataArray.getJSONObject(i).getString("name"));
                            reference_rangelist.add(dataArray.getJSONObject(i).getString("reference_range"));
                            unitlist.add(dataArray.getJSONObject(i).getString("unit"));
                            patient_rangelist.add(dataArray.getJSONObject(i).getString("patient_range"));
                            messure_datelist.add(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,dataArray.getJSONObject(i).getString("messure_date")));
                            patient_vital_idlist.add(dataArray.getJSONObject(i).getString("patient_vital_id"));

                        }

                        Double height1=Double.parseDouble(dataArray.getJSONObject(0).getString("patient_range"))*0.01;
                        Double bmiheight=height1*height1;
                        Double bmi=Double.parseDouble(dataArray.getJSONObject(1).getString("patient_range"))/bmiheight;


                        bminame.setText("BMI");
                        bmivitalvalue.setText(String.format("%.2f",bmi));
                        bmivitalvalue.setBackgroundResource(R.drawable.green_border);
                        bmivitalvalue.setTextColor(getResources().getColor(R.color.white));

                        currentVitalAdapter.notifyDataSetChanged();

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
                Toast.makeText(requireActivity().getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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