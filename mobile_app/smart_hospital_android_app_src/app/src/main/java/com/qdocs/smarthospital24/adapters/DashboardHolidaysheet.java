package com.qdocs.smarthospital24.adapters;

import static android.widget.Toast.makeText;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class DashboardHolidaysheet extends RecyclerView.Adapter<DashboardHolidaysheet.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> holidayIdList;
    private ArrayList<String> holidayTitleList;
    private ArrayList<String> holidayTypeList;
    private ArrayList<String> holidayDescList;
    private ArrayList<String> holidayDateList;

    private Map<String, String> deleteTaskParams = new Hashtable<String, String>();
    private Map<String, String> updateTaskParams = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    public DashboardHolidaysheet(FragmentActivity fragmentActivity, ArrayList<String> holidayIdList,
                                 ArrayList<String> holidayTitleList, ArrayList<String> holidayTypeList,
                                 ArrayList<String> holidayDescList, ArrayList<String> holidayDateList) {

        this.context = fragmentActivity;
        this.holidayIdList = holidayIdList;
        this.holidayTitleList = holidayTitleList;
        this.holidayTypeList = holidayTypeList;
        this.holidayDescList = holidayDescList;
        this.holidayDateList = holidayDateList;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {

        TextView holidayTV, holidayDateTV, holidayDesc;
        CheckBox holidayCheckbox;
        RelativeLayout header;
        ImageView icon;

        public MyViewHolder(View view) {
            super(view);
            holidayTV = view.findViewById(R.id.adapter_task_TaskNameTV);
            holidayCheckbox = view.findViewById(R.id.adapter_task_checkbox);
            header = view.findViewById(R.id.adapter_task_header);
            holidayDateTV = view.findViewById(R.id.adapter_task_TaskDateTV);
            icon = view.findViewById(R.id.adapter_task_taskIcon);
            holidayDesc = view.findViewById(R.id.adapter_task_taskDesc);
        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_dashboard_bottomsheet, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {


        holder.holidayTV.setText(holidayTitleList.get(position));
        holder.holidayDateTV.setText(holidayDateList.get(position));

       /* if(taskStatusList.get(position).equals("yes")) {
            holder.taskCheckbox.setChecked(true);
            holder.taskTV.setPaintFlags(holder.taskTV.getPaintFlags() | Paint.STRIKE_THRU_TEXT_FLAG);
        } else {
            holder.taskCheckbox.setChecked(false);
        }*/

        holder.holidayCheckbox.setVisibility(View.GONE);
        holder.header.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                holder.holidayDesc.setText(holidayDescList.get(position));
                holder.holidayDesc.setVisibility(View.VISIBLE);
            }
        });

    }


    @Override
    public int getItemCount() {
        return holidayIdList.size();
    }

}
