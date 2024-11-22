package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class BloodBankListAdapter extends RecyclerView.Adapter<BloodBankListAdapter.MyViewHolder> {
    Context context;
    ArrayList<String> dateofissuelist;
    ArrayList<String> patient_namelist;
    ArrayList<String> bill_nolist;
    ArrayList<String> bag_nolist;
    ArrayList<String> donornamelist;
    ArrayList<String> bloodgrouplist;
    ArrayList<String> amountlist;

    public BloodBankListAdapter(Context applicationContext, ArrayList<String> dateofissuelist, ArrayList<String> patient_namelist,ArrayList<String> bill_nolist, ArrayList<String> donornamelist,ArrayList<String> bag_nolist, ArrayList<String> bloodgrouplist, ArrayList<String> amountlist) {
        this.dateofissuelist = dateofissuelist;
        this.donornamelist = donornamelist;
        this.bloodgrouplist = bloodgrouplist;
        this.patient_namelist = patient_namelist;
        this.bill_nolist = bill_nolist;
        this.bag_nolist = bag_nolist;
        this.amountlist = amountlist;
        this.context = applicationContext;
    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView issue_date,adapter_donor_name,bloodgroup,adapter_bagno,adapter_billno,adapter_charge,adapter_amount;
        public CardView viewContainer;
        RelativeLayout headLay;

    public MyViewHolder(View view) {
        super(view);
        issue_date = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_issuedate);
        bloodgroup = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_bloodgroup);
        adapter_donor_name = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_donor);
        adapter_bagno = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_bagno);
        adapter_billno = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_billno);
        adapter_charge = (TextView) view.findViewById(R.id.adapter_patient_bloodbank_charge);
        viewContainer = (CardView) view.findViewById(R.id.adapter_patient_bloodbank_viewContainer);
        headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_bloodbank_subjectLayout);

    }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_bloodbank_list, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        holder.issue_date.setText(context.getString(R.string.Issuedate)+"  "+dateofissuelist.get(position));
        holder.bloodgroup.setText(bloodgrouplist.get(position));
        holder.adapter_donor_name.setText(donornamelist.get(position));
        holder.adapter_bagno.setText(bag_nolist.get(position));
        holder.adapter_billno.setText(context.getString(R.string.billno)+"  "+bill_nolist.get(position));
        holder.adapter_charge.setText(context.getString(R.string.amount)+"  "+currency+amountlist.get(position));

        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
    }
    @Override
    public int getItemCount() {
        return dateofissuelist.size();
    }
}