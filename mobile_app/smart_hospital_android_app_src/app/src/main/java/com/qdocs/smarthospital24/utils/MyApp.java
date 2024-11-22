package com.qdocs.smarthospital24.utils;

import android.app.Application;

public class MyApp extends Application {

    private static MyApp mContext;

    @Override
    public void onCreate() {
        super.onCreate();
        CustomFont.overrideFont(getApplicationContext(), "SANS", "fonts/roboto.ttf");
        mContext = this;
    }
    public static MyApp getContext() {
        return mContext;
    }
}
