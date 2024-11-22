package com.qdocs.smarthospital24.patient;

import android.annotation.TargetApi;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;

public class PatientOpdLiveConsult extends BaseActivity {
String JoinUrl;
    WebView webView;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_patient_opd_live_consult, null, false);
        mDrawerLayout.addView(contentView, 0);
        JoinUrl = getIntent().getStringExtra("join_url");
        final ProgressDialog pd = ProgressDialog.show(PatientOpdLiveConsult.this, "", "Loading..", true);
        webView = findViewById(R.id.liveclass_webview);
        webView.getSettings().setJavaScriptEnabled(true); //enable javascript
        webView.getSettings().setDomStorageEnabled(true);
        webView.getSettings().setUseWideViewPort(true);
        webView.getSettings().setLoadWithOverviewMode(true);
        final Activity activity = this;
       webView.loadUrl(JoinUrl);
       /* String url = JoinUrl;
        Intent intent = new Intent(Intent.ACTION_VIEW);
        intent.setData(Uri.parse(url));
        startActivity(intent);*/

        webView.setWebViewClient(new WebViewClient() {
            @SuppressWarnings("deprecation")
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                if (Uri.parse(url).getScheme().equals("market")) {
                    try {
                        startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url)));
                        return true;
                    } catch (ActivityNotFoundException e) { }
                }
                if (Uri.parse(url).getScheme().equals("zoomus")) {
                    try {
                        startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url)));
                        return true;
                    } catch (ActivityNotFoundException e) { }
                }
                if(url.toLowerCase().startsWith("http") || url.toLowerCase().startsWith("https") || url.toLowerCase().startsWith("file")) {
                    view.loadUrl(url);
                }
                return true;
            }
            @Override
            public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
                Toast.makeText(activity, description, Toast.LENGTH_SHORT).show();
            }
            @TargetApi(Build.VERSION_CODES.M)
            @Override
            public void onReceivedError(WebView view, WebResourceRequest req, WebResourceError rerr) {
                onReceivedError(view, rerr.getErrorCode(), rerr.getDescription().toString(), req.getUrl().toString());
            }
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                 pd.show();
            }
            @Override
            public void onPageFinished(WebView view, String url) {
                 pd.dismiss();
                System.out.println("join_url "+JoinUrl+ " = finished");
            }
        });
    }
}
