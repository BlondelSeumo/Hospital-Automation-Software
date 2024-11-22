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
import com.qdocs.smarthospital24.model.PathologyModel;
import com.qdocs.smarthospital24.patient.PathologyPayment;
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

public class PatientPathologyAdapter extends RecyclerView.Adapter<PatientPathologyAdapter.MyViewHolder> {

    private FragmentActivity context;
    ArrayList<String> parameter_namelist = new ArrayList<String>();
    ArrayList<String> reference_rangelist = new ArrayList<String>();
    ArrayList<String> unit_name = new ArrayList<String>();

    ArrayList<String> pathology_report_valuelist = new ArrayList<String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> pathology_report = new ArrayList<String>();
    ArrayList<String> pathology_result = new ArrayList<String>();
    PatientPathologyReportAdapter adapter;
    long downloadID;
    TextView pathology_total,pathology_prescriptionno,pathology_doctor,Pathology_totaltax,Pathology_totaldeposit,Pathology_discount,Pathology_balaceamount,Pathology_netamount;
    String defaultDatetimeFormat,defaultDateFormat;
    private ArrayList<PathologyModel> pathology_detail_list;
    ArrayList<String> payment_modelist = new ArrayList<String>();
    ArrayList<String> typelist = new ArrayList<String>();
    ArrayList<String> cheque_datelist = new ArrayList<String>();
    ArrayList<String> cheque_nolist = new ArrayList<String>();
    ArrayList<String> attachment_namelist = new ArrayList<String>();
    ArrayList<String> notelist = new ArrayList<String>();
    ArrayList<String> paidamountlist = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> id_list = new ArrayList<String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    PatientPaymentDetailAdapter paymentadapter;
    TextView total_payment;
    Fragment fragment;
    Double balance;
    String currency;
    public PatientPathologyAdapter(FragmentActivity context, ArrayList<PathologyModel> pathology_detail_list, Fragment fragment) {

        this.context = context;
        this.pathology_detail_list = pathology_detail_list;
        this.fragment=fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView billno, date, paidamount, statusTV;
        TextView paymentBtn, reference, caseid, description,BalanceAmount,tpa,tpaId,tpaValidity;
        ImageView viewpaymentBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            billno = (TextView) view.findViewById(R.id.adapter_patient_pathology_billno);
            date = (TextView) view.findViewById(R.id.adapter_patient_pathology_reportingdate);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_pathology_caseid);
            paidamount = (TextView) view.findViewById(R.id.adapter_patient_pathology_paidamount);
            reference = (TextView) view.findViewById(R.id.adapter_patient_pathology_reference_doctor);
            description = (TextView) view.findViewById(R.id.adapter_patient_pathology_description);
            BalanceAmount = (TextView) view.findViewById(R.id.adapter_patient_pathology_balance);
            tpa = (TextView) view.findViewById(R.id.adapter_tpa);
            tpaId = (TextView) view.findViewById(R.id.adapter_tpaId);
            tpaValidity = (TextView) view.findViewById(R.id.adapter_tpaValidity);
            viewpaymentBtn = (ImageView) view.findViewById(R.id.adapter_patient_pathology_viewpaymentBtn);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_pathology_detailsBtn);
            paymentBtn = view.findViewById(R.id.adapter_patient_pathology_paymentBtn);
            headLay = view.findViewById(R.id.adapter_patient_pathology_headLayout);
            statusTV = view.findViewById(R.id.adapter_patient_pathology_charge);
            recyclerview = view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_pathologylist, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
        final PathologyModel pathologyModel=pathology_detail_list.get(position);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
         currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);

        holder.billno.setText(context.getString(R.string.pathologyprefix)+pathologyModel.getId());
        holder.date.setText(pathologyModel.getDate());
        if(pathologyModel.getPaid_amount().equals("")){
            holder.paidamount.setText(currency+"0.00");
            balance=(Double.parseDouble(pathologyModel.getNet_amount())-Double.parseDouble("0.00"));
            holder.BalanceAmount.setText(currency + String.format("%.2f", balance));
        }else{
            holder.paidamount.setText(currency+pathologyModel.getPaid_amount());
            balance=(Double.parseDouble(pathologyModel.getNet_amount())-Double.parseDouble(pathologyModel.getPaid_amount()));
            holder.BalanceAmount.setText(currency + String.format("%.2f", balance));
        }
        holder.statusTV.setText(currency+pathologyModel.getNet_amount());
        holder.description.setText(pathologyModel.getNote());
        holder.caseid.setText(pathologyModel.getCase_reference_id());
        holder.tpa.setText(pathologyModel.getTpa());
        holder.tpaId.setText(pathologyModel.getTpa_id());
        holder.tpaValidity.setText(pathologyModel.getTpa_validity());
        if(pathologyModel.getTpa_validity().equals("")){
            holder.tpaValidity.setText("");
        }else{
            holder.tpaValidity.setText( Utility.parseDate("yyyy-MM-dd", defaultDateFormat,pathologyModel.getTpa_validity()));

        }


        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(context.getApplicationContext(), PathologyPayment.class);
                intent.putExtra("Id",pathologyModel.getId());
                context.startActivity(intent);
            }
        });

        if(pathologyModel.getDoctor_name().equals("")){
            holder.reference.setText("-");
        }else{
            holder.reference.setText(pathologyModel.getDoctor_name());
        }

        ArrayList<CustomFieldModel> customList = pathologyModel.getCustomfield();
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
                        params.put("bill_id", pathologyModel.getId());
                        params.put("module_type", "pathology");
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
                View view = inflater.inflate(R.layout.fragment_pathology_bottom_sheet, null);

                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientPathology_bottomSheet__header);
                TextView detailsbill = view.findViewById(R.id.patientPathology_bottomSheet_billno);
                TextView detailsdate = view.findViewById(R.id.patientPathology_bottomSheet_date);
                ImageView crossBtn = view.findViewById(R.id.patientPathology_bottomSheet__crossBtn);

                 pathology_total = view.findViewById(R.id.Pathology_total);
                 pathology_doctor = view.findViewById(R.id.Pathology_reference_doctor);
                 pathology_prescriptionno = view.findViewById(R.id.Pathology_prescriptionno);
                 Pathology_totaltax = view.findViewById(R.id.Pathology_totaltax);
                 Pathology_totaldeposit = view.findViewById(R.id.Pathology_totaldeposit);
                Pathology_netamount = view.findViewById(R.id.Pathology_netamount);
                Pathology_discount = view.findViewById(R.id.Pathology_discount);
                 Pathology_balaceamount = view.findViewById(R.id.Pathology_balaceamount);

                RecyclerView pharmacy_recyclerview = view.findViewById(R.id.recyclerview);
                adapter = new PatientPathologyReportAdapter (context, parameter_namelist,
                        reference_rangelist, unit_name, pathology_report_valuelist,idlist,pathology_report,pathology_result);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                pharmacy_recyclerview.setLayoutManager(mLayoutManager);
                pharmacy_recyclerview.setItemAnimator(new DefaultItemAnimator());
                pharmacy_recyclerview.setAdapter(adapter);
                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("reportid", pathologyModel.getId());
                    JSONObject obj=new JSONObject(params);
                    Log.e(" details params ", obj.toString());
                    getDataFromApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.billdetails));

                detailsbill.setText(context.getString(R.string.billno)+" "+context.getString(R.string.pathologyprefix)+pathologyModel.getId());
                detailsdate.setText(context.getString(R.string.date)+"  " +pathologyModel.getDate());
                Pathology_netamount.setText(currency+pathologyModel.getNet_amount());
                if(pathologyModel.getPaid_amount().equals("")){
                    Pathology_totaldeposit.setText(currency+"0.00");
                    balance=(Double.parseDouble(pathologyModel.getNet_amount())-Double.parseDouble("0.00"));
                    Pathology_balaceamount.setText(currency + String.format("%.2f", balance));
                }else{
                    Pathology_totaldeposit.setText(currency+pathologyModel.getPaid_amount());
                    balance=(Double.parseDouble(pathologyModel.getNet_amount())-Double.parseDouble(pathologyModel.getPaid_amount()));
                    Pathology_balaceamount.setText(currency + String.format("%.2f", balance));
                }



                if(pathologyModel.getDoctor_name().equals("")){
                    pathology_doctor.setText("-");
                }else{
                    pathology_doctor.setText(pathologyModel.getDoctor_name());
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
                        double sum=0;
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);

                        JSONArray reportarray = obj.getJSONArray("detail");
                        datelist.clear();
                        id_list.clear();
                        paidamountlist.clear();
                        notelist.clear();
                        payment_modelist.clear();
                        typelist.clear();
                        cheque_nolist.clear();
                        cheque_datelist.clear();
                        attachment_namelist.clear();

                        if(reportarray.length() != 0) {
                            for(int i = 0; i < reportarray.length(); i++) {
                                datelist.add(Utility.parseDate("yyyy-MM-dd hh:mm",
                                        defaultDatetimeFormat,reportarray.getJSONObject(i).getString("payment_date")));
                                id_list.add(reportarray.getJSONObject(i).getString("id"));
                                paidamountlist.add(reportarray.getJSONObject(i).getString("amount"));
                                notelist.add(reportarray.getJSONObject(i).getString("note"));
                                attachment_namelist.add(reportarray.getJSONObject(i).getString("attachment"));
                                cheque_nolist.add(reportarray.getJSONObject(i).getString("cheque_no"));
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
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getPathologyParameterDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject dataArray = obj.getJSONObject("pathology_parameter");
                        if(dataArray.getString("ipd_prescription_basic_id").equals("")){
                            pathology_prescriptionno.setText("");
                        }else{
                            pathology_prescriptionno.setText(context.getString(R.string.opdprefix)+dataArray.getString("ipd_prescription_basic_id"));
                        }

                        pathology_total.setText(currency+dataArray.getString("total"));
                        pathology_doctor.setText(dataArray.getString("doctor_name"));
                        Pathology_totaltax.setText(currency+dataArray.getString("tax"));
                        Pathology_discount.setText("("+dataArray.getString("discount_percentage")+"%) "+currency+dataArray.getString("discount"));
                        JSONArray reportArray = dataArray.getJSONArray("pathology_report");
                        parameter_namelist.clear();
                        reference_rangelist.clear();
                        unit_name.clear();
                        pathology_report_valuelist.clear();
                        idlist.clear();
                        pathology_result.clear();
                        pathology_report.clear();

                        if(reportArray.length() != 0) {
                            for(int i = 0; i < reportArray.length(); i++) {

                                idlist.add(reportArray.getJSONObject(i).getString("id"));
                                pathology_report.add(reportArray.getJSONObject(i).getString("pathology_report"));
                                pathology_result.add(reportArray.getJSONObject(i).getString("pathology_result"));
                                parameter_namelist.add(reportArray.getJSONObject(i).getString("test_name")+"("+reportArray.getJSONObject(i).getString("short_name")+")");
                                reference_rangelist.add(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,reportArray.getJSONObject(i).getString("reporting_date")));
                                unit_name.add(reportArray.getJSONObject(i).getString("apply_charge"));
                                if(reportArray.getJSONObject(i).getString("approved_by_staff_name").equals("")){
                                    pathology_report_valuelist.add("");
                                }else{
                                    pathology_report_valuelist.add("Approved By: "+reportArray.getJSONObject(i).getString("approved_by_staff_name")+" "+reportArray.getJSONObject(i).getString("approved_by_staff_surname")+
                                            " ("+reportArray.getJSONObject(i).getString("approved_by_staff_employee_id")+") "+Utility.parseDate("yyyy-MM-dd", defaultDateFormat,reportArray.getJSONObject(i).getString("parameter_update")));
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
    public int getItemCount() {
        return pathology_detail_list.size();
    }
}
