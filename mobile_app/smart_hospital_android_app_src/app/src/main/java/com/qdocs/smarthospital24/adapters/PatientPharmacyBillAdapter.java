package com.qdocs.smarthospital24.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.ArrayList;


public class PatientPharmacyBillAdapter extends RecyclerView.Adapter<PatientPharmacyBillAdapter.MyViewHolder> {

    private Context context;
    private ArrayList<String> medicinenamelist;
    private ArrayList<String> unitlist;
    private ArrayList<String> Quantitylist;
    private ArrayList<String> batchnolist;
    private ArrayList<String> expirydate;
    private ArrayList<String> salepricelist;
    private ArrayList<String> detail_idlist;

    public PatientPharmacyBillAdapter(Context applicationContext, ArrayList<String> medicinenamelist,ArrayList<String> unitlist,
                                      ArrayList<String> Quantitylist,ArrayList<String> batchnolist, ArrayList<String> expirydate,
                                      ArrayList<String> salepricelist,ArrayList<String> detail_idlist) {

        this.context = applicationContext;
        this.medicinenamelist = medicinenamelist;
        this.unitlist = unitlist;
        this.Quantitylist = Quantitylist;
        this.batchnolist = batchnolist;
        this.expirydate = expirydate;
        this.salepricelist = salepricelist;
        this.detail_idlist = detail_idlist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView headerTV;
        TextView medicine,unit,quantity,batchno,expirydate,price;

        public MyViewHolder(View view) {
             super(view);
             medicine = (TextView) view.findViewById(R.id.medicine);
             unit = (TextView) view.findViewById(R.id.unit);
             quantity = (TextView) view.findViewById(R.id.quantity);
             batchno = (TextView) view.findViewById(R.id.batchno);
             expirydate = (TextView) view.findViewById(R.id.expirydate);
             price = (TextView) view.findViewById(R.id.price);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_pharmacy_bill, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.medicine.setText(medicinenamelist.get(position));
        holder.unit.setText(unitlist.get(position));
        holder.quantity.setText(Quantitylist.get(position));
        holder.batchno.setText(batchnolist.get(position));

        String date=expirydate.get(position);
        holder.expirydate.setText(Utility.parseDate("yyyy-MM-dd","MMM/yyyy",date));

        holder.price.setText(currency+salepricelist.get(position));

    }

    @Override
     public int getItemCount() {
        return detail_idlist.size();
    }

}