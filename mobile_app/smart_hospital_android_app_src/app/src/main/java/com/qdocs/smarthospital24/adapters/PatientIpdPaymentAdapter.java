package com.qdocs.smarthospital24.adapters;

import static android.content.Context.RECEIVER_EXPORTED;

import android.app.DownloadManager;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Color;
import android.os.Build;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.core.app.NotificationCompat;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientIpdPaymentAdapter extends RecyclerView.Adapter<PatientIpdPaymentAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> payment_modeList;
    private ArrayList<String> dateList;
    private ArrayList<String> pnoteList;
    private ArrayList<String> paid_amountList;
    private ArrayList<String> cheque_noList;
    private ArrayList<String> cheque_dateList;
    private ArrayList<String> transactionIDList;
    private ArrayList<String> attachmentList;
    long downloadID;

    public PatientIpdPaymentAdapter(FragmentActivity fragmentActivity, ArrayList<String> payment_modeList, ArrayList<String> dateList,
                                    ArrayList<String> pnoteList, ArrayList<String> paid_amountList, ArrayList<String> cheque_noList, ArrayList<String> cheque_dateList, ArrayList<String> transactionIDList, ArrayList<String> attachmentList) {

        this.context = fragmentActivity;
        this.payment_modeList = payment_modeList;
        this.dateList = dateList;
        this.pnoteList = pnoteList;
        this.paid_amountList = paid_amountList;
        this.cheque_noList = cheque_noList;
        this.cheque_dateList = cheque_dateList;
        this.transactionIDList = transactionIDList;
        this.attachmentList = attachmentList;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView note,date,paymentmode,paidamount,transactionid,chequedate,chequeno;
        ImageView downloadBtn;
        RelativeLayout detailsBtn,headLay;
        public CardView containerView;
        LinearLayout attachment_layout;

        public MyViewHolder(View view) {
            super(view);
            note = (TextView) view.findViewById(R.id.note);
            date = (TextView) view.findViewById(R.id.adapter_date);
            paymentmode = (TextView) view.findViewById(R.id.paymentmode);
            paidamount = (TextView) view.findViewById(R.id.adapter_amount);
            chequeno = (TextView) view.findViewById(R.id.chequeno);
            chequedate = (TextView) view.findViewById(R.id.chequedate);
            transactionid = (TextView) view.findViewById(R.id.adapter_transactionid);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_ipd_headLayout);
            attachment_layout = (LinearLayout)view.findViewById(R.id.attachment_layout);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_payment, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        holder.date.setText(dateList.get(position));
        holder.note.setText(pnoteList.get(position));
        holder.transactionid.setText(transactionIDList.get(position));
        if(attachmentList.get(position).equals("")){
            holder.attachment_layout.setVisibility(View.GONE);
        }else{
            holder.attachment_layout.setVisibility(View.VISIBLE);
        }
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }
        holder.attachment_layout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                urlStr += "uploads/payment_document/"+attachmentList.get(position);
                downloadID =Utility.beginDownload(context, attachmentList.get(position), urlStr);
                System.out.println("Image Ipd"+urlStr);
                Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                intent.putExtra("imageUrl",urlStr);
                context.startActivity(intent);
            }
        });



        if(payment_modeList.get(position).equals("UPI")){
            holder.chequeno.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.paymentmode.setText(payment_modeList.get(position));
            holder.attachment_layout.setVisibility(View.GONE);
        }else if(payment_modeList.get(position).equals("Cheque")){
            holder.chequeno.setVisibility(View.VISIBLE);
            holder.chequedate.setVisibility(View.VISIBLE);
            if(attachmentList.get(position).equals("")){
                holder.attachment_layout.setVisibility(View.GONE);
            }else{
                holder.attachment_layout.setVisibility(View.VISIBLE);
            }
            holder.chequedate.setText("Cheque Date: "+cheque_dateList.get(position));
            holder.chequeno.setText("Cheque No: "+cheque_noList.get(position));
            String str = snakeToCamel(payment_modeList.get(position));
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
            holder.paymentmode.setText(result);
        }else{
            holder.chequeno.setVisibility(View.GONE);
            holder.chequedate.setVisibility(View.GONE);
            holder.attachment_layout.setVisibility(View.GONE);
            String str = snakeToCamel(payment_modeList.get(position));
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
            holder.paymentmode.setText(result);
        }
        holder.paidamount.setText("Paid Amount: "+currency+paid_amountList.get(position));

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

    @Override
    public int getItemCount() {
        return dateList.size();
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
