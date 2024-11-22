package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class PharmacyModel {
    private String id;
    private String customer_name;
    private String date;
    private String doctor_name;
    private String total;
    private String discount;

    public String getDiscount_percentage() {
        return discount_percentage;
    }

    public void setDiscount_percentage(String discount_percentage) {
        this.discount_percentage = discount_percentage;
    }

    private String discount_percentage;
    private String net_amount;
    private String tax;
    private String paid_amount;
    private String refund_amount;
    private String note;
    private String case_reference_id;

    public String getCollectedby() {
        return collectedby;
    }

    public void setCollectedby(String collectedby) {
        this.collectedby = collectedby;
    }

    private String collectedby;

    public String getCase_reference_id() {
        return case_reference_id;
    }

    public void setCase_reference_id(String case_reference_id) {
        this.case_reference_id = case_reference_id;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getCustomer_name() {
        return customer_name;
    }

    public void setCustomer_name(String customer_name) {
        this.customer_name = customer_name;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getDoctor_name() {
        return doctor_name;
    }

    public void setDoctor_name(String doctor_name) {
        this.doctor_name = doctor_name;
    }

    public String getTotal() {
        return total;
    }

    public void setTotal(String total) {
        this.total = total;
    }

    public String getDiscount() {
        return discount;
    }

    public void setDiscount(String discount) {
        this.discount = discount;
    }

    public String getNet_amount() {
        return net_amount;
    }

    public void setNet_amount(String net_amount) {
        this.net_amount = net_amount;
    }

    public String getTax() {
        return tax;
    }

    public void setTax(String tax) {
        this.tax = tax;
    }

    public String getPaid_amount() {
        return paid_amount;
    }

    public void setPaid_amount(String paid_amount) {
        this.paid_amount = paid_amount;
    }

    public String getRefund_amount() {
        return refund_amount;
    }

    public void setRefund_amount(String refund_amount) {
        this.refund_amount = refund_amount;
    }

    public String getNote() {
        return note;
    }

    public void setNote(String note) {
        this.note = note;
    }

    public ArrayList<CustomFieldModel> getCustomfield() {
        return customfield;
    }

    public void setCustomfield(ArrayList<CustomFieldModel> customfield) {
        this.customfield = customfield;
    }

    ArrayList<CustomFieldModel> customfield = new ArrayList<>();

}