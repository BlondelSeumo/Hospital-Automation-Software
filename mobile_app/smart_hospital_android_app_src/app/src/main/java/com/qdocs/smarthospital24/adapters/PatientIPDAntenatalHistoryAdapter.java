package com.qdocs.smarthospital24.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.AntenatalModel;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientIPDAntenatalHistoryAdapter extends RecyclerView.Adapter<PatientIPDAntenatalHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<AntenatalModel> antenatal_detail_list;
    long downloadID;
    Fragment fragment;
    public PatientIPDAntenatalHistoryAdapter(FragmentActivity activity, ArrayList<AntenatalModel> antenatal_detail_list, Fragment fragment) {

        this.context = activity;
        this.antenatal_detail_list = antenatal_detail_list;
        this.fragment=fragment;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView bleeding, headache, pain , constipation,cough,weight,primary_vaginal,height,specialfindingsremark,uter_size,presentation_position,foeta_heart,vaginal,antenatal_weight,urine_aaibumen;
        TextView  opd_ipd_no,opdcheckupid,date,vomiting,primaryexamine_date,discharg,oedema,condition,pelvicexamination,sp,uterus_size,presenting_part_brim,blood_pressure,antenatal_oedema,urine,remark,nextvisit;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
        RecyclerView recyclerview;
        public MyViewHolder(View view) {
            super(view);
            opd_ipd_no = (TextView) view.findViewById(R.id.opd_ipd_no);
            opdcheckupid = (TextView) view.findViewById(R.id.opdcheckupid);
            date = (TextView) view.findViewById(R.id.date);
            bleeding = (TextView) view.findViewById(R.id.bleeding);
            headache = (TextView) view.findViewById(R.id.headache);
            pain = (TextView) view.findViewById(R.id.pain);
            constipation = (TextView) view.findViewById(R.id.constipation);
            vomiting = (TextView) view.findViewById(R.id.vomiting);
            cough = (TextView) view.findViewById(R.id.cough);
            primary_vaginal = (TextView) view.findViewById(R.id.primary_vaginal);
            weight = (TextView) view.findViewById(R.id.weight);
            height = (TextView) view.findViewById(R.id.height);
            primaryexamine_date = (TextView) view.findViewById(R.id.primaryexamine_date);
            discharg = (TextView) view.findViewById(R.id.discharg);
            oedema = (TextView) view.findViewById(R.id.oedema);
            condition = (TextView) view.findViewById(R.id.condition);
            specialfindingsremark = (TextView) view.findViewById(R.id.specialfindingsremark);
            pelvicexamination = (TextView) view.findViewById(R.id.pelvicexamination);
            sp = (TextView) view.findViewById(R.id.sp);
            uter_size = (TextView) view.findViewById(R.id.uter_size);
            uterus_size = (TextView) view.findViewById(R.id.uterus_size);
            presentation_position = (TextView) view.findViewById(R.id.presentation_position);
            presenting_part_brim = (TextView) view.findViewById(R.id.presenting_part_brim);
            foeta_heart = (TextView) view.findViewById(R.id.foeta_heart);
            blood_pressure = (TextView) view.findViewById(R.id.blood_pressure);
            vaginal = (TextView) view.findViewById(R.id.vaginal);
            antenatal_weight = (TextView) view.findViewById(R.id.antenatal_weight);
            antenatal_oedema = (TextView) view.findViewById(R.id.antenatal_oedema);
            urine = (TextView) view.findViewById(R.id.urine);
            urine_aaibumen = (TextView) view.findViewById(R.id.urine_aaibumen);
            remark = (TextView) view.findViewById(R.id.remark);
            nextvisit = (TextView) view.findViewById(R.id.nextvisit);
            recyclerview = (RecyclerView)view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_antenatal_treatment, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final AntenatalModel antenatalModel = antenatal_detail_list.get(position);
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE

        holder.opd_ipd_no.setText(antenatalModel.getId());
        holder.opdcheckupid.setText(antenatalModel.getOpd_checkupid());
        holder.date.setText(antenatalModel.getDate());
        holder.bleeding.setText(antenatalModel.getBleeding());
        holder.headache.setText(antenatalModel.getHeadache());
        holder.pain.setText(antenatalModel.getPain());
        holder.constipation.setText(antenatalModel.getConstipation());
        holder.vomiting.setText(antenatalModel.getVomiting());
        holder.cough.setText(antenatalModel.getCough());
        holder.primary_vaginal.setText(antenatalModel.getVaginal());
        holder.weight.setText(antenatalModel.getWeight());
        holder.height.setText(antenatalModel.getHeight());
        holder.primaryexamine_date.setText(antenatalModel.getDate());
        holder.discharg.setText(antenatalModel.getDischarge());
        holder.oedema.setText(antenatalModel.getOedema());
        holder.condition.setText(antenatalModel.getGeneral_condition());
        holder.specialfindingsremark.setText(antenatalModel.getFinding_remark());
        holder.pelvicexamination.setText(antenatalModel.getPelvic_examination());
        holder.sp.setText(antenatalModel.getSp());
        holder.uter_size.setText(antenatalModel.getUter_size());
        holder.uterus_size.setText(antenatalModel.getUterus_size());
        holder.presentation_position.setText(antenatalModel.getPresentation_position());
        holder.presenting_part_brim.setText(antenatalModel.getBrim_presentation());
        holder.foeta_heart.setText(antenatalModel.getFoeta_heart());
        holder.blood_pressure.setText(antenatalModel.getBlood_pressure());
        holder.vaginal.setText(antenatalModel.getVaginal());
        holder.antenatal_weight.setText(antenatalModel.getAntenatal_weight());
        holder.antenatal_oedema.setText(antenatalModel.getAntenatal_Oedema());
        holder.urine.setText(antenatalModel.getUrine_sugar());
        holder.urine_aaibumen.setText(antenatalModel.getUrine());
        holder.remark.setText(antenatalModel.getRemark());
        holder.nextvisit.setText(antenatalModel.getNext_visit());

        ArrayList<CustomFieldModel> customList = antenatalModel.getCustomfield();
        CustomlistAdapter customlistAdapter = new CustomlistAdapter(context, customList, fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL, false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(customlistAdapter);


    }
    @Override
    public int getItemCount() {
        return antenatal_detail_list.size();
    }
}
