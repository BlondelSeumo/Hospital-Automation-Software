package com.qdocs.smarthospital24.adapters;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Paint;
import android.util.Log;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.fragment.app.FragmentActivity;
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
import com.qdocs.smarthospital24.patient.PatientTasks;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientTaskAdapter extends RecyclerView.Adapter<PatientTaskAdapter.MyViewHolder> implements DatePickerDialog.OnDateSetListener {

    private FragmentActivity context;
    private ArrayList<String> taskIdList;
    private ArrayList<String> taskTitleList;
    private ArrayList<String> taskStatusList;
    private ArrayList<String> taskDateList;
     TextView dateTV;
    private boolean isDateSelected = false;
    private ArrayList<String> showDateList;
    String  date="";
    public String defaultDateFormat;
    private Map<String, String> deleteTaskParams = new Hashtable<String, String>();
    private Map<String, String> updateTaskParams = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    public PatientTaskAdapter(FragmentActivity fragmentActivity, ArrayList<String> taskIdList,ArrayList<String> taskTitleList,
                              ArrayList<String> taskStatusList, ArrayList<String> taskDateList) {

        this.context = fragmentActivity;
        this.taskIdList = taskIdList;
        this.taskTitleList = taskTitleList;
        this.taskStatusList = taskStatusList;
        this.taskDateList = taskDateList;
        this.showDateList = showDateList;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        TextView taskTV, taskDateTV;
        CheckBox taskCheckbox;
        RelativeLayout header;
        ImageView delete,edit;

        public MyViewHolder(View view) {
            super(view);
            taskTV = view.findViewById(R.id.adapter_task_TaskNameTV);
            taskCheckbox = view.findViewById(R.id.adapter_task_checkbox);
            header = view.findViewById(R.id.adapter_task_header);
            taskDateTV = view.findViewById(R.id.adapter_task_TaskDateTV);
            delete = view.findViewById(R.id.delete);
            edit = view.findViewById(R.id.edit);
        }
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_patient_tasks, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, final int position) {
        defaultDateFormat = Utility.getSharedPreferences(context.getApplicationContext(), "dateFormat");
        //DECORATE
        holder.header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.secondaryColour)));
        //setCheckBoxColor(holder.taskCheckbox, R.color.colorAccent, Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));

        holder.taskTV.setText(taskTitleList.get(position));
        holder.taskDateTV.setText( Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat,taskDateList.get(position)));

        if(taskStatusList.get(position).equals("yes")) {
            holder.taskCheckbox.setChecked(true);
            holder.taskTV.setPaintFlags(holder.taskTV.getPaintFlags() | Paint.STRIKE_THRU_TEXT_FLAG);
            holder.taskTV.setTextColor(Color.parseColor("#668858"));
            holder.taskCheckbox.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton compoundButton, boolean checkedStatus) {
                    updateTaskParams.put("eventId", taskIdList.get(position));
                    JSONObject obj=new JSONObject(updateTaskParams);
                    Log.e("change status params ", obj.toString());
                    if(Utility.isConnectingToInternet(context.getApplicationContext())){
                        uncheckStatusApi(obj.toString());//Api Call
                        System.out.println("uncheckStatusApi Call");
                    }else{
                        makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }

                    context.finish();
                    Intent intent = new Intent(context, PatientTasks.class);
                    context.startActivity(intent);
                 }
            });
        } else {
            holder.taskCheckbox.setChecked(false);
            holder.taskTV.setTextColor(Color.parseColor("#FF7400"));

            holder.taskCheckbox.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton compoundButton, boolean checkedStatus) {

                    updateTaskParams.put("eventId", taskIdList.get(position));
                    JSONObject obj=new JSONObject(updateTaskParams);
                    Log.e("change status params ", obj.toString());
                    if(Utility.isConnectingToInternet(context.getApplicationContext())){
                        checkStatusApi(obj.toString());//Api Call
                    }else{
                        makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }

                    context.finish();
                    Intent intent = new Intent(context, PatientTasks.class);
                    context.startActivity(intent);
                }
            });
        }
        holder.delete.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                android.app.AlertDialog.Builder builder = new android.app.AlertDialog.Builder(context);
                builder.setCancelable(false);
                builder.setMessage(R.string.deleteMsg);
                builder.setTitle("");
                builder.setPositiveButton(R.string.yes, new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {
                        deleteTaskParams.put("eventId", taskIdList.get(position));
                        JSONObject obj=new JSONObject(deleteTaskParams);
                        Log.e("params ", obj.toString());
                        if(Utility.isConnectingToInternet(context.getApplicationContext())){
                            deleteTaskApi(obj.toString());//Api Call
                        }else{
                            makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                        context.finish();
                        Intent intent = new Intent(context, PatientTasks.class);
                        context.startActivity(intent);
                    }
                });
                builder.setNegativeButton(R.string.no, new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                    }
                });
                android.app.AlertDialog alert = builder.create();
                alert.show();
            }
        });

        holder.edit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                showAddDialog(context,taskIdList.get(position),taskTitleList.get(position),taskDateList.get(position));
            }
        });

        holder.header.setOnCreateContextMenuListener(new View.OnCreateContextMenuListener() {
            @Override
            public void onCreateContextMenu(ContextMenu contextMenu, View view, ContextMenu.ContextMenuInfo contextMenuInfo) {
                MenuItem delete = contextMenu.add(0, position, 0, "Delete");
                delete.setOnMenuItemClickListener(onDeleteMenu);
            }
        });
    }
    private void showAddDialog(final Context context, final String eventid, String Title, final String Dates) {

        final Dialog dialog = new Dialog(context);
        dialog.setContentView(R.layout.edit_task_dialog);
        RelativeLayout headerLay = (RelativeLayout) dialog.findViewById(R.id.addTask_dialog_header);
         RelativeLayout dateBtn = (RelativeLayout) dialog.findViewById(R.id.addTask_dialog_dateBtn);
        ImageView closeBtn = (ImageView) dialog.findViewById(R.id.addTask_dialog_crossIcon);
        dateTV = dialog.findViewById(R.id.addTask_dialog_dateTV);
        final EditText titleET = dialog.findViewById(R.id.addTask_dialog_titleET);
        Button submitBtn = dialog.findViewById(R.id.addTask_dialog_submitBtn);
        titleET.setText(Title);
        dateTV.setText(Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat,Dates));
        Utility.parseDate("yyyy-MM-dd HH:mm:ss", defaultDateFormat,Dates);
        final Calendar c = Calendar.getInstance();
        int startYear = c.get(Calendar.YEAR);
        int starthMonth = c.get(Calendar.MONTH);
        int startDay = c.get(Calendar.DAY_OF_MONTH);
        final DatePickerDialog datePickerDialog = new DatePickerDialog(context, PatientTaskAdapter.this, startYear, starthMonth, startDay);
        dateTV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                datePickerDialog.show();
            }
        });


        submitBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                deleteTaskParams.put("eventId", eventid);
                deleteTaskParams.put("task_title", titleET.getText().toString());
                if(date.equals("")){
                    date=Dates;
                    deleteTaskParams.put("task_date", date);
                }else{
                    deleteTaskParams.put("task_date", date);
                }
                JSONObject obj=new JSONObject(deleteTaskParams);
                Log.e("params ", obj.toString());
                if(Utility.isConnectingToInternet(context.getApplicationContext())){
                    editTaskApi(obj.toString());
                }else{
                    makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }

               dialog.dismiss();
            }
        });
        closeBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dialog.dismiss();
            }
        });
        //DECORATE
        headerLay.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
        submitBtn.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(context.getApplicationContext(), Constants.primaryColour)));
        //DECORATE
        dialog.show();
    }
    private final MenuItem.OnMenuItemClickListener onDeleteMenu = new MenuItem.OnMenuItemClickListener() {
        @Override
        public boolean onMenuItemClick(MenuItem item) {
            Log.e("eventId", item.getItemId()+"..");
            deleteTaskParams.put("eventId", taskIdList.get(item.getItemId()));
            JSONObject obj=new JSONObject(deleteTaskParams);
            Log.e("params ", obj.toString());
            if(Utility.isConnectingToInternet(context.getApplicationContext())){
                deleteTaskApi(obj.toString());//Api Call
            }else{
                makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
            }
            ((Activity)context).finish();
            Intent intent = new Intent(context, PatientTasks.class);
            context.startActivity(intent);
            return true;
        }
    };

    @Override
    public int getItemCount() {
        return taskIdList.size();
    }

    private void deleteTaskApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(context);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl") + Constants.deleteTaskUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        //TODO success
                        String success = object.getString("status");
                        Toast.makeText(context.getApplicationContext(), "Successfully Deleted", Toast.LENGTH_SHORT).show();
                        if (success.equals("1")) {
                            context.finish();

                            context.startActivity(context.getIntent());
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
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

    private void checkStatusApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(context);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl") + Constants.checkCompleteTaskUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        String success = object.getString("status");
                        Toast.makeText(context.getApplicationContext(), object.getString("message"), Toast.LENGTH_SHORT).show();
                        if (success.equals("success")) {
                            context.finish();
                            //context.startActivity(context.getIntent());
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
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
        RequestQueue requestQueue = Volley.newRequestQueue(context); //Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }
    private void uncheckStatusApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(context);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl") + Constants.uncheckCompleteTaskUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        String success = object.getString("status");
                        Toast.makeText(context.getApplicationContext(), object.getString("message"), Toast.LENGTH_SHORT).show();
                        if (success.equals("success")) {
                            context.finish();
                            //context.startActivity(context.getIntent());
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
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
        RequestQueue requestQueue = Volley.newRequestQueue(context); //Creating a Request Queue
        requestQueue.add(stringRequest);  //Adding request to the queue
    }

    private void editTaskApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(context);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl") + Constants.markTaskUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        String success = object.getString("status");
                        Toast.makeText(context.getApplicationContext(),"Edit Successfully", Toast.LENGTH_SHORT).show();
                        if (success.equals("success")) {
                            context.finish();
                            context.startActivity(context.getIntent());
                        }/*else{
                            Toast.makeText(context, ""+success, Toast.LENGTH_SHORT).show();
                        }*/
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
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
        RequestQueue requestQueue = Volley.newRequestQueue(context); //Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }

    @Override
    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
        String dates = year+"-"+(++monthOfYear)+"-"+dayOfMonth;

        System.out.println("Date=="+Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dates));
        dateTV.setText(Utility.parseDate("yyyy-MM-dd", defaultDateFormat,dates));
        date=dates;
        isDateSelected = true;
    }
}
