package com.qdocs.smarthospital24.adapters;

import android.app.Activity;
import android.graphics.drawable.Drawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.GridView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;
import androidx.fragment.app.FragmentActivity;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class SlotsAdapter extends BaseAdapter {
    private FragmentActivity context;
    private ArrayList<String> ModelArrayList;
    private ArrayList<String> filledlist;
    GridView listView;
    View view;
    public SlotsAdapter(FragmentActivity context, ArrayList<String> ModelArrayList, ArrayList<String> filledlist, GridView listView) {
        this.context = context;
        this.ModelArrayList = ModelArrayList;
        this.filledlist = filledlist;
        this.listView = listView;
    }

    @Override
    public int getCount() {
        return ModelArrayList.size();
    }

    @Override
    public Object getItem(int position) {
        return ModelArrayList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return 0;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        final ViewHolder holder;
        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
        // If holder not exist then locate all view from UI file.
        if (convertView == null) {
            // inflate UI from XML file
            convertView = inflater.inflate(R.layout.adapter_tag_selection, parent, false);
            holder = new ViewHolder(convertView);
            convertView.setTag(holder);
        } else {
            // if holder created, get tag from view
            holder = (ViewHolder) convertView.getTag();
        }
        String defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
        String obj=ModelArrayList.get(position);
        String new_obj= obj.replaceAll("\"", "");
        holder.slot.setText(new_obj);
        if(filledlist.get(position).equals("1")){
            Drawable mDrawable = ContextCompat.getDrawable(context, R.drawable.booked_select);
            holder.slot.setBackground(mDrawable);
            holder.slot.setTextColor(context.getResources().getColor(R.color.redDanger));
            holder.slot.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Toast.makeText(context, "Already Booked", Toast.LENGTH_SHORT).show();
                }
            });
        }else{
            Drawable mDrawable = ContextCompat.getDrawable(context, R.drawable.notbooked_select);
            holder.slot.setBackground(mDrawable);
            holder.slot.setTextColor(context.getResources().getColor(R.color.darkgreen));
        }

        return convertView;
    }


    private class ViewHolder {
        private TextView slot,date,message,id;

        public ViewHolder(View v) {

            slot = (TextView) v.findViewById(R.id.slot);


        }
    }
}
