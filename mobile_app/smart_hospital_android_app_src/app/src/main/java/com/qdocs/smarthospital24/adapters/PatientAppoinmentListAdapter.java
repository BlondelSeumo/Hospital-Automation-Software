package com.qdocs.smarthospital24.adapters;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
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
import androidx.appcompat.app.AlertDialog;
import androidx.cardview.widget.CardView;
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
import com.qdocs.smarthospital24.model.AppointmentModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.patient.AppointmentPayment;
import com.qdocs.smarthospital24.patient.PatientAppoinmentList;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;
import static android.widget.Toast.makeText;

public class PatientAppoinmentListAdapter extends RecyclerView.Adapter<PatientAppoinmentListAdapter.MyViewHolder> {

    Context context;
    Fragment fragment;
    ArrayList<AppointmentModel> appointment_detail;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    LinearLayout chequedate_layout,chequeno_layout,attachment_layout;
    TextView appoinment_doctor,appoinment_discount,appoinment_shift,appoinment_netamount,appoinment_amount,appoinment_slot,appoinment_serialno,appoinment_paymentmode,appoinment_ChequeNo,appoinment_ChequeDate,appoinment_Source,
            appoinment_TransactionID,appoinment_note,appointmentno,appointmentdate,appoinment_liveconsultant;
    LinearLayout downloadBtn;
    long downloadID;
    public PatientAppoinmentListAdapter(Context context, ArrayList<AppointmentModel> appointment_detail, Fragment fragment) {

        this.appointment_detail = appointment_detail;
        this.context = context;
        this.fragment = fragment;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
    public TextView appoinmentdate,doctor,statusTV,Message,appoinment_no,specialist,paymentBtn,priority;
    ImageView delete_button;
    public CardView viewContainer;
    RelativeLayout relative;
    RecyclerView recyclerview;
    LinearLayout detailsBtn;

    public MyViewHolder(View view) {
        super(view);
        appoinmentdate = (TextView) view.findViewById(R.id.adapter_appoinment_date);
        statusTV = (TextView) view.findViewById(R.id.adapter_appoinment_statusTV);
        doctor = (TextView) view.findViewById(R.id.adapter_appoinment_doctor);
        Message = (TextView) view.findViewById(R.id.adapter_appoinment_message);
        appoinment_no = (TextView) view.findViewById(R.id.adapter_appoinment_no);
        specialist = (TextView) view.findViewById(R.id.adapter_appoinment_specialist);
        priority = (TextView) view.findViewById(R.id.adapter_appoinment_priority);
        delete_button = (ImageView) view.findViewById(R.id.delete_button);
        viewContainer = (CardView) view.findViewById(R.id.card_layout);
        relative = (RelativeLayout) view.findViewById(R.id.relative);
        recyclerview = (RecyclerView) view.findViewById(R.id.recyclerview);
        paymentBtn = (TextView) view.findViewById(R.id.adapter_patient_appointment_paymentBtn);
       detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_appointment_detailsBtn);

    }
}
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_appointments_list, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
       final AppointmentModel appointmentModel=appointment_detail.get(position);

        holder.appoinmentdate.setText(appointmentModel.getDate());
        holder.doctor.setText(appointmentModel.getName()+" "+appointmentModel.getSurname()+"("+appointmentModel.getEmployee_id()+")");
        holder.Message.setText(appointmentModel.getMessage());
        holder.priority.setText(appointmentModel.getPriority());
        holder.specialist.setText(appointmentModel.getSpecialist_name());

        ArrayList<CustomFieldModel> customList = appointmentModel.getCustomfield();
        System.out.println("customList"+customList);
        CustomlistAdapter customlistAdapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(customlistAdapter);

        holder.delete_button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                AlertDialog.Builder builder = new AlertDialog.Builder(context);
                builder.setCancelable(false);
                builder.setTitle(context.getApplicationContext().getString(R.string.deleteAppointment));
                builder.setMessage(R.string.deleteAppointmentmsg);
                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        if(Utility.isConnectingToInternet(context.getApplicationContext())){
                            params.put("appointmentId", appointmentModel.getId());
                            JSONObject obj=new JSONObject(params);
                            Log.e("params", obj.toString());
                            getDataFromApi(obj.toString());
                        }else{
                            makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }

                        ((Activity)context).finish();
                        Intent intent = new Intent(context, PatientAppoinmentList.class);
                        context.startActivity(intent);
                    }
                });
                builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        dialogInterface.dismiss();
                    }
                });
                builder.show();
            }
        });
        if(appointmentModel.getAppointment_status().equals("approved")) {
            holder.appoinment_no.setText(context.getString(R.string.appointmentprefix)+appointmentModel.getId());
            holder.statusTV.setBackgroundResource(R.drawable.green_border);
            holder.delete_button.setVisibility(View.GONE);
            holder.statusTV.setText(context.getApplicationContext().getString(R.string.approve));
            holder.paymentBtn.setVisibility(View.GONE);

        } else if (appointmentModel.getAppointment_status().equals("pending")){
            holder.appoinment_no.setText(context.getApplicationContext().getString(R.string.pending));
            holder.statusTV.setBackgroundResource(R.drawable.orange_border);
            holder.delete_button.setVisibility(View.VISIBLE);
            holder.statusTV.setText(context.getApplicationContext().getString(R.string.pending));
            if(appointmentModel.getSource().equals("Online")){
                holder.paymentBtn.setVisibility(View.VISIBLE);
                holder.paymentBtn.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Intent intent=new Intent(context.getApplicationContext(), AppointmentPayment.class);
                        intent.putExtra("Id",appointmentModel.getId());
                        context.startActivity(intent);
                    }
                });
            }else{
                holder.paymentBtn.setVisibility(View.GONE);
            }

        } else if (appointmentModel.getAppointment_status().equals("cancel")){
            holder.appoinment_no.setText(context.getApplicationContext().getString(R.string.cancel));
            holder.statusTV.setBackgroundResource(R.drawable.red_border);
            holder.delete_button.setVisibility(View.GONE);
            holder.statusTV.setText(context.getApplicationContext().getString(R.string.cancel));
            holder.paymentBtn.setVisibility(View.GONE);
        }

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @SuppressLint("SetTextI18n")
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_appointment_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientappoinment_bottomSheet__header);
                appointmentno = view.findViewById(R.id.patientappoinment_bottomSheet_appoinmentno);
                appointmentdate = view.findViewById(R.id.patientappoinment_bottomSheet_date);
                ImageView crossBtn = view.findViewById(R.id.patientappoinment_bottomSheet__crossBtn);
                appoinment_doctor = view.findViewById(R.id.appoinment_reference_doctor);
                appoinment_shift = view.findViewById(R.id.appoinment_shift);
                appoinment_discount = view.findViewById(R.id.appoinment_discount);
                appoinment_amount = view.findViewById(R.id.appoinment_amount);
                appoinment_netamount = view.findViewById(R.id.appoinment_netamount);
                appoinment_slot = view.findViewById(R.id.appoinment_slot);
                appoinment_liveconsultant = view.findViewById(R.id.appoinment_live_consultant);
                appoinment_serialno = view.findViewById(R.id.appoinment_serialno);
                appoinment_paymentmode = view.findViewById(R.id.appoinment_paymentmode);
                appoinment_ChequeNo = view.findViewById(R.id.appoinment_ChequeNo);
                appoinment_ChequeDate = view.findViewById(R.id.appoinment_ChequeDate);
                appoinment_Source = view.findViewById(R.id.appoinment_Source);
                appoinment_TransactionID = view.findViewById(R.id.appoinment_TransactionID);
                appoinment_note = view.findViewById(R.id.appoinment_note);
                downloadBtn = view.findViewById(R.id.downloadBtn);
                chequeno_layout = view.findViewById(R.id.chequeno_layout);
                chequedate_layout = view.findViewById(R.id.chequedate_layout);
                attachment_layout = view.findViewById(R.id.attachment_layout);

                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("appointment_id",appointmentModel.getId());
                    JSONObject obj=new JSONObject(params);
                    Log.e(" details params ", obj.toString());
                    getDetailFromApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
                appoinment_serialno.setText(appointmentModel.getAppointment_serial_no());
                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.appointmentdetails));
                if(appointmentModel.getLive_consult().equals("yes")){
                    appoinment_liveconsultant.setText(R.string.yes);
                }else{
                    appoinment_liveconsultant.setText(R.string.no);
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

        holder.relative.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
    }
    @Override
    public int getItemCount() {
        return appointment_detail.size();
    }

    private void getDataFromApi (String bodyParams) {
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.deleteAppointmentUrl;


        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            public void onResponse(String result) {
                if (result != null) {
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);

                        Toast.makeText(context, "Successfully Deleted !!!", Toast.LENGTH_SHORT).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(context);
        requestQueue.add(stringRequest);
    }
    private void getDetailFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getAppointmentDetailsUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                  String defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
                   String defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                    final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        final JSONObject dataArray = obj.getJSONObject("result");



                        if(dataArray.getString("amount").equals("")){
                            appoinment_amount.setText(currency+"0");
                        }else{
                            if(dataArray.getString("appointment_status").equals("pending")){
                                appoinment_amount.setText(currency+"0.00");
                                appoinment_netamount.setText(currency+"0.00");
                            }else{
                                if(dataArray.getString("discount_percentage").equals("0.00")){
                                    appoinment_amount.setText(currency+dataArray.getString("paid_amount"));
                                    appoinment_netamount.setText(currency+dataArray.getString("paid_amount"));
                                    appoinment_discount.setText(currency+dataArray.getString("discount_percentage")+" ("+dataArray.getString("discount_percentage")+"%)");
                                }else{
                                    appoinment_amount.setText(currency+dataArray.getString("paid_amount"));
                                    appoinment_netamount.setText(currency+dataArray.getString("standard_amount"));
                                    Float dis_amount=Float.valueOf(String.valueOf((Double.parseDouble(dataArray.getString("discount_percentage"))/100)*Double.parseDouble(dataArray.getString("standard_amount"))));
                                    appoinment_discount.setText(currency + String.format("%.2f", dis_amount)+" ("+dataArray.getString("discount_percentage")+"%)");
                                }

                            }

                        }


                        if(dataArray.getString("payment_mode").equals("Cheque")){
                            chequedate_layout.setVisibility(View.VISIBLE);
                            chequeno_layout.setVisibility(View.VISIBLE);
                            attachment_layout.setVisibility(View.VISIBLE);
                            appoinment_ChequeDate.setText(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dataArray.getString("cheque_date")));
                            appoinment_ChequeNo.setText(dataArray.getString("cheque_no"));
                            downloadBtn.setOnClickListener(new View.OnClickListener() {
                                 @Override
                                 public void onClick(View view) {
                                     try {
                                         String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                                         urlStr += dataArray.getString("attachment");
                                         downloadID = Utility.beginDownload(context, dataArray.getString("attachment"), urlStr);
                                         System.out.println("Image URL "+urlStr);
                                         Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                                         intent.putExtra("imageUrl",urlStr);
                                         context.startActivity(intent);

                                     } catch (JSONException e) {
                                         e.printStackTrace();
                                     }
                                 }
                             });

                        }else if(dataArray.getString("payment_mode").equals("UPI")){
                            appoinment_paymentmode.setText(dataArray.getString("payment_mode"));
                             chequedate_layout.setVisibility(View.GONE);
                             chequeno_layout.setVisibility(View.GONE);
                             attachment_layout.setVisibility(View.GONE);
                        }else if(dataArray.getString("payment_mode").equals("")){
                            appoinment_paymentmode.setText("");
                             chequedate_layout.setVisibility(View.GONE);
                             chequeno_layout.setVisibility(View.GONE);
                             attachment_layout.setVisibility(View.GONE);
                        }else{
                             chequedate_layout.setVisibility(View.GONE);
                             chequeno_layout.setVisibility(View.GONE);
                             attachment_layout.setVisibility(View.GONE);
                            String str = snakeToCamel(dataArray.getString("payment_mode"));
                            StringBuffer sb = new StringBuffer();
                            for (int i = 0; i < str.length(); i++) {
                                if(Character.isUpperCase(str.charAt(i))) {
                                    sb.append(" ");
                                    sb.append(str.charAt(i));
                                } else {
                                    sb.append(str.charAt(i));
                                }
                            }
                            String moderesult = sb.toString();
                            System.out.println(moderesult);
                            appoinment_paymentmode.setText(moderesult);
                        }

                        appoinment_slot.setText(dataArray.getString("start_time")+" - "+dataArray.getString("end_time"));

                        appoinment_shift.setText(dataArray.getString("global_shift_name"));
                        appoinment_note.setText(dataArray.getString("payment_note"));
                        if(dataArray.getString("transaction_id").equals("")){
                            appoinment_TransactionID.setText("");
                        }else{
                             appoinment_TransactionID.setText(context.getString(R.string.transactionprefix)+dataArray.getString("transaction_id"));
                        }
                        appointmentdate.setText(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDatetimeFormat,dataArray.getString("date")));
                        if(dataArray.getString("appointment_status").equals("approved")){
                            appointmentno.setText(dataArray.getString("appointment_no"));
                        }else{
                            appointmentno.setText("");
                        }
                        appoinment_Source.setText(dataArray.getString("source"));
                        appoinment_doctor.setText(dataArray.getString("name")+" "+dataArray.getString("surname")+" ("+dataArray.getString("employee_id")+")");
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

    public static String snakeToCamel(String str) {
        // Capitalize first letter of string
        str = str.substring(0, 1).toUpperCase()
                + str.substring(1);

        // Convert to StringBuilder
        StringBuilder builder
                = new StringBuilder(str);

        // Traverse the string character by
        // character and remove underscore
        // and capitalize next letter
        for (int i = 0; i < builder.length(); i++) {

            // Check char is underscore
            if (builder.charAt(i) == '_') {

                builder.deleteCharAt(i);
                builder.replace(
                        i, i + 1,
                        String.valueOf(
                                Character.toUpperCase(
                                        builder.charAt(i))));
            }
        }
        // Return in String type
        return builder.toString();
    }


}