package com.qdocs.smarthospital24;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.Picasso;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;
import java.util.Random;
import static android.widget.Toast.makeText;

public class ForgotPassword extends AppCompatActivity {

    ImageView logoIV;
    EditText emailET;
    Button submitBtn;
    RadioGroup typeRadiogroup;
    RadioButton rb_Patient, rb_Admin;
    String loginType = "";
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.forgot_password_activity);

        submitBtn = (Button)findViewById(R.id.btn_submit_fp);
        emailET = (EditText)findViewById(R.id.et_email_fp);
        logoIV = findViewById(R.id.fp_logo);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(getResources().getColor(R.color.textHeading));
        }
        String appLogo = Utility.getSharedPreferences(this,Constants.appLogo)+"?"+new Random().nextInt(11);
        Log.e("appLogo", appLogo);
        Picasso.with(getApplicationContext()).load(appLogo).into(logoIV);

        submitBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String emailId = emailET.getText().toString().trim();
                if (emailId.length() == 0) {
                    makeText(getApplicationContext(),"Please enter registered email id", Toast.LENGTH_SHORT).show();
                } else {
                    if (Utility.isConnectingToInternet(ForgotPassword.this)) {
                        params.put("email", emailId);
                        params.put("usertype","patient");
                        params.put("site_url", Utility.getSharedPreferences(getApplicationContext(),Constants.imagesUrl));
                        JSONObject obj=new JSONObject(params);
                        Log.e("params ", obj.toString());
                        System.out.println("params "+ obj.toString());
                      System.out.println("site_url "+Utility.getSharedPreferences(getApplicationContext(),Constants.imagesUrl) );
                      System.out.println("usertype "+"patient");
                      System.out.println("email "+emailId);
                        getDataFromApi(obj.toString());
                    } else {
                        makeText(getApplicationContext(),"Please connect to internet and try again", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });
    }
    private void showAddDialog(Context context,String message) {
        final Dialog dialog = new Dialog(context);
        dialog.setContentView(R.layout.forgot_email);
        TextView submitBtn = dialog.findViewById(R.id.addTask_dialog_ok);
        TextView messages = dialog.findViewById(R.id.message);
        messages.setText(message);
        submitBtn.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        submitBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dialog.dismiss();
                finish();
            }
        });
        dialog.show();
    }
    private void getDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.forgotPasswordUrl;
        Log.e("Forgot Password Url", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);
                        String success = object.getString("status");
                        if (success.equals("202")) {
                          // makeText(getApplicationContext(), object.getString("message"), Toast.LENGTH_SHORT).show();
                           showAddDialog(ForgotPassword.this,object.getString("message"));
                        } else {
                            makeText(getApplicationContext(), object.getString("message"), Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error 1", volleyError.toString());
                makeText(ForgotPassword.this, R.string.invalidUsername, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", "smarthospital");
                headers.put("Auth-Key", "hospitalAdmin@");
                headers.put("Content-Type", "application/json");
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
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
        //SETTING RETRY Policy
        stringRequest.setRetryPolicy(new DefaultRetryPolicy(5000, 1, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        RequestQueue requestQueue = Volley.newRequestQueue(ForgotPassword.this); //Creating a Request Queue
        requestQueue.add(stringRequest);//Adding request to the queue
    }
}


