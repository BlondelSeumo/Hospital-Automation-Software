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
import com.qdocs.smarthospital24.model.BloodBankModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.patient.BloodBankPayment;
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

public class PatientBloodComponentAdapter extends RecyclerView.Adapter<PatientBloodComponentAdapter.MyViewHolder> {

    private FragmentActivity context;
    String defaultDatetimeFormat,defaultDateFormat;
    private ArrayList<BloodBankModel> bloodbank_detail_list;
    ArrayList<String> payment_modelist = new ArrayList<String>();
    ArrayList<String> typelist = new ArrayList<String>();
    ArrayList<String> cheque_datelist = new ArrayList<String>();
    ArrayList<String> notelist = new ArrayList<String>();
    ArrayList<String> cheque_nolist = new ArrayList<String>();
    ArrayList<String> paidamountlist = new ArrayList<String>();
    ArrayList<String> attachment_namelist = new ArrayList<String>();
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> id_list = new ArrayList<String>();
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    PatientPaymentDetailAdapter paymentadapter;
    Fragment fragment;
    long downloadID;
    Double balance;
    TextView total_payment;

    public PatientBloodComponentAdapter(FragmentActivity context, ArrayList<BloodBankModel> bloodbank_detail_list, Fragment fragment) {
        this.context = context;
        this.bloodbank_detail_list = bloodbank_detail_list;
        this.fragment=fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView issue_date,adapter_donor_name,caseid,bloodgroup,adapter_bagno,adapter_billno,bloodbank_transaction,
                component,adapter_charge,paymentBtn,paidamount,balanceamount,bloodbank_tpa,bloodbank_technician,bloodbank_tpaValidity,bloodbank_tpaId;
        public CardView viewContainer;
        RelativeLayout headLay;
        LinearLayout componentlayout;
        ImageView viewpaymentBtn;
        RecyclerView recyclerview;
        LinearLayout detailsBtn;
        public MyViewHolder(View view) {
            super(view);
            issue_date = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_issuedate);
            bloodbank_transaction = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_transaction);
            bloodbank_tpa = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_tpa);
            bloodbank_technician = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_technician);
            bloodbank_tpaValidity = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_tpaValidity);
            bloodbank_tpaId = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_tpaId);
            bloodgroup = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_bloodgroup);
            adapter_donor_name = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_donor);
            adapter_bagno = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_bagno);
            viewpaymentBtn = (ImageView) view.findViewById(R.id.adapter_patient_bloodbank_viewpaymentBtn);
            paymentBtn = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_paymentBtn);
            adapter_billno = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_billno);
            paidamount = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_paidamount);
            balanceamount = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_balance);
            caseid = (TextView) view.findViewById(R.id.adapter_patient_caseid);
            adapter_charge = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_charge);
            component = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_component);
            viewContainer = (CardView) view.findViewById(R.id.adapter_patient_bloodbank_viewContainer);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_bloodbank_subjectLayout);
            detailsBtn = (LinearLayout)view.findViewById(R.id.adapter_patient_bloodbank_detailsBtn);
            recyclerview = (RecyclerView)view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_component_list, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final BloodBankModel bloodBankModel = bloodbank_detail_list.get(position);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        holder.issue_date.setText(bloodBankModel.getDate_of_issue());
        holder.bloodgroup.setText(bloodBankModel.getBlood_group());
        holder.bloodbank_tpa.setText(bloodBankModel.getOrganisation_name());
        holder.adapter_donor_name.setText(bloodBankModel.getDonor_name());
        holder.adapter_bagno.setText(bloodBankModel.getBag_no());
        holder.caseid.setText(bloodBankModel.getCase_reference_id());
        holder.paidamount.setText(currency+bloodBankModel.getPaid_amount());
        holder.adapter_billno.setText(context.getString(R.string.bloodbankprefix)+bloodBankModel.getId());
        holder.component.setText(bloodBankModel.getComponent_name());
        holder.bloodbank_technician.setText(bloodBankModel.getTechnician());
        holder.bloodbank_tpaValidity.setText(bloodBankModel.getInsurance_validity());
        holder.bloodbank_tpaId.setText(bloodBankModel.getInsurance_id());
        holder.adapter_charge.setText(currency + bloodBankModel.getNet_amount());
        balance=(Double.parseDouble(bloodBankModel.getNet_amount())-Double.parseDouble(bloodBankModel.getPaid_amount()));
        holder.balanceamount.setText(currency + String.format("%.2f", balance));

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(context.getApplicationContext(), BloodBankPayment.class);
                intent.putExtra("Id", bloodBankModel.getId());
                context.startActivity(intent);
            }
        });

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_bloodbank_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientbloodbank_bottomSheet__header);
                TextView reference_doctor = view.findViewById(R.id.radiology_reference_doctor);
                TextView detailsbill = view.findViewById(R.id.patientbloodbank_bottomSheet_billno);
              //  TextView transid = view.findViewById(R.id.patientbloodbank_bottomSheet_transid);
                ImageView crossBtn = view.findViewById(R.id.patientbloodbank_bottomSheet__crossBtn);
                TextView bloodbank_amount = view.findViewById(R.id.bloodbank_amount);
                TextView bloodbank_netamount = view.findViewById(R.id.bloodbank_netamount);
                TextView bloodbank_discount = view.findViewById(R.id.bloodbank_discount);
                TextView bloodbank_tax = view.findViewById(R.id.bloodbank_tax);
                TextView bloodbank_paidamount = view.findViewById(R.id.bloodbank_paidamount);
                TextView bloodbank_balanceamount = view.findViewById(R.id.bloodbank_balanceamount);

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.componentissuedetail));

                detailsbill.setText(context.getString(R.string.billno)+"  "+context.getString(R.string.bloodbankprefix)+bloodBankModel.getId());
                //transid.setText(context.getString(R.string.transactionprefix)+bloodBankModel.getTransaction_id());
                bloodbank_amount.setText(currency+bloodBankModel.getAmount());
                bloodbank_netamount.setText(currency+bloodBankModel.getNet_amount());
                bloodbank_discount.setText("("+bloodBankModel.getDiscount_percentage()+"%)"+currency+bloodBankModel.getDiscount_amount());
                bloodbank_tax.setText("("+bloodBankModel.getTax_percentage()+"%)"+currency+bloodBankModel.getTax_amount());
                bloodbank_paidamount.setText(currency +bloodBankModel.getPaid_amount());
                balance=(Double.parseDouble(bloodBankModel.getNet_amount())-Double.parseDouble(bloodBankModel.getPaid_amount()));
                bloodbank_balanceamount.setText(currency + String.format("%.2f", balance));

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

        ArrayList<CustomFieldModel> customList = bloodBankModel.getCustomfield();
        System.out.println("customList" + customList);
        CustomlistAdapter customlistAdapter = new CustomlistAdapter(context, customList, fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL, false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(customlistAdapter);

        holder.viewpaymentBtn.setOnClickListener(new View.OnClickListener() {
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
                    params.put("bill_id", bloodBankModel.getId());
                    params.put("module_type", "blood_bank");
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
                                  datelist.add(Utility.parseDate("yyyy-MM-dd hh:mm",
                                          defaultDatetimeFormat,reportarray.getJSONObject(i).getString("payment_date")));
                                  id_list.add(reportarray.getJSONObject(i).getString("id"));
                                  paidamountlist.add(reportarray.getJSONObject(i).getString("amount"));
                                  notelist.add(reportarray.getJSONObject(i).getString("note"));
                                  attachment_namelist.add(reportarray.getJSONObject(i).getString("attachment_name"));
                                  cheque_nolist.add(reportarray.getJSONObject(i).getString("cheque_no"));
                                  cheque_datelist.add(Utility.parseDate("yyyy-MM-dd",
                                          defaultDateFormat,reportarray.getJSONObject(i).getString("cheque_date")));
                                  payment_modelist.add(reportarray.getJSONObject(i).getString("payment_mode"));
                                  typelist.add(reportarray.getJSONObject(i).getString("type"));
                                  sum += Double.parseDouble(reportarray.getJSONObject(i).getString("amount"));
                              }
                              total_payment.setText(currency+String.format("%.2f", sum));
                              paymentadapter.notifyDataSetChanged();
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
        return bloodbank_detail_list.size();
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
