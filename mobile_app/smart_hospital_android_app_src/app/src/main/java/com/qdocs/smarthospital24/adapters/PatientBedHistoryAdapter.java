package com.qdocs.smarthospital24.adapters;

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
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientBedHistoryAdapter extends RecyclerView.Adapter<PatientBedHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> bedgrouplist;
    private ArrayList<String> bedlist;
    private ArrayList<String> fromdateList;
    private ArrayList<String> todateList;
    private ArrayList<String> activelist;

    public PatientBedHistoryAdapter(FragmentActivity fragmentActivity, ArrayList<String> bedgrouplist, ArrayList<String> bedlist,
                                    ArrayList<String> fromdateList, ArrayList<String> todateList, ArrayList<String> activelist) {

        this.context = fragmentActivity;
        this.bedgrouplist = bedgrouplist;
        this.bedlist = bedlist;
        this.fromdateList = fromdateList;
        this.todateList = todateList;
        this.activelist = activelist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView bedgroup, bed, fromdate , todate, active;
        TextView  opeartion_date,name,charge;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            bedgroup = (TextView) view.findViewById(R.id.adapter_patient_bedgroup);
            bed = (TextView) view.findViewById(R.id.adapter_patient_bed);
            fromdate = (TextView) view.findViewById(R.id.adapter_patient_fromdate);
            todate = (TextView) view.findViewById(R.id.adapter_patient_todate);
            active = (TextView) view.findViewById(R.id.adapter_patient_active);
            headLay = (RelativeLayout) view.findViewById(R.id.adapter_patient_ot_headLayout);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_bed_history, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));

        holder.bedgroup.setText(bedgrouplist.get(position));
        holder.bed.setText(bedlist.get(position));
        holder.fromdate.setText(fromdateList.get(position));
        holder.todate.setText(todateList.get(position));
       // holder.charge.setText(context.getString(R.string.appliedcharge)+"  " +currency+chargelist.get(position));
        holder.active.setText(String.valueOf(activelist.get(position).charAt(0)).toUpperCase() + activelist.get(position).substring(1, activelist.get(position).length()));
        

    }
    @Override
    public int getItemCount() {
        return bedgrouplist.size();
    }
}
