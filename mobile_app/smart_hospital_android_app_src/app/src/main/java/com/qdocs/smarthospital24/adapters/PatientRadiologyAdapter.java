package com.qdocs.smarthospital24.adapters;

import android.app.Dialog;
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
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
import androidx.core.app.NotificationCompat;
import androidx.fragment.app.Fragment;
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
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.RadiologyModel;
import com.qdocs.smarthospital24.patient.RadiologyPayment;
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

public class PatientRadiologyAdapter extends RecyclerView.Adapter<PatientRadiologyAdapter.MyViewHolder> {

    private FragmentActivity context;
    long downloadID;
    TextView radiology_total,radiology_prescriptionno,radiology_discount,radiology_tax,radiology_netamount;
    PatientRadiologyReportAdapter adapter;
    PatientPaymentDetailAdapter paymentadapter;
    ArrayList<String> parameter_namelist = new ArrayList<String>();
    ArrayList<String> samplecollectedlist = new ArrayList<String>();
    ArrayList<String> taxlist = new ArrayList<String>();
    ArrayList<String> amountlist = new ArrayList<String>();
    ArrayList<String> radiology_report = new ArrayList<String>();
    ArrayList<String> radiology_result = new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    String defaultDatetimeFormat,defaultDateFormat;
    TextView total_payment;
    Double balance;
    private ArrayList<RadiologyModel> radiology_detail_list;
    ArrayList<String> typelist = new ArrayList<String>();
    ArrayList<String> payment_modelist = new ArrayList<String>();
    ArrayList<String> notelist = new ArrayList<String>();
    ArrayList<String> cheque_datelist = new ArrayList<String>();
    ArrayList<String> cheque_nolist = new ArrayList<String>();
    ArrayList<String> paidamountlist = new ArrayList<String>();
    ArrayList<String> attachment_namelist = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> id_list = new ArrayList<String>();
    Fragment fragment;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
     String currency;

    public PatientRadiologyAdapter(FragmentActivity context, ArrayList<RadiologyModel> radiology_detail_list, Fragment fragment) {

        this.context = context;
        this.radiology_detail_list = radiology_detail_list;
       this.fragment=fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView billno, radiology_date, amount, paidamount, statusTV,caseid,tpa,tpaId,tpaValidity;
        TextView  reference, name, description,Balanceamount,paymentBtn;
        ImageView viewpaymentBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
         RecyclerView recyclerview;
        public MyViewHolder(View view) {
            super(view);
            billno = (TextView) view.findViewById(R.id.adapter_patient_radiology_billno);
            radiology_date = (TextView) view.findViewById(R.id.adapter_patient_radiology_reportingdate);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_radiology_caseid);
            paidamount = (TextView) view.findViewById(R.id.adapter_patient_radiology_paidamount);
            reference = (TextView) view.findViewById(R.id.adapter_patient_radiology_reference_doctor);
            description = (TextView) view.findViewById(R.id.adapter_patient_radiology_description);
            Balanceamount = (TextView) view.findViewById(R.id.adapter_patient_radiology_balanceamount);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_radiology_detailsBtn);
            viewpaymentBtn = (ImageView) view.findViewById(R.id.adapter_patient_radiology_viewpaymentBtn);
            headLay = view.findViewById(R.id.adapter_patient_radiology_headLayout);
            tpa = (TextView) view.findViewById(R.id.adapter_tpa);
            tpaId = (TextView) view.findViewById(R.id.adapter_tpaId);
            tpaValidity = (TextView) view.findViewById(R.id.adapter_tpaValidity);
            statusTV = view.findViewById(R.id.adapter_patient_radiology_charge);
            paymentBtn = view.findViewById(R.id.adapter_patient_radiology_paymentBtn);
            recyclerview = view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_radiology, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
        RadiologyModel radiologyModel=radiology_detail_list.get(position);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        holder.tpa.setText(radiologyModel.getTpa());
        holder.tpaId.setText(radiologyModel.getTpa_id());
        if(radiologyModel.getTpa_validity().equals("")){
            holder.tpaValidity.setText("");
        }else{
            holder.tpaValidity.setText( Utility.parseDate("yyyy-MM-dd", defaultDateFormat,radiologyModel.getTpa_validity()));

        }


        holder.billno.setText(context.getString(R.string.radiologyprefix)+radiologyModel.getId());
        holder.radiology_date.setText(radiologyModel.getDate());
        holder.paidamount.setText(currency+radiologyModel.getPaid_amount());
        if(radiologyModel.getPaid_amount().equals("")){
            holder.paidamount.setText(currency+"0.00");
            balance=(Double.parseDouble(radiologyModel.getNet_amount())-Double.parseDouble("0.00"));
            holder.Balanceamount.setText(currency + String.format("%.2f", balance));
        }else{
            holder.paidamount.setText(currency+radiologyModel.getPaid_amount());
            balance=(Double.parseDouble(radiologyModel.getNet_amount())-Double.parseDouble(radiologyModel.getPaid_amount()));
            holder.Balanceamount.setText(currency + String.format("%.2f", balance));
        }
        holder.caseid.setText(radiologyModel.getCase_reference_id());
        holder.reference.setText(radiologyModel.getDoctor_name());
        holder.statusTV.setText(currency+radiologyModel.getNet_amount());
        holder.description.setText(radiologyModel.getNote());

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(context.getApplicationContext(), RadiologyPayment.class);
                intent.putExtra("Id",radiologyModel.getId());
                context.startActivity(intent);
            }
        });

        ArrayList<CustomFieldModel> customList = radiologyModel.getCustomfield();
        System.out.println("customList"+customList);
        CustomlistAdapter customlistAdapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(customlistAdapter);

            holder.viewpaymentBtn.setOnClickListener( new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    final Dialog dialog = new Dialog(context);
                    dialog.setContentView(R.layout.paymentdetails);
                    dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.FILL_PARENT);
                    dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;
                    final ImageView closeBtn = (ImageView) dialog.findViewById(R.id.dialog_crossIcon);
                    final RelativeLayout header = dialog.findViewById(R.id.addappoint_dialog_header);
                    final TextView headertext = dialog.findViewById(R.id.headertext);
                     total_payment = dialog.findViewById(R.id.total_payment);
                    final RecyclerView recyclerview = dialog.findViewById(R.id.recyclerview);
                    header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                    paymentadapter = new PatientPaymentDetailAdapter(context, id_list, datelist,
                            paidamountlist, notelist, payment_modelist, typelist,cheque_nolist,cheque_datelist,attachment_namelist);
                    RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    recyclerview.setLayoutManager(mLayoutManager);
                    recyclerview.setItemAnimator(new DefaultItemAnimator());
                    recyclerview.setAdapter(paymentadapter);
                    if (Utility.isConnectingToInternet(context.getApplicationContext())) {
                        params.put("patient_id", Utility.getSharedPreferences(context.getApplicationContext(), Constants.patient_id));
                        params.put("bill_id", radiologyModel.getId());
                        params.put("module_type", "radiology");
                        JSONObject obj = new JSONObject(params);
                        Log.e(" details params ", obj.toString());
                        getPaymentDataFromApi(obj.toString());
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

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_radiology_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientradiology_bottomSheet__header);
                TextView reference_doctor = view.findViewById(R.id.radiology_reference_doctor);
                TextView detailsbill = view.findViewById(R.id.patientradiology_bottomSheet_billno);
                TextView detailsdate = view.findViewById(R.id.patientradiology_bottomSheet_date);
                ImageView crossBtn = view.findViewById(R.id.patientradiology_bottomSheet__crossBtn);
                radiology_prescriptionno = view.findViewById(R.id.radiology_prescriptionno);
                 radiology_total = view.findViewById(R.id.radiology_total);
                 radiology_discount = view.findViewById(R.id.radiology_discount);
                 radiology_tax = view.findViewById(R.id.radiology_tax);
                 radiology_netamount = view.findViewById(R.id.radiology_netamount);
                TextView  radiology_totaldeposit = view.findViewById(R.id.radiology_totaldeposit);
                TextView  radiology_dueamount = view.findViewById(R.id.radiology_dueamount);

                RecyclerView pharmacy_recyclerview = view.findViewById(R.id.recyclerview);
                adapter = new PatientRadiologyReportAdapter(context, parameter_namelist,
                        samplecollectedlist, taxlist, amountlist,idlist,radiology_report,radiology_result);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                pharmacy_recyclerview.setLayoutManager(mLayoutManager);
                pharmacy_recyclerview.setItemAnimator(new DefaultItemAnimator());
                pharmacy_recyclerview.setAdapter(adapter);
                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("report_id", radiologyModel.getId());
                    JSONObject obj=new JSONObject(params);
                    Log.e(" details params ", obj.toString());
                    getDataFromApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.billdetails));

                detailsbill.setText(context.getString(R.string.billno)+" "+context.getString(R.string.radiologyprefix)+radiologyModel.getId());
                reference_doctor.setText(radiologyModel.getDoctor_name());
                detailsdate.setText(context.getString(R.string.date)+"  "+radiologyModel.getDate());
                radiology_totaldeposit.setText(currency +radiologyModel.getPaid_amount());
                if(radiologyModel.getPaid_amount().equals("")){
                    radiology_totaldeposit.setText(currency+"0.00");
                    balance=(Double.parseDouble(radiologyModel.getNet_amount())-Double.parseDouble("0.00"));
                    radiology_dueamount.setText(currency + String.format("%.2f", balance));
                }else{
                    radiology_totaldeposit.setText(currency+radiologyModel.getPaid_amount());
                    balance=(Double.parseDouble(radiologyModel.getNet_amount())-Double.parseDouble(radiologyModel.getPaid_amount()));
                    radiology_dueamount.setText(currency + String.format("%.2f", balance));
                }

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
    private void getPaymentDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getPaymentUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {

                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                            double sum=0;
                        JSONArray reportarray = obj.getJSONArray("detail");
                        datelist.clear();
                        id_list.clear();
                        paidamountlist.clear();
                        notelist.clear();
                        payment_modelist.clear();
                        typelist.clear();
                        cheque_nolist.clear();
                        cheque_datelist.clear();

                        if(reportarray.length() != 0) {
                            for(int i = 0; i < reportarray.length(); i++) {
                                datelist.add(Utility.parseDate("yyyy-MM-dd hh:mm",
                                        defaultDatetimeFormat,reportarray.getJSONObject(i).getString("payment_date")));
                                id_list.add(reportarray.getJSONObject(i).getString("id"));
                                paidamountlist.add(reportarray.getJSONObject(i).getString("amount"));
                                notelist.add(reportarray.getJSONObject(i).getString("note"));
                                cheque_nolist.add(reportarray.getJSONObject(i).getString("cheque_no"));
                                attachment_namelist.add(reportarray.getJSONObject(i).getString("attachment"));
                                cheque_datelist.add(Utility.parseDate("yyyy-MM-dd",
                                        defaultDateFormat,reportarray.getJSONObject(i).getString("cheque_date")));
                                payment_modelist.add(reportarray.getJSONObject(i).getString("payment_mode"));
                                typelist.add(reportarray.getJSONObject(i).getString("type"));
                                sum += Double.parseDouble(reportarray.getJSONObject(i).getString("amount"));
                            }
                            total_payment.setText(currency+String.format("%.2f", sum));
                            paymentadapter.notifyDataSetChanged();
                        } else {
                            //Toast.makeText(getApplicationContext(), getApplicationContext().getString(R.string.noData), Toast.LENGTH_SHORT).show();
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
    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getRadiologyParameterDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);

                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);

                        JSONObject dataArray = obj.getJSONObject("radiology_parameter");


                        radiology_total.setText(currency+dataArray.getString("total"));
                        radiology_tax.setText(currency+dataArray.getString("tax"));
                        radiology_netamount.setText(currency+dataArray.getString("net_amount"));
                        radiology_discount.setText(currency+dataArray.getString("discount"));
                        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        JSONArray reportarray = dataArray.getJSONArray("radiology_report");
                        parameter_namelist.clear();
                        amountlist.clear();
                        taxlist.clear();
                        samplecollectedlist.clear();
                        radiology_report.clear();
                        idlist.clear();
                        radiology_result.clear();

                        if(reportarray.length() != 0) {
                            for(int i = 0; i < reportarray.length(); i++) {

                                idlist.add(reportarray.getJSONObject(i).getString("id"));
                                radiology_report.add(reportarray.getJSONObject(i).getString("radiology_report"));
                                radiology_result.add(reportarray.getJSONObject(i).getString("radiology_result"));
                                parameter_namelist.add(reportarray.getJSONObject(i).getString("test_name")+"("+reportarray.getJSONObject(i).getString("short_name")+")");
                                samplecollectedlist.add(Utility.parseDate("yyyy-MM-dd",
                                        defaultDateFormat,reportarray.getJSONObject(i).getString("reporting_date")));
                                if(reportarray.getJSONObject(i).getString("approved_by_staff_name").equals("")){
                                    taxlist.add("");
                                }else{
                                    taxlist.add("Approved By: "+reportarray.getJSONObject(i).getString("approved_by_staff_name")+" "+reportarray.getJSONObject(i).getString("approved_by_staff_surname")+
                                            " ("+reportarray.getJSONObject(i).getString("approved_by_staff_employee_id")+") "+Utility.parseDate("yyyy-MM-dd",
                                            defaultDateFormat,reportarray.getJSONObject(i).getString("parameter_update")));
                                }
                                amountlist.add(currency+reportarray.getJSONObject(i).getString("apply_charge"));
                            }

                            if(dataArray.getString("ipd_prescription_basic_id").equals("")){
                                radiology_prescriptionno.setText("");
                            }else{
                              //  radiology_prescriptionno.setText(context.getString(R.string.opdprefix)+dataArray.getString("ipd_prescription_basic_id"));
                              if(dataArray.getString("type").equals("OPD")){
                                  radiology_prescriptionno.setText(context.getString(R.string.opdprefix)+dataArray.getString("ipd_prescription_basic_id"));
                              }else if(dataArray.getString("type").equals("IPD")){
                                  radiology_prescriptionno.setText(context.getString(R.string.ipdprefix)+dataArray.getString("ipd_prescription_basic_id"));
                              }else{
                                  radiology_prescriptionno.setText("");
                              }
                            }
                            adapter.notifyDataSetChanged();
                        } else {
                            //Toast.makeText(getApplicationContext(), getApplicationContext().getString(R.string.noData), Toast.LENGTH_SHORT).show();
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
   @Override
    public int getItemCount() { return radiology_detail_list.size(); }
}
