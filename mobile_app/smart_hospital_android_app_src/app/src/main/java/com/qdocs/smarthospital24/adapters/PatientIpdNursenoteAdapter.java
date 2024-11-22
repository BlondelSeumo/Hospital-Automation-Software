package com.qdocs.smarthospital24.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.IpdNurseNoteModel;
import com.qdocs.smarthospital24.model.StaffCommentModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;
import java.util.List;

public class PatientIpdNursenoteAdapter extends RecyclerView.Adapter<PatientIpdNursenoteAdapter.MyViewHolder> {

    private FragmentActivity context;
    private List<String> titlelist;
    private List<String> dateList;
    private List<String> noteList;
    private List<String> idlist;
    private List<String> commentlist;
    private List<IpdNurseNoteModel> nurse_note_list;
    Fragment fragment;
    long downloadID;

    public PatientIpdNursenoteAdapter(FragmentActivity activity, ArrayList<IpdNurseNoteModel> nurse_note_list,
                                      Fragment fragment) {

        this.context = activity;
        this.nurse_note_list = nurse_note_list;
        this.fragment = fragment;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        //TODO delete un-necessasry code
        public TextView dateTV, nameTV, noteTV,commentTV, titleTV, descTV;
        public ImageView downloadBtn;
        View lineView;
        LinearLayout mainlayout,layout;
        RelativeLayout clockBtn;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            dateTV = view.findViewById(R.id.adapter_patientnursenote_dateTV);
            nameTV = view.findViewById(R.id.adapter_patientnursenote_nameTV);
            noteTV = view.findViewById(R.id.adapter_patientnursenote_note);
            commentTV = view.findViewById(R.id.adapter_patientnursenote_comment);
            lineView = view.findViewById(R.id.adapter_nursenote_line);
            mainlayout = view.findViewById(R.id.mainlayout);
            layout = view.findViewById(R.id.layout);
            recyclerview = view.findViewById(R.id.recyclerview);
            downloadBtn = view.findViewById(R.id.adapter_patientnursenote_downloadBtn);
            clockBtn = view.findViewById(R.id.adapter_patientnursenote_clockBtn);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_nursenote, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        IpdNurseNoteModel nurseNoteModel=nurse_note_list.get(position);
        holder.dateTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
        //holder.dateTV.setText(Utility.parseDate("yyyy-MM-dd", Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat"), timeline_dateList.get(position)));

            holder.dateTV.setText(nurseNoteModel.getDate());
            holder.nameTV.setText(nurseNoteModel.getName());
            holder.noteTV.setText(nurseNoteModel.getNote());
            holder.commentTV.setText(nurseNoteModel.getComment());

        ArrayList<StaffCommentModel> commentList = nurseNoteModel.getStaffcomment();
        System.out.println("customList"+commentList);
        StaffCommentAdapter adapter = new StaffCommentAdapter(context,commentList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(adapter);



        if(position == nurse_note_list.size()-1) {
            holder.clockBtn.setVisibility(View.VISIBLE);
        }else{
            holder.clockBtn.setVisibility(View.GONE);
        }


    };

    @Override
    public int getItemCount() {
        return nurse_note_list.size();
    }
}