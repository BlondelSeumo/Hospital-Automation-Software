package com.qdocs.smarthospital24.fragments;

import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
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
import com.qdocs.smarthospital24.adapters.FindingsAdapter;
import com.qdocs.smarthospital24.adapters.IPDCurrentVitalAdapter;
import com.qdocs.smarthospital24.adapters.PatientDoctorAdapter;
import com.qdocs.smarthospital24.adapters.PatientIPDMedicationAdapter;
import com.qdocs.smarthospital24.model.MedicationModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.MemoryPolicy;
import com.squareup.picasso.NetworkPolicy;
import com.squareup.picasso.Picasso;
import org.eazegraph.lib.charts.PieChart;
import org.eazegraph.lib.models.PieModel;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientIPDOverviewFragment extends Fragment implements SwipeRefreshLayout.OnRefreshListener{

    ArrayList<MedicationModel> medication_list = new ArrayList<>();
    ArrayList<String> doctorlist = new ArrayList<String>();
    ArrayList<String> imagelist = new ArrayList<String>();
    ArrayList<String> findingslist = new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> currentvitallist = new ArrayList<String>();
    ArrayList<String> reference_rangelist = new ArrayList<String>();
    ArrayList<String> unitlist = new ArrayList<String>();
    ArrayList<String> patient_rangelist = new ArrayList<String>();
    ArrayList<String> messure_datelist = new ArrayList<String>();
    ArrayList<String> patient_vital_idlist = new ArrayList<String>();
    private String ipdno;
    IPDCurrentVitalAdapter ipdCurrentVitalAdapter;
    PatientIPDMedicationAdapter adapter;
    PatientDoctorAdapter doctoradapter;
    FindingsAdapter findingadapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    PieChart pieChart;
    ImageView doctorimage;
    RecyclerView findings_recyclerview,doctor_recyclerview,currentvitals_recyclerview;
    ProgressBar progressBar,pharmacy_progressBar,pathology_progressBar,radiology_progressBar,bloodbank_progressBar,ambulance_progressBar;
    public String defaultDatetimeFormat,defaultDateFormat, currency;
    public PatientIPDOverviewFragment(String ipdno) {
        this.ipdno=ipdno;
    }
    CardView pharmacy_card,pathology_card,radiology_card,bloodbank_card,ambulance_card;
    LinearLayout nodata_layout,data_layout,limit_exceed;
    TextView creditlimit,usedcredit,balancedcredit,caseid,ipdnotv,admissiondate,bed,graph_per;
    TextView known_allergies,findings,symptoms,doctorname;
    TextView totalbillratio,pharmacy_totalbillratio,pathology_totalbillratio,radiology_totalbillratio,bloodbank_totalbillratio,ambulance_totalbillratio;
    TextView totalbillpayment,pharmacy_totalbillpayment,pathology_totalbillpayment,radiology_totalbillpayment,bloodbank_totalbillpayment,ambulance_totalbillpayment;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        //loadData();
    }
    TextView bminame,bmivitalvalue;
    private void loadData() {
        if(Utility.isConnectingToInternet(getActivity().getApplicationContext())){
            params.put("patient_id", Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.patient_id));
            params.put("ipd_id",ipdno);
            JSONObject obj=new JSONObject(params);
            Log.e("params ", obj.toString());
            getDataFromApi(obj.toString());
            getCurrentVitalFromApi(obj.toString());

        }else{
            makeText(getActivity().getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.overview_list, container, false);
        pieChart = mainView.findViewById(R.id.piechart);
        caseid = mainView.findViewById(R.id.caseid);
        ipdnotv = mainView.findViewById(R.id.ipdnotv);
        admissiondate = mainView.findViewById(R.id.admissiondate);
        bed = mainView.findViewById(R.id.bed);
        creditlimit = mainView.findViewById(R.id.creditlimit);
        usedcredit = mainView.findViewById(R.id.usedcredit);
        balancedcredit = mainView.findViewById(R.id.balancedcredit);
        graph_per = mainView.findViewById(R.id.graph_per);

        nodata_layout =mainView.findViewById(R.id.nodata_layout);
        data_layout =mainView.findViewById(R.id.data_layout);
        bminame=mainView.findViewById(R.id.bminame);
        bmivitalvalue=mainView.findViewById(R.id.bmivitalvalue);
        loadData();

        doctorimage = mainView.findViewById(R.id.doctorimage);
        doctorname = mainView.findViewById(R.id.doctorname);

        progressBar = mainView.findViewById(R.id.progressBar);

        currentvitals_recyclerview = mainView.findViewById(R.id.currentvitals_recyclerview);
        ipdCurrentVitalAdapter = new IPDCurrentVitalAdapter(getActivity(),idlist, currentvitallist,reference_rangelist,unitlist,patient_rangelist,messure_datelist,patient_vital_idlist);
        RecyclerView.LayoutManager cvLayoutManager = new LinearLayoutManager(getActivity());
        currentvitals_recyclerview.setLayoutManager(cvLayoutManager);
        currentvitals_recyclerview.setItemAnimator(new DefaultItemAnimator());
        currentvitals_recyclerview.setAdapter(ipdCurrentVitalAdapter);

        pharmacy_progressBar = mainView.findViewById(R.id.pharmacy_progressBar);
        pathology_progressBar = mainView.findViewById(R.id.pathology_progressBar);
        radiology_progressBar = mainView.findViewById(R.id.radiology_progressBar);
        bloodbank_progressBar = mainView.findViewById(R.id.bloodbank_progressBar);
        ambulance_progressBar = mainView.findViewById(R.id.ambulance_progressBar);

        totalbillpayment = mainView.findViewById(R.id.totalbillpayment);
        pharmacy_totalbillpayment = mainView.findViewById(R.id.pharmacy_totalbillpayment);
        pathology_totalbillpayment = mainView.findViewById(R.id.pathology_totalbillpayment);
        radiology_totalbillpayment = mainView.findViewById(R.id.radiology_totalbillpayment);
        bloodbank_totalbillpayment = mainView.findViewById(R.id.bloodbank_totalbillpayment);
        ambulance_totalbillpayment = mainView.findViewById(R.id.ambulance_totalbillpayment);

        totalbillratio = mainView.findViewById(R.id.totalbillratio);
        pharmacy_totalbillratio = mainView.findViewById(R.id.pharmacy_totalbillratio);
        pathology_totalbillratio = mainView.findViewById(R.id.pathology_totalbillratio);
        radiology_totalbillratio = mainView.findViewById(R.id.radiology_totalbillratio);
        bloodbank_totalbillratio = mainView.findViewById(R.id.bloodbank_totalbillratio);
        ambulance_totalbillratio = mainView.findViewById(R.id.ambulance_totalbillratio);

        known_allergies = mainView.findViewById(R.id.known_allergies);

        limit_exceed = mainView.findViewById(R.id.limit_exceed);

        pharmacy_card = mainView.findViewById(R.id.pharmacy_card);
        pathology_card = mainView.findViewById(R.id.pathology_card);
        radiology_card = mainView.findViewById(R.id.radiology_card);
        bloodbank_card = mainView.findViewById(R.id.bloodbank_card);
        ambulance_card = mainView.findViewById(R.id.ambulance_card);

        findings_recyclerview = mainView.findViewById(R.id.findings_recyclerview);
        findingadapter = new FindingsAdapter(getActivity(), findingslist);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getActivity());
        findings_recyclerview.setLayoutManager(mLayoutManager);
        findings_recyclerview.setItemAnimator(new DefaultItemAnimator());
        findings_recyclerview.setAdapter(findingadapter);

        symptoms = mainView.findViewById(R.id.symptoms);

        doctor_recyclerview = mainView.findViewById(R.id.doctor_recyclerview);
        doctoradapter = new PatientDoctorAdapter(getActivity(), doctorlist,imagelist);
        RecyclerView.LayoutManager LayoutManager = new LinearLayoutManager(getActivity());
        doctor_recyclerview.setLayoutManager(LayoutManager);
        doctor_recyclerview.setItemAnimator(new DefaultItemAnimator());
        doctor_recyclerview.setAdapter(doctoradapter);

        defaultDatetimeFormat = Utility.getSharedPreferences(getActivity(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getActivity(), "dateFormat");
        currency = Utility.getSharedPreferences(getActivity(), Constants.currency);

        try {
            JSONArray modulesArray = new JSONArray(Utility.getSharedPreferences(getActivity().getApplicationContext(), Constants.modulesArray));

            if (modulesArray.length() != 0) {
                ArrayList<String> moduleCodeList = new ArrayList<String>();
                ArrayList<String> moduleStatusList = new ArrayList<String>();

                for (int i = 0; i < modulesArray.length(); i++) {
                    if (modulesArray.getJSONObject(i).getString("short_code").equals("pharmacy")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        pharmacy_card.setVisibility(View.GONE);
                    }
                    if (modulesArray.getJSONObject(i).getString("short_code").equals("pathology")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        pathology_card.setVisibility(View.GONE);
                    }if (modulesArray.getJSONObject(i).getString("short_code").equals("radiology")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        radiology_card.setVisibility(View.GONE);
                    }if (modulesArray.getJSONObject(i).getString("short_code").equals("ambulance")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        ambulance_card.setVisibility(View.GONE);
                    }if (modulesArray.getJSONObject(i).getString("short_code").equals("blood_bank")
                            && modulesArray.getJSONObject(i).getString("is_active").equals("0")) {
                        bloodbank_card.setVisibility(View.GONE);
                    }
                }
            }
        } catch (JSONException e) {
            Log.d("Error", e.toString());
        }


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
                            nodata_layout.setVisibility(View.GONE);
                            data_layout.setVisibility(View.VISIBLE);
                            JSONObject dataArray = obj.getJSONObject("result");
                           System.out.println("Result is object");
                           System.out.println("dataArray Length=="+dataArray.length());

                            known_allergies.setText(dataArray.getString("known_allergies"));
                            symptoms.setText(dataArray.getString("symptoms"));
                            doctorname.setText(dataArray.getString("name") + " " + dataArray.getString("surname") + " (" + dataArray.getString("employee_id") + ")");
                            String imgUrl = Utility.getSharedPreferences(getActivity(), "imagesUrl") + "uploads/staff_images/" + dataArray.getString("doctor_image");
                            Picasso.with(getActivity()).load(imgUrl).placeholder(R.drawable.placeholder_user).memoryPolicy(MemoryPolicy.NO_CACHE)
                                    .networkPolicy(NetworkPolicy.NO_CACHE).into(doctorimage);

                            doctorlist.clear();
                            imagelist.clear();
                            JSONArray doctorArray = obj.getJSONArray("doctors_ipd");
                            for (int i = 0; i < doctorArray.length(); i++) {
                                doctorlist.add(doctorArray.getJSONObject(i).getString("ipd_doctorname") + " " + doctorArray.getJSONObject(i).getString("ipd_doctorsurname") + " (" + doctorArray.getJSONObject(i).getString("employee_id") + ")");
                                imagelist.add(doctorArray.getJSONObject(i).getString("image"));
                            }
                            doctoradapter.notifyDataSetChanged();

                            findingslist.clear();
                            JSONArray prescriptionArray = obj.getJSONArray("prescription");
                            for (int j = 0; j < prescriptionArray.length(); j++) {
                                findingslist.add(prescriptionArray.getJSONObject(j).getString("finding_description"));
                            }
                            findingadapter.notifyDataSetChanged();

                            JSONObject graphArray = obj.getJSONObject("graph");

                            JSONObject ipdArray = graphArray.getJSONObject("ipd");
                            String bill_payment_ratio = ipdArray.getString("ipd_bill_payment_ratio");
                            totalbillratio.setText(bill_payment_ratio + "%");
                            String bill_balance = ipdArray.getString("ipd_bill_balance");
                            JSONObject billArray = ipdArray.getJSONObject("bill");
                            String total_bill = billArray.getString("total_bill");
                            JSONObject paymentArray = ipdArray.getJSONObject("payment");
                            String total_payment = paymentArray.getString("total_payment");
                            totalbillpayment.setText(currency + total_payment + "/" + currency + total_bill);
                            Integer progressvalueint = (int) (Double.parseDouble(bill_payment_ratio));
                            progressBar.setProgress(progressvalueint);
                            if (progressvalueint == 100) {
                                progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }

                            JSONObject pharmacyArray = graphArray.getJSONObject("pharmacy");
                            String pharmacy_bill_payment_ratio = pharmacyArray.getString("pharmacy_bill_payment_ratio");
                            pharmacy_totalbillratio.setText(pharmacy_bill_payment_ratio + "%");
                            String pharmacy_bill_balance = pharmacyArray.getString("pharmacy_bill_balance");
                            JSONObject pharmacybillArray = pharmacyArray.getJSONObject("bill");
                            String pharmacytotal_bill = pharmacybillArray.getString("total_bill");
                            JSONObject pharmacypaymentArray = pharmacyArray.getJSONObject("payment");
                            String pharmacytotal_payment = pharmacypaymentArray.getString("total_payment");
                            JSONObject pharmacypayment_refundArray = pharmacyArray.getJSONObject("payment_refund");
                            String pharmacytotalrefund_payment = pharmacypayment_refundArray.getString("total_payment");
                            pharmacy_totalbillpayment.setText(currency + pharmacytotal_payment + "/" + currency + pharmacytotal_bill);
                            Integer pharmacyprogress = (int) (Double.parseDouble(pharmacy_bill_payment_ratio));
                            pharmacy_progressBar.setProgress(pharmacyprogress);
                            if (pharmacyprogress == 100) {
                                pharmacy_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                pharmacy_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }

                            JSONObject radiologyArray = graphArray.getJSONObject("radiology");
                            String radiology_bill_payment_ratio = radiologyArray.getString("radiology_bill_payment_ratio");
                            radiology_totalbillratio.setText(radiology_bill_payment_ratio + "%");
                            String radiology_bill_balance = radiologyArray.getString("radiology_bill_balance");
                            JSONObject radiologybillArray = radiologyArray.getJSONObject("bill");
                            String radiologytotal_bill = radiologybillArray.getString("total_bill");
                            JSONObject radiologypaymentArray = radiologyArray.getJSONObject("payment");
                            String radiologytotal_payment = radiologypaymentArray.getString("total_payment");
                            radiology_totalbillpayment.setText(currency + radiologytotal_payment + "/" + currency + radiologytotal_bill);
                            Integer radiologyprogress = (int) (Double.parseDouble(radiology_bill_payment_ratio));
                            radiology_progressBar.setProgress(radiologyprogress);

                            if (radiologyprogress == 100) {
                                radiology_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                radiology_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }

                            JSONObject pathologyArray = graphArray.getJSONObject("pathology");
                            String pathology_bill_payment_ratio = pathologyArray.getString("pathology_bill_payment_ratio");
                            pathology_totalbillratio.setText(pathology_bill_payment_ratio + "%");
                            String pathology_bill_balance = pathologyArray.getString("pathology_bill_balance");
                            JSONObject pathologybillArray = pathologyArray.getJSONObject("bill");
                            String pathologytotal_bill = pathologybillArray.getString("total_bill");
                            JSONObject pathologypaymentArray = pathologyArray.getJSONObject("payment");
                            String pathologytotal_payment = pathologypaymentArray.getString("total_payment");
                            pathology_totalbillpayment.setText(currency + pathologytotal_payment + "/" + currency + pathologytotal_bill);
                            Integer pathologyprogress = (int) (Double.parseDouble(pathology_bill_payment_ratio));
                            pathology_progressBar.setProgress(pathologyprogress);
                            if (pathologyprogress == 100) {
                                pathology_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                pathology_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }


                            JSONObject ambulanceArray = graphArray.getJSONObject("ambulance");
                            String ambulance_bill_payment_ratio = ambulanceArray.getString("ambulance_bill_payment_ratio");
                            ambulance_totalbillratio.setText(ambulance_bill_payment_ratio + "%");
                            String ambulance_bill_balance = ambulanceArray.getString("ambulance_bill_balance");
                            JSONObject ambulancebillArray = ambulanceArray.getJSONObject("bill");
                            String ambulancetotal_bill = ambulancebillArray.getString("total_bill");
                            JSONObject ambulancepaymentArray = ambulanceArray.getJSONObject("payment");
                            String ambulancetotal_payment = ambulancepaymentArray.getString("total_payment");
                            ambulance_totalbillpayment.setText(currency + ambulancetotal_payment + "/" + currency + ambulancetotal_bill);
                            Integer ambulance_bankprogress = (int) (Double.parseDouble(ambulance_bill_payment_ratio));
                            ambulance_progressBar.setProgress(ambulance_bankprogress);
                            if (ambulance_bankprogress == 100) {
                                ambulance_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                ambulance_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }


                            JSONObject blood_bankArray = graphArray.getJSONObject("blood_bank");
                            String blood_bank_bill_payment_ratio = blood_bankArray.getString("blood_bank_bill_payment_ratio");
                            bloodbank_totalbillratio.setText(blood_bank_bill_payment_ratio + "%");
                            String blood_bank_bill_balance = blood_bankArray.getString("blood_bank_bill_balance");
                            JSONObject blood_bankbillArray = blood_bankArray.getJSONObject("bill");
                            String blood_banktotal_bill = blood_bankbillArray.getString("total_bill");
                            JSONObject blood_bankpaymentArray = blood_bankArray.getJSONObject("payment");
                            String blood_banktotal_payment = blood_bankpaymentArray.getString("total_payment");
                            bloodbank_totalbillpayment.setText(currency + blood_banktotal_payment + "/" + currency + blood_banktotal_bill);
                            Integer blood_bankprogress = (int) (Double.parseDouble(blood_bank_bill_payment_ratio));
                            bloodbank_progressBar.setProgress(blood_bankprogress);
                            if (blood_bankprogress == 100) {
                                bloodbank_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                                //holder.progressBar.setProgressTintList(context.getResources().getDrawable(R.drawable.green_border));
                            } else if (progressvalueint > 0 && progressvalueint < 100) {
                                bloodbank_progressBar.getProgressDrawable().setColorFilter(
                                        getActivity().getResources().getColor(R.color.hyperLink), android.graphics.PorterDuff.Mode.SRC_IN);
                            }



                            String credit_limit = obj.getString("credit_limit");
                            Integer balance_credit_limit = obj.getInt("balance_credit_limit");
                            graph_per.setText(obj.getString("donut_graph_percentage") + "%");
                            String donut_graph_percentage = Float.parseFloat(obj.getString("donut_graph_percentage")) + "f";
                            Float f = Float.valueOf(donut_graph_percentage);
                            int val = f.intValue();
                            int restval = 100 - val;
                            System.out.println("donut_graph_percentage==" + val);

                            String used_credit_limit = obj.getString("used_credit_limit");
                            caseid.setText(dataArray.getString("case_reference_id"));
                            ipdnotv.setText("IPDN" + dataArray.getString("ipdid"));
                            admissiondate.setText(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat, dataArray.getString("date")));
                            bed.setText(dataArray.getString("bed_name") + " - " + dataArray.getString("bedgroup_name") + " - " + dataArray.getString("floor_name"));

                            pieChart.addPieSlice(
                                    new PieModel(
                                            "Used Credit Limit",
                                            Integer.parseInt(String.valueOf(restval)),
                                            Color.parseColor("#ed5c5c")));
                            pieChart.addPieSlice(
                                    new PieModel(
                                            "Balanced Credit Limit",
                                            Integer.parseInt(String.valueOf(val)),
                                            Color.parseColor("#66aa18")));

                            // To animate the pie chart
                            pieChart.startAnimation();
                            creditlimit.setText(currency + String.format("%.2f", Double.parseDouble(credit_limit)));
                            usedcredit.setText(currency +  String.format("%.2f",Double.parseDouble(used_credit_limit)));
                            System.out.println("credit_limit="+credit_limit);
                            System.out.println("used_credit_limit="+used_credit_limit);
                            if(credit_limit.equals(used_credit_limit)){
                                limit_exceed.setVisibility(View.VISIBLE);
                            }if(Double.parseDouble(credit_limit)<Double.parseDouble(used_credit_limit)){
                                limit_exceed.setVisibility(View.VISIBLE);
                            }else if(Double.parseDouble(credit_limit)>Double.parseDouble(used_credit_limit)){
                                limit_exceed.setVisibility(View.GONE);
                            }
                            balancedcredit.setText(currency + String.format("%.2f", Double.parseDouble(String.valueOf(balance_credit_limit))));


                        } else if (obj.get("result") instanceof JSONArray) {
                            JSONArray dataobject = obj.getJSONArray("result");
                            System.out.println("Result is Array");
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
                        ipdCurrentVitalAdapter.notifyDataSetChanged();

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