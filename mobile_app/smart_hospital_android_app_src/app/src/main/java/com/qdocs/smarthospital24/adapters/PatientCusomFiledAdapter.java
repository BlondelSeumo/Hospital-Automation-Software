package com.qdocs.smarthospital24.adapters;

import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.RecyclerView;

import com.qdocs.smarthospital24.DataTransferInterface;
import com.qdocs.smarthospital24.R;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class PatientCusomFiledAdapter extends RecyclerView.Adapter<PatientCusomFiledAdapter.MyViewHolder> {
    DataTransferInterface dtInterface;
    FragmentActivity context;
    ArrayList<String> nameList;
    ArrayList<String> typeList;
    ArrayList<String> idlist;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String customfield_data;
    JSONArray dlist=new JSONArray();
    JSONArray finaldata=new JSONArray();
    public PatientCusomFiledAdapter(FragmentActivity applicationContext, ArrayList<String> idlist,ArrayList<String> nameList, ArrayList<String> typeList,DataTransferInterface dtInterface) {
        this.nameList = nameList;
        this.typeList = typeList;
        this.idlist = idlist;
        this.context = applicationContext;
        this.dtInterface = dtInterface;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView field_name;
        EditText inputET,textareaET;
        CheckBox checkup_yes,checkup_no;
        LinearLayout inputlayout,textarealayout,checkboxlayout;

        public MyViewHolder(View view) {
            super(view);
            field_name = (TextView) view.findViewById(R.id.field_name);
            inputlayout = view.findViewById(R.id.input_layout);
            textarealayout = view.findViewById(R.id.textarea_layout);
            checkboxlayout = view.findViewById(R.id.checkbox_layout);
            textareaET = view.findViewById(R.id.textareaET);
            checkup_yes = view.findViewById(R.id.checkup_yes);
            checkup_no = view.findViewById(R.id.checkup_no);
            inputET = view.findViewById(R.id.inputET);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.adapter_customlist, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {

        holder.field_name.setText(nameList.get(position));
        if(typeList.get(position).equals("input")){
            customfield_data=holder.inputET.getText().toString();
            holder.inputET.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) { }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {}

                @Override
                public void afterTextChanged(Editable editable) {

                    JSONObject jsonObject = new JSONObject();
                    try {
                        jsonObject.put("custom_field_id", idlist.get(position));
                        jsonObject.put("field_value", holder.inputET.getText().toString());
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    JSONArray custom_fields = new JSONArray();
                    custom_fields.put(jsonObject);
                    dlist.put(jsonObject);
                    System.out.println("dlist="+dlist);
                    finaldata.put(jsonObject);
                    System.out.println("finaldata="+finaldata);
                    dtInterface.onSetValues(finaldata);
                }
            });

          holder.inputlayout.setVisibility(View.VISIBLE);
          holder.checkboxlayout.setVisibility(View.GONE);
          holder.textarealayout.setVisibility(View.GONE);
        }else if(typeList.get(position).equals("checkbox")){
          holder.inputlayout.setVisibility(View.GONE);
          holder.checkboxlayout.setVisibility(View.VISIBLE);
          holder.textarealayout.setVisibility(View.GONE);
        }else if(typeList.get(position).equals("textarea")){
            customfield_data=holder.textareaET.getText().toString();
            holder.textareaET.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) { }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) { }

                @Override
                public void afterTextChanged(Editable editable) {

                    JSONObject jsonObject = new JSONObject();
                    try {
                        jsonObject.put("custom_field_id", idlist.get(position));
                        jsonObject.put("field_value", holder.textareaET.getText().toString());
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    JSONArray custom_fields = new JSONArray();
                    custom_fields.put(jsonObject);
                    dlist.put(jsonObject);
                    System.out.println("dlist="+dlist);
                    finaldata.put(jsonObject);
                    System.out.println("finaldata="+finaldata);
                    dtInterface.onSetValues(finaldata);

                }
            });
          holder.inputlayout.setVisibility(View.GONE);
          holder.checkboxlayout.setVisibility(View.GONE);
          holder.textarealayout.setVisibility(View.VISIBLE);
        }
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("custom_field_id", idlist.get(position));
            jsonObject.put("field_value", customfield_data);
        } catch (JSONException e) {
            e.printStackTrace();
        }

    }
    @Override
    public int getItemCount() {
        return idlist.size();
    }

}