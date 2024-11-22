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
import com.qdocs.smarthospital24.adapters.PatientIPDAntenatalHistoryAdapter;
import com.qdocs.smarthospital24.model.AntenatalModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
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

public class PatientIPDAntenatalHistoryFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener {

    ArrayList<AntenatalModel> antenatal_detail_list = new ArrayList<>();
    RecyclerView ListView;
    PatientIPDAntenatalHistoryAdapter adapter;
    public String defaultDateFormat, currency;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdno;
    public PatientIPDAntenatalHistoryFragment(String ipdno) {
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
        adapter = new PatientIPDAntenatalHistoryAdapter(getActivity(), antenatal_detail_list,null);
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
                        JSONArray dataArray = obj.getJSONArray("antenatallist");
                        String defaultDateFormat = Utility.getSharedPreferences(getActivity().getApplicationContext(), "dateFormat");
                        antenatal_detail_list.clear();
                        if(dataArray.length() != 0) {
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);
                            for(int i = 0; i < dataArray.length(); i++) {
                                AntenatalModel antenatalModel = new AntenatalModel();


                                if(dataArray.getJSONObject(i).getString("status").equals("ipd")){
                                    antenatalModel.setId("IPDN"+dataArray.getJSONObject(i).getString("ipdid"));
                                    antenatalModel.setOpd_checkupid("");
                                }else{
                                    antenatalModel.setId("OPDN"+dataArray.getJSONObject(i).getString("opd_detail_id"));
                                    antenatalModel.setOpd_checkupid("OCID"+dataArray.getJSONObject(i).getString("visit_details_id"));
                                }
                                antenatalModel.setDate(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDateFormat,dataArray.getJSONObject(i).getString("date")));
                                antenatalModel.setBleeding(dataArray.getJSONObject(i).getString("bleeding"));
                                antenatalModel.setHeadache(dataArray.getJSONObject(i).getString("headache"));
                                antenatalModel.setPain(dataArray.getJSONObject(i).getString("pain"));
                                antenatalModel.setConstipation(dataArray.getJSONObject(i).getString("constipation"));
                                antenatalModel.setUrinary_symptoms(dataArray.getJSONObject(i).getString("urinary_symptoms"));
                                antenatalModel.setVomiting(dataArray.getJSONObject(i).getString("vomiting"));
                                antenatalModel.setCough(dataArray.getJSONObject(i).getString("cough"));
                                antenatalModel.setVaginal(dataArray.getJSONObject(i).getString("vaginal"));
                                antenatalModel.setOedema(dataArray.getJSONObject(i).getString("oedema"));
                                antenatalModel.setDischarge(dataArray.getJSONObject(i).getString("discharge"));
                                antenatalModel.setHaemoroids(dataArray.getJSONObject(i).getString("haemoroids"));
                                antenatalModel.setWeight(dataArray.getJSONObject(i).getString("weight"));
                                antenatalModel.setHeight(dataArray.getJSONObject(i).getString("height"));
                                antenatalModel.setGeneral_condition(dataArray.getJSONObject(i).getString("general_condition"));
                                antenatalModel.setFinding_remark(dataArray.getJSONObject(i).getString("finding_remark"));
                                antenatalModel.setPelvic_examination(dataArray.getJSONObject(i).getString("pelvic_examination"));
                                antenatalModel.setSp(dataArray.getJSONObject(i).getString("sp"));

                                antenatalModel.setUter_size(dataArray.getJSONObject(i).getString("uter_size"));
                                antenatalModel.setUterus_size(dataArray.getJSONObject(i).getString("uterus_size"));
                                antenatalModel.setPresentation_position(dataArray.getJSONObject(i).getString("presentation_position"));
                                antenatalModel.setBrim_presentation(dataArray.getJSONObject(i).getString("brim_presentation"));
                                antenatalModel.setFoeta_heart(dataArray.getJSONObject(i).getString("foeta_heart"));
                                antenatalModel.setBlood_pressure(dataArray.getJSONObject(i).getString("blood_pressure"));
                                antenatalModel.setAntenatal_Oedema(dataArray.getJSONObject(i).getString("antenatal_Oedema"));
                                antenatalModel.setAntenatal_weight(dataArray.getJSONObject(i).getString("antenatal_weight"));
                                antenatalModel.setUrine_sugar(dataArray.getJSONObject(i).getString("urine_sugar"));
                                antenatalModel.setUrine(dataArray.getJSONObject(i).getString("urine"));
                                antenatalModel.setRemark(dataArray.getJSONObject(i).getString("remark"));
                                antenatalModel.setNext_visit(dataArray.getJSONObject(i).getString("next_visit"));
                                JSONArray customArray = dataArray.getJSONObject(i).getJSONArray("customfield");
                                ArrayList<CustomFieldModel> customArrayList = new ArrayList<>();
                                for(int j = 0; j < customArray.length(); j++) {
                                    CustomFieldModel customFieldModel = new CustomFieldModel();
                                    customFieldModel.setFieldname(customArray.getJSONObject(j).getString("fieldname"));
                                    customFieldModel.setFieldvalue(customArray.getJSONObject(j).getString("fieldvalue"));
                                    customArrayList.add(customFieldModel);
                                }

                                antenatalModel.setCustomfield(customArrayList);
                                antenatal_detail_list.add(antenatalModel);
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