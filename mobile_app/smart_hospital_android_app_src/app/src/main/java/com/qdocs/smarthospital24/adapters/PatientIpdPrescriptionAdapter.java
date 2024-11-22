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
import static android.content.Context.RECEIVER_EXPORTED;
import static android.widget.Toast.makeText;

public class PatientIpdPrescriptionAdapter extends RecyclerView.Adapter<PatientIpdPrescriptionAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> ipd_idList;
    private ArrayList<String> dateList;
    private ArrayList<String> idlist;
    private ArrayList<String> headerList;
    private ArrayList<String> findingslist;
    private ArrayList<String> footerList;
    TextView symptoms;
    ImageView attachment;
    ArrayList<String> medicinenamelist = new ArrayList<String>();
    ArrayList<String> dose_interval_namelist = new ArrayList<String>();
    ArrayList<String> dose_duration_namelist = new ArrayList<String>();
    ArrayList<String> pathotestlist = new ArrayList<String>();
    ArrayList<String> radiotestlist = new ArrayList<String>();
    ArrayList<String> instructionlist = new ArrayList<String>();
    ArrayList<String> medicine_categorylist = new ArrayList<String>();
    ArrayList<String> dosagelist = new ArrayList<String>();
    long downloadID;
    PatientIpdAdapter adapter;
    PatientIpdpathotestAdapter pathotestAdapter;
    PatientIpdradiotestAdapter radiotestAdapter;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public PatientIpdPrescriptionAdapter(FragmentActivity fragmentActivity, ArrayList<String> idlist, ArrayList<String> ipd_idList,
                                         ArrayList<String> dateList, ArrayList<String> headerList, ArrayList<String> footerList, ArrayList<String> findingslist) {

        this.context = fragmentActivity;
        this.idlist = idlist;
        this.ipd_idList = ipd_idList;
        this.dateList = dateList;
        this.headerList = headerList;
        this.footerList = footerList;
        this.findingslist = findingslist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView ipd_no,date,prescno,finding;
        ImageView viewPresc;
        RelativeLayout detailsBtn,headLay;
        public CardView containerView;

        public MyViewHolder(View view) {
            super(view);
            ipd_no = (TextView) view.findViewById(R.id.adapter_patient_ipd_ipdno);
            date = (TextView) view.findViewById(R.id.adapter_patient_ipd_date);
            prescno = (TextView) view.findViewById(R.id.adapter_patient_ipd_presno);
           finding = (TextView) view.findViewById(R.id.adapter_patient_ipd_finding);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_ipd_headLayout);
            viewPresc = (ImageView) view.findViewById(R.id.adapter_ipd_viewPresc);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_prescription, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.ipd_no.setText("IPDP"+idlist.get(position));
        holder.date.setText(dateList.get(position));
        holder.finding.setText(findingslist.get(position));
        holder.prescno.setText("IPDP"+idlist.get(position));

        holder.viewPresc.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {

                View view = context.getLayoutInflater().inflate(R.layout.fragment_ipd_presc_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientpres_bottomSheet__header);
                TextView prescno= view.findViewById(R.id.patientodp_bottomSheet_prescno);
               LinearLayout findings_layout= view.findViewById(R.id.findings_layout);
                WebView footer= view.findViewById(R.id.patient_bottomSheet_footer);
                 attachment= view.findViewById(R.id.patient_attachment);
                footer.getSettings().setJavaScriptEnabled(true);
                footer.getSettings().setBuiltInZoomControls(true);
                footer.getSettings().setLoadWithOverviewMode(true);
                footer.getSettings().setUseWideViewPort(true);
                footer.getSettings().setDefaultFontSize(30);
                WebView header= view.findViewById(R.id.patient_bottomSheet_header);
                header.getSettings().setJavaScriptEnabled(true);
                header.getSettings().setBuiltInZoomControls(true);
                header.getSettings().setLoadWithOverviewMode(true);
                header.getSettings().setUseWideViewPort(true);
                header.getSettings().setDefaultFontSize(30);
                TextView findings= view.findViewById(R.id.patient_bottomSheet_findings);
                 symptoms= view.findViewById(R.id.patient_bottomSheet_symptoms);
                TextView prescdate= view.findViewById(R.id.patientopd_bottomSheet_prescdate);
                ImageView crossBtn = view.findViewById(R.id.patientpres_bottomSheet__crossBtn);
                RecyclerView recyclerview = view.findViewById(R.id.recyclerview);
                RecyclerView patho_recyclerview = view.findViewById(R.id.patho_recyclerview);
                RecyclerView radio_recyclerview = view.findViewById(R.id.radio_recyclerview);
                 adapter = new PatientIpdAdapter(context.getApplicationContext(), medicine_categorylist,
                        medicinenamelist,dosagelist,instructionlist,dose_interval_namelist,dose_duration_namelist);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                recyclerview.setLayoutManager(mLayoutManager);
                recyclerview.setItemAnimator(new DefaultItemAnimator());
                recyclerview.setAdapter(adapter);


                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                    context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
                }else {
                    context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
                }

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

                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("prescription_no", idlist.get(position));
                    JSONObject obj=new JSONObject(params);
                    Log.e("details params ", obj.toString());
                    getDataFromApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

                prescno.setText("IPDP"+idlist.get(position));
                if(findingslist.get(position).equals("")){
                    findings_layout.setVisibility(View.GONE);
                }else {
                    findings_layout.setVisibility(View.VISIBLE);
                    findings.setText(findingslist.get(position));
                }
                prescdate.setText(dateList.get(position));

                String head=headerList.get(position);
                head = head.replaceAll("&nbsp;", "");
                header.loadDataWithBaseURL(null,head,"text/html; charset=utf-8", "utf-8", null);
                String foot=footerList.get(position);
                foot = foot.replaceAll("&nbsp;", "");
                footer.loadDataWithBaseURL(null,foot,"text/html; charset=utf-8", "utf-8", null);

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.ipd_prescription));
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

    @Override
    public int getItemCount() {

        return idlist.size();
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


    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getipdprescriptionUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {

                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject dataArray = obj.getJSONObject("result");
                        symptoms.setText(dataArray.getString("symptoms"));
                       if(dataArray.getString("attachment").equals("")){
                           attachment.setVisibility(View.GONE);
                       }else{
                           attachment.setVisibility(View.VISIBLE);
                           attachment.setOnClickListener(new View.OnClickListener() {
                               @Override
                               public void onClick(View view) {
                                   String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                                   try {
                                       urlStr += "uploads/prescription_document/"+dataArray.getString("attachment");
                                       downloadID =Utility.beginDownload(context, dataArray.getString("attachment"), urlStr);
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

                        JSONArray medicineArray = dataArray.getJSONArray("medicines");
                        JSONArray pathologyArray = dataArray.getJSONArray("pathology");
                        JSONArray radiologyArray = dataArray.getJSONArray("radiology");
                        medicinenamelist.clear();
                        instructionlist.clear();
                        medicine_categorylist.clear();
                        dose_duration_namelist.clear();
                        dose_interval_namelist.clear();
                        dosagelist.clear();
                        radiotestlist.clear();
                        pathotestlist.clear();

                        if(medicineArray.length() != 0) {
                            for(int i = 0; i < medicineArray.length(); i++) {
                                medicine_categorylist.add(medicineArray.getJSONObject(i).getString("medicine_category"));
                                dosagelist.add(medicineArray.getJSONObject(i).getString("dosage")+" "+medicineArray.getJSONObject(i).getString("unit"));
                                instructionlist.add(medicineArray.getJSONObject(i).getString("instruction"));
                                medicinenamelist.add(medicineArray.getJSONObject(i).getString("medicine_name"));
                                dose_interval_namelist.add(medicineArray.getJSONObject(i).getString("dose_interval_name"));
                                dose_duration_namelist.add(medicineArray.getJSONObject(i).getString("dose_duration_name"));
                            }
                            adapter.notifyDataSetChanged();
                        }

                        if(pathologyArray.length() != 0) {
                            for(int i = 0; i < pathologyArray.length(); i++) {
                                pathotestlist.add(pathologyArray.getJSONObject(i).getString("test_name")+"("+pathologyArray.getJSONObject(i).getString("short_name")+")");
                            }
                            pathotestAdapter.notifyDataSetChanged();
                        }

                        if(radiologyArray.length() != 0) {
                            for (int i = 0; i < radiologyArray.length(); i++) {
                                radiotestlist.add(radiologyArray.getJSONObject(i).getString("radio_test_name") + "(" + radiologyArray.getJSONObject(i).getString("radio_short_name") + ")");
                            }
                            radiotestAdapter.notifyDataSetChanged();
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
}
