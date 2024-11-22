package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.OpenPdf;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientPathologyReportAdapter extends RecyclerView.Adapter<PatientPathologyReportAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<String> parameter_namelist;
    private ArrayList<String> reference_rangelist;
    private ArrayList<String> unit_name;
    private ArrayList<String> pathology_report_valuelist;
    private ArrayList<String> idlist;
    private ArrayList<String> pathology_report;
    private ArrayList<String> pathology_result;
    long downloadID;

    public PatientPathologyReportAdapter(Context applicationContext, ArrayList<String> parameter_namelist, ArrayList<String> reference_rangelist,
                                         ArrayList<String> unit_name, ArrayList<String> pathology_report_valuelist, ArrayList<String> idlist, ArrayList<String> pathology_report, ArrayList<String> pathology_result
                                        ) {

        this.context = applicationContext;
        this.parameter_namelist = parameter_namelist;
        this.reference_rangelist = reference_rangelist;
        this.unit_name = unit_name;
        this.pathology_report = pathology_report;
        this.pathology_result = pathology_result;
        this.pathology_report_valuelist = pathology_report_valuelist;
        this.idlist = idlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView headerTV;
        TextView testparameter,expecteddate,uploaddate,amount,result;
        LinearLayout downloadBtn,result_layout;

        public MyViewHolder(View view) {
             super(view);
            testparameter = (TextView) view.findViewById(R.id.testparameter);
            expecteddate = (TextView) view.findViewById(R.id.expecteddate);
            uploaddate = (TextView) view.findViewById(R.id.uploaddate);
            amount = (TextView) view.findViewById(R.id.amount);
            result = (TextView) view.findViewById(R.id.result);
            result_layout = (LinearLayout) view.findViewById(R.id.result_layout);
            downloadBtn = (LinearLayout) view.findViewById(R.id.adapter_downloadBtn);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_pathology_report, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.testparameter.setText(parameter_namelist.get(position));
        holder.expecteddate.setText(reference_rangelist.get(position));
        if(pathology_result.get(position).equals("")){
            holder.result_layout.setVisibility(View.GONE);
        }else{
            holder.result_layout.setVisibility(View.VISIBLE);
            holder.result.setText(pathology_result.get(position));
        }

        holder.uploaddate.setText(pathology_report_valuelist.get(position));
        holder.amount.setText(currency+unit_name.get(position));
        if(pathology_report.get(position).equals("")){
            holder.downloadBtn.setVisibility(View.GONE);
        }else{
            holder.downloadBtn.setVisibility(View.VISIBLE);
            holder.downloadBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    String urlStr = Utility.getSharedPreferences(context.getApplicationContext(), Constants.imagesUrl);
                    System.out.println("Image URL "+urlStr);
                    urlStr += pathology_report.get(position);
                    downloadID = Utility.beginDownload(context, pathology_report.get(position), urlStr);
                    System.out.println("Image URL "+urlStr);
                    Intent intent=new Intent(context.getApplicationContext(), OpenPdf.class);
                    intent.putExtra("imageUrl",urlStr);
                    context.startActivity(intent);
                }
            });
        }

    }

    @Override
     public int getItemCount() {
        return idlist.size();
    }
}