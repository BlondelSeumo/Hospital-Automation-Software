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
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.core.app.NotificationCompat;
import androidx.fragment.app.FragmentActivity;

import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientOPDDiagnosisAdapter extends BaseAdapter {

    private FragmentActivity context;
    private ArrayList<String> report_type_list;
    private ArrayList<String> report_date;
    private ArrayList<String> descriptionlist;
    private ArrayList<String> documentlist;
    private long downloadID;

    public PatientOPDDiagnosisAdapter(FragmentActivity activity, ArrayList<String> report_type_list, ArrayList<String> report_date,
                                      ArrayList<String> descriptionlist, ArrayList<String> documentlist) {
        this.context = activity;
        this.report_type_list = report_type_list;
        this.report_date = report_date;
        this.descriptionlist = descriptionlist;
        this.documentlist = documentlist;
    }
    @Override
    public int getCount() { return report_type_list.size(); }

    @Override
    public Object getItem(int i) { return null; }

    @Override
    public long getItemId(int i) { return 0; }

    @Override
    public View getView(final int position, View view, ViewGroup viewGroup) {
        View rowView = view;
        ViewHolder viewHolder = null;

        if (rowView == null) {

            LayoutInflater inflater = LayoutInflater.from(context);
            rowView = inflater.inflate(R.layout.adapter_opd_diagnosis, viewGroup, false);
            viewHolder = new ViewHolder();

            viewHolder.viewContainer = (CardView) rowView.findViewById(R.id.adapter_fragment__diagnosis);
            viewHolder.report_type = (TextView) rowView.findViewById(R.id.adapter_fragment_report_type);
            viewHolder.report_date = (TextView) rowView.findViewById(R.id.adapter_fragment_dateTV);
            viewHolder.description = (TextView) rowView.findViewById(R.id.adapter_fragment_description);
            viewHolder.downloadBtn = (ImageView) rowView.findViewById(R.id.adapter_fragment_download_downloadBtn);

            viewHolder.nameLay = (RelativeLayout) rowView.findViewById(R.id.adapter_fragment_nameLay);
            viewHolder.viewContainer.setTag(position);
            viewHolder.report_type.setTag(position);
            viewHolder.report_date.setTag(position);
            viewHolder.description.setTag(position);

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                context.registerReceiver(onDownloadComplete,new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), RECEIVER_EXPORTED);
            }else {
                context.registerReceiver(onDownloadComplete, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
            }

        } else {
            viewHolder = (ViewHolder) rowView.getTag();
        }
        viewHolder.report_type.setText(report_type_list.get(position));
        viewHolder.report_date.setText(report_date.get(position));
        viewHolder.description.setText(descriptionlist.get(position));
        if(documentlist.get(position).equals("")){
            viewHolder.downloadBtn.setVisibility(View.GONE);
        }else{
            viewHolder.downloadBtn.setVisibility(View.VISIBLE);
        }
        viewHolder.downloadBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                urlStr += documentlist.get(position);
                downloadID = Utility.beginDownload(context, documentlist.get(position), urlStr);
                System.out.println("Image Ipd"+urlStr);
                Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                intent.putExtra("imageUrl",urlStr);
                context.startActivity(intent);

            }
        });

        viewHolder.nameLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        rowView.setTag(viewHolder);
        return rowView;
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
                                .setContentText(context.getApplicationContext().getString(R.string.download));
                NotificationManager notificationManager = (NotificationManager) context.getSystemService(Context.NOTIFICATION_SERVICE);
                notificationManager.notify(455, mBuilder.build());
            }
        }
    };

    private static class ViewHolder {
        private TextView report_type, report_date, description;
        private ImageView downloadBtn;
        private RelativeLayout nameLay;
        private CardView viewContainer;
    }
}