package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.StaffCommentModel;

import java.util.ArrayList;


public class StaffCommentAdapter extends RecyclerView.Adapter<StaffCommentAdapter.MyViewHolder> {

    Context  applicationContext;
    ArrayList<StaffCommentModel> customlist;
    Fragment fragment;

    public StaffCommentAdapter(Context applicationContext, ArrayList<StaffCommentModel> customlist, Fragment fragment) {

        this.fragment = fragment;
        this.customlist = customlist;
        this.applicationContext = applicationContext;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView staffcomment,date,staffname;



        public MyViewHolder(View view) {
            super(view);
            staffcomment = (TextView) view.findViewById(R.id.staffcomment);
            date = (TextView) view.findViewById(R.id.date);
            staffname = (TextView) view.findViewById(R.id.staffname);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.staffcommentlist, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
        StaffCommentModel staffCommentModel=customlist.get(position);
        holder.staffcomment.setText(staffCommentModel.getComment_staff());
        holder.date.setText(staffCommentModel.getCreated_at());
        holder.staffname.setText(staffCommentModel.getStaffname());

    }
    @Override
    public int getItemCount() {
        return customlist.size();
    }


}