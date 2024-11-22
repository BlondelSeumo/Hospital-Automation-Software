package com.qdocs.smarthospital24.adapters;

import android.app.DownloadManager;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Color;
import android.os.Build;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
import androidx.core.app.NotificationCompat;
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
import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.OpdDetailModel;
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

import static android.content.Context.RECEIVER_EXPORTED;
import static android.content.Intent.FLAG_ACTIVITY_NEW_TASK;
import static android.widget.Toast.makeText;

public class PatientOpdVisitAdapter extends RecyclerView.Adapter<PatientOpdVisitAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<OpdDetailModel> opd_detail_list;
    ArrayList<String> medicine_category_list = new ArrayList<String>();
    ArrayList<String> medicineList = new ArrayList<String>();
    ArrayList<String> dosagelist = new ArrayList<String>();
    ArrayList<String> instructionlist = new ArrayList<String>();
    ArrayList<String> durationlist = new ArrayList<String>();
    ArrayList<String> intervallist = new ArrayList<String>();
    ArrayList<String> testnamelist = new ArrayList<String>();
    PatientOpdPrescriptionAdapter padapter;
    ArrayList<String> pathotestlist = new ArrayList<String>();
    ArrayList<String> radiotestlist = new ArrayList<String>();
    PatientIpdpathotestAdapter pathotestAdapter;
    PatientIpdradiotestAdapter radiotestAdapter;
    Fragment fragment;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    TextView  prescdate,prescno,findings,symptoms,opdcheckupid,opdid,radio_TV,patho_TV;
    ImageView patient_attachment;
    long downloadID;
    WebView header,footer;
    LinearLayout findings_layout,symptoms_layout,pathologytest_layout,radiologytest_layout,document;
    public PatientOpdVisitAdapter(Context context, ArrayList<OpdDetailModel> opd_detail_list, Fragment fragment) {

        this.context = context;
        this.opd_detail_list = opd_detail_list;
        this.fragment = fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView opdno, date, reference , symptoms, consultant,caseid;
        ImageView downloadBtn,prescription;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
        RecyclerView recyclerview;

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
            recyclerview = (RecyclerView)view.findViewById(R.id.recyclerview);

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
        final OpdDetailModel model = opd_detail_list.get(position);
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.opdno.setText("OPDN"+model.getOpdid());
        holder.date.setText(model.getAppointment_date());
        holder.reference.setText(model.getRefference());
        holder.consultant.setText(model.getName());
        holder.caseid.setText(model.getCase_reference_id());
        holder.symptoms.setText(model.getSymptoms());

        ArrayList<CustomFieldModel> customList = model.getCustomfield();
         System.out.println("customList"+customList);
        CustomlistAdapter adapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(adapter);

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                Intent myactivity = new Intent(context.getApplicationContext(), PatientOpdVisitDetailsList.class);
                myactivity.addFlags(FLAG_ACTIVITY_NEW_TASK);
                myactivity.putExtra("opd_id", model.getOpdid());
                context.getApplicationContext().startActivity(myactivity);

            }
        });
        if(model.getPrescription().equals("1")){
            holder.prescription.setVisibility(View.VISIBLE);
            holder.prescription.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View viewClick) {
                    LayoutInflater inflater= LayoutInflater.from(context);
                    View view = inflater.inflate(R.layout.fragment_opd_presc_bottom_sheet, null);
                    // View view = context.getLayoutInflater().inflate(R.layout.fragment_ipd_presc_bottom_sheet, null);
                    view.setMinimumHeight(500);

                    TextView headerTV = view.findViewById(R.id.patientpres_bottomSheet__header);
                    ImageView crossBtn = view.findViewById(R.id.patientpres_bottomSheet__crossBtn);
                    document = view.findViewById(R.id.document);
                    prescdate = view.findViewById(R.id.patientopd_bottomSheet_prescdate);
                    patient_attachment = view.findViewById(R.id.patient_attachment);
                    patho_TV = view.findViewById(R.id.patho_TV);
                    radio_TV = view.findViewById(R.id.radio_TV);
                    radiologytest_layout = view.findViewById(R.id.radiologytest_layout);
                    pathologytest_layout = view.findViewById(R.id.pathologytest_layout);
                    prescno = view.findViewById(R.id.patientodp_bottomSheet_prescno);
                    opdcheckupid = view.findViewById(R.id.patient_opdcheckupid);
                    opdid = view.findViewById(R.id.patient_opdid);
                    findings_layout = view.findViewById(R.id.findings_layout);
                    symptoms_layout = view.findViewById(R.id.symptoms_layout);
                    header = view.findViewById(R.id.patient_bottomSheet_header);
                    header.getSettings().setJavaScriptEnabled(true);
                    header.getSettings().setBuiltInZoomControls(true);
                    header.getSettings().setLoadWithOverviewMode(true);
                    header.getSettings().setUseWideViewPort(true);
                    header.getSettings().setDefaultFontSize(30);
                    findings = view.findViewById(R.id.patient_bottomSheet_findings);
                    symptoms = view.findViewById(R.id.patient_bottomSheet_symptoms);
                    footer = view.findViewById(R.id.patient_bottomSheet_footer);
                    footer.getSettings().setJavaScriptEnabled(true);
                    footer.getSettings().setBuiltInZoomControls(true);
                    footer.getSettings().setLoadWithOverviewMode(true);
                    footer.getSettings().setUseWideViewPort(true);
                    footer.getSettings().setDefaultFontSize(30);
                    RecyclerView recyclerview = view.findViewById(R.id.recyclerview);
                    RecyclerView patho_recyclerview = view.findViewById(R.id.patho_recyclerview);
                    RecyclerView radio_recyclerview = view.findViewById(R.id.radio_recyclerview);
                    if(Utility.isConnectingToInternet(context.getApplicationContext())){
                        params.put("visitid",model.getId());
                        JSONObject obj=new JSONObject(params);
                        Log.e("params prescr", obj.toString());
                        getDataApi(obj.toString());
                    }else{
                        makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }

                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                        context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
                    }else {
                        context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
                    }
                    padapter = new PatientOpdPrescriptionAdapter(context.getApplicationContext(), medicine_category_list,
                            medicineList, dosagelist, instructionlist,intervallist,durationlist);
                    RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    recyclerview.setLayoutManager(mLayoutManager);
                    recyclerview.setItemAnimator(new DefaultItemAnimator());
                    recyclerview.setAdapter(padapter);

                    pathotestAdapter = new PatientIpdpathotestAdapter(context.getApplicationContext(), pathotestlist);
                    RecyclerView.LayoutManager pLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    patho_recyclerview.setLayoutManager(pLayoutManager);
                    patho_recyclerview.setItemAnimator(new DefaultItemAnimator());
                    patho_recyclerview.setAdapter(pathotestAdapter);

                    radiotestAdapter = new PatientIpdradiotestAdapter(context.getApplicationContext(), radiotestlist);
                    RecyclerView.LayoutManager rLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    radio_recyclerview.setLayoutManager(rLayoutManager);
                    radio_recyclerview.setItemAnimator(new DefaultItemAnimator());
                    radio_recyclerview.setAdapter(radiotestAdapter);

                    headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                    headerTV.setText(context.getString(R.string.prescription));


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
        }else{
            holder.prescription.setVisibility(View.GONE);
        }



    }

    public BroadcastReceiver onDownloadComplete = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {
            //Fetching the download id received with the broadcast
            long id = intent.getLongExtra(DownloadManager.EXTRA_DOWNLOAD_ID, -1);
            //Checking if the received broadcast is for our enqueued download by matching download id
            if (downloadID == id) {
                NotificationCompat.Builder mBuilder =
                        new NotificationCompat.Builder(context)
                                .setSmallIcon(R.drawable.notification_logo)
                                .setContentTitle(context.getApplicationContext().getString(R.string.app_name))
                                .setContentText(context.getApplicationContext().getString(R.string.download));

                NotificationManager notificationManager = (NotificationManager) context.getSystemService(Context.NOTIFICATION_SERVICE);
                notificationManager.notify(455, mBuilder.build());
                context.unregisterReceiver(onDownloadComplete);
            }
        }
    };

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
                        prescno.setText(context.getString(R.string.prescription)+" "+context.getString(R.string.opdprefix)+detailArray.getString("prescription_id"));
                        header.loadDataWithBaseURL(null,detailArray.getString("header_note"),"text/html; charset=utf-8", "utf-8", null);
                        footer.loadDataWithBaseURL(null,detailArray.getString("footer_note"),"text/html; charset=utf-8", "utf-8", null);
                        if(detailArray.getString("symptoms").equals("")){
                            symptoms_layout.setVisibility(View.GONE);
                        }else{
                            symptoms_layout.setVisibility(View.VISIBLE);
                            symptoms.setText(detailArray.getString("symptoms"));
                        }

                        if(detailArray.getString("ipd_prescription_attachment").equals("")){
                            document.setVisibility(View.GONE);
                        }else{
                            document.setVisibility(View.VISIBLE);
                            document.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View view) {
                                    String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                                    try {
                                        urlStr += "uploads/prescription_document/"+detailArray.getString("ipd_prescription_attachment");
                                        downloadID =Utility.beginDownload(context, detailArray.getString("ipd_prescription_attachment"), urlStr);
                                        System.out.println("Image Ipd"+urlStr);
                                        Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                                        intent.putExtra("imageUrl",urlStr);
                                        context.startActivity(intent);
                                    } catch (JSONException e) {
                                        throw new RuntimeException(e);
                                    }


                                }
                            });
                        }
                        if(detailArray.getString("finding_description").equals("")||detailArray.getString("finding_description").equals(" ")){
                            findings_layout.setVisibility(View.GONE);
                        }else{
                            findings_layout.setVisibility(View.VISIBLE);
                            findings.setText(detailArray.getString("finding_description"));
                        }
                        opdid.setText("OPDN"+detailArray.getString("opd_detail_id"));
                        opdcheckupid.setText("OCID"+detailArray.getString("visitid"));

                            medicineList.clear();
                            instructionlist.clear();
                            dosagelist.clear();
                            medicine_category_list.clear();
                            testnamelist.clear();
                            pathotestlist.clear();
                            radiotestlist.clear();
                             intervallist.clear();
                            durationlist.clear();

                        JSONArray medicinearray=detailArray.getJSONArray("medicines");
                        JSONArray pathologyArray = detailArray.getJSONArray("pathology");
                        JSONArray radiologyArray = detailArray.getJSONArray("radiology");
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

                        if(pathologyArray.length() != 0) {
                            pathologytest_layout.setVisibility(View.VISIBLE);
                            for(int i = 0; i < pathologyArray.length(); i++) {
                                pathotestlist.add(pathologyArray.getJSONObject(i).getString("test_name")+"("+pathologyArray.getJSONObject(i).getString("short_name")+")");
                            }
                            pathotestAdapter.notifyDataSetChanged();
                        }else{
                            pathologytest_layout.setVisibility(View.GONE);
                        }

                        if(radiologyArray.length() != 0) {
                            radiologytest_layout.setVisibility(View.VISIBLE);
                            for (int i = 0; i < radiologyArray.length(); i++) {
                                radiotestlist.add(radiologyArray.getJSONObject(i).getString("radio_test_name") + "(" + radiologyArray.getJSONObject(i).getString("radio_short_name") + ")");
                            }
                            radiotestAdapter.notifyDataSetChanged();
                        }else{
                            radiologytest_layout.setVisibility(View.GONE);
                        }

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
        return opd_detail_list.size();
    }
}