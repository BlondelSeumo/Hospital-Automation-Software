package com.qdocs.smarthospital24.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.IpdConsultantModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class PatientIpdConsultantAdapter extends RecyclerView.Adapter<PatientIpdConsultantAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<IpdConsultantModel> ipd_consultant_list;
    private ArrayList<String> ins_dateList;
    private ArrayList<String> instructionlist;
    private ArrayList<String> consultantList;
    long downloadID;
    Fragment fragment;
    public PatientIpdConsultantAdapter(FragmentActivity fragmentActivity, ArrayList<IpdConsultantModel> ipd_consultant_list,Fragment fragment) {

        this.context = fragmentActivity;
        this.ipd_consultant_list = ipd_consultant_list;
        this.fragment = fragment;


    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView doctor,date,inst_date,instruction;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        LinearLayout headLay;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            date = (TextView) view.findViewById(R.id.adapter_patient_ipd_date);
            doctor = (TextView) view.findViewById(R.id.adapter_patient_ipd_doctor);
            inst_date = (TextView) view.findViewById(R.id.adapter_patient_ipd_inst_date);
            instruction = (TextView) view.findViewById(R.id.adapter_patient_ipd_inst);
            recyclerview = (RecyclerView) view.findViewById(R.id.recyclerview);
            headLay = (LinearLayout)view.findViewById(R.id.adapter_patient_ipd_nameView);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_consultant, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
          IpdConsultantModel model=ipd_consultant_list.get(position);
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.date.setText(model.getDate());
        holder.inst_date.setText(model.getIns_date());
        holder.doctor.setText(model.getName());
        holder.instruction.setText(model.getInstruction());
         ArrayList<CustomFieldModel> customList = model.getCustomfield();
        System.out.println("customList"+customList);
        CustomlistAdapter adapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(adapter);


    }

    @Override
    public int getItemCount() {
        return ipd_consultant_list.size();
    }
}
