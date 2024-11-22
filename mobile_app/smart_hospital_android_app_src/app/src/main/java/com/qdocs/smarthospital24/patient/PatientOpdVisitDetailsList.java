package com.qdocs.smarthospital24.patient;

import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;

import androidx.viewpager.widget.ViewPager;
import com.google.android.material.tabs.TabLayout;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.ViewPagerAdapter;
import com.qdocs.smarthospital24.fragments.PatientOPDLiveFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDMedicationFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDOperationFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitChargeFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitDetailFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitOverviewFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitPaymentFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitTimelineFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitsVitalsFragment;
import com.qdocs.smarthospital24.fragments.PatientVisitInvestigationFragment;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

public class PatientOpdVisitDetailsList extends BaseActivity {

    TabLayout tabLayout;
    ViewPager viewPager;
    ViewPagerAdapter viewPagerAdapter;
    private int[] tabIcons = {
            R.drawable.ic_overview,
            R.drawable.ic_visit,
            R.drawable.medication,
            R.drawable.ic_labinvestigation,
            R.drawable.ic_operation,
            R.drawable.ic_charges,
            R.drawable.payment,
            R.drawable.ic_liveconsult,
            R.drawable.ic_timeline,
            R.drawable.ic_vitals
    };
    public String defaultDateFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_patient_opd_details, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.OPDcheckup));
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        viewPager = (ViewPager) findViewById(R.id.viewpager);
        String opdid = getIntent().getStringExtra("opd_id");

        viewPagerAdapter = new ViewPagerAdapter(getSupportFragmentManager(),this);
        viewPagerAdapter.addFragment(new PatientOPDVisitOverviewFragment(opdid), getApplicationContext().getString(R.string.Overview),tabIcons[0]);
        viewPagerAdapter.addFragment(new PatientOPDVisitDetailFragment(opdid), getApplicationContext().getString(R.string.visit),tabIcons[1]);
        viewPagerAdapter.addFragment(new PatientOPDMedicationFragment(opdid), getApplicationContext().getString(R.string.medication),tabIcons[2]);
        viewPagerAdapter.addFragment(new PatientVisitInvestigationFragment(opdid), getApplicationContext().getString(R.string.labinvestigation),tabIcons[3]);
        viewPagerAdapter.addFragment(new PatientOPDOperationFragment(opdid), getApplicationContext().getString(R.string.operation),tabIcons[4]);
        viewPagerAdapter.addFragment(new PatientOPDVisitChargeFragment(opdid),  getApplicationContext().getString(R.string.charge),tabIcons[5]);
        viewPagerAdapter.addFragment(new PatientOPDVisitPaymentFragment(opdid),  getApplicationContext().getString(R.string.payment),tabIcons[6]);
        viewPagerAdapter.addFragment(new PatientOPDLiveFragment(opdid),  getApplicationContext().getString(R.string.liveconsult),tabIcons[7]);
        viewPagerAdapter.addFragment(new PatientOPDVisitTimelineFragment(opdid),  getApplicationContext().getString(R.string.timeline),tabIcons[8]);
        viewPagerAdapter.addFragment(new PatientOPDVisitsVitalsFragment(opdid),  getApplicationContext().getString(R.string.vitals),tabIcons[9]);

        viewPager.setAdapter(viewPagerAdapter);
        tabLayout.setupWithViewPager(viewPager);
        tabLayout.setSelectedTabIndicatorColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        highLightCurrentTab(0);
        viewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {

            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) { }
            @Override
            public void onPageSelected(int position) { highLightCurrentTab(position); }
            @Override
            public void onPageScrollStateChanged(int state) { }
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