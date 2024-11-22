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
import com.qdocs.smarthospital24.patient.PatientIpdDetailsList;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;
import static android.content.Intent.FLAG_ACTIVITY_NEW_TASK;

public class PatientIpdDischargePatientAdapter extends RecyclerView.Adapter<PatientIpdDischargePatientAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> patientNameList;
    private ArrayList<String> bedList;
    private ArrayList<String> ipd_nolist;
    private ArrayList<String> genderlist;
    private ArrayList<String> mobilenolist;
    private ArrayList<String> consultantlist;
    private ArrayList<String> credit_limitlist;
    private ArrayList<String> paymentlist;
    private ArrayList<String> chargeslist;
    private ArrayList<String> duelist;
    private ArrayList<String> dischargedlist;
    long downloadID;
    public PatientIpdDischargePatientAdapter(FragmentActivity fragmentActivity, ArrayList<String> bedList,
                                             ArrayList<String> patientNameList, ArrayList<String> ipd_nolist, ArrayList<String> genderlist,
                                             ArrayList<String> mobilenolist, ArrayList<String> consultantlist, ArrayList<String> credit_limitlist, ArrayList<String> paymentlist, ArrayList<String> chargeslist,
                                             ArrayList<String> duelist,ArrayList<String> dischargedlist) {

        this.context = fragmentActivity;
        this.patientNameList = patientNameList;
        this.bedList = bedList;
        this.ipd_nolist = ipd_nolist;
        this.genderlist = genderlist;
        this.mobilenolist = mobilenolist;
        this.consultantlist = consultantlist;
        this.credit_limitlist = credit_limitlist;
        this.paymentlist = paymentlist;
        this.chargeslist = chargeslist;
        this.duelist = duelist;
        this.dischargedlist = dischargedlist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView ipdno, discharge_status, gender, phone, bed;
        TextView doctor, name, charge,payment,due_payment,credit_limit;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            ipdno = (TextView) view.findViewById(R.id.adapter_patient_dischrge_ipdno);
//            name = (TextView) view.findViewById(R.id.adapter_patient_dischrge_name);
            discharge_status = (TextView) view.findViewById(R.id.adapter_patient_discharge_status);
         //   gender = (TextView) view.findViewById(R.id.adapter_patient_dischrge_gender);
            //phone = (TextView) view.findViewById(R.id.adapter_patient_dischrge_phone);
            doctor = (TextView) view.findViewById(R.id.adapter_patient_dischrge_doctor);
            bed = (TextView) view.findViewById(R.id.adapter_patient_dischrge_bed);
            charge = (TextView) view.findViewById(R.id.adapter_patient_dischrge_charge);
            payment = (TextView) view.findViewById(R.id.adapter_patient_dischrge_payment);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_dischrge_detailsBtn);
            headLay = view.findViewById(R.id.adapter_patient_dischrge_headLayout);
        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_discharge, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {

        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);

        holder.ipdno.setText("IPD No.-  " +ipd_nolist.get(position));
        holder.doctor.setText(consultantlist.get(position));
        holder.bed.setText(bedList.get(position));
        holder.charge.setText(currency+chargeslist.get(position));
        holder.payment.setText(currency+paymentlist.get(position));

        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent myactivity = new Intent(context.getApplicationContext(), PatientIpdDetailsList.class);
                myactivity.addFlags(FLAG_ACTIVITY_NEW_TASK);
                myactivity.putExtra("IPDNO", ipd_nolist.get(position));
                context.getApplicationContext().startActivity(myactivity);
            }
        });

        if(dischargedlist.get(position).equals("yes")){
            holder.discharge_status.setVisibility(View.VISIBLE);
        }else{
            holder.discharge_status.setVisibility(View.GONE);
        }
    }
    @Override
    public int getItemCount() {
        return ipd_nolist.size();
    }
}
