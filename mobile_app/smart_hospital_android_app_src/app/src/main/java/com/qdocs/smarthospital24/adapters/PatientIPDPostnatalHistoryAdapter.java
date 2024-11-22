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

public class PatientIPDPostnatalHistoryAdapter extends RecyclerView.Adapter<PatientIPDPostnatalHistoryAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> ipdidlist;
    private ArrayList<String> labor_timelist;
    private ArrayList<String> delivery_timelist;
    private ArrayList<String> routine_questionlist;
    private ArrayList<String> general_remarklist;


    public PatientIPDPostnatalHistoryAdapter(FragmentActivity fragmentActivity, ArrayList<String> ipdidlist, ArrayList<String> labor_timelist,
                                             ArrayList<String> delivery_timelist, ArrayList<String> routine_questionlist, ArrayList<String> general_remarklist) {

        this.context = fragmentActivity;
        this.ipdidlist = ipdidlist;
        this.labor_timelist = labor_timelist;
        this.delivery_timelist = delivery_timelist;
        this.routine_questionlist = routine_questionlist;
        this.general_remarklist = general_remarklist;


    }

    public class MyViewHolder extends RecyclerView.ViewHolder {


        TextView  labour_time,delivery_time,routine_question,general_remark;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;

        public MyViewHolder(View view) {
            super(view);
            labour_time = (TextView) view.findViewById(R.id.labour_time);
            delivery_time = (TextView) view.findViewById(R.id.delivery_time);
            routine_question = (TextView) view.findViewById(R.id.routine_question);
            general_remark = (TextView) view.findViewById(R.id.general_remark);


        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_postnatal_treatment, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE

        holder.labour_time.setText(labor_timelist.get(position));
        holder.delivery_time.setText(delivery_timelist.get(position));
        holder.routine_question.setText(routine_questionlist.get(position));
        holder.general_remark.setText(general_remarklist.get(position));




    }
    @Override
    public int getItemCount() {
        return ipdidlist.size();
    }
}
