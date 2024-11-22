package com.qdocs.smarthospital24.adapters;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.MedicineModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.util.ArrayList;

public class PatientIpdDoseAdapter extends RecyclerView.Adapter<PatientIpdDoseAdapter.MyViewHolder> {

    private Context context;
    private int[] othersHeaderArray;
    private ArrayList<MedicineModel> medicinelist;
    Fragment fragment;

    public PatientIpdDoseAdapter(Context applicationContext, ArrayList<MedicineModel> medicinelist, Fragment fragment) {

        this.context = applicationContext;
        this.medicinelist = medicinelist;
        this.fragment = fragment;


    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView doseno;
        TextView doselist;

        public MyViewHolder(View view) {
            super(view);
            doseno = (TextView) view.findViewById(R.id.adapter_patien_doseno);
            doselist = (TextView) view.findViewById(R.id.doselist);

        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_ipd_doselist, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(final MyViewHolder holder, int position) {
            final MedicineModel medicineModel=medicinelist.get(position);
        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.doseno.setText(medicineModel.getMedicine_name());
        holder.doselist.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                final Dialog dialog = new Dialog(context);
                dialog.setContentView(R.layout.gridview_row);
                dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
                dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;
                final ImageView closeBtn = (ImageView) dialog.findViewById(R.id.dialog_crossIcon);
                final RelativeLayout header = dialog.findViewById(R.id.addappoint_dialog_header);
                final TextView headertext = dialog.findViewById(R.id.headertext);
                GridView doselist = dialog.findViewById(R.id.doselist);

                header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                DoselistAdapter doselistAdapter = new DoselistAdapter(context, medicineModel.getDoses(),doselist);
                doselist.setAdapter(doselistAdapter);

                closeBtn.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        dialog.dismiss();
                    }
                });
                dialog.show();

            }
        });




    }

    @Override
    public int getItemCount() {
        return medicinelist.size();
    }
}