package com.qdocs.smarthospital24.adapters;

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

public class PatientIPDObstetricHistoryAdapter extends RecyclerView.Adapter<PatientIPDObstetricHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> ipdidlist;
    private ArrayList<String> place_of_deliverylist;
    private ArrayList<String> pregnancy_durationlist;
    private ArrayList<String> pregnancy_complicationslist;
    private ArrayList<String> birth_weightlist;
    private ArrayList<String> genderlist;
    private ArrayList<String> infant_feedinglist;
    private ArrayList<String> birth_statuslist;
    private ArrayList<String> alive_deaddatelist;
    private ArrayList<String> death_causelist;
    private ArrayList<String> previous_medical_historylist;
    private ArrayList<String> special_instructionlist;
    long downloadID;

    public PatientIPDObstetricHistoryAdapter(FragmentActivity fragmentActivity, ArrayList<String> ipdidlist, ArrayList<String> place_of_deliverylist,
                                             ArrayList<String> pregnancy_durationlist, ArrayList<String> pregnancy_complicationslist, ArrayList<String> birth_weightlist, ArrayList<String> genderlist,
             ArrayList<String> infant_feedinglist, ArrayList<String> birth_statuslist, ArrayList<String> alive_deaddatelist, ArrayList<String> death_causelist, ArrayList<String> previous_medical_historylist, ArrayList<String> special_instructionlist) {

        this.context = fragmentActivity;
        this.ipdidlist = ipdidlist;
        this.place_of_deliverylist = place_of_deliverylist;
        this.pregnancy_durationlist = pregnancy_durationlist;
        this.pregnancy_complicationslist = pregnancy_complicationslist;
        this.birth_weightlist = birth_weightlist;
        this.genderlist = genderlist;
        this.infant_feedinglist = infant_feedinglist;
        this.birth_statuslist = birth_statuslist;
        this.alive_deaddatelist = alive_deaddatelist;
        this.death_causelist = death_causelist;
        this.previous_medical_historylist = previous_medical_historylist;
        this.special_instructionlist = special_instructionlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView birth_weight, gender, infant_feeding , birth_status,death_cause,spec_instr,prev_med_history;
        TextView  place_delivery,pregnancy_duration,pregnancy_complication,alive_death_date;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            place_delivery = (TextView) view.findViewById(R.id.place_delivery);
            pregnancy_duration = (TextView) view.findViewById(R.id.pregnancy_duration);
            pregnancy_complication = (TextView) view.findViewById(R.id.pregnancy_complication);
            birth_weight = (TextView) view.findViewById(R.id.birth_weight);
            gender = (TextView) view.findViewById(R.id.gender);
            infant_feeding = (TextView) view.findViewById(R.id.infant_feeding);
            birth_status = (TextView) view.findViewById(R.id.birth_status);
            alive_death_date = (TextView) view.findViewById(R.id.alive_death_date);
            death_cause = (TextView) view.findViewById(R.id.death_cause);
            prev_med_history = (TextView) view.findViewById(R.id.prev_med_history);
            spec_instr = (TextView) view.findViewById(R.id.spec_instr);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_obs_treatment, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE

        holder.place_delivery.setText(place_of_deliverylist.get(position));
        holder.pregnancy_duration.setText(pregnancy_durationlist.get(position));
        holder.pregnancy_complication.setText(pregnancy_complicationslist.get(position));
        holder.birth_weight.setText(birth_weightlist.get(position));
        holder.gender.setText(genderlist.get(position));
        holder.infant_feeding.setText(infant_feedinglist.get(position));
        holder.alive_death_date.setText(alive_deaddatelist.get(position));
        String birth_status=birth_statuslist.get(position);
        holder.birth_status.setText(String.valueOf(birth_status.charAt(0)).toUpperCase() + birth_status.substring(1, birth_status.length()));
        holder.death_cause.setText(death_causelist.get(position));
        holder.prev_med_history.setText(previous_medical_historylist.get(position));
        holder.spec_instr.setText(special_instructionlist.get(position));



    }
    @Override
    public int getItemCount() {
        return ipdidlist.size();
    }
}
