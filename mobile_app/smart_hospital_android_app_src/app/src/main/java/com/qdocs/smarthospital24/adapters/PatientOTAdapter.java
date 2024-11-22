package com.qdocs.smarthospital24.adapters;

import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.model.CustomFieldModel;
import com.qdocs.smarthospital24.model.IpdOperationModel;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientOTAdapter extends RecyclerView.Adapter<PatientOTAdapter.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<IpdOperationModel> ipd_operation_list;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    long downloadID;
    Fragment fragment;
    String defaultDateFormat;
    TextView referenceno,operation_name,date,case_id,operation_categ,consulatntdoc,assistantconsulatnt1,assistantconsultant2,anesthetist,
            anesthesiatype,ot_technician,ot_assistant,remark,resultTv;
    public PatientOTAdapter(FragmentActivity fragmentActivity, ArrayList<IpdOperationModel> ipd_operation_list, Fragment fragment){
        this.context = fragmentActivity;
        this.ipd_operation_list = ipd_operation_list;
        this.fragment = fragment;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        public TextView billno, id, operation_name , operation_type, technician;
        TextView  opeartion_date,name,charge;
        ImageView downloadBtn;
        LinearLayout detailsBtn;
        public CardView containerView;
        RelativeLayout headLay;
        RecyclerView recyclerview;

        public MyViewHolder(View view) {
            super(view);
            billno = (TextView) view.findViewById(R.id.adapter_patient_ot_billno);
            operation_name = (TextView) view.findViewById(R.id.adapter_patient_ot_operationname);
            operation_type = (TextView) view.findViewById(R.id.adapter_patient_pathology_opaerationtype);
            technician = (TextView) view.findViewById(R.id.adapter_patient_ot_technician);
            opeartion_date = (TextView) view.findViewById(R.id.adapter_patient_ot_operationdate);
            detailsBtn = (LinearLayout) view.findViewById(R.id.adapter_patient_ot_detailsBtn);
            headLay = (RelativeLayout)view.findViewById(R.id.adapter_patient_ot_headLayout);
            recyclerview = (RecyclerView)view.findViewById(R.id.recyclerview);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ot, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        final IpdOperationModel ipdOperationModel=ipd_operation_list.get(position);
        final String currency = Utility.getSharedPreferences(context.getApplicationContext(), Constants.currency);
        //DECORATE
        holder.headLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));

        holder.billno.setText("OTRN"+ipdOperationModel.getId());
        holder.operation_name.setText(ipdOperationModel.getOperation());
        holder.operation_type.setText(ipdOperationModel.getCategory());
        holder.technician.setText(ipdOperationModel.getOt_technician());
        holder.opeartion_date.setText(ipdOperationModel.getDate());

        ArrayList<CustomFieldModel> customList = ipdOperationModel.getCustomfield();
        System.out.println("customList"+customList);
        CustomlistAdapter adapter = new CustomlistAdapter(context,customList,fragment);
        holder.recyclerview.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.VERTICAL,false));
        holder.recyclerview.setItemAnimator(new DefaultItemAnimator());
        holder.recyclerview.setAdapter(adapter);


        holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View viewClick) {
                LayoutInflater inflater= LayoutInflater.from(context);
                View view = inflater.inflate(R.layout.fragment_ipd_operate_bottom_sheet, null);
                view.setMinimumHeight(500);

                referenceno = view.findViewById(R.id.referenceno);
                operation_name= view.findViewById(R.id.operation_name);
                date= view.findViewById(R.id.date);
                case_id= view.findViewById(R.id.case_id);
                consulatntdoc= view.findViewById(R.id.consulatntdoc);
                operation_categ= view.findViewById(R.id.operation_categ);
                assistantconsulatnt1= view.findViewById(R.id.assistantconsulatnt1);
                assistantconsultant2= view.findViewById(R.id.assistantconsultant2);
                anesthetist= view.findViewById(R.id.anesthetist);
                anesthesiatype= view.findViewById(R.id.anesthesiatype);
                ot_technician= view.findViewById(R.id.ot_technician);
                ot_assistant= view.findViewById(R.id.ot_assistant);
                remark= view.findViewById(R.id.remark);
                resultTv= view.findViewById(R.id.result);
                TextView headerTV= view.findViewById(R.id.patientpres_bottomSheet__header);
                ImageView crossBtn = view.findViewById(R.id.patientpres_bottomSheet__crossBtn);
                case_id.setText(ipdOperationModel.getCase_id());
                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    params.put("operation_id", ipdOperationModel.getId());
                    JSONObject obj=new JSONObject(params);
                    Log.e("details params ", obj.toString());
                    getDataFromApi(obj.toString());
                }else {
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

                headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                headerTV.setText(context.getString(R.string.ot_details));
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


    }
    @Override
    public int getItemCount() {
        return ipd_operation_list.size();
    }

    private void getDataFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getipdoperationdetailUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONObject dataArray = obj.getJSONObject("result");
                        referenceno.setText("OTRN"+dataArray.getString("id"));
                        operation_name.setText(dataArray.getString("operation"));
                        operation_categ.setText(dataArray.getString("category_name"));
                        date.setText(Utility.parseDate("yyyy-MM-dd hh:mm", defaultDateFormat,dataArray.getString("date")));
                        consulatntdoc.setText(dataArray.getString("name")+" "+dataArray.getString("surname")+" ("+dataArray.getString("employee_id")+")");
                        assistantconsulatnt1.setText(dataArray.getString("ass_consultant_1"));
                        assistantconsultant2.setText(dataArray.getString("ass_consultant_2"));
                        anesthetist.setText(dataArray.getString("anesthetist"));
                        anesthesiatype.setText(dataArray.getString("anaethesia_type"));
                        ot_technician.setText(dataArray.getString("ot_technician"));
                        ot_assistant.setText(dataArray.getString("ot_assistant"));
                        remark.setText(dataArray.getString("remark"));
                        resultTv.setText(dataArray.getString("result"));

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(context, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(context.getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(context.getApplicationContext(), "accessToken"));
                Log.e("Headers", headers.toString());
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }
            @Override
            public byte[] getBody() throws AuthFailureError {
                try {
                    return requestBody == null ? null : requestBody.getBytes("utf-8");
                } catch (UnsupportedEncodingException uee) {
                    VolleyLog.wtf("Unsupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");
                    return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(context);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue

    }
}
