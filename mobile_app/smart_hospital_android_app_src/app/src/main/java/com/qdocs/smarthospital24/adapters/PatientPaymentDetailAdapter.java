package com.qdocs.smarthospital24.adapters;

import android.app.DownloadManager;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.core.app.NotificationCompat;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientPaymentDetailAdapter extends RecyclerView.Adapter<PatientPaymentDetailAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<String> datelist;
    private ArrayList<String> paidamountlist;
    private ArrayList<String> notelist;
    private ArrayList<String> payment_modelist;
    private ArrayList<String> typelist;
    private ArrayList<String> id_list;
    private ArrayList<String> cheque_datelist;
    private ArrayList<String> cheque_nolist;
    private ArrayList<String> attachment_namelist;
    long downloadID;

    public PatientPaymentDetailAdapter(Context context, ArrayList<String> id_list,ArrayList<String> datelist,
                                       ArrayList<String> paidamountlist, ArrayList<String> notelist, ArrayList<String> payment_modelist, ArrayList<String> typelist,
                                       ArrayList<String> cheque_nolist, ArrayList<String> cheque_datelist, ArrayList<String> attachment_namelist) {

        this.context = context;
        this.datelist = datelist;
        this.id_list = id_list;
        this.paidamountlist = paidamountlist;
        this.notelist = notelist;
        this.cheque_nolist = cheque_nolist;
        this.payment_modelist = payment_modelist;
        this.attachment_namelist = attachment_namelist;
        this.typelist = typelist;
        this.cheque_datelist = cheque_datelist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView headerTV;
        TextView date,note,payment_mode,payment_type,paidamount,chequeno,chequedate;
        LinearLayout downloadBtn,noteLayout;

        public MyViewHolder(View view) {
             super(view);
            date = (TextView) view.findViewById(R.id.date);
            note = (TextView) view.findViewById(R.id.note);
            payment_mode = (TextView) view.findViewById(R.id.payment_mode);
            payment_type = (TextView) view.findViewById(R.id.payment_type);
            paidamount = (TextView) view.findViewById(R.id.paidamount);
            chequeno = (TextView) view.findViewById(R.id.chequeno);
            chequedate = (TextView) view.findViewById(R.id.chequedate);
            noteLayout = (LinearLayout) view.findViewById(R.id.noteLayout);
            downloadBtn = (LinearLayout) view.findViewById(R.id.adapter_downloadBtn);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_payment_detail, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.date.setText(datelist.get(position));
        if(notelist.get(position).equals("")){
            holder.noteLayout.setVisibility(View.GONE);
        }else{
            holder.noteLayout.setVisibility(View.VISIBLE);
            holder.note.setText(notelist.get(position));
        }

        if(payment_modelist.get(position).equals("Cheque")){
            holder.payment_mode.setText(payment_modelist.get(position));
            holder.chequedate.setVisibility(View.VISIBLE);
            holder.chequeno.setVisibility(View.VISIBLE);
            holder.downloadBtn.setVisibility(View.VISIBLE);
            holder.chequedate.setText("Cheque Date: "+cheque_datelist.get(position));
            holder.chequeno.setText("Cheque No: "+cheque_nolist.get(position));

            holder.downloadBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                    urlStr += "uploads/payment_document/"+attachment_namelist.get(position);
                    downloadID = Utility.beginDownload(context, attachment_namelist.get(position), urlStr);
                    System.out.println("Image URL "+urlStr);
                    Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                    intent.putExtra("imageUrl",urlStr);
                    context.startActivity(intent);

                }
            });
        }else if(payment_modelist.get(position).equals("UPI")){
            holder.payment_mode.setText(payment_modelist.get(position));
            holder.downloadBtn.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.chequeno.setVisibility(View.GONE);
        }else if(payment_modelist.get(position).equals("Online")){
            holder.payment_mode.setText(payment_modelist.get(position));
            holder.downloadBtn.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.chequeno.setVisibility(View.GONE);
        }else if(payment_modelist.get(position).equals("")){
            holder.payment_mode.setText("");
            holder.downloadBtn.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.chequeno.setVisibility(View.GONE);
        }else{
            holder.downloadBtn.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.chequeno.setVisibility(View.GONE);
            String str = snakeToCamel(payment_modelist.get(position));
            StringBuffer sb = new StringBuffer();
            for (int i = 0; i < str.length(); i++) {
                if(Character.isUpperCase(str.charAt(i))) {
                    sb.append(" ");
                    sb.append(str.charAt(i));
                } else {
                    sb.append(str.charAt(i));
                }
            }
            String result = sb.toString();
            System.out.println(result);
            holder.payment_mode.setText(result);
        }

        holder.payment_type.setText(String.valueOf(typelist.get(position).charAt(0)).toUpperCase() + typelist.get(position).substring(1, typelist.get(position).length()));
        holder.paidamount.setText(currency+paidamountlist.get(position));

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
    private BroadcastReceiver onDownloadComplete = new BroadcastReceiver() {
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

    @Override
     public int getItemCount() {
        return id_list.size();
    }
}