package com.qdocs.smarthospital24.utils;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.media.AudioAttributes;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;
import android.util.Log;
import androidx.core.app.NotificationCompat;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import com.qdocs.smarthospital24.R;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Map;

public class MyFirebaseMessagingService extends FirebaseMessagingService {
    PendingIntent pendingIntent;
    private static final String TAG = "MyFirebaseMsgService";

    private void pushNotification(Intent intent, String title, String message) {

        Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);

        NotificationManager mNotificationManager = (NotificationManager) this.getSystemService(Context.NOTIFICATION_SERVICE);
        if(Build.VERSION.SDK_INT >= 31) {
            pendingIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_IMMUTABLE);
        }else{
            pendingIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_UPDATE_CURRENT);
        }
        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(this, "alert_001")

                .setWhen(System.currentTimeMillis())
                .setContentTitle(title)
                .setContentText(message)
                .setStyle(new NotificationCompat.BigTextStyle().bigText(message))
                .setContentIntent(pendingIntent)
                .setOngoing(false)
                .setSound(defaultSoundUri)
                .setPriority(NotificationCompat.PRIORITY_MAX)
                .setDefaults(Notification.DEFAULT_ALL)
                .setAutoCancel(false);



        if(Build.MANUFACTURER.equalsIgnoreCase("xiaomi")) {
            mBuilder.setSmallIcon(R.drawable.notification_logo);
            mBuilder.setColorized(true);
            mBuilder.setColor(Color.parseColor("#f38000"));
            Log.e("MANUFACTURER", Build.MANUFACTURER);
        } else {
            if(Build.VERSION.SDK_INT >= 34) {
                mBuilder.setSmallIcon(R.drawable.notification_logo_trans);
                mBuilder.setColorized(true);
                mBuilder.setColor(Color.parseColor("#f38000"));
                Log.e("MANUFACTURER", Build.MANUFACTURER);
            }else{
                mBuilder.setSmallIcon(R.drawable.notification_logo);
            }
            //mBuilder.setSmallIcon(R.drawable.notification_logo);
        }
        if (mNotificationManager != null) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {

                AudioAttributes attributes = new AudioAttributes.Builder().setUsage(AudioAttributes.USAGE_NOTIFICATION).build();

                NotificationChannel channel = new NotificationChannel("alert_001",
                        "Alert",
                        NotificationManager.IMPORTANCE_HIGH);
                channel.setSound(defaultSoundUri, attributes);

                mNotificationManager.createNotificationChannel(channel);
                mBuilder.setChannelId(channel.getId());
            }
            mNotificationManager.notify(1, mBuilder.build());
        }
    }
    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);
        Log.e(TAG, "Status: Message Received");
        if (remoteMessage != null)
        {
            Log.e(TAG, "Data: "+remoteMessage.getData().toString());
            Map<String, String> data = remoteMessage.getData();
            Log.e("data", data.toString());
            String mTitle = data.get("title");
            String mMessage = data.get("body");
            String action = data.get("action");
            Intent intent;
            intent = new Intent(this, com.qdocs.smarthospital24.patient.PatientDashboard.class);
            try {
                switch (action) {
                    default:
                        intent = new Intent(this, com.qdocs.smarthospital24.patient.PatientDashboard.class);
                        break;
                }
            } catch (NullPointerException e) {
                intent = new Intent(this, com.qdocs.smarthospital24.patient.PatientDashboard.class);
            }
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            pushNotification(intent,mTitle,mMessage);

            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            String currentDateandTime = sdf.format(new Date());

            DatabaseHelper dataBaseHelper = new DatabaseHelper(MyFirebaseMessagingService.this);
            dataBaseHelper.insertUserDetails(mTitle,mMessage,"0",currentDateandTime);
            dataBaseHelper.close();

        }
    }
}
