package com.qdocs.smarthospital24.adapters;

import android.annotation.SuppressLint;
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
import com.qdocs.smarthospital24.model.PharmacyModel;
import com.qdocs.smarthospital24.patient.PharmacyPayment;
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

public class PatientPharmacyAdapter extends RecyclerView.Adapter<PatientPharmacyAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<PharmacyModel> pharmacy_detail_list;
    ArrayList<String> medicinenamelist = new ArrayList<String>();
    ArrayList<String> batchnolist = new ArrayList<String>();
    ArrayList<String> expirydate = new ArrayList<String>();
    ArrayList<String> Quantitylist = new ArrayList<String>();
    ArrayList<String> salepricelist = new ArrayList<String>();
    ArrayList<String> unitlist = new ArrayList<String>();
    ArrayList<String> detail_idlist = new ArrayList<String>();
    ArrayList<String> note_list = new ArrayList<String>();
    Fragment fragment;
    TextView total_payment;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    PatientPharmacyBillAdapter adapter;
    long downloadID;
    PatientPaymentDetailAdapter paymentadapter;
    String defaultDatetimeFormat,defaultDateFormat;
    ArrayList<String> typelist = new ArrayList<String>();
    ArrayList<String> payment_modelist = new ArrayList<String>();
    ArrayList<String> cheque_nolist = new ArrayList<String>();
    ArrayList<String> attachment_namelist = new ArrayList<String>();
    ArrayList<String> cheque_datelist = new ArrayList<String>();
    ArrayList<String> notelists = new ArrayList<String>();
    ArrayList<String> paidamountlist = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> id_list = new ArrayList<String>();
    String currency;
    public PatientPharmacyAdapter(FragmentActivity context, ArrayList<PharmacyModel> pharmacy_detail_list, Fragment fragment) {
        this.context = context;
        this.pharmacy_detail_list = pharmacy_detail_list;
        this.fragment = fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView billno, radiology_date, statusTV,caseid,paidamount,balanceamount,note;
        TextView reference, paymentBtn;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
        ImageView viewpaymentBtn;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            billno = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_billno);
            radiology_date = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_reportingdate);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_caseid);
            paidamount = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_paidamount);
            note = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_note);
            balanceamount = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_balance);
            reference = (TextView) view.findViewById(R.id.adapter_patient_pharmacy_reference_doctor);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_pharmacy_detailsBtn);
            headLay = view.findViewById(R.id.adapter_patient_pharmacy_headLayout);
            statusTV = view.findViewById(R.id.adapter_patient_pharmacy_charge);
            viewpaymentBtn = view.findViewById(R.id.adapter_patient_pharmacy_viewpaymentBtn);
            paymentBtn = view.findViewById(R.id.adapter_patient_pharmacy_paymentBtn);
            recyclerview = view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_pharmacy, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
            final PharmacyModel pharmacyModel=pharmacy_detail_list.get(position);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
         currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);

        holder.billno.setText(context.getString(R.string.pharmacyprefix)+pharmacyModel.getId());
        holder.radiology_date.setText(pharmacyModel.getDate());
        holder.reference.setText(pharmacyModel.getDoctor_name());
        holder.caseid.setText(pharmacyModel.getCase_reference_id());
        holder.paidamount.setText(currency+ String.format("%.2f", Double.parseDouble(pharmacyModel.getPaid_amount())));
        holder.statusTV.setText(currency+pharmacyModel.getNet_amount());
        holder.note.setText(pharmacyModel.getNote());
        Double balance=(Double.parseDouble(pharmacyModel.getNet_amount())-Double.parseDouble(pharmacyModel.getPaid_amount()));
        holder.balanceamount.setText(currency + String.format("%.2f", balance));


        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(context.getApplicationContext(), PharmacyPayment.class);
                intent.putExtra("Id",pharmacyModel.getId());
                context.startActivity(intent);
            }
        });

        ArrayList<CustomFieldModel> customList = pharmacyModel.getCustomfield();
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
                        paidamountlist, notelists, payment_modelist, typelist,cheque_nolist,cheque_datelist,attachment_namelist);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                recyclerview.setLayoutManager(mLayoutManager);
                recyclerview.setItemAnimator(new DefaultItemAnimator());
                recyclerview.setAdapter(paymentadapter);

                if (Utility.isConnectingToInternet(context.getApplicationContext())) {
                    params.put("patient_id", Utility.getSharedPreferences(context.getApplicationContext(), Constants.patient_id));
                    params.put("bill_id", pharmacyModel.getId());
                    params.put("module_type", "pharmacy");
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
            @SuppressLint("SetTextI18n")
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_pharmacy_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientPharmacy_bottomSheet__header);
                TextView detailsbill = view.findViewById(R.id.patientPharmacy_bottomSheet_billno);
                TextView detailsdate = view.findViewById(R.id.patientPharmacy_bottomSheet_date);
                ImageView crossBtn = view.findViewById(R.id.patientPharmacy_bottomSheet__crossBtn);
                TextView pathology_reference_doctor = view.findViewById(R.id.pharmacy_reference_doctor);
                TextView pharmacy_total = view.findViewById(R.id.pharmacy_total);
                TextView pharmacy_discount = view.findViewById(R.id.pharmacy_discount);
                TextView pharmacy_tax = view.findViewById(R.id.pharmacy_tax);
                TextView pharmacy_netamount = view.findViewById(R.id.pharmacy_netamount);
                TextView pharmacy_paidamount = view.findViewById(R.id.pharmacy_paidamount);
                TextView pharmacy_refundamount = view.findViewById(R.id.pharmacy_refundamount);
                TextView pharmacy_dueamount = view.findViewById(R.id.pharmacy_dueamount);
                TextView pharmacy_note = view.findViewById(R.id.pharmacy_note);
                LinearLayout notelayout = view.findViewById(R.id.notelayout);

                RecyclerView pharmacy_recyclerview = view.findViewById(R.id.pharmacy_recyclerview);
                adapter = new PatientPharmacyBillAdapter(context, medicinenamelist,
                        unitlist, Quantitylist, batchnolist,expirydate,salepricelist,detail_idlist);
                RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                pharmacy_recyclerview.setLayoutManager(mLayoutManager);
                pharmacy_recyclerview.setItemAnimator(new DefaultItemAnimator());
                pharmacy_recyclerview.setAdapter(adapter);
                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("patientId", Utility.getSharedPreferences(context.getApplicationContext(), Constants.patient_id));
                    params.put("billid",pharmacyModel.getId());
                    JSONObject obj=new JSONObject(params);
                    Log.e(" details params ", obj.toString());
                    getDataFromApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.billdetails));

                detailsbill.setText(context.getString(R.string.pharmacyprefix)+pharmacyModel.getId());
                detailsdate.setText(context.getString(R.string.date)+"  "+pharmacyModel.getDate());
                pathology_reference_doctor.setText(pharmacyModel.getDoctor_name());
                pharmacy_total.setText(currency+" "+pharmacyModel.getTotal());
                pharmacy_tax.setText(currency+" "+pharmacyModel.getTax());
                pharmacy_discount.setText(currency+" "+pharmacyModel.getDiscount()+" ("+pharmacyModel.getDiscount_percentage()+"%)");
                pharmacy_netamount.setText(currency+" "+pharmacyModel.getNet_amount());
                pharmacy_paidamount.setText(currency+" "+String.format("%.2f", Double.parseDouble(pharmacyModel.getPaid_amount())));
                pharmacy_refundamount.setText(currency+" "+pharmacyModel.getRefund_amount());
                Double balance=(Double.parseDouble(pharmacyModel.getNet_amount())-Double.parseDouble(pharmacyModel.getPaid_amount()));
                pharmacy_dueamount.setText(currency + String.format("%.2f", balance));

                if(pharmacyModel.getCollectedby().equals("")){
                    notelayout.setVisibility(View.GONE);
                }else{
                    notelayout.setVisibility(View.VISIBLE);
                    pharmacy_note.setText(pharmacyModel.getCollectedby());
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

    private void getDataFromApi (String bodyParams) {
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getPharmacyMedicineDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("detail");
                        medicinenamelist.clear();
                        batchnolist.clear();
                        expirydate.clear();
                        Quantitylist.clear();
                        salepricelist.clear();
                        unitlist.clear();
                        detail_idlist.clear();
                        defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                medicinenamelist.add(dataArray.getJSONObject(i).getString("medicine_name"));
                                batchnolist.add(dataArray.getJSONObject(i).getString("batch_no"));
                                expirydate.add(dataArray.getJSONObject(i).getString("expiry"));
                                Quantitylist.add(dataArray.getJSONObject(i).getString("quantity"));
                                Double amount=Double.parseDouble(dataArray.getJSONObject(i).getString("sale_price"))*Double.parseDouble(dataArray.getJSONObject(i).getString("quantity"));
                                salepricelist.add(String.format("%.2f", amount));
                                unitlist.add(dataArray.getJSONObject(i).getString("unit_name"));
                                detail_idlist.add(dataArray.getJSONObject(i).getString("id"));
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
                        double refsum=0;
                        double value=0;
                        JSONArray reportarray = obj.getJSONArray("detail");
                        datelist.clear();
                        id_list.clear();
                        paidamountlist.clear();
                        notelists.clear();
                        payment_modelist.clear();
                        typelist.clear();
                        cheque_nolist.clear();
                        cheque_datelist.clear();
                        attachment_namelist.clear();
                        defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
                        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        if(reportarray.length() != 0) {
                            for(int i = 0; i < reportarray.length(); i++) {
                                datelist.add(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,reportarray.getJSONObject(i).getString("payment_date")));
                                id_list.add(reportarray.getJSONObject(i).getString("id"));
                                paidamountlist.add(reportarray.getJSONObject(i).getString("amount"));
                                notelists.add(reportarray.getJSONObject(i).getString("note"));
                                attachment_namelist.add(reportarray.getJSONObject(i).getString("attachment"));
                                cheque_nolist.add(reportarray.getJSONObject(i).getString("cheque_no"));
                                cheque_datelist.add(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,reportarray.getJSONObject(i).getString("cheque_date")));
                                payment_modelist.add(reportarray.getJSONObject(i).getString("payment_mode"));
                                typelist.add(reportarray.getJSONObject(i).getString("type"));
                                System.out.println("type=="+reportarray.getJSONObject(i).getString("type"));
                                if(reportarray.getJSONObject(i).getString("type").equals("refund")){
                                    refsum += Double.parseDouble(reportarray.getJSONObject(i).getString("amount"));
                                }else if(reportarray.getJSONObject(i).getString("type").equals("payment")){
                                    sum += Double.parseDouble(reportarray.getJSONObject(i).getString("amount"));
                                }

                                System.out.println("refsum=="+refsum);
                                System.out.println("sum=="+sum);
                                value=sum-refsum;

                              //  sum += Double.parseDouble(reportarray.getJSONObject(i).getString("amount"));
                            }


                            total_payment.setText(currency+String.format("%.2f", value));
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
        return pharmacy_detail_list.size();
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
