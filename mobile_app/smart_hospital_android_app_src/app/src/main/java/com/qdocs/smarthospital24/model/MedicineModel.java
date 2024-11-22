package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class MedicineModel {
    private String medicine_name;
    ArrayList<DoseModel> doses = new ArrayList<>();

    public String getMedicine_name() {
        return medicine_name;
    }

    public void setMedicine_name(String medicine_name) {
        this.medicine_name = medicine_name;
    }

    public ArrayList<DoseModel> getDoses() {
        return doses;
    }

    public void setDoses(ArrayList<DoseModel> doses) {
        this.doses = doses;
    }
}
