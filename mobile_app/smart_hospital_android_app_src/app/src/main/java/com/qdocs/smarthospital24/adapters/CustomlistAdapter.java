package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.fragment.app.Fragment;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;


public class CustomlistAdapter extends RecyclerView.Adapter<CustomlistAdapter.MyViewHolder> {

    Context  applicationContext;
    ArrayList<CustomFieldModel> customlist;
    Fragment fragment;

    public CustomlistAdapter(Context applicationContext, ArrayList<CustomFieldModel> customlist, Fragment fragment) {

        this.fragment = fragment;
        this.customlist = customlist;
        this.applicationContext = applicationContext;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView field_name,field_value;



        public MyViewHolder(View view) {
            super(view);
            field_name = (TextView) view.findViewById(R.id.fieldname);
            field_value = (TextView) view.findViewById(R.id.fieldvalue);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.customlist, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
CustomFieldModel customFieldModel=customlist.get(position);
        holder.field_name.setText(customFieldModel.getFieldname());
        holder.field_value.setText(customFieldModel.getFieldvalue());

    }
    @Override
    public int getItemCount() {
        return customlist.size();
    }


}