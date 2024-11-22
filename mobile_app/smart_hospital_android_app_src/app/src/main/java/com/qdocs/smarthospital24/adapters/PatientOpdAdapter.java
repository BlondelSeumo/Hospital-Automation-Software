package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientOpdAdapter extends RecyclerView.Adapter<PatientOpdAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<String> opdno;
    private ArrayList<String> appointmentdate;
    private ArrayList<String> consultant;
    private ArrayList<String> reference;
    private ArrayList<String> symptoms;

    public PatientOpdAdapter(Context applicationContext, ArrayList<String> opdno,ArrayList<String> appointmentdate,ArrayList<String> consultant,ArrayList<String> reference,ArrayList<String> symptoms) {

        this.context = applicationContext;
        this.opdno = opdno;
        this.appointmentdate = appointmentdate;
        this.consultant = consultant;
        this.reference = reference;
        this.symptoms = symptoms;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView opdno,appoinmentdate,consultant,reference,symptoms;

        public MyViewHolder(View view) {
            super(view);

            opdno = (TextView) view.findViewById(R.id.adapter_patient_opd_opdno);
            appoinmentdate = (TextView) view.findViewById(R.id.adapter_patient_opd_reportingdate);
            consultant = (TextView) view.findViewById(R.id.adapter_patient_opd_consultant);
            reference = (TextView) view.findViewById(R.id.adapter_patient_opd_reference_doctor);
            symptoms = (TextView) view.findViewById(R.id.adapter_patient_opd_symptoms);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_opdlist, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {

        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));

        holder.opdno.setText(opdno.get(position));
        holder.appoinmentdate.setText(appointmentdate.get(position));
        holder.appoinmentdate.setText(appointmentdate.get(position));
        holder.reference.setText(reference.get(position));
        holder.symptoms.setText(symptoms.get(position));
    }

    @Override
    public int getItemCount() {
        return opdno.size();
    }
}