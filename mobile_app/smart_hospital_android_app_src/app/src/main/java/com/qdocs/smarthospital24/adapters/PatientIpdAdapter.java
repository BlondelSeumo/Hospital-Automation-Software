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

public class PatientIpdAdapter extends RecyclerView.Adapter<PatientIpdAdapter.MyViewHolder> {

    private Context context;
    private int[] othersHeaderArray;
    private ArrayList<String> medicine_categorylist;
    private ArrayList<String> medicinenamelist;
    private ArrayList<String> dose_duration_namelist;
    private ArrayList<String> dose_interval_namelist;
    private ArrayList<String> dosagelist;
    private ArrayList<String> instructionlist;

    public PatientIpdAdapter(Context applicationContext,ArrayList<String> medicine_categorylist,ArrayList<String> medicinenamelist,
                             ArrayList<String> dosagelist,ArrayList<String> instructionlist,ArrayList<String> dose_interval_namelist,ArrayList<String> dose_duration_namelist) {

        this.context = applicationContext;
        this.medicine_categorylist = medicine_categorylist;
        this.medicinenamelist = medicinenamelist;
        this.dosagelist = dosagelist;
        this.instructionlist = instructionlist;
        this.dose_duration_namelist = dose_duration_namelist;
        this.dose_interval_namelist = dose_interval_namelist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView headerTV, valueTV;
        TextView patient_ipd_duration,patient_ipd_interval,patient_ipd_medicine,patient_ipd_dose,instruction;

        public MyViewHolder(View view) {
            super(view);
            patient_ipd_duration = (TextView) view.findViewById(R.id.patient_ipd_duration);
            patient_ipd_interval = (TextView) view.findViewById(R.id.patient_ipd_interval);
            patient_ipd_dose = (TextView) view.findViewById(R.id.patient_ipd_dose);
            patient_ipd_medicine = (TextView) view.findViewById(R.id.patient_ipd_medicine);
            instruction = (TextView) view.findViewById(R.id.instruction);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_opd_prescription, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {

        Utility.setLocale(context,Utility.getSharedPreferences(context.getApplicationContext(),Constants.langCode));
        holder.patient_ipd_interval.setText(dose_interval_namelist.get(position));
        holder.patient_ipd_medicine.setText(medicinenamelist.get(position));
        holder.patient_ipd_dose.setText(dosagelist.get(position));
        holder.patient_ipd_duration.setText(dose_duration_namelist.get(position));
        holder.instruction.setText(instructionlist.get(position));
    }

    @Override
    public int getItemCount() {
        return medicinenamelist.size();
    }
}