package com.qdocs.smarthospital24.patient;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.TextView;
import androidx.viewpager.widget.ViewPager;
import com.google.android.material.tabs.TabLayout;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.ViewPagerAdapter;
import com.qdocs.smarthospital24.fragments.PatientIPDAntenatalHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDBedHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDChargeFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDConsultantFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDLabInvestigationFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDLiveFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDMedicationFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDNurseNoteFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDObstericHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDOperationFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDOverviewFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDPaymentFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDPostnatalHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDPrescriptionFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDTimelineFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDTreatmentHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientIPDVitalsFragment;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class PatientIpdDetailsList extends BaseActivity {
    TabLayout tabLayout;
    ViewPager viewPager;
    ViewPagerAdapter viewPagerAdapter;
    private int[] tabIcons = {
            R.drawable.ic_overview,
            R.drawable.ic_timeline,
            R.drawable.prescription,
            R.drawable.ic_profile_plus,
            R.drawable.ic_labinvestigation,
            R.drawable.ic_operation,
            R.drawable.ic_charges,
            R.drawable.payment,
            R.drawable.ic_liveconsult,
            R.drawable.nursenote,
            R.drawable.ic_timeline,
            R.drawable.ic_treatmenthistory,
            R.drawable.ic_bed,
            R.drawable.ic_vitals,
            R.drawable.ic_labinvestigation,
            R.drawable.ic_labinvestigation,
            R.drawable.ic_labinvestigation,
    };

    FrameLayout dischargelist;
    public TextView ipdno,gender,phone, bed;
    public String defaultDateFormat,defaultDatetimeFormat,currency;
    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();
    String ipdnos="";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_patient_ipd_details_list2, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDatetimeFormat = Utility.getSharedPreferences(getApplicationContext(), "datetimeFormat");
        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);
        titleTV.setText(getApplicationContext().getString(R.string.IPD));
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        viewPager = (ViewPager) findViewById(R.id.viewpager);
        dischargelist = (FrameLayout) findViewById(R.id.dischargelist);


        String gender=Utility.getSharedPreferences(getApplicationContext(), "gender");
        if(gender.equals("Female")){
            viewPagerAdapter = new ViewPagerAdapter(getSupportFragmentManager(), this);
            viewPagerAdapter.addFragment(new PatientIPDOverviewFragment(ipdnos), getApplicationContext().getString(R.string.Overview), tabIcons[0]);
            viewPagerAdapter.addFragment(new PatientIPDMedicationFragment(ipdnos), getApplicationContext().getString(R.string.medication), tabIcons[1]);
            viewPagerAdapter.addFragment(new PatientIPDPrescriptionFragment(ipdnos), getApplicationContext().getString(R.string.prescription), tabIcons[2]);
            viewPagerAdapter.addFragment(new PatientIPDConsultantFragment(ipdnos), getApplicationContext().getString(R.string.consultant), tabIcons[3]);
            viewPagerAdapter.addFragment(new PatientIPDLabInvestigationFragment(ipdnos), getApplicationContext().getString(R.string.labinvestigation), tabIcons[4]);
            viewPagerAdapter.addFragment(new PatientIPDOperationFragment(ipdnos), getApplicationContext().getString(R.string.operation), tabIcons[5]);
            viewPagerAdapter.addFragment(new PatientIPDChargeFragment(ipdnos), getApplicationContext().getString(R.string.charge), tabIcons[6]);
            viewPagerAdapter.addFragment(new PatientIPDPaymentFragment(ipdnos), getApplicationContext().getString(R.string.payment), tabIcons[7]);
            viewPagerAdapter.addFragment(new PatientIPDLiveFragment(ipdnos), getApplicationContext().getString(R.string.liveconsult), tabIcons[8]);
            viewPagerAdapter.addFragment(new PatientIPDNurseNoteFragment(ipdnos), getApplicationContext().getString(R.string.nurse_note), tabIcons[9]);
            viewPagerAdapter.addFragment(new PatientIPDTimelineFragment(ipdnos), getApplicationContext().getString(R.string.timeline), tabIcons[10]);
            viewPagerAdapter.addFragment(new PatientIPDTreatmentHistoryFragment(ipdnos), getApplicationContext().getString(R.string.treatmenthistory), tabIcons[11]);
            viewPagerAdapter.addFragment(new PatientIPDBedHistoryFragment(ipdnos), getApplicationContext().getString(R.string.bed_history), tabIcons[12]);
            viewPagerAdapter.addFragment(new PatientIPDVitalsFragment(ipdnos), getApplicationContext().getString(R.string.vitals), tabIcons[13]);
            viewPagerAdapter.addFragment(new PatientIPDObstericHistoryFragment(ipdnos), getApplicationContext().getString(R.string.prev_obs_history), tabIcons[14]);
            viewPagerAdapter.addFragment(new PatientIPDPostnatalHistoryFragment(ipdnos), getApplicationContext().getString(R.string.post_history), tabIcons[15]);
            viewPagerAdapter.addFragment(new PatientIPDAntenatalHistoryFragment(ipdnos), getApplicationContext().getString(R.string.antenatal), tabIcons[16]);

        }else{
            viewPagerAdapter = new ViewPagerAdapter(getSupportFragmentManager(), this);
            viewPagerAdapter.addFragment(new PatientIPDOverviewFragment(ipdnos), getApplicationContext().getString(R.string.Overview), tabIcons[0]);
            viewPagerAdapter.addFragment(new PatientIPDMedicationFragment(ipdnos), getApplicationContext().getString(R.string.medication), tabIcons[1]);
            viewPagerAdapter.addFragment(new PatientIPDPrescriptionFragment(ipdnos), getApplicationContext().getString(R.string.prescription), tabIcons[2]);
            viewPagerAdapter.addFragment(new PatientIPDConsultantFragment(ipdnos), getApplicationContext().getString(R.string.consultant), tabIcons[3]);
            viewPagerAdapter.addFragment(new PatientIPDLabInvestigationFragment(ipdnos), getApplicationContext().getString(R.string.labinvestigation), tabIcons[4]);
            viewPagerAdapter.addFragment(new PatientIPDOperationFragment(ipdnos), getApplicationContext().getString(R.string.operation), tabIcons[5]);
            viewPagerAdapter.addFragment(new PatientIPDChargeFragment(ipdnos), getApplicationContext().getString(R.string.charge), tabIcons[6]);
            viewPagerAdapter.addFragment(new PatientIPDPaymentFragment(ipdnos), getApplicationContext().getString(R.string.payment), tabIcons[7]);
            viewPagerAdapter.addFragment(new PatientIPDLiveFragment(ipdnos), getApplicationContext().getString(R.string.liveconsult), tabIcons[8]);
            viewPagerAdapter.addFragment(new PatientIPDNurseNoteFragment(ipdnos), getApplicationContext().getString(R.string.nurse_note), tabIcons[9]);
            viewPagerAdapter.addFragment(new PatientIPDTimelineFragment(ipdnos), getApplicationContext().getString(R.string.timeline), tabIcons[10]);
            viewPagerAdapter.addFragment(new PatientIPDTreatmentHistoryFragment(ipdnos), getApplicationContext().getString(R.string.treatmenthistory), tabIcons[11]);
            viewPagerAdapter.addFragment(new PatientIPDBedHistoryFragment(ipdnos), getApplicationContext().getString(R.string.bed_history), tabIcons[12]);
            viewPagerAdapter.addFragment(new PatientIPDVitalsFragment(ipdnos), getApplicationContext().getString(R.string.vitals), tabIcons[13]);
        }


        viewPager.setAdapter(viewPagerAdapter);
        tabLayout.setupWithViewPager(viewPager);
        tabLayout.setSelectedTabIndicatorColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        highLightCurrentTab(0);
        viewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) { }

            @Override
            public void onPageSelected(int position) {
                highLightCurrentTab(position);  }

            @Override
            public void onPageScrollStateChanged(int state) { }

        });
        dischargelist.setVisibility(View.VISIBLE);
        dischargelist.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent=new Intent(PatientIpdDetailsList.this,PatientIpdPatientLists.class);
                startActivity(intent);
            }
        });
    }

    private void highLightCurrentTab(int position) {
        for (int i = 0; i < tabLayout.getTabCount(); i++) {
            TabLayout.Tab tab = tabLayout.getTabAt(i);
            assert tab != null;
            tab.setCustomView(null);
            tab.setCustomView(viewPagerAdapter.getTabView(i));
        }
        TabLayout.Tab tab = tabLayout.getTabAt(position);
        assert tab != null;
        tab.setCustomView(null);
        tab.setCustomView(viewPagerAdapter.getSelectedTabView(position));
    }
}