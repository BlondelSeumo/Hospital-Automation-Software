package com.qdocs.smarthospital24.adapters;

import static android.content.Context.RECEIVER_EXPORTED;

import android.app.DownloadManager;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.Uri;
import android.os.Build;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.core.app.NotificationCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.DownloadContentModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;


public class DownloadContentlistAdapter extends RecyclerView.Adapter<DownloadContentlistAdapter.MyViewHolder> {
    long downloadID;
    Context  applicationContext;
    ArrayList<DownloadContentModel> customlist;
    Fragment fragment;

    public DownloadContentlistAdapter(Context applicationContext, ArrayList<DownloadContentModel> customlist, Fragment fragment) {

        this.fragment = fragment;
        this.customlist = customlist;
        this.applicationContext = applicationContext;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView field_name;
        ImageView field_value;
        LinearLayout sym_layout;



        public MyViewHolder(View view) {
            super(view);
            field_name = (TextView) view.findViewById(R.id.fieldname);
            field_value = (ImageView) view.findViewById(R.id.fieldvalue);
            sym_layout = (LinearLayout) view.findViewById(R.id.sym_layout);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.downloadcontentlist, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
    DownloadContentModel downloadContentModel=customlist.get(position);
        holder.field_name.setText(downloadContentModel.getReal_name());
        //holder.field_value.setText(downloadContentModel.getFile_type());
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            applicationContext.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
        }else {
            applicationContext.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }
        if(downloadContentModel.getFile_type().equals("video")){
            holder.field_value.setVisibility(View.GONE);

            holder.sym_layout.setOnClickListener(new View.OnClickListener() {

                public void onClick(View v) {

                    applicationContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(downloadContentModel.getVid_url())));
                    Log.i("Video", "Video Playing....");

                }
            });

        }else{
            holder.field_value.setVisibility(View.VISIBLE);
            holder.sym_layout.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    String urlStr = Utility.getSharedPreferences(applicationContext.getApplicationContext(), Constants.imagesUrl);
                    urlStr += downloadContentModel.getThumb_path()+ downloadContentModel.getThumb_name();
                    downloadID =Utility.beginDownload(applicationContext, downloadContentModel.getImg_name(), urlStr);
                    System.out.println("Image Ipd"+urlStr);
                    Intent intent=new Intent(applicationContext.getApplicationContext(), OpenPdf.class);
                    intent.putExtra("imageUrl",urlStr);
                    applicationContext.startActivity(intent);
                }
            });
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
                context.unregisterReceiver(onDownloadComplete);
            }
        }
    };


    @Override
    public int getItemCount() {
        return customlist.size();
    }


}