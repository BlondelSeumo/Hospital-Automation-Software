package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import java.util.ArrayList;

public class PatientProfileAdapter extends RecyclerView.Adapter<PatientProfileAdapter.MyViewHolder> {

    private Context context;
    private int[] othersHeaderArray;
    private ArrayList<String> othersValues;

    public PatientProfileAdapter(Context applicationContext, int[] personalHeaderArray, ArrayList<String> personalValues) {

        this.context = applicationContext;
        this.othersHeaderArray = personalHeaderArray;
        this.othersValues = personalValues;

    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView headerTV,valueTV;

        public MyViewHolder(View view) {
            super(view);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_pharmacy_bill, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {
       // Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
       /* holder.headerTV.setText(context.getString(othersHeaderArray[position]));
        holder.valueTV.setText(othersValues.get(position));*/
    }

    @Override
    public int getItemCount() {
        return othersValues.size();
    }
}