package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class OpdDetailModel {
    private String opdid;
    private String name;

    public String getIs_antenatal() {
        return is_antenatal;
    }

    public void setIs_antenatal(String is_antenatal) {
        this.is_antenatal = is_antenatal;
    }

    private String is_antenatal;
    private String surname;
    private String appointment_date;
    private String refference;
    private String symptoms;
    private String visitid;
    private String case_reference_id;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    private String id;

    public String getOpdid() {
        return opdid;
    }

    public void setOpdid(String opdid) {
        this.opdid = opdid;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getSurname() {
        return surname;
    }

    public void setSurname(String surname) {
        this.surname = surname;
    }

    public String getAppointment_date() {
        return appointment_date;
    }

    public void setAppointment_date(String appointment_date) {
        this.appointment_date = appointment_date;
    }

    public String getRefference() {
        return refference;
    }

    public void setRefference(String refference) {
        this.refference = refference;
    }

    public String getSymptoms() {
        return symptoms;
    }

    public void setSymptoms(String symptoms) {
        this.symptoms = symptoms;
    }

    public String getVisitid() {
        return visitid;
    }

    public void setVisitid(String visitid) {
        this.visitid = visitid;
    }

    public String getCase_reference_id() {
        return case_reference_id;
    }

    public void setCase_reference_id(String case_reference_id) {
        this.case_reference_id = case_reference_id;
    }

    public String getPrescription() {
        return prescription;
    }

    public void setPrescription(String prescription) {
        this.prescription = prescription;
    }

    public ArrayList<CustomFieldModel> getCustomfield() {
        return customfield;
    }

    public void setCustomfield(ArrayList<CustomFieldModel> customfield) {
        this.customfield = customfield;
    }

    private String prescription;
    ArrayList<CustomFieldModel> customfield = new ArrayList<>();

}