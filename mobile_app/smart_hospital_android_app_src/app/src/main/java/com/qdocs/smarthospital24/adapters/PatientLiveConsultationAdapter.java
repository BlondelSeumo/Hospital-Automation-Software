package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.patient.PatientOpdLiveConsult;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientLiveConsultationAdapter extends RecyclerView.Adapter<PatientLiveConsultationAdapter.MyViewHolder> {

    Context context;
    private ArrayList<String> titlelist;
    private ArrayList<String> create_by_namelist;
    private ArrayList<String> consultantList;
    private ArrayList<String> dateList;
    private ArrayList<String> patient_namelist;
    private ArrayList<String> idlist;
    private ArrayList<String> statuslist;
    private ArrayList<String> join_urllist;
    long downloadID;

    public PatientLiveConsultationAdapter(Context context, ArrayList<String> datelist,
                                          ArrayList<String> titlelist, ArrayList<String> consultantList, ArrayList<String> create_by_namelist,
                                          ArrayList<String> statuslist, ArrayList<String> join_urllist, ArrayList<String> patient_namelist, ArrayList<String> idlist) {

        this.context = context;
        this.titlelist = titlelist;
        this.create_by_namelist = create_by_namelist;
        this.consultantList = consultantList;
        this.dateList = datelist;
        this.patient_namelist = patient_namelist;
        this.statuslist = statuslist;
        this.idlist = idlist;
        this.join_urllist = join_urllist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView title, date, status , creadtedby, createdfor;
        ImageView downloadBtn;
        LinearLayout JoinBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            title = (TextView) view.findViewById(R.id.adapter_patient_opd_title);
            date = (TextView) view.findViewById(R.id.adapter_patient_opd_date);
            createdfor = (TextView) view.findViewById(R.id.adapter_patient_opd_createdfor);
            creadtedby = (TextView) view.findViewById(R.id.adapter_patient_opd_creadtedby);
            status = (TextView) view.findViewById(R.id.adapter_patient_opd_status);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_opd_headLayout);
            JoinBtn = (LinearLayout)view.findViewById(R.id.adapter_patient_opd_JoinBtn);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_opd_livelist, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {

        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.title.setText(titlelist.get(position));
        holder.date.setText(dateList.get(position));
        holder.creadtedby.setText(create_by_namelist.get(position));
        holder.createdfor.setText(consultantList.get(position));


        if(statuslist.get(position).equals("0")){
            holder.status.setText("Awaited");
            holder.status.setBackgroundResource(R.drawable.orange_border);
            holder.JoinBtn.setVisibility(View.VISIBLE);
            holder.JoinBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View viewClick) {
                    Intent myactivity = new Intent(context.getApplicationContext(), PatientOpdLiveConsult.class);
                    myactivity.putExtra("join_url", join_urllist.get(position));
                    System.out.println("join_url"+join_urllist.get(position));
                    myactivity.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    context.startActivity(myactivity);
                }
            });
        }else if(statuslist.get(position).equals("1")){
            holder.status.setText("Cancelled");
            holder.status.setBackgroundResource(R.drawable.red_border);
            holder.JoinBtn.setVisibility(View.GONE);
        }else if(statuslist.get(position).equals("2")){
            holder.status.setText("Finished");
            holder.status.setBackgroundResource(R.drawable.green_border);
            holder.JoinBtn.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() {
        return idlist.size();
    }
}
