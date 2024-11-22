package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientInvestigationDetailAdapter extends RecyclerView.Adapter<PatientInvestigationDetailAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<String> unitlist;
    private ArrayList<String> referencelist;
    private ArrayList<String> parameterlist;
    private ArrayList<String> descriptionlist;
    long downloadID;
    ArrayList<String> report_idlist;

    public PatientInvestigationDetailAdapter(Context applicationContext, ArrayList<String> parameterlist, ArrayList<String> unitlist,
                                             ArrayList<String> referencelist, ArrayList<String> descriptionlist) {

        this.context = applicationContext;
        this.unitlist = unitlist;
        this.parameterlist = parameterlist;
        this.referencelist = referencelist;
        this.descriptionlist = descriptionlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView headerTV;
        TextView parameter,reference,unit,description;
        LinearLayout downloadBtn;
        public MyViewHolder(View view) {
             super(view);
            parameter = (TextView) view.findViewById(R.id.parameter);
            reference = (TextView) view.findViewById(R.id.reference);
            unit = (TextView) view.findViewById(R.id.unit);
            description = (TextView) view.findViewById(R.id.description);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_invest_detail, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.parameter.setText(parameterlist.get(position));
        holder.reference.setText(referencelist.get(position));
        holder.unit.setText(unitlist.get(position));
        holder.description.setText(descriptionlist.get(position));

    }

    @Override
     public int getItemCount() {
        return parameterlist.size();
    }
}