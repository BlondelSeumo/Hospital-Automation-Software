package com.qdocs.smarthospital24;

import static android.widget.Toast.makeText;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.os.Bundle;
import android.os.Handler;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Window;
import android.view.WindowManager;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.qdocs.smarthospital24.patient.PatientDashboard;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

public class SplashActivity extends Activity {
    private static final int SPLASH_TIME_OUT = 2000;
    ImageView logoIV;
    WebView webView;
    Boolean isStatus;
    Boolean isLoggegIn;
    Boolean isUrlTaken;
    public Map<String, String>  headers = new HashMap<String, String>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.activity_splash);

        Boolean isLocaleSet;
        try {
            isLocaleSet = Utility.getSharedPreferencesBoolean(getApplicationContext(), "isLocaleSet");
        } catch (NullPointerException e) {
            isLocaleSet = false;
        }
        if(isLocaleSet) {
            setLocale(Utility.getSharedPreferences(getApplicationContext(), Constants.langCode));
        }
        splash();
    }


    private void splash() {
        new Handler().postDelayed(new Runnable() {
            public void run() {

                try {
                    isLoggegIn = Utility.getSharedPreferencesBoolean(getApplicationContext(), Constants.isLoggegIn);
                    isStatus = Utility.getSharedPreferencesBoolean(getApplicationContext(), Constants.isStatusIn);
                    isUrlTaken = Utility.getSharedPreferencesBoolean(getApplicationContext(), "isUrlTaken");
                } catch (NullPointerException NPE) {
                    isLoggegIn = false;
                    isStatus = false;
                    isUrlTaken = false;
                }
                Log.e("loggeg", isLoggegIn.toString());
                Log.e("isUrlTaken", isUrlTaken.toString());
                Log.e("isStatus", isStatus.toString());

                if(Constants.askUrlFromUser) {
                    if(isUrlTaken) {
                        if(Utility.isConnectingToInternet(SplashActivity.this)){
                            patientpanelstatus(Utility.getSharedPreferences(getApplicationContext(), "apiUrl"));
                        }else{
                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }

                    } else {
                        Intent asd = new Intent(getApplicationContext(),TakeUrl.class);
                        startActivity(asd);
                        finish();
                    }
                } else {
                    if(Utility.isConnectingToInternet(SplashActivity.this)){
                        patientpanelstatus(Constants.domain+"/api/");
                    }else{
                        makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }

                }
            }
        }, SPLASH_TIME_OUT);
    }
    public void setLocale(String localeName) {
        Locale myLocale = new Locale(localeName);
        Locale.setDefault(myLocale);
        Resources res = getResources();
        DisplayMetrics dm = res.getDisplayMetrics();
        Configuration conf = res.getConfiguration();
        conf.locale = myLocale;
        res.updateConfiguration(conf, dm);
        Log.e("Status", "Locale updated!");
    }


    private void patientpanelstatus(String siteurl) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        String url = siteurl+ Constants.getpatientpanelstatusUrl;
        System.out.println("url=="+url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                try {
                    JSONObject object = new JSONObject(result);
                    String patient_panel = object.getString("patient_panel");
                    System.out.println("patient_panel="+patient_panel.toString());
                    if(patient_panel.equals("enabled")){
                        Utility.setSharedPreferenceBoolean(getApplicationContext(), "patient_panel", false);
                        pd.dismiss();
                        if(isLoggegIn){
                              if(isLoggegIn){
                                    Intent i = new Intent(getApplicationContext(),PatientDashboard.class);
                                    startActivity(i);
                                    finish();
                              }else {
                                Intent i = new Intent(getApplicationContext(),Login.class);
                                startActivity(i);
                                finish();
                              }
                        }else {
                            Intent i = new Intent(getApplicationContext(), Login.class);
                            startActivity(i);
                            finish();
                        }
                    } else{
                        Utility.setSharedPreferenceBoolean(getApplicationContext(), "maintenance_mode", true);
                        pd.dismiss();
                        android.app.AlertDialog.Builder builder = new android.app.AlertDialog.Builder(SplashActivity.this);
                        builder.setCancelable(false);
                        builder.setMessage(R.string.patientpanelstatus);
                        builder.setTitle("");
                        android.app.AlertDialog alert = builder.create();
                        alert.show();

                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                volleyError.printStackTrace();
                Toast.makeText(SplashActivity.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                Log.e("Headers", headers.toString());
                return headers;
            }

            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }


        };
        RequestQueue requestQueue = Volley.newRequestQueue(SplashActivity.this);//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue


    }
}
