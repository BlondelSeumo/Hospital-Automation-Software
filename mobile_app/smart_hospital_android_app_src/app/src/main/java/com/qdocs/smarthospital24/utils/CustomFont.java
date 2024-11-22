package com.qdocs.smarthospital24.utils;

import android.content.Context;
import android.graphics.Typeface;
import android.os.Build;
import android.util.Log;
import android.widget.Toast;
import java.lang.reflect.Field;
import java.util.HashMap;
import java.util.Map;

public class CustomFont {

    public static void overrideFont(Context context, String defaultFontNameToOverride, String customFontFileNameInAssets) {

        final Typeface customFontTypeface = Typeface.createFromAsset(context.getAssets(), customFontFileNameInAssets);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Map<String, Typeface> newMap = new HashMap<String, Typeface>();
            newMap.put(defaultFontNameToOverride, customFontTypeface);
            try {
                final Field staticField = Typeface.class
                        .getDeclaredField("sSystemFontMap");
                staticField.setAccessible(true);
                staticField.set(null, newMap);

            } catch (NoSuchFieldException e) {
                e.printStackTrace();
                Log.e("MEHUL ERROR", "Can not set custom font 1");
                Toast.makeText(context, "ERROR", Toast.LENGTH_LONG).show();
            } catch (IllegalAccessException e) {
                e.printStackTrace();
                Log.e("MEHUL ERROR", "Can not set custom font 1");
                Toast.makeText(context, "ERROR 2" , Toast.LENGTH_LONG).show();
            }
        } else {
            try {
                final Field defaultFontTypefaceField = Typeface.class.getDeclaredField(defaultFontNameToOverride);
                defaultFontTypefaceField.setAccessible(true);
                defaultFontTypefaceField.set(null, customFontTypeface);
            } catch (Exception e) {
                Log.e(CustomFont.class.getSimpleName(), "Can not set custom font " + customFontFileNameInAssets + " instead of " + defaultFontNameToOverride);
            }
        }
    }
}
