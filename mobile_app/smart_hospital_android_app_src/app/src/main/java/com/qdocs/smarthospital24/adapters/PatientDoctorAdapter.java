package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.MemoryPolicy;
import com.squareup.picasso.NetworkPolicy;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;


public class PatientDoctorAdapter extends RecyclerView.Adapter<PatientDoctorAdapter.MyViewHolder> {

    Context  applicationContext;
    ArrayList<String> doctorlist;
    ArrayList<String> imagelist;


    public PatientDoctorAdapter(Context applicationContext, ArrayList<String> doctorlist, ArrayList<String> imagelist) {


        this.doctorlist = doctorlist;
        this.imagelist = imagelist;
        this.applicationContext = applicationContext;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView field_name,field_value;
        ImageView doctorimage;



        public MyViewHolder(View view) {
            super(view);
            field_name = (TextView) view.findViewById(R.id.fieldname);
            field_value = (TextView) view.findViewById(R.id.fieldvalue);
            doctorimage = (ImageView) view.findViewById(R.id.doctorimage);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.doctorlist, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
        holder.field_name.setText(doctorlist.get(position));
        String imgUrl = Utility.getSharedPreferences(applicationContext.getApplicationContext(), "imagesUrl")+"uploads/staff_images/"+imagelist.get(position);
        Picasso.with(applicationContext.getApplicationContext()).load(imgUrl).placeholder(R.drawable.placeholder_user).memoryPolicy(MemoryPolicy.NO_CACHE)
                .networkPolicy(NetworkPolicy.NO_CACHE).into(holder.doctorimage);

    }
    @Override
    public int getItemCount() {
        return doctorlist.size();
    }


}