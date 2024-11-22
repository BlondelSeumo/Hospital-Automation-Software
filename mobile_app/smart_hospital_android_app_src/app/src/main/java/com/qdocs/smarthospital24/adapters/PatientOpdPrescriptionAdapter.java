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

public class PatientOpdPrescriptionAdapter extends RecyclerView.Adapter<PatientOpdPrescriptionAdapter.MyViewHolder> {

    private Context context;
    private int[] othersHeaderArray;
    private ArrayList<String> medicine_category_idlist;
    private ArrayList<String> medicineList;
    private ArrayList<String> dosagelist;
    private ArrayList<String> instructionlist;
    private ArrayList<String> intervallist;
    private ArrayList<String> durationlist;

    public PatientOpdPrescriptionAdapter(Context applicationContext, ArrayList<String> medicine_category_idlist, ArrayList<String> medicineList,
                                         ArrayList<String> dosagelist, ArrayList<String> instructionlist, ArrayList<String> intervallist, ArrayList<String> durationlist) {

        this.context = applicationContext;
        this.medicine_category_idlist = medicine_category_idlist;
        this.medicineList = medicineList;
        this.dosagelist = dosagelist;
        this.instructionlist = instructionlist;
        this.intervallist = intervallist;
        this.durationlist = durationlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView headerTV;
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

        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.patient_ipd_interval.setText(intervallist.get(position));
        holder.patient_ipd_medicine.setText(medicineList.get(position));
        holder.patient_ipd_dose.setText(dosagelist.get(position));
        holder.patient_ipd_duration.setText(durationlist.get(position));
        holder.instruction.setText(instructionlist.get(position));
    }

    @Override
    public int getItemCount() {
        return medicineList.size();
    }
}