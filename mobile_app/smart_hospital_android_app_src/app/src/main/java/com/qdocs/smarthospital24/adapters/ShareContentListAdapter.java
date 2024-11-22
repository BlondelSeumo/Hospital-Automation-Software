package com.qdocs.smarthospital24.adapters;

import static android.widget.Toast.makeText;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.DownloadContentModel;
import com.qdocs.smarthospital24.model.ShareContentModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class ShareContentListAdapter extends RecyclerView.Adapter<ShareContentListAdapter.MyViewHolder> {

    Context context;
    Fragment fragment;
    ArrayList<ShareContentModel> appointment_detail;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    LinearLayout chequedate_layout,chequeno_layout,attachment_layout;
    TextView appoinment_doctor,appoinment_shift,appoinment_amount,appoinment_slot,appoinment_serialno,appoinment_paymentmode,appoinment_ChequeNo,appoinment_ChequeDate,appoinment_Source,
            appoinment_TransactionID,appoinment_note,appointmentno,appointmentdate,appoinment_liveconsultant;
    LinearLayout downloadBtn;
    long downloadID;
    public ShareContentListAdapter(Context context, ArrayList<ShareContentModel> appointment_detail, Fragment fragment) {

        this.appointment_detail = appointment_detail;
        this.context = context;
        this.fragment = fragment;


    }
    public class MyViewHolder extends RecyclerView.ViewHolder {
    public TextView title,shareddate,validdate,sharedby,sorry_link;
    ImageView delete_button;
    public CardView viewContainer;
    RelativeLayout relative;
    RecyclerView recyclerview;
    LinearLayout detailsBtn;

    public MyViewHolder(View view) {
        super(view);
       title = (TextView) view.findViewById(R.id.adapter_title);
       shareddate = (TextView) view.findViewById(R.id.adapter_shareddate);
       validdate = (TextView) view.findViewById(R.id.adapter_validdate);
       sharedby = (TextView) view.findViewById(R.id.adapter_sharedby);
        sorry_link = (TextView) view.findViewById(R.id.sorry_link);
        detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_detailsBtn);
        recyclerview = (RecyclerView) view.findViewById(R.id.recyclerview);
        relative = (RelativeLayout) view.findViewById(R.id.relative);

    }
}
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_sharecontent_list, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(MyViewHolder holder,final int position) {
       final ShareContentModel shareContentModel=appointment_detail.get(position);

        holder.title.setText(shareContentModel.getTitle());
        holder.shareddate.setText(shareContentModel.getShare_date());
        holder.validdate.setText(shareContentModel.getValid_upto());
        holder.sharedby.setText(shareContentModel.getSharedby());


                Calendar c = Calendar.getInstance();
                final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                final String getCurrentDate = sdf.format(c.getTime());
                final String getsvaliddate=shareContentModel.getDate();
                if (getCurrentDate.compareTo(getsvaliddate)>0) {
                    holder.recyclerview.setVisibility(View.GONE);
                    holder.sorry_link.setVisibility(View.VISIBLE);
                 //   holder.sorry_link.setText(shareContentModel.getTitle());
                    System.out.println("helloo current date "+getCurrentDate);
                    System.out.println("helloo valid date "+getsvaliddate);
                    System.out.println("helloo valid date Expired");
                }else  if (getCurrentDate.compareTo(getsvaliddate)<0) {
                    System.out.println("helloo current date "+getCurrentDate);
                    System.out.println("helloo valid date "+getsvaliddate);
                    holder.recyclerview.setVisibility(View.VISIBLE);
                    ArrayList<DownloadContentModel> customList = shareContentModel.getContent();
                    System.out.println("customList"+customList);
                    DownloadContentlistAdapter downloadContentlistAdapter = new DownloadContentlistAdapter(context,customList,fragment);
                    holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
                    holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
                    holder.recyclerview.setAdapter(downloadContentlistAdapter);
                    holder.sorry_link.setVisibility(View.GONE);

                }else{
                    holder.recyclerview.setVisibility(View.GONE);
                    holder.sorry_link.setVisibility(View.GONE);
                }

        holder.relative.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
    }
    @Override
    public int getItemCount() {
        return appointment_detail.size();
    }




}