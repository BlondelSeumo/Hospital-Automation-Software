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

public class PatientOpdTestAdapter extends RecyclerView.Adapter<PatientOpdTestAdapter.MyViewHolder> {

    private Context context;
    private int[] othersHeaderArray;
    private ArrayList<String> testnamelist;

    public PatientOpdTestAdapter(Context applicationContext, ArrayList<String> testnamelist) {
        this.context = applicationContext;
        this.testnamelist = testnamelist;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView headerTV;
        TextView testname;

        public MyViewHolder(View view) {
            super(view);
            testname = (TextView) view.findViewById(R.id.name);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.test_row_item, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {

        Utility.setLocale(context, Utility.getSharedPreferences(context.getApplicationContext(), Constants.langCode));
        holder.testname.setText(testnamelist.get(position));

    }

    @Override
    public int getItemCount() {
        return testnamelist.size();
    }
}