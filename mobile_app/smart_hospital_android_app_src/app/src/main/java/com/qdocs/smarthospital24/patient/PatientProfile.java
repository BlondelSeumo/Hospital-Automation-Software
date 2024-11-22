package com.qdocs.smarthospital24.patient;

import androidx.recyclerview.widget.RecyclerView;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.PatientProfileAdapter;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.Picasso;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

import static android.widget.Toast.makeText;

public class PatientProfile extends BaseActivity {
    public Map<String, String> headers = new HashMap<String, String>();
    public Map<String, String> params = new Hashtable<String, String>();
    TextView nameTV,patient_id,gender,age,address,guardian_name,marital_status,email,phone,bloodgroup;

    Button add_appoinment;
    RecyclerView listView;
    int[] personalHeaderArray = {R.string.patientid, R.string.gender, R.string.maritial_status, R.string.phone,
            R.string.email, R.string.address, R.string.email, R.string.age, R.string.guardian};
    ImageView profileIV,barcode,qrcode;
    ArrayList<String> personalValues = new ArrayList<String>();
    PatientProfileAdapter adapter;
    public LinearLayout linear;
    public String defaultDateFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_patient_profile, null, false);
        mDrawerLayout.addView(contentView, 0);

        linear = findViewById(R.id.linear);
        linear.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        profileIV = (ImageView) findViewById(R.id.patientProfile_profileImageview);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.profile));

        nameTV=findViewById(R.id.name);
        patient_id=findViewById(R.id.patient_id);
        gender=findViewById(R.id.gender);
        age=findViewById(R.id.age);
        address=findViewById(R.id.address);
        email=findViewById(R.id.email);
        marital_status=findViewById(R.id.marital_status);
        phone=findViewById(R.id.phone);
        barcode=findViewById(R.id.barcode);
        qrcode=findViewById(R.id.qrcode);
        guardian_name=findViewById(R.id.guardian_name);
        Picasso.with(getApplicationContext()).load(Utility.getSharedPreferences(this, "userImage")).placeholder(R.drawable.placeholder_user).into(profileIV);

        if(Utility.isConnectingToInternet(PatientProfile.this)){
            params.put("patientId", Utility.getSharedPreferences(getApplicationContext(), "patient_id"));
            JSONObject obj=new JSONObject(params);
            Log.e("params ", obj.toString());
            getDataFromApi(obj.toString());
        }else{
            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }

    }

    private void getDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+ Constants.getPatientProfileUrl;
        Log.e("URL: ",url);
        Log.e("bodyParams ",bodyParams);
        Log.e("userId ", Utility.getSharedPreferences(getApplicationContext(), "userId"));
        Log.e("accessToken ", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
        Log.e("clientService ",Constants.clientService);
        Log.e("Auth ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);

        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONArray object = new JSONArray(result);


                            JSONObject data = object.getJSONObject(0);

                            nameTV.setText(data.getString("patient_name"));
                            patient_id.setText(data.getString("id"));
                            gender.setText(data.getString("gender"));
                            if(data.getString("age").equals("0") && data.getString("month").equals("0")&& data.getString("day").equals("0")){
                                age.setText("");
                            } else if(data.getString("age").equals("0") && data.getString("month").equals("0")){
                                age.setText(data.getString("day")+" Days");
                            } else if(data.getString("age").equals("0")){
                                age.setText(data.getString("month")+" Months "+data.getString("day")+" Days");
                            }else if(data.getString("month").equals("0")){
                                age.setText(data.getString("age") + " Years");
                            } else if(data.getString("day").equals("0")){
                                age.setText(data.getString("age") + " Years "+data.getString("month")+" Months");
                            } else {
                                age.setText(data.getString("age") + " Years "+data.getString("month")+" Months "+data.getString("day")+" Days");
                            }
                            email.setText(data.getString("email"));
                            phone.setText(data.getString("mobileno"));
                            address.setText(data.getString("address"));
                            guardian_name.setText(data.getString("guardian_name"));
                            marital_status.setText(data.getString("marital_status"));
                         String qrcode_img =data.getString("qrcode");
                         String barcode_img =data.getString("barcode");
                        qrcode_img = qrcode_img.startsWith("/") ? qrcode_img.substring(1) : qrcode_img;
                        barcode_img = barcode_img.startsWith("/") ? barcode_img.substring(1) : barcode_img;
                        System.out.println("barcode_img=="+barcode_img);
                        System.out.println("qrcode_img=="+qrcode_img);
                        System.out.println("apiUrl=="+Utility.getSharedPreferences(getApplicationContext(), "apiUrl"));
                        String url=Utility.getSharedPreferences(getApplicationContext(), "apiUrl");
                        url.replace("/api/","");
                        System.out.println("apiUrl=="+  url.replace("/api/","")+data.getString("qrcode"));
                        Picasso.with(getApplicationContext()).load(url.replace("/api/","")+data.getString("qrcode")).into(qrcode);
                        Picasso.with(getApplicationContext()).load(url.replace("/api/","")+data.getString("barcode")).into(barcode);

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientProfile.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientProfile.this);//Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
    private String checkData(String key) {
        if(key.equals("null")) {
            return "";
        } else {
            return key;
        }
    }
}
