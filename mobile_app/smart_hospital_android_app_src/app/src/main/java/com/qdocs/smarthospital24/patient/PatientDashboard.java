package com.qdocs.smarthospital24.patient;

import android.Manifest;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Resources;
import android.graphics.Color;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.provider.Settings;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.navigation.NavigationView;
import com.qdocs.smarthospital24.AboutHospital;
import com.qdocs.smarthospital24.Login;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.fragments.PatientDashboardFragment;
import com.qdocs.smarthospital24.fragments.PatientDashboardOPDList;
import com.qdocs.smarthospital24.fragments.PatientDashboardPathology;
import com.qdocs.smarthospital24.fragments.PatientDashboardPharmacy;
import com.qdocs.smarthospital24.fragments.PatientDashboardRadiology;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.DatabaseHelper;
import com.qdocs.smarthospital24.utils.DrawerArrowDrawable;
import com.qdocs.smarthospital24.utils.Utility;
import com.squareup.picasso.Picasso;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.security.spec.InvalidKeySpecException;
import java.security.spec.InvalidParameterSpecException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;
import java.util.Random;
import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.SecretKey;
import static android.widget.Toast.makeText;

public class PatientDashboard extends AppCompatActivity implements SwipeRefreshLayout.OnRefreshListener{

    //RUNTIME PERMISSIONS
    private static final int PERMISSION_CALLBACK_CONSTANT = 100;
    private static final int REQUEST_PERMISSION_SETTING = 101;
    String[] permissionsRequired = new String[]{Manifest.permission.READ_EXTERNAL_STORAGE,
            Manifest.permission.WRITE_EXTERNAL_STORAGE,
            Manifest.permission.CALL_PHONE,Manifest.permission.CAMERA,Manifest.permission.POST_NOTIFICATIONS};
    private boolean sentToSettings = false;
    TextView unread_count;
    //RUNTIME PERMISSIONS
    public DrawerArrowDrawable drawerArrowDrawable;
    ImageView drawerIndicator;
    public float offset;
    public boolean flipped;
    public DrawerLayout drawer;
    protected FrameLayout mDrawerLayout, actionBar;
    ImageView actionBarLogo;
    BottomNavigationView bottomNavigation;
    private NavigationView navigationView;
    private RelativeLayout drawerHead;
    private TextView PatientId, nameTV, childDetailsTV;
    private ImageView profileImageIV;
    ArrayList<String> moduleCodeList = new ArrayList<String>();
    ArrayList<String> moduleStatusList = new ArrayList<String>();
    public Map<String, String> headers = new HashMap<String, String>();
    FrameLayout viewContainer;
    boolean doubleBackToExitPressedOnce = false;
    public Map<String, String> params = new Hashtable<String, String>();
    JSONArray modulesJson;
    FrameLayout notification_alert;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.patient_dashboard_activity1);

        drawerIndicator = findViewById(R.id.drawer_indicator);
        actionBar = findViewById(R.id.actionBar);
        drawer = findViewById(R.id.drawer_layout);
        navigationView = findViewById(R.id.nav_view);
        unread_count = findViewById(R.id.unread_count);
        actionBarLogo = findViewById(R.id.actionBar_logo);
        notification_alert = findViewById(R.id.notification_alert);
        prepareNavList();
        setUpDrawer();
        decorate();
        setUpPermission();

        DatabaseHelper db = new DatabaseHelper(PatientDashboard.this);
        int profile_counts = db.getProfilesCount();
        db.close();
        if(String.valueOf(profile_counts).equals("0")){
            unread_count.setVisibility(View.GONE);
        }else{
            unread_count.setText(String.valueOf(profile_counts));
        }

        notification_alert.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                DatabaseHelper db = new DatabaseHelper(PatientDashboard.this);
                db.updatestatus("0","1");
                Intent intent=new Intent(PatientDashboard.this,NotificationList.class);
                startActivity(intent);
            }
        });
        params.put("site_url", Utility.getSharedPreferences(getApplicationContext(), Constants.imagesUrl));
        JSONObject obj=new JSONObject(params);
        Log.e("params", obj.toString());
        getDataFromApi(obj.toString());

        viewContainer = findViewById(R.id.Dashboard_frame);
        bottomNavigation = (BottomNavigationView) findViewById(R.id.navigation);

        bottomNavigation.setOnNavigationItemSelectedListener(new BottomNavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem item) {
                Fragment fragment = null;
                Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.container);

                switch (item.getItemId()) {


                    case R.id.navigation_home:
                        loadFragment(new PatientDashboardFragment());
                        drawer.closeDrawer(GravityCompat.START);
                        return true;

                    case R.id.navigation_opd:
                        loadFragment(new PatientDashboardOPDList());
                        drawer.closeDrawer(GravityCompat.START);
                        return true;

                    case R.id.navigation_pharmacy:
                        loadFragment(new PatientDashboardPharmacy());
                        drawer.closeDrawer(GravityCompat.START);
                        return true;

                    case R.id.navigation_pathology:
                        loadFragment(new PatientDashboardPathology());
                        drawer.closeDrawer(GravityCompat.START);
                        return true;

                    case R.id.navigation_radiology:
                        loadFragment(new PatientDashboardRadiology());
                        drawer.closeDrawer(GravityCompat.START);
                        return true;

                }
                return false;
            }
        });
         loadFragment(new PatientDashboardFragment());
    }


    @Override
    public void onRestart()
    {
        super.onRestart();
        DatabaseHelper db = new DatabaseHelper(PatientDashboard.this);
        int profile_counts = db.getProfilesCount();
        db.close();
        if(String.valueOf(profile_counts).equals("0")){
            unread_count.setVisibility(View.GONE);
        }else{
            unread_count.setText(String.valueOf(profile_counts));
        }
    }

    private void getModulesFromApi () {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getModuleUrl;
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Modules Result", result);
                        JSONObject object = new JSONObject(result);

                        modulesJson = object.getJSONArray("module_list");
                        Utility.setSharedPreference(getApplicationContext(), Constants.modulesArray, modulesJson.toString());
                        if (modulesJson.length() != 0) {
                            for(int i = 0; i < modulesJson.length(); i++) {
                                moduleCodeList.add(modulesJson.getJSONObject(i).getString("short_code"));
                                moduleStatusList.add(modulesJson.getJSONObject(i).getString("is_active"));
                            }
                            setMenu(navigationView.getMenu(), bottomNavigation.getMenu());
                        } else {
                            Toast.makeText(getApplicationContext(), object.getString("errorMsg"), Toast.LENGTH_SHORT).show();
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
                Toast.makeText(PatientDashboard.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(getApplicationContext(), "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
                Log.e("Headers", headers.toString());
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientDashboard.this);//Creating a Request Queue
        requestQueue.add(stringRequest);  //Adding request to the queue
    }
    private void setUpPermission() {

        if(ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[0]) != PackageManager.PERMISSION_GRANTED
                || ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[1]) != PackageManager.PERMISSION_GRANTED
                || ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[2]) != PackageManager.PERMISSION_GRANTED
                || ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[3]) != PackageManager.PERMISSION_GRANTED
                || ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[4]) != PackageManager.PERMISSION_GRANTED){

            if(ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[0])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[1])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[2])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[3])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[4]
            )){

                ActivityCompat.requestPermissions(PatientDashboard.this,permissionsRequired,PERMISSION_CALLBACK_CONSTANT);
            } else {
                ActivityCompat.requestPermissions(PatientDashboard.this,permissionsRequired,PERMISSION_CALLBACK_CONSTANT);
            }
            Utility.setSharedPreferenceBoolean(getApplicationContext(), Constants.permissionStatus, true);
        }
    }

    private void setUpDrawer() {

        View headerLayout = navigationView.getHeaderView(0);

        PatientId = headerLayout.findViewById(R.id.drawer_userPatientId);
        nameTV = headerLayout.findViewById(R.id.drawer_userName);
        profileImageIV = headerLayout.findViewById(R.id.drawer_logo);
        drawerHead = headerLayout.findViewById(R.id.drawer_head);

        Resources resources = getResources();
        drawerArrowDrawable = new DrawerArrowDrawable(resources);
        drawerArrowDrawable.setStrokeColor(resources.getColor(R.color.drawerIndicatorColour));
        drawerIndicator.setImageDrawable(drawerArrowDrawable);
        drawer.setDrawerListener(new DrawerLayout.SimpleDrawerListener() {
            @Override
            public void onDrawerSlide(View drawerView, float slideOffset) {
                offset = slideOffset;
                // Sometimes slideOffset ends up so close to but not quite 1 or 0.
                if (slideOffset >= .995) {
                    flipped = true;
                    drawerArrowDrawable.setFlip(flipped);
                } else if (slideOffset <= .005) {
                    flipped = false;
                    drawerArrowDrawable.setFlip(flipped);
                }
                drawerArrowDrawable.setParameter(offset);
            }
        });
        drawerIndicator.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (drawer.isDrawerVisible(GravityCompat.START)) {
                    drawer.closeDrawer(GravityCompat.START);
                } else {
                    drawer.openDrawer(GravityCompat.START);
                }
            }
        });
    }
     private void decorate() {

        Utility.setLocale(getApplicationContext(), Utility.getSharedPreferences(getApplicationContext(), Constants.langCode));
        String appLogo = Utility.getSharedPreferences(this, Constants.appLogo)+"?"+new Random().nextInt(11);

        Picasso.with(getApplicationContext()).load(Utility.getSharedPreferences(this, "userImage")).placeholder(R.drawable.placeholder_user).into(profileImageIV);
        Picasso.with(getApplicationContext()).load(appLogo).fit().centerInside().placeholder(null).into(actionBarLogo);

         nameTV.setText(Utility.getSharedPreferences(this, Constants.userName));
         PatientId.setText("Patient Id "+Utility.getSharedPreferences(this, Constants.patient_unique_id));

        System.out.println("PRIMARY COLOUR="+Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour));

        actionBar.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        drawerHead.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.secondaryColour)));
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        }
    }
    private void prepareNavList() {
        /*params.put("patient_id", Utility.getSharedPreferences(getApplicationContext(), Constants.patient_id));
        JSONObject obj=new JSONObject(params);
        Log.e("params ", obj.toString());*/

        if(Utility.isConnectingToInternet(getApplicationContext())){
            getModulesFromApi();
        }else{
            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }

        navigationView.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {
                int id = menuItem.getItemId();
                switch (menuItem.getItemId()) {
                    case R.id.nav_home:
                        Intent dashboard = new Intent(PatientDashboard.this, PatientDashboard.class);
                        startActivity(dashboard);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_profile:
                        Intent profile = new Intent(PatientDashboard.this, PatientProfile.class);
                        startActivity(profile);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_appoinments:
                        Intent appoinment = new Intent(PatientDashboard.this, PatientAppoinmentList.class);
                        startActivity(appoinment);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_opd:
                        Intent fees = new Intent(PatientDashboard.this, PatientOpdDetailsList.class);
                        startActivity(fees);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_ipd:
                        Intent classTimeTable = new Intent(PatientDashboard.this, PatientIpdDetailsList.class);
                        startActivity(classTimeTable);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_pharmacy:
                        Intent homework = new Intent(PatientDashboard.this, PatientPharmacyReport.class);
                        startActivity(homework);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_pathology:
                        Intent download = new Intent(PatientDashboard.this, PatientPathology.class);
                        startActivity(download);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_radiology:
                        Intent examSchedule = new Intent(PatientDashboard.this, PatientRadiology.class);
                        startActivity(examSchedule);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_ambulance:
                        Intent timeline = new Intent(PatientDashboard.this, PatientAmbulanceLists.class);
                        startActivity(timeline);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_bloodbank:
                        Intent doc = new Intent(PatientDashboard.this, PatientBloodBank.class);
                        startActivity(doc);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_liveconsult:
                        Intent liveconsult = new Intent(PatientDashboard.this, PatientLiveConsulation.class);
                        startActivity(liveconsult);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_contentsharelist:
                        Intent contentshare = new Intent(PatientDashboard.this, DownloadContentActivity.class);
                        startActivity(contentshare);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_about:
                        Intent about = new Intent(PatientDashboard.this, AboutHospital.class);
                        startActivity(about);
                        overridePendingTransition(R.anim.slide_leftright,  R.anim.no_animation);
                        drawer.closeDrawer(GravityCompat.START);
                        break;

                    case R.id.nav_logout:
                        android.app.AlertDialog.Builder builder = new android.app.AlertDialog.Builder(PatientDashboard.this);
                        builder.setCancelable(false);
                        builder.setMessage(R.string.logoutMsg);
                        builder.setTitle("");
                        builder.setPositiveButton(R.string.yes, new DialogInterface.OnClickListener() {

                            public void onClick(DialogInterface dialog, int which) {
                                if(Utility.isConnectingToInternet(getApplicationContext())){
                                    loginOutApi();
                                }else{
                                    makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                }

                            }
                        });
                        builder.setNegativeButton(R.string.no, new DialogInterface.OnClickListener() {

                            public void onClick(DialogInterface dialog, int which) {
                                dialog.dismiss();
                            }
                        });
                        android.app.AlertDialog alert = builder.create();
                        alert.show();
                        break;
                }
                return false;
            }
        });
    }
    private void setMenu(Menu navMenu, Menu bottomNavMenu) {
        for (int i = 0; i<moduleCodeList.size(); i++) {
            if (moduleCodeList.get(i).equals("my_appointments")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_appoinments).setVisible(false);
            }if (moduleCodeList.get(i).equals("opd")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_opd).setVisible(false);
                bottomNavMenu.findItem(R.id.navigation_opd).setVisible(false);
            } if (moduleCodeList.get(i).equals("ipd")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_ipd).setVisible(false);
            } if (moduleCodeList.get(i).equals("pharmacy")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_pharmacy).setVisible(false);
               bottomNavMenu.findItem(R.id.navigation_pharmacy).setVisible(false);
            } if (moduleCodeList.get(i).equals("pathology")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_pathology).setVisible(false);
                bottomNavMenu.findItem(R.id.navigation_pathology).setVisible(false);
            } if (moduleCodeList.get(i).equals("radiology")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_radiology).setVisible(false);
                bottomNavMenu.findItem(R.id.navigation_radiology).setVisible(false);
            } if (moduleCodeList.get(i).equals("ambulance")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_ambulance).setVisible(false);
            } if (moduleCodeList.get(i).equals("blood_bank")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_bloodbank).setVisible(false);
            } if (moduleCodeList.get(i).equals("live_consultation")&& moduleStatusList.get(i).equals("0")) {
                navMenu.findItem(R.id.nav_liveconsult).setVisible(false);
            }
        }

    }
    private void loadFragment(Fragment fragment) {
        FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
        transaction.replace(R.id.Dashboard_frame, fragment);
        transaction.commit();
    }
    //RUNTIME PERMISSIONS
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if(requestCode == PERMISSION_CALLBACK_CONSTANT){

            if(ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[0])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[1])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[2])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[3])
                    || ActivityCompat.shouldShowRequestPermissionRationale(PatientDashboard.this,permissionsRequired[4])){

                AlertDialog.Builder builder = new AlertDialog.Builder(PatientDashboard.this);
                builder.setTitle("Need Multiple Permissions");
                builder.setMessage("This app needs to access to your storage permissions.");
                builder.setPositiveButton("Grant", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.cancel();
                        ActivityCompat.requestPermissions(PatientDashboard.this,permissionsRequired,PERMISSION_CALLBACK_CONSTANT);
                    }
                });
                builder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.cancel();
                    }
                });
                builder.show();
            } /*else {
                if(Build.MANUFACTURER.equalsIgnoreCase("xiaomi")) {

                    AlertDialog.Builder builder = new AlertDialog.Builder(PatientDashboard.this);
                    builder.setTitle("Allow Notifications");
                    builder.setMessage("For smooth functioning of app, please provide Auto-Start permission and allow notification access.");
                    builder.setPositiveButton("Grant", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialog, int which) {
                            dialog.cancel();
                            //goToSettings();
                        }
                    });
                    builder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialog, int which) {
                            dialog.cancel();
                        }
                    });
                    builder.show();
                } else {
                  //  goToSettings();
                }
            }*/
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        Log.e("STATUS", "1");
        if (requestCode == REQUEST_PERMISSION_SETTING) {
            if (ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[0]) == PackageManager.PERMISSION_GRANTED) {

            } else {
                Log.e("PERMISSION MANAGER", "PERMISSION MISSING");
            }
        }
    }
    @Override
    protected void onPostResume() {
        super.onPostResume();
        if (sentToSettings) {
            if (ActivityCompat.checkSelfPermission(PatientDashboard.this, permissionsRequired[0]) != PackageManager.PERMISSION_GRANTED) {
                Log.e("PERMISSION MANAGER", "PERMISSION MISSING");
            }
        }
    }
    @Override
    public void onBackPressed() {
        if (doubleBackToExitPressedOnce) {
            super.onBackPressed();
            return;
        }
        this.doubleBackToExitPressedOnce = true;
        Toast.makeText(this, "Please click back again to exit", Toast.LENGTH_SHORT).show();

        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                doubleBackToExitPressedOnce=false;
            }
        }, 2000);
    }
    private void goToSettings() {
        sentToSettings = true;
        Intent intent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
        Uri uri = Uri.fromParts("package", getPackageName(), null);

        intent.setData(uri);
        startActivityForResult(intent, REQUEST_PERMISSION_SETTING);
    }
    private void loginOutApi () {

        DatabaseHelper dataBaseHelpers = new DatabaseHelper(PatientDashboard.this);
        dataBaseHelpers.deleteAll() ;

        final ProgressDialog pd = new ProgressDialog(PatientDashboard.this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        String url = Utility.getSharedPreferences(PatientDashboard.this, "apiUrl")+ Constants.logoutUrl;
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
                            Utility.setSharedPreferenceBoolean(getApplicationContext(), "isLoggegIn", false);
                            Intent logout = new Intent(PatientDashboard.this, Login.class);
                            logout.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                            logout.putExtra("EXIT", true);
                            startActivity(logout);
                            finish();
                        } else {
                            Toast.makeText(PatientDashboard.this, object.getString("errorMsg"), Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                } else {
                    pd.dismiss();
                    Toast.makeText(PatientDashboard.this, R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientDashboard.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                headers.put("User-ID", Utility.getSharedPreferences(PatientDashboard.this, "userId"));
                headers.put("Authorization", Utility.getSharedPreferences(PatientDashboard.this, "accessToken"));
                Log.e("Headers", headers.toString());
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }
        };
        //Creating a Request Queue
        RequestQueue requestQueue = Volley.newRequestQueue(PatientDashboard.this);
        //Adding request to the queue
        requestQueue.add(stringRequest);
    }
    private void getDataFromApi(String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;

        try {

            SecretKey key = Utility.generateKey();

            StringRequest stringRequest = new StringRequest(Request.Method.POST, Utility.decryptMsg(Utility.encryptMsg(key), key ), new Response.Listener<String>() {
                @Override
                public void onResponse(String result) {
                    if (result != null) {
                        pd.dismiss();
                        try {

                            JSONObject object = new JSONObject(result);

                            if(object.getString("status").equals("0")) {
                                Utility.setSharedPreferenceBoolean(getApplicationContext(), Constants.isLoggegIn, false);

                                android.app.AlertDialog.Builder builder = new android.app.AlertDialog.Builder(PatientDashboard.this);
                                builder.setCancelable(false);
                                builder.setMessage(object.getString("msg"));
                                builder.setTitle("");
                                builder.setPositiveButton(R.string.ok, new DialogInterface.OnClickListener() {

                                    public void onClick(DialogInterface dialog, int which) {
                                        if(Utility.isConnectingToInternet(getApplicationContext())){
                                            loginOutApi();
                                        }else{
                                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                });

                                android.app.AlertDialog alert = builder.create();
                                alert.show();
                            }

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    } else {
                        pd.dismiss();
                        Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                    }
                }
            },
                    new Response.ErrorListener() {
                        @Override
                        public void onErrorResponse(VolleyError volleyError) {
                            pd.dismiss();
                            Log.e("Volley Error", volleyError.toString());
                            Toast.makeText(PatientDashboard.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
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
            //Creating a Request Queue
            RequestQueue requestQueue = Volley.newRequestQueue(PatientDashboard.this);

            //Adding request to the queue
            requestQueue.add(stringRequest);


        } catch (NoSuchAlgorithmException | InvalidKeySpecException | NoSuchPaddingException | InvalidKeyException | InvalidParameterSpecException |
                IllegalBlockSizeException | BadPaddingException | UnsupportedEncodingException | InvalidAlgorithmParameterException  exp) {
            Log.e("ENCRYPTION", exp.toString());
        }
    }

    @Override
    public void onRefresh() {

    }
}
