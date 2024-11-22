package com.qdocs.smarthospital24;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.text.method.PasswordTransformationMethod;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.ClientError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.InstanceIdResult;
import com.qdocs.smarthospital24.patient.PatientDashboard;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.MyApp;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.MemoryPolicy;
import com.squareup.picasso.NetworkPolicy;
import com.squareup.picasso.Picasso;

import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Locale;
import java.util.Map;
import java.util.Random;

import static android.widget.Toast.makeText;

public class Login extends Activity {
    Locale myLocale;
    ImageView logoIV;
    TextView tv_forgotPass, privacyTV,addappoint;
    Button btn_login;
    EditText et_userName, et_password;
    LinearLayout changeUrlBtn;
    ImageView btn_showPassword, usernameIcon, passwordIcon;
    boolean isPasswordVisible = false;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String>  headers = new HashMap<String, String>();
    String dates="";
    String id="";
    String device_token;
    String langCode = "";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.activity_login);

        tv_forgotPass = (TextView)findViewById(R.id.tv_passwordReset_login);
        addappoint = (TextView)findViewById(R.id.addappoint);
        btn_login = (Button)findViewById(R.id.btn_login);
        et_userName = (EditText)findViewById(R.id.et_username_login);
        et_password = (EditText)findViewById(R.id.et_password_login);
        btn_showPassword = (ImageView) findViewById(R.id.login_password_visibleBtn);
        usernameIcon = (ImageView) findViewById(R.id.icon_username_login);
        passwordIcon = (ImageView) findViewById(R.id.icon_password_login);
        logoIV = (ImageView) findViewById(R.id.login_logo);
        changeUrlBtn = (LinearLayout) findViewById(R.id.btn_changeUrl_login);
        privacyTV = (TextView) findViewById(R.id.login_privacyTV);
        privacyTV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                String domain = Utility.getSharedPreferences(getApplicationContext(), Constants.appDomain);
                System.out.println(" BEFORE PRIVACY URL"+domain);
                if(!domain.endsWith("/")) {
                    domain += "/";
                }
                System.out.println("PRIVACY URL"+domain);
                domain += Constants.privacyPolicyUrl;
                System.out.println("PRIVACY URL"+domain);

                Log.e("PRIVACY URL", domain);
                Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(domain));
                startActivity(browserIntent);

            }
        });

        String appLogo = Utility.getSharedPreferences(this, Constants.appLogo)+"?"+new Random().nextInt(11);
        Picasso.with(getApplicationContext()).load(appLogo).into(logoIV);


        FirebaseInstanceId.getInstance().getInstanceId().addOnSuccessListener( Login.this,  new OnSuccessListener<InstanceIdResult>() {
            @Override
            public void onSuccess(InstanceIdResult instanceIdResult) {
                device_token = FirebaseInstanceId.getInstance().getToken()+"";
                Log.e("DEVICE TOKEN",device_token);
                System.out.println("DEVICE TOKEN="+device_token);
            }
        });
        if(!Constants.askUrlFromUser) {
            changeUrlBtn.setVisibility(View.GONE);
           getSettingsFromApi(Constants.domain);
        }
        if(Constants.isDemoModeOn) {
            et_userName.setText("pat1");
            et_password.setText("password");
        }

        btn_showPassword.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(!isPasswordVisible) {
                    et_password.setTransformationMethod(null);
                    btn_showPassword.setImageResource(R.drawable.eye_black);
                    isPasswordVisible = true;
                } else {
                    et_password.setTransformationMethod(new PasswordTransformationMethod());
                    btn_showPassword.setImageResource(R.drawable.eye_grey);
                    isPasswordVisible = false;
                }
            }
        });

        tv_forgotPass.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(getApplicationContext(), ForgotPassword.class);
                startActivity(intent);
            }
        });

        btn_login.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                    String username = et_userName.getText().toString();
                    String password = et_password.getText().toString();
                    if(username.isEmpty()) {
                        et_userName.setError("Please enter your registered username");
                        et_userName.requestFocus();
                        return;
                    } else if (password.isEmpty()) {
                        et_password.setError("Please Enter Password");
                        et_password.requestFocus();
                        return;
                    } else {
                        if(Utility.isConnectingToInternet(Login.this)){
                            params.put("username", username);
                            params.put("password", password);
                            params.put("deviceToken", device_token);
                            JSONObject obj=new JSONObject(params);
                            Log.e("params ", obj.toString());
                            getDataFromApi(obj.toString());

                            getSettingsFromApi(Utility.getSharedPreferences(getApplicationContext(), Constants.appDomain));

                        }else{
                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                }
            }
        });

        addappoint.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(Login.this,PatientFrontAddAppoinment.class);
                startActivity(intent);

            }
        });

        changeUrlBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Utility.setSharedPreferenceBoolean(getApplicationContext(), "isLoggegIn", false);
                Utility.setSharedPreferenceBoolean(getApplicationContext(), "isUrlTaken", false);
                Intent url = new Intent(getApplicationContext(), TakeUrl.class);
                startActivity(url);
                finish();
            }
        });
        //DECORATE//
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(getResources().getColor(R.color.textHeading));
        }
        //DECORATE//
    }

    private void getSettingsFromApi (String domain) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        if(!domain.endsWith("/")) {
            domain += "/";
        }
        final String url = domain+"app";
        Log.e("Verification Url", url);
        System.out.println("url== "+url);

        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                Log.e("Result", result);
                if (result != null) {
                    pd.dismiss();
                    try {
                        JSONObject object = new JSONObject(result);
                        String success = "200";
                        if (success.equals("200")) {
                            Utility.setSharedPreferenceBoolean(getApplicationContext(), "isUrlTaken", true);
                            Utility.setSharedPreference(MyApp.getContext(), Constants.apiUrl, object.getString("url"));
                            Utility.setSharedPreference(MyApp.getContext(), Constants.imagesUrl, object.getString("site_url"));
                            String appLogo = object.getString("site_url")+"uploads/hospital_content/logo/"+object.getString("app_logo");
                            Utility.setSharedPreference(MyApp.getContext(), Constants.appLogo, appLogo);
                            System.out.println("IMAGE LOGO "+appLogo);
                            Picasso.with(getApplicationContext()).load(appLogo).memoryPolicy(MemoryPolicy.NO_CACHE)
                                    .networkPolicy(NetworkPolicy.NO_CACHE).into(logoIV);
                            String secColour = object.getString("app_secondary_color_code");
                            String primaryColour = object.getString("app_primary_color_code");

                            if(secColour.length() == 7 && primaryColour.length() == 7 ) {
                                Utility.setSharedPreference(getApplicationContext(), Constants.secondaryColour, secColour);
                                Utility.setSharedPreference(getApplicationContext(), Constants.primaryColour, primaryColour);
                            } else {
                                Utility.setSharedPreference(getApplicationContext(), Constants.secondaryColour, Constants.defaultSecondaryColour);
                                Utility.setSharedPreference(getApplicationContext(), Constants.primaryColour, Constants.defaultPrimaryColour);
                            }
                            Log.e("apiUrl Utility", Utility.getSharedPreferences(getApplicationContext(), "apiUrl"));
                            langCode = object.getString("lang_code");
                            Utility.setSharedPreference(getApplicationContext(), Constants.langCode, langCode);
                            if(!langCode.isEmpty()) {
                                setLocale(langCode);
                            }
                        } else {
                            Toast.makeText(getApplicationContext(), "Invalid Domain.", Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), "Invalid Domain.", Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                pd.dismiss();
                try {
                    int  statusCode = error.networkResponse.statusCode;
                    NetworkResponse response = error.networkResponse;
                    Log.e("Volley Error",""+statusCode+" "+response.data.toString());
                    if( error instanceof ClientError) {
                        Toast.makeText(getApplicationContext(), "Invalid Domain.", Toast.LENGTH_SHORT).show();
                    } else {
                        Toast.makeText(getApplicationContext(), "Invalid Domain.", Toast.LENGTH_SHORT).show();
                    }
                } catch (NullPointerException npe) {
                    Toast.makeText(getApplicationContext(), "Invalid Domain.", Toast.LENGTH_SHORT).show();
                }
            }
        });
        RequestQueue requestQueue = Volley.newRequestQueue(Login.this); //Creating a Request Queue
        requestQueue.add(stringRequest);  //Adding request to the queue
    }

    private void getDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.loginUrl;
        Log.e("URL", url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject object = new JSONObject(result);

                        String success = object.getString("status");
                        if (success.equals("200")) {

                         if(object.getString("message").equals("Successfully login.")){
                            JSONObject data = object.getJSONObject("record");

                            Utility.setSharedPreference(getApplicationContext(), Constants.userId, object.getString("id"));
                            Utility.setSharedPreference(getApplicationContext(), "accessToken", object.getString("token"));
                            Utility.setSharedPreference(getApplicationContext(), Constants.currency, data.getString("currency_symbol"));
                            Utility.setSharedPreference(getApplicationContext(), Constants.loginType, data.getString("role"));
                            String gender = data.getString("gender");
                            Utility.setSharedPreference(getApplicationContext(), "gender", gender);
                            String dateFormat = data.getString("date_format");
                            dateFormat = dateFormat.replace("m", "MM");
                            dateFormat = dateFormat.replace("d", "dd");
                            Utility.setSharedPreference(getApplicationContext(), "dateFormat", dateFormat);

                            if(data.getString("time_format").equals("12-hour")) {
                                String datesFormat = data.getString("date_format");
                                datesFormat = datesFormat.replace("m", "MM");
                                datesFormat = datesFormat.replace("d", "dd");
                                String datetimeFormat = datesFormat + " " + "hh:mm aa";
                                System.out.println("datetimeFormat===" + datetimeFormat);
                                Utility.setSharedPreference(getApplicationContext(), "datetimeFormat", datetimeFormat);
                            }else{
                                String datesFormat = data.getString("date_format");
                                datesFormat = datesFormat.replace("m", "MM");
                                datesFormat = datesFormat.replace("d", "dd");
                                String datetimeFormat = datesFormat + " " + "HH:mm";
                                System.out.println("datetimeFormat===" + datetimeFormat);
                                Utility.setSharedPreference(getApplicationContext(), "datetimeFormat", datetimeFormat);
                            }

                            String imgUrl = Utility.getSharedPreferences(getApplicationContext(), "imagesUrl") + data.getString("image");
                            Utility.setSharedPreference(getApplicationContext(), Constants.userImage, imgUrl);
                            Utility.setSharedPreference(getApplicationContext(), Constants.userName, data.getString("username"));

                            Utility.setSharedPreferenceBoolean(getApplicationContext(), "isLoggegIn", true);
                            Utility.setSharedPreference(getApplicationContext(), Constants.patient_id, data.getString("patient_id"));
                            Utility.setSharedPreference(getApplicationContext(), Constants.patient_unique_id, data.getString("patient_unique_id"));
                            System.out.println("patient_unique_id=="+data.getString("patient_unique_id"));
                            Intent asd = new Intent(getApplicationContext(), PatientDashboard.class);
                            startActivity(asd);
                            finish();

                        }else{
                                Toast.makeText(getApplicationContext(), object.getString("message"), Toast.LENGTH_SHORT).show();
                        }
                     }else {
                            Toast.makeText(getApplicationContext(), object.getString("errorMsg"), Toast.LENGTH_SHORT).show();
                            Utility.setSharedPreferenceBoolean(getApplicationContext(), "isLoggegIn", false);
                        }
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
                Toast.makeText(Login.this, R.string.invalidPassword, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", "smarthospital");
                headers.put("Auth-Key", "hospitalAdmin@");
                headers.put("Content-Type", "application/json");
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
        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                50000,
                DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        RequestQueue requestQueue = Volley.newRequestQueue(Login.this);//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }
    public void setLocale(String localeName) {
        if(localeName.isEmpty() || localeName.equals("null")) {
            localeName = "en";
            Log.e("localName status", "empty");
        }
        Locale myLocale = new Locale(localeName);
        Resources res = getResources();
        DisplayMetrics dm = res.getDisplayMetrics();
        Configuration conf = res.getConfiguration();
        conf.locale = myLocale;
        res.updateConfiguration(conf, dm);
        Log.e("Status", "Locale updated!");
        Utility.setSharedPreferenceBoolean(getApplicationContext(), Constants.isLocaleSet, true);
        Utility.setSharedPreference(getApplicationContext(), Constants.currentLocale, localeName);
    }

}
