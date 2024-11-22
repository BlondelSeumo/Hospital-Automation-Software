package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.MedicationModel;
import com.qdocs.smarthospital24.model.MedicineModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class PatientIPDMedicationAdapter extends RecyclerView.Adapter<PatientIPDMedicationAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<MedicationModel> medication_list;
    Fragment fragment;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    public PatientIPDMedicationAdapter(Context context, ArrayList<MedicationModel> medication_list, Fragment fragment) {
        this.context = context;
        this.medication_list = medication_list;
        this.fragment = fragment;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView  date;
        public CardView containerView;
        RelativeLayout headLay;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            date = (TextView) view.findViewById(R.id.adapter_patient_ipd_date);
            headLay = (RelativeLayout) view.findViewById(R.id.adapter_patient_ipd_headLayout);
            recyclerview = (RecyclerView) view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_medicationlist, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        MedicationModel medicationModel=medication_list.get(position);
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.date.setText(medicationModel.getMedicine_date()+"\n("+medicationModel.getMedicine_day()+")");
        ArrayList<MedicineModel> medicinelist = medicationModel.getMedicine();
        System.out.println("doselist"+medicinelist);
        PatientIpdDoseAdapter doselistAdapter = new PatientIpdDoseAdapter(context,medicinelist,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(doselistAdapter);
    }

    @Override
    public int getItemCount() {
        return medication_list.size();
    }
}
