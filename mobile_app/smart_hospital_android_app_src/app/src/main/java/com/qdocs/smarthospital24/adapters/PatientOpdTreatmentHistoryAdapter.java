package com.qdocs.smarthospital24.adapters;

import android.content.Intent;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.cardview.widget.CardView;
import androidx.fragment.app.FragmentActivity;
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
import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.patient.PatientOpdVisitDetailsList;
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

import static android.content.Intent.FLAG_ACTIVITY_NEW_TASK;
import static android.widget.Toast.makeText;

public class PatientOpdTreatmentHistoryAdapter extends RecyclerView.Adapter<PatientOpdTreatmentHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> opd_nolist;
    private ArrayList<String> refferencelist;
    private ArrayList<String> symptomslist;
    private ArrayList<String> consultantList;
    private ArrayList<String> dateList;
    private ArrayList<String> casereference_idlist;
    private ArrayList<String> visitidlist;
    private ArrayList<String> prescriptionlist;
    ArrayList<String> medicine_category_list = new ArrayList<String>();
    ArrayList<String> medicineList = new ArrayList<String>();
    ArrayList<String> dosagelist = new ArrayList<String>();
    ArrayList<String> instructionlist = new ArrayList<String>();
    ArrayList<String> durationlist = new ArrayList<String>();
    ArrayList<String> intervallist = new ArrayList<String>();
    ArrayList<String> testnamelist = new ArrayList<String>();
    PatientOpdPrescriptionAdapter padapter;
    PatientOpdTestAdapter pathotestadapter;
    long downloadID;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    TextView  prescdate,prescno,header,footer;
    public PatientOpdTreatmentHistoryAdapter(FragmentActivity fragmentActivity, ArrayList<String> opd_nolist, ArrayList<String> consultantList,
                                             ArrayList<String> refferencelist, ArrayList<String> symptomslist, ArrayList<String> dateList,
                                             ArrayList<String> visitidlist, ArrayList<String> prescriptionlist, ArrayList<String> casereference_idlist) {

        this.context = fragmentActivity;
        this.opd_nolist = opd_nolist;
        this.refferencelist = refferencelist;
        this.symptomslist = symptomslist;
        this.consultantList = consultantList;
        this.dateList = dateList;
        this.casereference_idlist = casereference_idlist;
        this.visitidlist = visitidlist;
        this.prescriptionlist = prescriptionlist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView opdno, date, reference , symptoms, consultant,caseid;
        ImageView downloadBtn,prescription;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            opdno = (TextView) view.findViewById(R.id.adapter_patient_opd_opdno);
            date = (TextView) view.findViewById(R.id.adapter_patient_opd_reportingdate);
            consultant = (TextView) view.findViewById(R.id.adapter_patient_opd_consultant);
            reference = (TextView) view.findViewById(R.id.adapter_patient_opd_reference_doctor);
            symptoms = (TextView) view.findViewById(R.id.adapter_patient_opd_symptoms);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_opd_caseid);
            prescription = (ImageView) view.findViewById(R.id.adapter_patient_opd_prescription);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_opd_detailsBtn);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_opd_headLayout);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_opdlist, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {

        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.opdno.setText("OPDN"+opd_nolist.get(position));
        holder.date.setText(dateList.get(position));
        holder.reference.setText(refferencelist.get(position));
        holder.consultant.setText(consultantList.get(position));
        holder.caseid.setText(casereference_idlist.get(position));

        String symp=symptomslist.get(position);
       // symp = symp.replaceAll("\\<.*?\\>", "");
        holder.symptoms.setText(symptomslist.get(position));


        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                Intent myactivity = new Intent(context.getApplicationContext(), PatientOpdVisitDetailsList.class);
                myactivity.addFlags(FLAG_ACTIVITY_NEW_TASK);
                myactivity.putExtra("opd_id", opd_nolist.get(position));
                context.getApplicationContext().startActivity(myactivity);

            }
        });

            holder.prescription.setVisibility(View.VISIBLE);
            holder.prescription.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View viewClick) {
                    View view = context.getLayoutInflater().inflate(R.layout.fragment_ipd_presc_bottom_sheet, null);
                    view.setMinimumHeight(500);

                    TextView headerTV = view.findViewById(R.id.patientpres_bottomSheet__header);
                    ImageView crossBtn = view.findViewById(R.id.patientpres_bottomSheet__crossBtn);
                    prescdate = view.findViewById(R.id.patientopd_bottomSheet_prescdate);
                    prescno = view.findViewById(R.id.patientodp_bottomSheet_prescno);
                    header = view.findViewById(R.id.patient_bottomSheet_header);
                    footer = view.findViewById(R.id.patient_bottomSheet_footer);
                    RecyclerView recyclerview = view.findViewById(R.id.recyclerview);
                    RecyclerView patho_recyclerview = view.findViewById(R.id.patho_recyclerview);
                    if(Utility.isConnectingToInternet(context.getApplicationContext())){
                        params.put("visitid",visitidlist.get(position));
                        JSONObject obj=new JSONObject(params);
                        Log.e("params prescr", obj.toString());
                        getDataApi(obj.toString());
                    }else{
                        makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }


                    padapter = new PatientOpdPrescriptionAdapter(context.getApplicationContext(), medicine_category_list,
                            medicineList, dosagelist, instructionlist,intervallist,durationlist);
                    pathotestadapter = new PatientOpdTestAdapter(context.getApplicationContext(), testnamelist);
                    RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    recyclerview.setLayoutManager(mLayoutManager);
                    recyclerview.setItemAnimator(new DefaultItemAnimator());
                    recyclerview.setAdapter(padapter);
                    RecyclerView.LayoutManager pLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    patho_recyclerview.setLayoutManager(pLayoutManager);
                    patho_recyclerview.setItemAnimator(new DefaultItemAnimator());
                    patho_recyclerview.setAdapter(pathotestadapter);

                    headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                    headerTV.setText(context.getString(R.string.prescription));

                    //detailsdate.setText(appointment_datelist.get(0));

                    final BottomSheetDialog dialog = new BottomSheetDialog(context);

                    dialog.setContentView(view);
                    BottomSheetBehavior mBehavior = BottomSheetBehavior.from((View) view.getParent());
                    mBehavior.setPeekHeight(800);
                    dialog.show();
                    crossBtn.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {
                            dialog.dismiss();
                        }
                    });
                }
            });
    }

    private void getDataApi (String bodyParams) {
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getopdprescriptionUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    try {
                        String defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject detailArray = obj.getJSONObject("result");
                        detailArray.getString("prescription_id");
                        prescdate.setText(context.getApplicationContext().getString(R.string.date)+"  "+Utility.parseDate("yyyy-MM-dd", defaultDateFormat,detailArray.getString("presdate")));
                        prescno.setText(context.getApplicationContext().getString(R.string.prescription)+"  OPDP"+detailArray.getString("prescription_id"));
                        header.setText(detailArray.getString("header_note"));
                        footer.setText(detailArray.getString("footer_note"));

                            medicineList.clear();
                            instructionlist.clear();
                            dosagelist.clear();
                            medicine_category_list.clear();
                            testnamelist.clear();
                        durationlist.clear();
                        intervallist.clear();

                        JSONArray medicinearray=detailArray.getJSONArray("medicines");
                            if(medicinearray.length() != 0) {
                                for(int j = 0; j < medicinearray.length(); j++) {
                                    medicine_category_list.add(medicinearray.getJSONObject(j).getString("medicine_category"));
                                    medicineList.add(medicinearray.getJSONObject(j).getString("medicine_name"));
                                    dosagelist.add(medicinearray.getJSONObject(j).getString("dosage"));
                                    instructionlist.add(medicinearray.getJSONObject(j).getString("instruction"));
                                    durationlist.add(medicinearray.getJSONObject(j).getString("dose_duration_name"));
                                    intervallist.add(medicinearray.getJSONObject(j).getString("dose_interval_name"));
                                }
                            }
                            padapter.notifyDataSetChanged();

                        JSONArray testsarray=detailArray.getJSONArray("tests");
                        if(testsarray.length() != 0) {
                            for(int j = 0; j < testsarray.length(); j++) {
                                testnamelist.add(testsarray.getJSONObject(j).getString("test_name")+"("+testsarray.getJSONObject(j).getString("short_name")+")");
                            }
                        }
                        pathotestadapter.notifyDataSetChanged();

                        } catch (JSONException e) {
                        e.printStackTrace();
                    }

                } else {
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(context.getApplicationContext(), R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(context.getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(context.getApplicationContext(), "accessToken"));
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
        RequestQueue requestQueue = Volley.newRequestQueue(context.getApplicationContext());//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }

    @Override
    public int getItemCount() {
        return opd_nolist.size();
    }
}
