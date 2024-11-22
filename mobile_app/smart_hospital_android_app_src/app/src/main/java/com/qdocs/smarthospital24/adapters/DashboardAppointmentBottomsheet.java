package com.qdocs.smarthospital24.adapters;

import android.app.ProgressDialog;
import android.util.Log;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
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

public class DashboardAppointmentBottomsheet extends RecyclerView.Adapter<DashboardAppointmentBottomsheet.MyViewHolder> {

    private FragmentActivity context;
    private ArrayList<String> appointIdList;
    private ArrayList<String> appointment_statusList;
    private ArrayList<String> appointment_noList;
    private ArrayList<String> appointDateList;
    private ArrayList<String> appointment_doctorlist;
    private ArrayList<String> appointment_messageList;
    private ArrayList<String> eventDescList;
    private Map<String, String> deleteTaskParams = new Hashtable<String, String>();
    private Map<String, String> updateTaskParams = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    public DashboardAppointmentBottomsheet(FragmentActivity fragmentActivity, ArrayList<String> appointIdList,
                                           ArrayList<String> appointment_statusList, ArrayList<String> appointment_noList,
                                           ArrayList<String> appointDateList, ArrayList<String> appointment_messageList, ArrayList<String> appointment_doctorlist) {
        this.context = fragmentActivity;
        this.appointIdList = appointIdList;
        this.appointment_statusList = appointment_statusList;
        this.appointment_noList = appointment_noList;
        this.appointDateList = appointDateList;
        this.appointment_doctorlist = appointment_doctorlist;
        this.appointment_messageList = appointment_messageList;

    }
    public class MyViewHolder extends RecyclerView.ViewHolder {

        TextView AppointTV, Appointstatus, AppointDateTV,AppointMessage;
        RelativeLayout header;
        ImageView icon;

        public MyViewHolder(View view) {
            super(view);
            AppointTV = view.findViewById(R.id.adapter_patient_AppointTV);
            Appointstatus = view.findViewById(R.id.adapter_patient_status);
            header = view.findViewById(R.id.adapter_patient_header);
            AppointDateTV = view.findViewById(R.id.adapter_patient_appointDateTV);
            icon = view.findViewById(R.id.adapter_patient_Icon);

        }
    }
    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.adapter_dashboard_appoint_bottomsheet, parent, false);
        return new MyViewHolder(itemView);
    }
    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {


        holder.AppointDateTV.setText(appointDateList.get(position));
        if(appointment_statusList.get(position).equals("approved")) {
            holder.Appointstatus.setBackgroundResource(R.drawable.green_border);
            holder.AppointTV.setText(context.getString(R.string.appointmentprefix)+appointment_noList.get(position));
            holder.Appointstatus.setText(context.getApplicationContext().getString(R.string.approve));
        } else if (appointment_statusList.get(position).equals("pending")){
            holder.AppointTV.setText(appointment_doctorlist.get(position));
            holder.Appointstatus.setBackgroundResource(R.drawable.orange_border);
            holder.Appointstatus.setText(context.getApplicationContext().getString(R.string.pending));
        } else if (appointment_statusList.get(position).equals("cancel")){
            holder.AppointTV.setText(appointment_doctorlist.get(position));
           holder.Appointstatus.setBackgroundResource(R.drawable.red_border);
            holder.Appointstatus.setText(context.getApplicationContext().getString(R.string.cancel));
        }

        holder.header.setOnCreateContextMenuListener(new View.OnCreateContextMenuListener() {
            @Override
            public void onCreateContextMenu(ContextMenu contextMenu, View view, ContextMenu.ContextMenuInfo contextMenuInfo) {
                MenuItem delete = contextMenu.add(0, position, 0, "Delete");
                delete.setOnMenuItemClickListener(onDeleteMenu);
            }
        });
    }

    private final MenuItem.OnMenuItemClickListener onDeleteMenu = new MenuItem.OnMenuItemClickListener() {
        @Override
        public boolean onMenuItemClick(MenuItem item) {
            if(Utility.isConnectingToInternet(context.getApplicationContext())){
                Log.e("itemId", item.getItemId()+"..");
                deleteTaskParams.put("appointmentId", appointIdList.get(item.getItemId()));
                JSONObject obj=new JSONObject(deleteTaskParams);
                Log.e("params", obj.toString());
                deleteTaskApi(obj.toString());
            }else{
                makeText(context.getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
            }

            return true;
        }
    };

    @Override
    public int getItemCount() {
        return appointIdList.size();
    }

    private void deleteTaskApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(context);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(context.getApplicationContext(), "apiUrl") + Constants.deleteAppointmentUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);

                        Toast.makeText(context, "Successfully Deleted !!!", Toast.LENGTH_SHORT).show();
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


}
