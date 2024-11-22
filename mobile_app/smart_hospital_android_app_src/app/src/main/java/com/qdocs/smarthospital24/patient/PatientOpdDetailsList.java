package com.qdocs.smarthospital24.patient;

import androidx.viewpager.widget.ViewPager;
import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import com.google.android.material.tabs.TabLayout;
import com.qdocs.smarthospital24.BaseActivity;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.ViewPagerAdapter;
import com.qdocs.smarthospital24.fragments.PatientOPDLabInvestigationFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDOverviewFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDTimelineFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDTreatHistoryFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVisitFragment;
import com.qdocs.smarthospital24.fragments.PatientOPDVitalsFragment;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;

public class PatientOpdDetailsList extends BaseActivity {
        TabLayout tabLayout;
        ViewPager viewPager;
        ViewPagerAdapter viewPagerAdapter;
        private int[] tabIcons = {
                R.drawable.ic_overview,
                R.drawable.ic_visit,
                R.drawable.ic_diagnosis,
                R.drawable.ic_timeline,
                R.drawable.ic_liveconsult,
                R.drawable.ic_vitals
        };
        public String defaultDateFormat, currency;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        LayoutInflater inflater = (LayoutInflater) this.getSystemService(LAYOUT_INFLATER_SERVICE);
        View contentView = inflater.inflate(R.layout.activity_patient_opd_details_list2, null, false);
        mDrawerLayout.addView(contentView, 0);

        defaultDateFormat = Utility.getSharedPreferences(getApplicationContext(), "dateFormat");
        currency = Utility.getSharedPreferences(getApplicationContext(), Constants.currency);

        titleTV.setText(getApplicationContext().getString(R.string.OPD));
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        viewPager = (ViewPager) findViewById(R.id.viewpager);

        viewPagerAdapter = new ViewPagerAdapter(getSupportFragmentManager(),this);
        viewPagerAdapter.addFragment(new PatientOPDOverviewFragment(), getApplicationContext().getString(R.string.Overview),tabIcons[0]);
        viewPagerAdapter.addFragment(new PatientOPDVisitFragment(), getApplicationContext().getString(R.string.visit),tabIcons[1]);
        viewPagerAdapter.addFragment(new PatientOPDLabInvestigationFragment(), getApplicationContext().getString(R.string.labinvestigation),tabIcons[2]);
        viewPagerAdapter.addFragment(new PatientOPDTreatHistoryFragment(), getApplicationContext().getString(R.string.treatmenthistory),tabIcons[2]);
        viewPagerAdapter.addFragment(new PatientOPDTimelineFragment(),  getApplicationContext().getString(R.string.timeline),tabIcons[3]);
        viewPagerAdapter.addFragment(new PatientOPDVitalsFragment(),  getApplicationContext().getString(R.string.vitals),tabIcons[5]);
        viewPager.setAdapter(viewPagerAdapter);
        tabLayout.setupWithViewPager(viewPager);
        tabLayout.setSelectedTabIndicatorColor(Color.parseColor(Utility.getSharedPreferences(getApplicationContext(), Constants.primaryColour)));
        highLightCurrentTab(0);
        viewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) { }
            @Override
            public void onPageSelected(int position) {
                highLightCurrentTab(position);
            }
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