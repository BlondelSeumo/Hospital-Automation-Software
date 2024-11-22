package com.qdocs.smarthospital24;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.webkit.DownloadListener;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import androidx.appcompat.app.AppCompatActivity;


public class OpenPdf extends AppCompatActivity {

    String imageUrl="";
    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_open_pdf);
        imageUrl =getIntent().getStringExtra("imageUrl");
        System.out.println(imageUrl+"imageUrl");

        final ProgressDialog pDialog = new ProgressDialog(OpenPdf.this);
        pDialog.setTitle(getString(R.string.app_name));
        pDialog.setMessage("Loading...");
        pDialog.setIndeterminate(false);
        pDialog.setCancelable(false);
        WebView webView =  findViewById(R.id.web_view);
        webView.getSettings().setJavaScriptEnabled(true);
        webView.getSettings().setBuiltInZoomControls(true);
        webView.getSettings().setLoadWithOverviewMode(true);
        webView.getSettings().setUseWideViewPort(true);

        if(imageUrl.endsWith(".pdf")||imageUrl.endsWith(".docx")||imageUrl.endsWith(".zip")){
            webView.loadUrl("https://docs.google.com/viewer?url="+imageUrl);
        }else {
            webView.loadUrl(imageUrl);
        }

        webView.setDownloadListener(new DownloadListener() {
            public void onDownloadStart(String url, String userAgent,
                                        String contentDisposition, String mimetype,
                                        long contentLength) {
                Intent i = new Intent(Intent.ACTION_VIEW);
                i.setData(Uri.parse(url));
                startActivity(i);
            }
        });

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                pDialog.show();
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                if (view.getTitle().equals(""))
                    view.reload();
                pDialog.dismiss();
            }
        });
    }


}
;