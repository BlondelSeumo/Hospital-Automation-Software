package com.qdocs.smarthospital24.adapters;

import android.app.Dialog;
import android.app.DownloadManager;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
import androidx.core.app.NotificationCompat;
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
import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
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

public class PatientOpdLabInvestigationAdapter extends RecyclerView.Adapter<PatientOpdLabInvestigationAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> opd_nolist;
    private ArrayList<String> test_namelist;
    private ArrayList<String> samplecollected;
    private ArrayList<String> report_idlist;
    private ArrayList<String> typelist;
    private ArrayList<String> dateList;
    ImageView downloadbtn;
    TextView resultTV;
    LinearLayout result_layout;
    ArrayList<String> parameterlist = new ArrayList<String>();
    ArrayList<String> radiology_report_idlist = new ArrayList<String>();
    ArrayList<String> unitlist = new ArrayList<String>();
    ArrayList<String> referencelist = new ArrayList<String>();
    ArrayList<String> descriptionlist = new ArrayList<String>();
    ArrayList<String> case_reference_idlist = new ArrayList<String>();
    ArrayList<String> approvedstaff = new ArrayList<String>();
    PatientInvestigationDetailAdapter adapter;
    long downloadID;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    TextView  prescdate,prescno,header,footer;
    public PatientOpdLabInvestigationAdapter(FragmentActivity fragmentActivity, ArrayList<String> dateList, ArrayList<String> test_namelist,
                                              ArrayList<String> typelist, ArrayList<String> samplecollected,
                                             ArrayList<String> case_reference_idlist,ArrayList<String> report_idlist,ArrayList<String> approvedstaff) {

        this.context = fragmentActivity;
        this.dateList = dateList;
        this.test_namelist = test_namelist;
        this.typelist = typelist;
        this.samplecollected = samplecollected;
        this.case_reference_idlist = case_reference_idlist;
        this.report_idlist = report_idlist;
        this.approvedstaff = approvedstaff;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView testname, date, lab , samplecollected,caseid,approvedby;
        ImageView downloadBtn,prescription;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            date = (TextView) view.findViewById(R.id.adapter_patient_opd_date);
            testname = (TextView) view.findViewById(R.id.adapter_patient_opd_testname);
            lab = (TextView) view.findViewById(R.id.adapter_patient_lab);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_opd_caseid);
            samplecollected = (TextView) view.findViewById(R.id.adapter_patient_samplecollected);
            approvedby = (TextView) view.findViewById(R.id.adapter_patient_approvedby);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_opd_detailsBtn);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_opd_headLayout);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_opdlabinvestigation, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {

        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.date.setText(context.getString(R.string.expecteddate)+" "+dateList.get(position));
        holder.testname.setText(test_namelist.get(position));
        holder.lab.setText(String.valueOf(typelist.get(position).charAt(0)).toUpperCase() + typelist.get(position).substring(1, typelist.get(position).length()));
        holder.samplecollected.setText(samplecollected.get(position));
        holder.caseid.setText(case_reference_idlist.get(position));
        holder.approvedby.setText(approvedstaff.get(position));

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                final Dialog dialog = new Dialog(context);
                dialog.setContentView(R.layout.investigationdetail);
                dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.FILL_PARENT);
                dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;
                final ImageView closeBtn = (ImageView) dialog.findViewById(R.id.dialog_crossIcon);
                downloadbtn = (ImageView) dialog.findViewById(R.id.adapter_patient_downloadbtn);
                resultTV = (TextView) dialog.findViewById(R.id.result);
                result_layout = (LinearLayout) dialog.findViewById(R.id.result_layout);
                final RelativeLayout header = dialog.findViewById(R.id.addappoint_dialog_header);
                final TextView headertext = dialog.findViewById(R.id.headertext);
                final RecyclerView recyclerview = dialog.findViewById(R.id.recyclerview);
                header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                adapter = new PatientInvestigationDetailAdapter(context.getApplicationContext(), parameterlist, unitlist,
                        referencelist,descriptionlist);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                recyclerview.setLayoutManager(mLayoutManager);
                recyclerview.setItemAnimator(new DefaultItemAnimator());
                recyclerview.setAdapter(adapter);
                headertext.setText(test_namelist.get(position));
                if (Utility.isConnectingToInternet(context.getApplicationContext())) {
                    params.put("record_id",report_idlist.get(position));
                    params.put("type",typelist.get(position) );
                    JSONObject obj = new JSONObject(params);
                    Log.e(" details params ", obj.toString());
                    getinvestigationparameter(obj.toString(),typelist.get(position));
                } else {
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
                closeBtn.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        dialog.dismiss();
                    }
                });
                dialog.show();
            }
        });
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
                                .setContentText("All Download completed");

                NotificationManager notificationManager = (NotificationManager) context.getSystemService(Context.NOTIFICATION_SERVICE);
                notificationManager.notify(455, mBuilder.build());
            }
        }
    };

    private void getinvestigationparameter (String bodyParams, final String type) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getinvestigationparameterUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {

                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        double sum=0;
                        final JSONObject reportarray = obj.getJSONObject("result");
                        if(type.equals("radiology")){
                            if(reportarray.getString("radiology_result").equals("")){
                                result_layout.setVisibility(View.GONE);
                            }else{
                                result_layout.setVisibility(View.VISIBLE);
                                resultTV.setText(reportarray.getString("radiology_result"));
                            }

                            if(reportarray.getString("radiology_report").equals("")){
                                downloadbtn.setVisibility(View.GONE);
                            }else{
                                downloadbtn.setVisibility(View.VISIBLE);
                                downloadbtn.setOnClickListener(new View.OnClickListener() {
                                    @Override
                                    public void onClick(View view) {

                                        String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                                        try {
                                            urlStr += reportarray.getString("radiology_report");
                                            downloadID = Utility.beginDownload(context,reportarray.getString("radiology_report"), urlStr);
                                            System.out.println("urlStr="+urlStr);

                                            Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                                            intent.putExtra("imageUrl",urlStr);
                                            context.startActivity(intent);
                                        } catch (JSONException e) {
                                            e.printStackTrace();
                                        }
                                    }
                                });
                            }
                        }else if(type.equals("pathology")){
                            if(reportarray.getString("pathology_result").equals("")){
                                result_layout.setVisibility(View.GONE);
                            }else{
                                result_layout.setVisibility(View.VISIBLE);
                                resultTV.setText(reportarray.getString("pathology_result"));
                            }

                            if(reportarray.getString("pathology_report").equals("")){
                                downloadbtn.setVisibility(View.GONE);
                            }else{
                                downloadbtn.setVisibility(View.VISIBLE);
                                downloadbtn.setOnClickListener(new View.OnClickListener() {
                                    @Override
                                    public void onClick(View view) {

                                        String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                                        try {
                                            urlStr += reportarray.getString("pathology_report");
                                            downloadID = Utility.beginDownload(context,reportarray.getString("pathology_report"), urlStr);
                                            System.out.println("urlStr="+urlStr);

                                            Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                                            intent.putExtra("imageUrl",urlStr);
                                            context.startActivity(intent);
                                        } catch (JSONException e) {
                                            e.printStackTrace();
                                        }
                                    }
                                });
                            }
                        }

                        JSONArray parameterArray = reportarray.getJSONArray("parameter");
                        parameterlist.clear();
                        unitlist.clear();
                        referencelist.clear();
                        descriptionlist.clear();
                        radiology_report_idlist.clear();
                        if(parameterArray.length() != 0) {
                            for(int i = 0; i < parameterArray.length(); i++) {
                                if(type.equals("radiology")){
                                    parameterlist.add(parameterArray.getJSONObject(i).getString("parameter_name"));
                                    descriptionlist.add(parameterArray.getJSONObject(i).getString("description"));
                                    if(parameterArray.getJSONObject(i).getString("radiology_report_value").equals("")){
                                        unitlist.add("");
                                    }else{
                                        unitlist.add(parameterArray.getJSONObject(i).getString("radiology_report_value")+" "+parameterArray.getJSONObject(i).getString("unit_name"));
                                    }
                                    referencelist.add(parameterArray.getJSONObject(i).getString("reference_range")+" "+parameterArray.getJSONObject(i).getString("unit_name"));
                                }else if(type.equals("pathology")){
                                    parameterlist.add(parameterArray.getJSONObject(i).getString("parameter_name"));
                                    descriptionlist.add(parameterArray.getJSONObject(i).getString("description"));
                                    unitlist.add(parameterArray.getJSONObject(i).getString("pathology_report_value")+" "+parameterArray.getJSONObject(i).getString("unit_name"));
                                    referencelist.add(parameterArray.getJSONObject(i).getString("reference_range")+" "+parameterArray.getJSONObject(i).getString("unit_name"));
                                }
                            }

                            adapter.notifyDataSetChanged();
                        } else {

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
                Toast.makeText(context, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(context);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue

    }

    @Override
    public int getItemCount() {
        return dateList.size();
    }
}
