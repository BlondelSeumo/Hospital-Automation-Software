package com.qdocs.smarthospital24.adapters;

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

import androidx.core.app.NotificationCompat;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.List;
import java.util.Map;

import static android.content.Context.RECEIVER_EXPORTED;
import static android.widget.Toast.makeText;

public class PatientIpdTimelineAdapter extends RecyclerView.Adapter<PatientIpdTimelineAdapter.MyViewHolder> {

    private FragmentActivity context;
    private List<String> titlelist;
    private List<String> timeline_dateList;
    private List<String> descriptionList;
    private List<String> idlist;
    private List<String> statuslist;
    private List<String> documentlist;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    long downloadID;

    public PatientIpdTimelineAdapter(FragmentActivity activity, ArrayList<String> titlelist,
                                     ArrayList<String> timeline_dateList, ArrayList<String> descriptionList, ArrayList<String> idlist, ArrayList<String> statuslist, ArrayList<String> documentlist) {

        this.context = activity;
        this.titlelist = titlelist;
        this.timeline_dateList = timeline_dateList;
        this.descriptionList = descriptionList;
        this.idlist = idlist;
        this.statuslist = statuslist;
        this.documentlist = documentlist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        //TODO delete un-necessasry code
        public TextView dateTV, titleTV, descTV;
        public ImageView downloadBtn;
        View lineView;
        LinearLayout mainlayout,layout;
        RelativeLayout clockBtn;

        public MyViewHolder(View view) {
            super(view);
            dateTV = view.findViewById(R.id.adapter_patientTimeline_dateTV);
            titleTV = view.findViewById(R.id.adapter_patientTimeline_titleTV);
            descTV = view.findViewById(R.id.adapter_patientTimeline_subtitleTV);
            lineView = view.findViewById(R.id.adapter_Timeline_line);
            mainlayout = view.findViewById(R.id.mainlayout);
            layout = view.findViewById(R.id.layout);
            downloadBtn = view.findViewById(R.id.adapter_patientTimeline_downloadBtn);
            clockBtn = view.findViewById(R.id.adapter_patientTimeline_clockBtn);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_timeline, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        holder.dateTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
        String defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
            holder.dateTV.setText(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,timeline_dateList.get(position)));
            holder.titleTV.setText(titlelist.get(position));
            holder.descTV.setText(descriptionList.get(position));
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }

        if(documentlist.get(position).equals("")){
            holder.downloadBtn.setVisibility(View.GONE);
        }else{
            holder.downloadBtn.setVisibility(View.VISIBLE);
        }

        holder.downloadBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                urlStr += "uploads/patient_timeline/"+documentlist.get(position);
                downloadID = Utility.beginDownload(context, documentlist.get(position), urlStr);
                System.out.println("Image Ipd"+urlStr);
                Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                intent.putExtra("imageUrl",urlStr);
                context.startActivity(intent);
            }
        });

        if(position == idlist.size()-1) {
            holder.clockBtn.setVisibility(View.VISIBLE);
        }else{
            holder.clockBtn.setVisibility(View.GONE);
        }
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

            }
        }
    };

    @Override
    public int getItemCount() {
        return idlist.size();
    }

}