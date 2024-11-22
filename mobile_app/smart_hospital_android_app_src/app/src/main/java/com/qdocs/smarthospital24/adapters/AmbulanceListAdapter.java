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
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.app.NotificationCompat;
import androidx.fragment.app.Fragment;
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
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.AmbulanceModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.patient.AmbulancePayment;
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

public class AmbulanceListAdapter extends RecyclerView.Adapter<AmbulanceListAdapter.MyViewHolder> {
    FragmentActivity context;
    long downloadID;
    PatientPaymentDetailAdapter paymentadapter;
    String defaultDatetimeFormat,defaultDateFormat;
    ArrayList<String> typelist = new ArrayList<String>();
    ArrayList<String> payment_modelist = new ArrayList<String>();
    ArrayList<String> notelist = new ArrayList<String>();
    ArrayList<String> cheque_nolist = new ArrayList<String>();
    ArrayList<String> cheque_datelist = new ArrayList<String>();
    ArrayList<String> attachment_namelist = new ArrayList<String>();
    ArrayList<String> paidamountlist = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> id_list = new ArrayList<String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    Fragment fragment;
    TextView total_payment;
    private ArrayList<AmbulanceModel> ambulance_detail_list;

    public AmbulanceListAdapter(FragmentActivity activity, ArrayList<AmbulanceModel> ambulance_detail_list,Fragment fragment) {
        this.context = activity;
        this.ambulance_detail_list = ambulance_detail_list;
        this.context = context;
    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView date,adapter_driver_name,note,discount,adapter_chargename,vehiclemodel,ChargeCategory,balanceamount,vehicleno,paymentBtn,adapter_contact,adapter_billno,caseid,adapter_charge,amount,tax,paidamount;
        public CardView viewContainer;
        ImageView viewpaymentBtn;
        RelativeLayout headLayout;
        RecyclerView recyclerview;
    public MyViewHolder(View view) {
        super(view);

        vehiclemodel = (TextView) view.findViewById(R.id.vehiclemodel);
        amount = (TextView) view.findViewById(R.id.vehicleno);
        tax = (TextView) view.findViewById(R.id.tax);
        discount = (TextView) view.findViewById(R.id.discount);
        paidamount = (TextView) view.findViewById(R.id.paidamount);
        amount = (TextView) view.findViewById(R.id.amount);
        vehicleno = (TextView) view.findViewById(R.id.vehicleno);
        adapter_driver_name = (TextView) view.findViewById(R.id.adapter_driver_name);
        adapter_contact = (TextView) view.findViewById(R.id.adapter_contact);
        adapter_billno = (TextView) view.findViewById(R.id.adapter_patient_ambulance_billno);
        adapter_charge = (TextView) view.findViewById(R.id.adapter_patient_ambulance_charge);
        adapter_chargename = (TextView) view.findViewById(R.id.adapter_chargename);
        note = (TextView) view.findViewById(R.id.adapter_note);
        ChargeCategory = (TextView) view.findViewById(R.id.ChargeCategory);
        balanceamount = (TextView) view.findViewById(R.id.balanceamount);
        viewContainer = (CardView) view.findViewById(R.id.adapter_report_card_exam_list);
        viewpaymentBtn = (ImageView) view.findViewById(R.id.adapter_patient_ambulance_viewpaymentBtn);
        paymentBtn = (TextView) view.findViewById(R.id.adapter_patient_ambulance_paymentBtn);
        caseid = (TextView) view.findViewById(R.id.caseid);
        headLayout = (RelativeLayout) view.findViewById(R.id.adapter_patient_ambulance_headLayout);
        recyclerview = (RecyclerView) view.findViewById(R.id.recyclerview);
    }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.
                adapter_patient_ambulance, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final AmbulanceModel ambulanceModel=ambulance_detail_list.get(position);
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        holder.headLayout.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.vehicleno.setText(ambulanceModel.getVehicle_no());
        holder.vehiclemodel.setText(ambulanceModel.getVehicle_model());
        holder.adapter_driver_name.setText(ambulanceModel.getDriver());
        holder.adapter_contact.setText(ambulanceModel.getDriver_contact());
        holder.adapter_billno.setText("AMCB"+ambulanceModel.getId());
        holder.adapter_charge.setText(currency+ambulanceModel.getNet_amount());
        holder.tax.setText(currency+ambulanceModel.getTax_percentage());
        holder.discount.setText(currency+ambulanceModel.getDiscount()+"("+ambulanceModel.getDiscount_percentage()+"%)");
        holder.paidamount.setText(currency+ambulanceModel.getPaid_amount());
        holder.amount.setText(currency+ambulanceModel.getAmount());
        holder.note.setText(ambulanceModel.getNote());
        holder.adapter_chargename.setText(ambulanceModel.getCharge_name());
        holder.ChargeCategory.setText(ambulanceModel.getCharge_category_name());
        holder.caseid.setText(ambulanceModel.getCase_reference_id());
        if(ambulanceModel.getPaid_amount().equals("")){
            holder.balanceamount.setText(currency+ambulanceModel.getNet_amount());
        }else{
            Double balance=(Double.parseDouble(ambulanceModel.getNet_amount())-Double.parseDouble(ambulanceModel.getPaid_amount()));
            holder.balanceamount.setText(currency + String.format("%.2f", balance));
        }
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        ArrayList<CustomFieldModel> customList = ambulanceModel.getCustomfield();
        System.out.println("customList"+customList);
        CustomlistAdapter customlistAdapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(customlistAdapter);

        holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(context.getApplicationContext(), AmbulancePayment.class);
                intent.putExtra("Id",ambulanceModel.getId());
                context.startActivity(intent);
            }
        });


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
                    params.put("bill_id", ambulanceModel.getId());
                    params.put("module_type", "ambulance");
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
                        attachment_namelist.clear();
                        String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
                        defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
                        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        if(reportarray.length() != 0) {
                            for(int i = 0; i < reportarray.length(); i++) {
                                datelist.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,reportarray.getJSONObject(i).getString("payment_date")));
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
    @Override
    public int getItemCount() {
        return ambulance_detail_list.size();
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

}

