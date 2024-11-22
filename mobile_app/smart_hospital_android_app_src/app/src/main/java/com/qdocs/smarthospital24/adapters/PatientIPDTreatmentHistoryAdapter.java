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

public class PatientIPDTreatmentHistoryAdapter extends RecyclerView.Adapter<PatientIPDTreatmentHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> ipdidlist;
    private ArrayList<String> symptomslist;
    private ArrayList<String> consultantlist;
    private ArrayList<String> bedlist;
    long downloadID;

    public PatientIPDTreatmentHistoryAdapter(FragmentActivity fragmentActivity, ArrayList<String> ipdidlist, ArrayList<String> symptomslist,
                                             ArrayList<String> consultantlist, ArrayList<String> bedlist) {

        this.context = fragmentActivity;
        this.ipdidlist = ipdidlist;
        this.symptomslist = symptomslist;
        this.consultantlist = consultantlist;
        this.bedlist = bedlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView symptoms, ipdid, consultant , bed;
        TextView  opeartion_date,name,charge;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            symptoms = (TextView) view.findViewById(R.id.adapter_patient_symptoms);
            ipdid = (TextView) view.findViewById(R.id.adapter_patient_ipdid);
            consultant = (TextView) view.findViewById(R.id.adapter_patient_consultant);
            bed = (TextView) view.findViewById(R.id.adapter_patient_bed);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_headLayout);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_treatment, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));

        holder.symptoms.setText(symptomslist.get(position));
        holder.ipdid.setText(ipdidlist.get(position));
        holder.consultant.setText(consultantlist.get(position));
        holder.bed.setText(bedlist.get(position));



    }
    @Override
    public int getItemCount() {
        return ipdidlist.size();
    }
}
