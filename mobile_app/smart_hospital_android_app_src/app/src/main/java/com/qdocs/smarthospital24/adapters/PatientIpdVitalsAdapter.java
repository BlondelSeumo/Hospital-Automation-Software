package com.qdocs.smarthospital24.adapters;

import static android.widget.Toast.makeText;

import android.app.Dialog;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

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
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.List;
import java.util.Map;

public class PatientIpdVitalsAdapter extends RecyclerView.Adapter<PatientIpdVitalsAdapter.MyViewHolder> {

    private FragmentActivity context;
    private List<String> namelist;
    private List<String> unitList;
    private List<String> reference_rangeList;
    private List<String> idlist;
    VitalRangeAdapter vitalRangeAdapter;
    ArrayList<String> datelist = new ArrayList<String>();
    ArrayList<String> patient_range_list = new ArrayList<String>();

    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    long downloadID;
    String defaultDatetimeFormat,defaultDateFormat;

    public PatientIpdVitalsAdapter(FragmentActivity activity, ArrayList<String> namelist,
                                   ArrayList<String> unitList, ArrayList<String> reference_rangeList, ArrayList<String> idlist) {

        this.context = activity;
        this.namelist = namelist;
        this.unitList = unitList;
        this.reference_rangeList = reference_rangeList;
        this.idlist = idlist;

    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        //TODO delete un-necessasry code
        public TextView parameterTV, rangeTV;
        View lineView;
        LinearLayout detailsBtn,layout;
        RelativeLayout clockBtn;

        public MyViewHolder(View view) {
            super(view);
            parameterTV = view.findViewById(R.id.adapter_patientvital_parameterTV);
            rangeTV = view.findViewById(R.id.adapter_patientvital_rangeTV);
            detailsBtn = view.findViewById(R.id.adapter_patientvitals_detailsBtn);
            layout = view.findViewById(R.id.layout);


        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_ipd_vitals, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {


            holder.parameterTV.setText(namelist.get(position));
            holder.rangeTV.setText("("+reference_rangeList.get(position)+" "+unitList.get(position)+")");
            holder.detailsBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    final Dialog dialog = new Dialog(context);
                    dialog.setContentView(R.layout.vitalrangeadapter);
                    dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.FILL_PARENT);
                    dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;
                    final ImageView closeBtn = (ImageView) dialog.findViewById(R.id.dialog_crossIcon);
                    final RelativeLayout header = dialog.findViewById(R.id.addappoint_dialog_header);
                    final TextView headertext = dialog.findViewById(R.id.headertext);
                    final TextView vital_parameterTV = dialog.findViewById(R.id.vital_parameterTV);
                    headertext.setText(context.getApplicationContext().getString(R.string.vitals));
                    vital_parameterTV.setText(namelist.get(position));
                    final RecyclerView recyclerview = dialog.findViewById(R.id.recyclerview);
                    header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
                    vitalRangeAdapter = new VitalRangeAdapter(context, datelist, patient_range_list);
                    RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(context.getApplicationContext());
                    recyclerview.setLayoutManager(mLayoutManager);
                    recyclerview.setItemAnimator(new DefaultItemAnimator());
                    recyclerview.setAdapter(vitalRangeAdapter);

                    if (Utility.isConnectingToInternet(context.getApplicationContext())) {
                        params.put("patient_id", Utility.getSharedPreferences(context.getApplicationContext(), Constants.patient_id));
                        params.put("vital_id", idlist.get(position));
                        JSONObject obj = new JSONObject(params);
                        Log.e(" details params ", obj.toString());
                        getVitalFromApi(obj.toString());
                    } else {
                        makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }
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

    private void getVitalFromApi (String bodyParams) {

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl")+Constants.getPatientVitalByPatientAndVitalIdUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {

                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);

                        JSONArray patient_vitalarray = obj.getJSONArray("patient_vital");
                        datelist.clear();
                        patient_range_list.clear();
                        String defaultDatetimeFormat = Utility.getSharedPreferences(context.getApplicationContext(), "datetimeFormat");
                        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
                        if(patient_vitalarray.length() != 0) {
                            for(int i = 0; i < patient_vitalarray.length(); i++) {
                                datelist.add(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDatetimeFormat,patient_vitalarray.getJSONObject(i).getString("messure_date")));
                                patient_range_list.add(patient_vitalarray.getJSONObject(i).getString("patient_range"));
                            }
                            vitalRangeAdapter.notifyDataSetChanged();
                        } else {
                            //Toast.makeText(getApplicationContext(), getApplicationContext().getString(R.string.noData), Toast.LENGTH_SHORT).show();
                        }
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

    @Override
    public int getItemCount() {
        return idlist.size();
    }

}