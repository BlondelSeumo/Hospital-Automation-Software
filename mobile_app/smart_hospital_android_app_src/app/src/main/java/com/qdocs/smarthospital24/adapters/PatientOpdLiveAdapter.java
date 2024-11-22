package com.qdocs.smarthospital24.adapters;

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
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.patient.PatientOpdLiveConsult;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientOpdLiveAdapter extends RecyclerView.Adapter<PatientOpdLiveAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> titlelist;
    private ArrayList<String> create_by_namelist;
    private ArrayList<String> consultantList;
    private ArrayList<String> dateList;
    private ArrayList<String> patient_namelist;
    private ArrayList<String> idlist;
    private ArrayList<String> statuslist;
    private ArrayList<String> join_urllist;
    long downloadID;

    public PatientOpdLiveAdapter(FragmentActivity fragmentActivity, ArrayList<String> consultantList, ArrayList<String> titlelist,
                                 ArrayList<String> create_by_namelist, ArrayList<String> dateList, ArrayList<String> statuslist,
                                 ArrayList<String> idlist, ArrayList<String> join_urllist) {

        this.context = fragmentActivity;
        this.titlelist = titlelist;
        this.create_by_namelist = create_by_namelist;
        this.consultantList = consultantList;
        this.dateList = dateList;
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
            holder.status.setText(context.getApplicationContext().getString(R.string.awaited));
            holder.status.setBackgroundResource(R.drawable.orange_border);
            holder.JoinBtn.setVisibility(View.VISIBLE);
            holder.JoinBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View viewClick) {
                    Intent myactivity = new Intent(context.getApplicationContext(), PatientOpdLiveConsult.class);
                    myactivity.putExtra("join_url", join_urllist.get(position));
                    System.out.println("join_url=="+join_urllist.get(position));
                    context.startActivity(myactivity);
                }
            });
        }else if(statuslist.get(position).equals("1")){
            holder.status.setText(context.getApplicationContext().getString(R.string.cancelled));
            holder.status.setBackgroundResource(R.drawable.red_border);
            holder.JoinBtn.setVisibility(View.GONE);
        }else if(statuslist.get(position).equals("2")){
            holder.status.setText(context.getApplicationContext().getString(R.string.finished));
            holder.status.setBackgroundResource(R.drawable.green_border);
            holder.JoinBtn.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() {
        return idlist.size();
    }
}
