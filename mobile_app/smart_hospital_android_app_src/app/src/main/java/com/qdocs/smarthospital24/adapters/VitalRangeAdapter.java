package com.qdocs.smarthospital24.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import java.util.ArrayList;
import java.util.List;

public class VitalRangeAdapter extends RecyclerView.Adapter<VitalRangeAdapter.MyViewHolder>  {

    private FragmentActivity context;
    private List<String> datelist;
    private List<String> patient_range_list;
    public VitalRangeAdapter(FragmentActivity context, ArrayList<String> datelist,ArrayList<String> patient_range_list) {
        this.context = context;
        this.datelist = datelist;
        this.patient_range_list = patient_range_list;
    }


    @NonNull
    @Override
    public VitalRangeAdapter.MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_vitalvalue_detail, parent, false);
        return new VitalRangeAdapter.MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull VitalRangeAdapter.MyViewHolder holder, int position) {
          holder.date.setText(datelist.get(position));
          holder.vitalvalue.setText(patient_range_list.get(position));
    }

    @Override
    public int getItemCount() {
        return datelist.size();
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        TextView date,vitalvalue;
        public MyViewHolder(@NonNull View itemView) {
            super(itemView);
            date=itemView.findViewById(R.id.date);
            vitalvalue=itemView.findViewById(R.id.vitalvalue);

        }
    }
}

