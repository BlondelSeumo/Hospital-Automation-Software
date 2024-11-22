package com.qdocs.smarthospital24.fragments;

import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.RecyclerView;
import androidx.viewpager.widget.ViewPager;
import com.google.android.material.tabs.TabLayout;
import com.qdocs.smarthospital24.R;
import com.qdocs.smarthospital24.adapters.ViewPagerAdapter;
import com.qdocs.smarthospital24.utils.Constants;
import com.qdocs.smarthospital24.utils.Utility;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map;

public class PatientDashboardOPDList extends Fragment{

    RecyclerView radiologyListView;
    LinearLayout nodata_layout;
    public String defaultDateFormat, currency;
    TabLayout tabLayout;
    protected FrameLayout mDrawerLayout, actionBar;
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

    public Map<String, String> params = new Hashtable<String, String>();
    public Map<String, String> headers = new HashMap<String, String>();

    public PatientDashboardOPDList() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View mainView = inflater.inflate(R.layout.patient_opd_details_fragment, container, false);
        radiologyListView = (RecyclerView) mainView.findViewById(R.id.recyclerview);
        nodata_layout = (LinearLayout) mainView.findViewById(R.id.nodata_layout);

        tabLayout = (TabLayout) mainView.findViewById(R.id.tabs);
        viewPager = (ViewPager) mainView.findViewById(R.id.viewpager);

        viewPagerAdapter = new ViewPagerAdapter(getChildFragmentManager(),getActivity());
        viewPagerAdapter.addFragment(new PatientOPDOverviewFragment(), getActivity().getString(R.string.Overview),tabIcons[0]);
        viewPagerAdapter.addFragment(new PatientOPDVisitFragment(), getActivity().getString(R.string.visit),tabIcons[1]);
        viewPagerAdapter.addFragment(new PatientOPDLabInvestigationFragment(), getActivity().getString(R.string.labinvestigation),tabIcons[2]);
        viewPagerAdapter.addFragment(new PatientOPDTreatHistoryFragment(), getActivity().getString(R.string.treatmenthistory),tabIcons[2]);
        viewPagerAdapter.addFragment(new PatientOPDTimelineFragment(),  getActivity().getString(R.string.timeline),tabIcons[3]);
        viewPagerAdapter.addFragment(new PatientOPDVitalsFragment(),  getActivity().getString(R.string.vitals),tabIcons[5]);
        viewPager.setAdapter(viewPagerAdapter);
        tabLayout.setupWithViewPager(viewPager);
        tabLayout.setSelectedTabIndicatorColor(Color.parseColor(Utility.getSharedPreferences(getActivity(), Constants.primaryColour)));
        highLightCurrentTab(0);

        viewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) { }
            @Override
            public void onPageSelected(int position) {
                highLightCurrentTab(position);
            }
            @Override
            public void onPageScrollStateChanged(int state) {
            }

        });

        return mainView;
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