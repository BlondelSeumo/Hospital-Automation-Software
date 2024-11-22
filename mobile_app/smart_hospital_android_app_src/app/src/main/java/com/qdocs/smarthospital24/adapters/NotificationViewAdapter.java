package com.qdocs.smarthospital24.adapters;

import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import com.daimajia.swipe.SwipeLayout;
import com.google.android.material.snackbar.Snackbar;
import com.qdocs.smarthospital24.NotificationModel;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.patient.NotificationList;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.DatabaseHelper;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;

public class NotificationViewAdapter extends BaseAdapter {
    private NotificationList context;
    private ArrayList<NotificationModel> ModelArrayList;
    ListView listView;
    View view;
    public NotificationViewAdapter(NotificationList context, ArrayList<NotificationModel> ModelArrayList, ListView listView) {
        this.context = context;
        this.ModelArrayList = ModelArrayList;
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
        ViewHolder holder;
        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
        // If holder not exist then locate all view from UI file.
        if (convertView == null) {
            // inflate UI from XML file
            convertView = inflater.inflate(R.layout.list_row, parent, false);
            holder = new ViewHolder(convertView);
            convertView.setTag(holder);
        } else {
            // if holder created, get tag from view
            holder = (ViewHolder) convertView.getTag();
        }
        System.out.println("Notification Data:"+ModelArrayList.get(position).getDate());

        String defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
        holder.name.setText(ModelArrayList.get(position).getName());
        holder.date.setText(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,ModelArrayList.get(position).getDate()));
        holder.message.setText(ModelArrayList.get(position).getLocation());
        holder.headLayout.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        holder.btnDelete.setOnClickListener(onDeleteListener(position, holder));
        return convertView;
    }

    private View.OnClickListener onDeleteListener(final int position, final ViewHolder holder) {
        return new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                DatabaseHelper dataBaseHelpers = new DatabaseHelper(context.getApplicationContext());
                dataBaseHelpers.deletenotification(ModelArrayList.get(position).getId()) ;
                Snackbar snackbar = Snackbar.make(listView, "Deleted Successfully !", Snackbar.LENGTH_SHORT);
                snackbar.show();
                ModelArrayList.remove(position);
                notifyDataSetChanged();
                holder.swipeLayout.close();
            }
        };
    }

    private class ViewHolder {
        private TextView name,date,message,id;
        private View btnDelete;
        private SwipeLayout swipeLayout;
        LinearLayout headLayout;

        public ViewHolder(View v) {
            swipeLayout = (SwipeLayout)v.findViewById(R.id.swipe_layout);
            btnDelete = v.findViewById(R.id.delete);
            name = (TextView) v.findViewById(R.id.name);
            date = (TextView) v.findViewById(R.id.date);
            message = (TextView) v.findViewById(R.id.message);
            headLayout = (LinearLayout) v.findViewById(R.id.headLayout);
            swipeLayout.setShowMode(SwipeLayout.ShowMode.LayDown);
        }
    }
}
