package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class RadiologyModel {
    String id;
    String patient_name;
    String date;
    String case_reference_id;

    public String getCase_reference_id() {
        return case_reference_id;
    }

    public void setCase_reference_id(String case_reference_id) {
        this.case_reference_id = case_reference_id;
    }

    public String getId() {
        return id;
    }

    String tpa;
    String tpa_id;

    public String getTpa() {
        return tpa;
    }

    public void setTpa(String tpa) {
        this.tpa = tpa;
    }

    public String getTpa_id() {
        return tpa_id;
    }

    public void setTpa_id(String tpa_id) {
        this.tpa_id = tpa_id;
    }

    public String getTpa_validity() {
        return tpa_validity;
    }

    public void setTpa_validity(String tpa_validity) {
        this.tpa_validity = tpa_validity;
    }

    String tpa_validity;

    public void setId(String id) {
        this.id = id;
    }

    public String getPatient_name() {
        return patient_name;
    }

    public void setPatient_name(String patient_name) {
        this.patient_name = patient_name;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getPaid_amount() {
        return paid_amount;
    }

    public void setPaid_amount(String paid_amount) {
        this.paid_amount = paid_amount;
    }

    public String getNet_amount() {
        return net_amount;
    }

    public void setNet_amount(String net_amount) {
        this.net_amount = net_amount;
    }

    public String getDoctor_name() {
        return doctor_name;
    }

    public void setDoctor_name(String doctor_name) {
        this.doctor_name = doctor_name;
    }

    public String getNote() {
        return note;
    }

    public void setNote(String note) {
        this.note = note;
    }

    String paid_amount;

    String total;

    public String getTotal() {
        return total;
    }

    public void setTotal(String total) {
        this.total = total;
    }

    String net_amount;
    String doctor_name;
    String organisation_id;

    public String getOrganisation_id() {
        return organisation_id;
    }

    public void setOrganisation_id(String organisation_id) {
        this.organisation_id = organisation_id;
    }

    public String getInsurance_validity() {
        return insurance_validity;
    }

    public void setInsurance_validity(String insurance_validity) {
        this.insurance_validity = insurance_validity;
    }

    public String getInsurance_id() {
        return insurance_id;
    }

    public void setInsurance_id(String insurance_id) {
        this.insurance_id = insurance_id;
    }

    String insurance_validity;
    String insurance_id;
    String note;
    public ArrayList<CustomFieldModel> getCustomfield() {
        return customfield;
    }

    public void setCustomfield(ArrayList<CustomFieldModel> customfield) {
        this.customfield = customfield;
    }

    ArrayList<CustomFieldModel> customfield = new ArrayList<>();

}
