package com.qdocs.smarthospital24;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.text.method.PasswordTransformationMethod;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.material.snackbar.Snackbar;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.InstanceIdResult;
import com.qdocs.smarthospital24.adapters.PatientCusomFiledAdapter;
import com.qdocs.smarthospital24.adapters.SlotsAdapter;
import com.qdocs.smarthospital24.patient.PatientDashboard;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Map;
import java.util.Set;
import static android.widget.Toast.makeText;

public class PatientFrontAddAppoinment extends AppCompatActivity implements DataTransferInterface {

    RecyclerView recyclerview;
    EditText Dates,message;
    ListView slots;
    GridView bottom_timeslots;
     String clickedItem;
    ArrayList<String> myList;
    TextView noslots;
     Button save_patient_appoinment;
     EditText phone;
    boolean isPasswordVisible = false;
     EditText email;
    Spinner Doctor,Live,Specialist,Shift;
    String doctor_id="",text="",shift_id="",spec_id="";
    String dates="",num="";
    ArrayList<String>doctorlist=new ArrayList<String>();
    ArrayList<String>specialistlist=new ArrayList<String>();
    ArrayList<String>shiftlist=new ArrayList<String>();
    ArrayList<String>slotlist=new ArrayList<String>();
    ArrayList<String>livelist=new ArrayList<String>();
    ArrayList<String>genderlist=new ArrayList<String>();
    ArrayList<String>doctoridlist=new ArrayList<String>();
    ArrayList<String>specialistidlist=new ArrayList<String>();
    ArrayList<String>shiftidlist=new ArrayList<String>();
    ArrayList<String>slotidlist=new ArrayList<String>();
    public Map<String, String> headers = new HashMap<String, String>();
    public Map<String, String> params = new Hashtable<String, String>();
    ArrayList<String> idlist = new ArrayList<String>();
    ArrayList<String> nameList = new ArrayList<String>();
    ArrayList<String> typeList = new ArrayList<String>();
    public ImageView backBtn;
    PatientCusomFiledAdapter adapter;
    String gender_item="";
    public String defaultDateFormat, currency;
    public TextView titleTV;
    protected FrameLayout mDrawerLayout, actionBar;
    String device_token;
    JSONArray custom_fields = new JSONArray();
    JSONArray finaldata = new JSONArray();
    JSONArray finalizedata = new JSONArray();
     Dialog slotsdialog;
    GridView timeslots;
    String slots_id;
     Dialog dialog;
    @SuppressLint("WrongViewCast")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.front_add_appointment);
        overridePendingTransition(R.anim.enter, R.anim.exit);
        backBtn = findViewById(R.id.actionBar_backBtn);
        mDrawerLayout = findViewById(R.id.container);
        actionBar = findViewById(R.id.actionBarSecondary);
        titleTV = findViewById(R.id.actionBar_title);
        timeslots = findViewById(R.id.timeslots);
        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);
        decorate();
        Utility.setLocale(getApplicationContext(),Utility.getSharedPreferences(getApplicationContext(), Constants.langCode));

        backBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        titleTV.setText(getApplicationContext().getString(R.string.bookAppoinments));

        if(Utility.isConnectingToInternet(getApplicationContext())){
            getScannerDataFromApi();
        }else{
            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
        }

        FirebaseInstanceId.getInstance().getInstanceId().addOnSuccessListener( PatientFrontAddAppoinment.this,  new OnSuccessListener<InstanceIdResult>() {
            @Override
            public void onSuccess(InstanceIdResult instanceIdResult) {
                device_token = FirebaseInstanceId.getInstance().getToken()+"";
                Log.e("DEVICE TOKEN",device_token);
                System.out.println("DEVICE TOKEN="+device_token);
            }
        });
        params.put("belong_to","appointment");
        JSONObject customobj=new JSONObject(params);
        Log.e("params ", customobj.toString());
        getCustomFieldApi(customobj.toString());

        specialistlist.add("Select");
        specialistidlist.add("0");
        doctorlist.add("Select");
        doctoridlist.add("0");
        shiftlist.add("Select");
        shiftidlist.add("0");
        slotlist.add("Select");
        slotidlist.add("0");

        Dates=findViewById(R.id.date);

        slots=findViewById(R.id.slots);
        message=findViewById(R.id.message);

        noslots=findViewById(R.id.noslots);

        Doctor=findViewById(R.id.Doctor_spinner);
        recyclerview=findViewById(R.id.recyclerview);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerview.setLayoutManager(mLayoutManager);
        recyclerview.setItemAnimator(new DefaultItemAnimator());
        adapter = new PatientCusomFiledAdapter(PatientFrontAddAppoinment.this,idlist,nameList,typeList,this);
        recyclerview.setAdapter(adapter);
        Live=findViewById(R.id.Live_spinner);
        Specialist=findViewById(R.id.specialist_spinner);
        Shift=findViewById(R.id.Shift_spinner);


        Dates.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Calendar mcurrentDate = Calendar.getInstance();

                int mDay   = mcurrentDate.get(Calendar.DAY_OF_MONTH);
                int mMonth = mcurrentDate.get(Calendar.MONTH);
                int mYear  = mcurrentDate.get(Calendar.YEAR);
                DatePickerDialog datePickerDialog = new DatePickerDialog(PatientFrontAddAppoinment.this, new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker datepicker, int selectedyear, int selectedmonth, int selectedday) {
                        Calendar newDate = Calendar.getInstance();
                        newDate.set(selectedyear, selectedmonth, selectedday);
                        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                        dates=sdf.format(newDate.getTime());
                        SimpleDateFormat sdfdate = new SimpleDateFormat("dd/MM/yyyy");
                      //  String add=sdfdate.format(newDate.getTime());
                        Dates.setText(sdfdate.format(newDate.getTime())) ;
                        params.clear();
                        params.put("date",dates);
                        params.put("doctor_id",doctor_id);
                        params.put("global_shift",shift_id);
                        JSONObject obj=new JSONObject(params);
                        Log.e("params ", obj.toString());
                        System.out.println("params== "+obj.toString());
                        if(Utility.isConnectingToInternet(getApplicationContext())){
                            getSlotFromApi(obj.toString());

                        }else{
                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                        }
                    }
                }, mYear, mMonth, mDay);
                datePickerDialog.show();
            }
        });

        livelist.add("No");
        livelist.add("Yes");
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(PatientFrontAddAppoinment.this, android.R.layout.simple_spinner_item, livelist);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Live.setAdapter(dataAdapter);
        Live.setSelection(0);
        Live.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                text = Live.getSelectedItem().toString();
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        ArrayAdapter<String> shiftAdapter = new ArrayAdapter<String>(PatientFrontAddAppoinment.this, android.R.layout.simple_spinner_item, shiftlist);
        shiftAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Shift.setAdapter(shiftAdapter);
        Shift.setSelection(0);
        Shift.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                shift_id = shiftidlist.get(i);
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        ArrayAdapter<String> doctorAdapter = new ArrayAdapter<String>(PatientFrontAddAppoinment.this, android.R.layout.simple_spinner_item, doctorlist);
        doctorAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Doctor.setAdapter(doctorAdapter);
        Doctor.setSelection(0);
        Doctor.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                params.clear();
                 doctor_id = doctoridlist.get(i);
                params.put("doctor_id",doctor_id);
                JSONObject obj=new JSONObject(params);
                Log.e("params ", obj.toString());
                if(Utility.isConnectingToInternet(getApplicationContext())){
                    getShiftFromApi(obj.toString());
                }else{
                    makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

        ArrayAdapter<String> dataAdapters = new ArrayAdapter<String>(PatientFrontAddAppoinment.this, android.R.layout.simple_spinner_item, specialistlist);
        dataAdapters.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        Specialist.setAdapter(dataAdapters);
        Specialist.setSelection(0);
        Specialist.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                   @Override
                   public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                       params.clear();
                        spec_id = specialistidlist.get(i);
                       params.put("specialistID",spec_id);
                       JSONObject obj=new JSONObject(params);
                       Log.e("params ", obj.toString());
                       if(Utility.isConnectingToInternet(getApplicationContext())){
                           getDoctorFromApi(obj.toString());
                       }else{
                           makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                       }
                       Doctor.setSelection(0);
                   }
                   @Override
                   public void onNothingSelected(AdapterView<?> adapterView) { }
               });
    }

    private void decorate() {
        actionBar.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        }
    }

    private void getloginDataFromApi (String bodyParams) {
        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.loginUrl;
        Log.e("URL", url);
        System.out.println("URL=="+ url);
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
                            JSONObject data = object.getJSONObject("record");

                            Utility.setSharedPreference(getApplicationContext(), Constants.userId, object.getString("id"));
                            Utility.setSharedPreference(getApplicationContext(), "accessToken", object.getString("token"));
                            Utility.setSharedPreference(getApplicationContext(), Constants.currency, data.getString("currency_symbol"));
                            Utility.setSharedPreference(getApplicationContext(), Constants.loginType, data.getString("role"));
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

                        } else {
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
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.invalidPassword, Toast.LENGTH_LONG).show();
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
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);//Creating a Request Queue
        requestQueue.add(stringRequest); //Adding request to the queue
    }


    private void getScannerDataFromApi () {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getSpecialistfronttUrl;

        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("specialist");

                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                specialistlist.add(dataArray.getJSONObject(i).getString("specialist_name"));
                                System.out.println("specialistlist=="+specialistlist);
                                specialistidlist.add(dataArray.getJSONObject(i).getString("id"));
                            }
                    }
                } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
                return headers;
            }
            @Override
            public String getBodyContentType() {
                return "application/json; charset=utf-8";
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getslotbyshift (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getslotbyshiftUrl;

        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        ArrayList<String> timelist=new ArrayList<String>();
                        ArrayList<String> filledlist=new ArrayList<String>();
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                timelist.add(dataArray.getJSONObject(i).getString("time"));
                                filledlist.add(dataArray.getJSONObject(i).getString("filled"));
                            }

                        }

                        bottom_timeslots.setVisibility(View.VISIBLE);
                        SlotsAdapter slotAdapter = new SlotsAdapter(PatientFrontAddAppoinment.this, timelist,filledlist,bottom_timeslots);
                        bottom_timeslots.setAdapter(slotAdapter);

                        bottom_timeslots.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                            @Override
                            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                                 slotsdialog.dismiss();
                                 clickedItem=(String) bottom_timeslots.getItemAtPosition(position);
                                dialog = new Dialog(PatientFrontAddAppoinment.this);
                                dialog.setContentView(R.layout.add_appointment_frontside);
                                dialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.FILL_PARENT);
                                dialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;
                                final EditText name = (EditText) dialog.findViewById(R.id.name);
                                RecyclerView recyclerView=dialog.findViewById(R.id.recyclerview);
                                 save_patient_appoinment = (Button) dialog.findViewById(R.id.save_patient_appoinment);
                                 phone = (EditText) dialog.findViewById(R.id.phone);
                                 email = (EditText) dialog.findViewById(R.id.email);
                                final EditText username = (EditText) dialog.findViewById(R.id.username);
                                final EditText password = (EditText) dialog.findViewById(R.id.password);
                                final RadioGroup radioGroup = (RadioGroup) dialog.findViewById(R.id.radiogroup);
                                final RadioButton new_radioButton =  dialog.findViewById(R.id.new_radioButton);
                                final ImageView btn_showPassword =  dialog.findViewById(R.id.login_password_visibleBtn);
                                new_radioButton.setChecked(true);
                                num="new";

                                final RadioButton old_radioButton =  dialog.findViewById(R.id.old_radioButton);
                                final LinearLayout newPatient_layout =  dialog.findViewById(R.id.newPatient_layout);
                                final LinearLayout oldPatient_layout =  dialog.findViewById(R.id.oldPatient_layout);
                                final RelativeLayout header  =  dialog.findViewById(R.id.addappoint_dialog_header);
                                btn_showPassword.setOnClickListener(new View.OnClickListener() {
                                    @Override
                                    public void onClick(View view) {
                                        if(!isPasswordVisible) {
                                            password.setTransformationMethod(null);
                                            btn_showPassword.setImageResource(R.drawable.eye_black);
                                            isPasswordVisible = true;
                                        } else {
                                            password.setTransformationMethod(new PasswordTransformationMethod());
                                            btn_showPassword.setImageResource(R.drawable.eye_grey);
                                            isPasswordVisible = false;
                                        }
                                    }
                                });

                                radioGroup.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                                    @Override
                                    public void onCheckedChanged(RadioGroup radioGroup, int i) {
                                        switch(i) {
                                            case R.id.new_radioButton:
                                                num="new";
                                                newPatient_layout.setVisibility(View.VISIBLE);
                                                oldPatient_layout.setVisibility(View.GONE);
                                                break;
                                            case R.id.old_radioButton:
                                                num="old";
                                                newPatient_layout.setVisibility(View.GONE);
                                                oldPatient_layout.setVisibility(View.VISIBLE);
                                                break;
                                        }
                                    }
                                });

                                final Spinner gender=(Spinner) dialog.findViewById(R.id.gender);
                                genderlist.clear();
                                genderlist.add("Male");
                                genderlist.add("Female");
                                ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(PatientFrontAddAppoinment.this, android.R.layout.simple_spinner_item, genderlist);
                                dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                                gender.setAdapter(dataAdapter);
                                gender.setSelection(0);
                                gender.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                                    @Override
                                    public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                                        gender_item = gender.getSelectedItem().toString();
                                    }
                                    @Override
                                    public void onNothingSelected(AdapterView<?> adapterView) { }
                                });

                                final ImageView closeBtn=(ImageView) dialog.findViewById(R.id.dialog_crossIcon);
                                header.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
                                save_patient_appoinment.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));

                                save_patient_appoinment.setOnClickListener(new View.OnClickListener() {
                                    @Override
                                    public void onClick(View view) {
                                        for(int i=0;i<custom_fields.length();i++){
                                            try {
                                                custom_fields.getJSONObject(i);
                                                finaldata.put(custom_fields.getJSONObject(i));
                                                System.out.println("custom_fields final=="+finaldata);

                                            } catch (JSONException e) {
                                                e.printStackTrace();
                                            }
                                        }

                                        Set<String> customdata=new HashSet<String>();
                                        JSONArray tempArray=new JSONArray();
                                        for(int i=0;i<finaldata.length();i++){
                                            try {
                                                String stationCode = finaldata.getJSONObject(i).getString("custom_field_id");
                                                String fieldvalue = finaldata.getJSONObject(i).getString("field_value");
                                                System.out.println("stationCode== " + stationCode);
                                                if(customdata.contains(stationCode)){
                                                    System.out.println("tempArraycontains== " + tempArray);
                                                    for(int j=0;j<tempArray.length();j++){
                                                        if(tempArray.getJSONObject(j).getString("custom_field_id").equals(stationCode)){
                                                            tempArray.getJSONObject(j).put("field_value",fieldvalue);
                                                        }
                                                    }
                                                  continue;

                                                } else{
                                                    customdata.add(stationCode);
                                                    tempArray.put(finaldata.getJSONObject(i));
                                                }
                                            } catch (JSONException e) {
                                                e.printStackTrace();
                                            }
                                        }
                                        System.out.println("customdata== " + customdata.toString());

                                        finaldata= tempArray; //assign temp to original
                                        System.out.println("tempArray== " + tempArray);

                                        if(Utility.isConnectingToInternet(getApplicationContext())){
                                            clickedItem= clickedItem.replaceAll("\"", "");
                                            clickedItem= clickedItem.replaceAll("AM", "");
                                            clickedItem= clickedItem.replaceAll("PM", "");
                                            clickedItem = clickedItem.trim();
                                            params.clear();
                                            params.put("time", clickedItem);
                                            params.put("date", dates);
                                            params.put("message", message.getText().toString());
                                            params.put("doctor", doctor_id);
                                            params.put("live_consult",text);
                                            params.put("specialist",spec_id);
                                            params.put("shift_id",slots_id);
                                            params.put("global_shift", shift_id);
                                            params.put("patient_type",num);
                                            params.put("patient_gender", gender_item);
                                            params.put("patient_email", email.getText().toString());
                                            params.put("patient_phone", phone.getText().toString());
                                            params.put("patient_name", name.getText().toString());
                                            params.put("username", username.getText().toString());
                                            params.put("password", password.getText().toString());
                                           // params.put("custom_fields",finaldata);
                                            if(num.equals("new")){
                                                if(name.getText().toString().isEmpty()){
                                                    name.setError("The Name Field is Required !!");
                                                    name.requestFocus();
                                                    return;
                                                }else if(email.getText().toString().isEmpty()){
                                                    email.setError("The Email Field is Required !!");
                                                    email.requestFocus();
                                                    return;
                                                }else if(gender_item.equals("")){
                                                    Toast.makeText(PatientFrontAddAppoinment.this, "The Gender Field is required!", Toast.LENGTH_SHORT).show();
                                                }else if(phone.getText().toString().isEmpty()){
                                                    phone.setError("The Phone Field is Required !!");
                                                    phone.requestFocus();
                                                    return;
                                                }else{
                                                    if (Utility.isConnectingToInternet(PatientFrontAddAppoinment.this)) {
                                                        try {
                                                            JSONObject obj = new JSONObject(params);
                                                            obj.put("custom_fields",finaldata);
                                                            Log.e("params ", obj.toString());
                                                            System.out.println("params==" + obj.toString());
                                                            addappointApi(obj.toString());
                                                        } catch (JSONException e) {
                                                            e.printStackTrace();
                                                        }


                                                    } else {
                                                        makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                                    }
                                                }
                                            }else if(num.equals("old")){
                                                if(username.getText().toString().isEmpty()){
                                                    username.setError("Please Enter Username");
                                                    username.requestFocus();
                                                    return;
                                                }else if(password.getText().toString().isEmpty()){
                                                    password.setError("Please Enter Password");
                                                    password.requestFocus();
                                                    return;
                                                }else{
                                                    if(Utility.isConnectingToInternet(PatientFrontAddAppoinment.this)){
                                                        try {
                                                            JSONObject obj = new JSONObject(params);
                                                            obj.put("custom_fields",finaldata);
                                                            Log.e("params ", obj.toString());
                                                            System.out.println("params==" + obj.toString());
                                                            addappointApi(obj.toString());
                                                        } catch (JSONException e) {
                                                            e.printStackTrace();
                                                        }
                                                        //dialog.dismiss();
                                                    }else{
                                                        makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                                    }
                                                }
                                            }
                                        }else{
                                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                });

                                closeBtn.setOnClickListener(new View.OnClickListener() {
                                    @Override
                                    public void onClick(View view) {
                                        dialog.dismiss();
                                    }
                                });
                                dialog.show();
                            }
                        });
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getDoctorFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getDoctorfrontUrl;

        Log.e("URL: ",url);
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("doctors");
                        doctorlist.clear();
                        doctoridlist.clear();
                        doctorlist.add("Select");
                        doctoridlist.add("0");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                doctorlist.add(dataArray.getJSONObject(i).getString("name")+" "+dataArray.getJSONObject(i).getString("surname")+"("+dataArray.getJSONObject(i).getString("employee_id")+")");
                                doctoridlist.add(dataArray.getJSONObject(i).getString("id"));
                            }
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getShiftFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.doctorshiftbyidUrl;

        Log.e("URL: ",url);
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");
                        shiftlist.clear();
                        shiftidlist.clear();
                        shiftlist.add("Select");
                        shiftidlist.add("0");
                        if(dataArray.length() != 0) {
                            for(int i = 0; i < dataArray.length(); i++) {
                                shiftlist.add(dataArray.getJSONObject(i).getString("name"));
                                shiftidlist.add(dataArray.getJSONObject(i).getString("id"));
                            }
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getSlotFromApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;
        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getslotUrl;

        Log.e("URL: ",url);
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("result");

                        slotlist.clear();
                        slotidlist.clear();
                        if(dataArray.length() != 0) {
                            noslots.setVisibility(View.GONE);
                            slots.setVisibility(View.VISIBLE);
                            for(int i = 0; i < dataArray.length(); i++) {
                                slotlist.add(dataArray.getJSONObject(i).getString("start_time")+"-"+dataArray.getJSONObject(i).getString("end_time"));
                                slotidlist.add(dataArray.getJSONObject(i).getString("id"));

                                ArrayAdapter<String> slotAdapter = new ArrayAdapter<String>(getApplicationContext(),android.R.layout.simple_list_item_1, slotlist);
                                slots.setAdapter(slotAdapter);
                                slots.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                                    @Override
                                    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                                        slots_id=slotidlist.get(position);
                                        Calendar c = Calendar.getInstance();
                                        final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                                        final String getCurrentDateTime = sdf.format(c.getTime());
                                        final String getstartTime= dates;
                                        if (getCurrentDateTime.compareTo(getstartTime) > 0) {
                                            Snackbar snackbar = Snackbar.make(slots, "Appointment Time Is Expired",Snackbar.LENGTH_SHORT);
                                            snackbar.show();
                                        }else{

                                        }
                                        final String clickedItem=(String) slots.getItemAtPosition(position);
                                        slotsdialog = new Dialog(PatientFrontAddAppoinment.this);
                                        slotsdialog.setContentView(R.layout.bottom_sheet);
                                        slotsdialog.getWindow().setLayout(WindowManager.LayoutParams.FILL_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
                                        slotsdialog.getWindow().getAttributes().windowAnimations = R.style.DialogTheme;

                                        TextView headerTV = slotsdialog.findViewById(R.id.patient_bottomSheet__header);
                                        ImageView crossBtn = slotsdialog.findViewById(R.id.patient_bottomSheet__crossBtn);
                                        bottom_timeslots = slotsdialog.findViewById(R.id.bottom_timeslots);

                                        if(Utility.isConnectingToInternet(getApplicationContext())){
                                            if(message.getText().toString().isEmpty()){
                                                message.setError("The Message Field is Required !!");
                                                message.requestFocus();
                                                return;
                                            }else {
                                                params.clear();
                                                params.put("doctor_id", doctor_id);
                                                params.put("shift", slotidlist.get(position));
                                                params.put("date", dates);
                                                params.put("global_shift", shift_id);
                                                JSONObject slotobj = new JSONObject(params);
                                                Log.e("slotparams== ", slotobj.toString());
                                                System.out.println("slotparams== "+slotobj.toString());
                                                getslotbyshift(slotobj.toString());
                                            }
                                        }else{
                                            makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                                        }
                                        headerTV.setBackgroundColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
                                        headerTV.setText(getString(R.string.slot));
                                        crossBtn.setOnClickListener(new View.OnClickListener() {
                                            @Override
                                            public void onClick(View view) {
                                                slotsdialog.dismiss();
                                            }
                                        });
                                        slotsdialog.show();
                                     }
                                });
                            }
                        }else{
                            noslots.setVisibility(View.VISIBLE);
                            slots.setVisibility(View.GONE);
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }else {
                    pd.dismiss();
                    Toast.makeText(getApplicationContext(), R.string.noInternetMsg, Toast.LENGTH_SHORT).show();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                pd.dismiss();
                Log.e("Volley Error", volleyError.toString());
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s",requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void getCustomFieldApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.getcustomfieldsUrl;

        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        JSONObject obj = new JSONObject(result);
                        JSONArray dataArray = obj.getJSONArray("custom_fields");
                        if(dataArray.length() != 0){
                            for(int i=0; i<dataArray.length(); i++) {
                                JSONObject appoint = dataArray.getJSONObject(i);
                                idlist.add(appoint.getString("id"));
                                nameList.add(appoint.getString("name"));
                                typeList.add(appoint.getString("type"));
                            }
                            adapter.notifyDataSetChanged();
                        }else{ }

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
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }

    private void addappointApi (String bodyParams) {

        final ProgressDialog pd = new ProgressDialog(this);
        pd.setMessage("Loading");
        pd.setCancelable(false);
        pd.show();
        final String requestBody = bodyParams;

        String url = Utility.getSharedPreferences(getApplicationContext(), "apiUrl")+Constants.addAppointmentFrontUrl;
        Log.e("URL: ",url);
        Log.e("bodyParams ",bodyParams);
        Log.e("userId ", Utility.getSharedPreferences(getApplicationContext(), "userId"));
        Log.e("accessToken ", Utility.getSharedPreferences(getApplicationContext(), "accessToken"));
        Log.e("clientService ",Constants.clientService);
        Log.e("authKey ",Constants.authKey);
        Log.e("contentType ",Constants.contentType);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String result) {
                if (result != null) {
                    pd.dismiss();
                    try {
                        Log.e("Result", result);
                        System.out.println("Result=="+ result);
                        JSONObject obj = new JSONObject(result);
                           String status=obj.getString("status");
                           if(status.equals("1")) {
                               JSONObject data = obj.getJSONObject("data");
                               params.put("username", data.getString("username"));
                               params.put("password", data.getString("password"));
                               params.put("deviceToken", device_token);
                               JSONObject loginobj = new JSONObject(params);
                               Log.e("params ", loginobj.toString());
                               getloginDataFromApi(loginobj.toString());
                               dialog.dismiss();
                               Toast.makeText(PatientFrontAddAppoinment.this, getApplicationContext().getString(R.string.successAppoinments), Toast.LENGTH_SHORT).show();
                           }else if(status.equals("0")) {
                               String results=obj.getString("result");
                               Toast.makeText(PatientFrontAddAppoinment.this, results.toString(), Toast.LENGTH_SHORT);
                           }/*else{
                               JSONObject data = obj.getJSONObject("data");
                               if (data.has("mobile_exist")) {
                                   //get Value of mobile_exist
                                   String mobile_exist = data.optString("mobile_exist");
                                   phone.setError(mobile_exist);
                                   phone.requestFocus();
                                   return;
                               }else if (data.has("email_exist")) {
                                   //get Value of email_exist
                                   String email_exist = data.optString("email_exist");
                                   email.setError(email_exist);
                                   email.requestFocus();
                                   return;
                               }
                               for(int k = 0; k < idlist.size(); k++) {
                                  String customid= idlist.get(k);
                                  System.out.println("customid="+data.getString("custom_field_"+customid));
                                  data.getString("custom_field_"+customid);
                                  Snackbar snackbar = Snackbar.make(recyclerview, data.getString("custom_field_"+customid),Snackbar.LENGTH_SHORT);
                                  snackbar.show();
                               }
                               return;
                           }*/
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
                Toast.makeText(PatientFrontAddAppoinment.this, R.string.apiErrorMsg, Toast.LENGTH_LONG).show();
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                headers.put("Client-Service", Constants.clientService);
                headers.put("Auth-Key", Constants.authKey);
                headers.put("Content-Type", Constants.contentType);
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
                    VolleyLog.wtf("UnSupported Encoding while trying to get the bytes of %s using %s", requestBody, "utf-8");return null;
                }
            }
        };
        RequestQueue requestQueue = Volley.newRequestQueue(PatientFrontAddAppoinment.this);
        requestQueue.add(stringRequest);
    }


    @Override
    public void onSetValues(JSONArray customfield_data) {

        custom_fields=customfield_data;
        System.out.println("custom_fields="+custom_fields);



    }
}
