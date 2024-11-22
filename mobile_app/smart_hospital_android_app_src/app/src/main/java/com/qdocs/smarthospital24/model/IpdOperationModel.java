package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class IpdOperationModel {

    public String getCase_id() {
        return case_id;
    }

    public void setCase_id(String case_id) {
        this.case_id = case_id;
    }

    private String case_id;
    private String ot_technician;
    private String category;
    private String date;

    public String getOt_technician() {
        return ot_technician;
    }

    public void setOt_technician(String ot_technician) {
        this.ot_technician = ot_technician;
    }

    public String getCategory() {
        return category;
    }

    public void setCategory(String category) {
        this.category = category;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getOperation() {
        return operation;
    }

    public void setOperation(String operation) {
        this.operation = operation;
    }

    private String id;
    private String operation;




    public ArrayList<CustomFieldModel> getCustomfield() {
        return customfield;
    }

    public void setCustomfield(ArrayList<CustomFieldModel> customfield) {
        this.customfield = customfield;
    }

    ArrayList<CustomFieldModel> customfield = new ArrayList<>();

}