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
import com.qdocs.smarthospital24.patient.PatientIpdDischargeDetailsList;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;
import static android.content.Intent.FLAG_ACTIVITY_NEW_TASK;

public class PatientIpdDischargeAdapter extends RecyclerView.Adapter<PatientIpdDischargeAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> patient_idList;
    private ArrayList<String> patientNameList;
    private ArrayList<String> bedList;
    private ArrayList<String> ipd_nolist;
    private ArrayList<String> genderlist;
    private ArrayList<String> mobilenolist;
    private ArrayList<String> consultantlist;
    private ArrayList<String> credit_limitlist;
    private ArrayList<String> paymentlist;
    private ArrayList<String> chargeslist;
   private ArrayList<String> dischargedlist;
    private ArrayList<String> ipdidlist;
    long downloadID;
    public PatientIpdDischargeAdapter(FragmentActivity fragmentActivity,ArrayList<String> bedList,
                                      ArrayList<String> patientNameList, ArrayList<String> ipd_nolist, ArrayList<String> genderlist,
                                      ArrayList<String> mobilenolist, ArrayList<String> consultantlist, ArrayList<String> credit_limitlist,
                                      ArrayList<String> paymentlist, ArrayList<String> chargeslist,ArrayList<String> dischargedlist,ArrayList<String> ipdidlist) {

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
        this.dischargedlist = dischargedlist;
        this.ipdidlist = ipdidlist;
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
            discharge_status = (TextView) view.findViewById(R.id.adapter_patient_discharge_status);
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

        holder.ipdno.setText(context.getString(R.string.ipdnoprefix)+ipd_nolist.get(position));
        holder.doctor.setText(consultantlist.get(position));
        holder.bed.setText(bedList.get(position));
       if(chargeslist.get(position).equals("")){
           holder.charge.setText("0.00");
       }else{
           float charge=Float.parseFloat(chargeslist.get(position));
           holder.charge.setText(currency+String.valueOf(charge));
       }

       if(paymentlist.get(position).equals("")){
           holder.payment.setText("0.00");
       }else{
           holder.payment.setText(currency+paymentlist.get(position));
       }


        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent myactivity = new Intent(context.getApplicationContext(), PatientIpdDischargeDetailsList.class);
                myactivity.addFlags(FLAG_ACTIVITY_NEW_TASK);
                myactivity.putExtra("IPDNO", ipd_nolist.get(position));
                context.getApplicationContext().startActivity(myactivity);
            }
        });

        if(dischargedlist.get(position).equals("no")){
            holder.discharge_status.setVisibility(View.GONE);
        }else{
           holder.discharge_status.setVisibility(View.VISIBLE);
           holder.discharge_status.setText(context.getString(R.string.dischargd));

        }

    }
    @Override
    public int getItemCount() {
        return ipd_nolist.size();
    }
}
