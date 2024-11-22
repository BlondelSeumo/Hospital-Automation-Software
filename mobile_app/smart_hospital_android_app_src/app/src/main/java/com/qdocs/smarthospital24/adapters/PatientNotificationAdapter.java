package com.qdocs.smarthospital24.adapters;

import android.graphics.Color;

import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.fragment.app.FragmentActivity;

import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientNotificationAdapter extends BaseAdapter {

    private FragmentActivity context;
    private ArrayList<String> noticeTitleId;
    private ArrayList<String> noticeTitleList;
    private ArrayList<String> noticeDateList;
    private ArrayList<String> noticeDescList;
    private ArrayList<String> readList;


    public PatientNotificationAdapter(FragmentActivity fragmentActivity, ArrayList<String> noticeTitleId,
                                      ArrayList<String> noticeTitleList, ArrayList<String> noticeDateList,
                                      ArrayList<String> noticeDescList,
                                      ArrayList<String> readList) {
        this.context = fragmentActivity;
        this.noticeTitleId = noticeTitleId;
        this.noticeTitleList = noticeTitleList;
        this.noticeDateList = noticeDateList;
        this.noticeDescList = noticeDescList;
        this.readList = readList;
    }

    @Override
    public int getCount() {
        return noticeTitleId.size();
    }

    @Override
    public Object getItem(int i) {
        return null;
    }

    @Override
    public long getItemId(int i) {
        return 0;
    }

    @Override
    public View getView(final int position, View view, ViewGroup viewGroup) {
        View rowView = view;
        ViewHolder viewHolder = null;

        if (rowView == null) {

            LayoutInflater inflater = LayoutInflater.from(context);
            rowView = inflater.inflate(R.layout.adapter_patient_notification, viewGroup, false);
            viewHolder = new ViewHolder();

            viewHolder.viewBtn = rowView.findViewById(R.id.patientNotificationAdapter_viewBtn);
            viewHolder.notifTitle = (TextView) rowView.findViewById(R.id.patientNotificationAdapter_titleTV);
            viewHolder.notifDate = (TextView) rowView.findViewById(R.id.patientNotificationAdapter_dateHeaderTV);
            viewHolder.mainlayout = (LinearLayout) rowView.findViewById(R.id.mainlayout);

            viewHolder.viewBtn.setTag(position);
            viewHolder.notifTitle.setTag(position);
            viewHolder.notifDate.setTag(position);

        }else{
            viewHolder  = (ViewHolder) rowView.getTag();
        }

        viewHolder.notifTitle.setText(noticeTitleList.get(position));
        viewHolder.notifDate.setText(noticeDateList.get(position));


        viewHolder.viewBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_notification_bottom_sheet, null);
                view.setMinimumHeight(500);

                TextView headerTV = view.findViewById(R.id.patientNotification_bottomSheet__header);
                ImageView crossBtn = view.findViewById(R.id.patientNotification_bottomSheet__crossBtn);
                TextView notificationDetailTV = view.findViewById(R.id.patientNotification_bottomSheet_descTV);

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));

                headerTV.setText(noticeTitleList.get(position));
                notificationDetailTV.setText(Html.fromHtml(noticeDescList.get(position)));


                final BottomSheetDialog dialog = new BottomSheetDialog(context);
                dialog.setContentView(view);
                BottomSheetBehavior mBehavior = BottomSheetBehavior.from((View) view.getParent());
                mBehavior.setPeekHeight(800);
                dialog.show();

                crossBtn.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        dialog.dismiss();
                    }
                });

            }
        });

        rowView.setTag(viewHolder);
        return rowView;
    }

    static class ViewHolder {
        public TextView notifTitle, notifDate;
        public LinearLayout viewBtn,mainlayout;
    }
}
