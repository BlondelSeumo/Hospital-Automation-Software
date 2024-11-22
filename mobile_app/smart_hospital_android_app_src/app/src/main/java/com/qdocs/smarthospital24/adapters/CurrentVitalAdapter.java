package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import java.util.ArrayList;


public class CurrentVitalAdapter extends RecyclerView.Adapter<CurrentVitalAdapter.MyViewHolder> {
    String newfirstSubString,new1secondSubString;
    Context  applicationContext;
    ArrayList<String> currentvitallist=new ArrayList<String>();
    ArrayList<String> reference_rangelist=new ArrayList<String>();
    ArrayList<String> unitlist=new ArrayList<String>();
    ArrayList<String> patient_rangelist=new ArrayList<String>();
    ArrayList<String> messure_datelist=new ArrayList<String>();
    ArrayList<String> patient_vital_idlist=new ArrayList<String>();
    ArrayList<String> idlist=new ArrayList<String>();

    public CurrentVitalAdapter(Context applicationContext,ArrayList<String> idlist, ArrayList<String> currentvitallist, ArrayList<String> reference_rangelist,
                               ArrayList<String> unitlist, ArrayList<String> patient_rangelist, ArrayList<String> messure_datelist,
                               ArrayList<String> patient_vital_idlist) {


        this.idlist = idlist;
        this.currentvitallist = currentvitallist;
        this.reference_rangelist = reference_rangelist;
        this.unitlist = unitlist;
        this.patient_rangelist = patient_rangelist;
        this.messure_datelist = messure_datelist;
        this.patient_vital_idlist = patient_vital_idlist;
        this.applicationContext = applicationContext;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView field_name,field_value,result,datetime;



        public MyViewHolder(View view) {
            super(view);
            field_name = (TextView) view.findViewById(R.id.name);
            field_value = (TextView) view.findViewById(R.id.vitalvalue);
            result = (TextView) view.findViewById(R.id.result);
            datetime = (TextView) view.findViewById(R.id.datetime);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.currentvitalopd, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {

        holder.field_name.setText(currentvitallist.get(position));
        holder.field_value.setText(patient_rangelist.get(position)+" "+unitlist.get(position));
        holder.result.setText(currentvitallist.get(position));
        holder.datetime.setText(messure_datelist.get(position));

        String resultvalue=reference_rangelist.get(position);
        System.out.println("resultvalue== "+resultvalue);
        String[] split = resultvalue.split(" - ");
        String firstSubString = split[0];
        String secondSubString = split[1];
        System.out.println("firstSubString== "+firstSubString);
        System.out.println("secondSubString== "+secondSubString);
        String newsecondSubString=secondSubString.replace(" ","");
        System.out.println("newsecondSubString== "+newsecondSubString);
        if(firstSubString.contains("/")){
            String str = firstSubString;
            String substr = "/";
            newfirstSubString = str.substring(0, str.indexOf(substr));
            System.out.println("newfirstSubString== "+newfirstSubString);
        }else{
             newfirstSubString = firstSubString;
            System.out.println("newfirstSubString== "+newfirstSubString);
        }
        if(newsecondSubString.contains("/")){
            String str = newsecondSubString;
            String substr = "/";
            new1secondSubString = str.substring(0, str.indexOf(substr));
            System.out.println("new1secondSubString== "+new1secondSubString);
        }else{
            new1secondSubString = newsecondSubString;
            System.out.println("new1secondSubString== "+new1secondSubString);
        }

        if (Double.parseDouble(patient_rangelist.get(position)) > Double.parseDouble(newfirstSubString) && Double.parseDouble(patient_rangelist.get(position)) < Double.parseDouble(new1secondSubString)){
            holder.result.setText("Normal");
            holder.result.setBackgroundResource(R.drawable.green_border);
            holder.result.setTextColor(applicationContext.getResources().getColor(R.color.white));
        }else if (Double.parseDouble(patient_rangelist.get(position)) < Double.parseDouble(newfirstSubString)){
            holder.result.setText("Low");
            holder.result.setBackgroundResource(R.drawable.red_border);
            holder.result.setTextColor(applicationContext.getResources().getColor(R.color.white));
        }else if(Double.parseDouble(patient_rangelist.get(position)) > Double.parseDouble(new1secondSubString)){
            holder.result.setText("High");
            holder.result.setBackgroundResource(R.drawable.red_border);
            holder.result.setTextColor(applicationContext.getResources().getColor(R.color.white));
        }else if (Double.parseDouble(patient_rangelist.get(position)) == Double.parseDouble(newfirstSubString) || Double.parseDouble(patient_rangelist.get(position)) == Double.parseDouble(new1secondSubString)){
            holder.result.setText("Normal");
            holder.result.setBackgroundResource(R.drawable.green_border);
            holder.result.setTextColor(applicationContext.getResources().getColor(R.color.white));
        }

    }

    @Override
    public int getItemCount() {
        return idlist.size();
    }

}