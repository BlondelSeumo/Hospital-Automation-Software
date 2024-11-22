package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class MedicationModel {

    String medicine_date;

    public String getMedicine_day() {
        return medicine_day;
    }

    public void setMedicine_day(String medicine_day) {
        this.medicine_day = medicine_day;
    }

    String medicine_day;

    public String getMedicine_date() {
        return medicine_date;
    }

    public void setMedicine_date(String medicine_date) {
        this.medicine_date = medicine_date;
    }

    public ArrayList<MedicineModel> getMedicine() {
        return medicine;
    }

    public void setMedicine(ArrayList<MedicineModel> medicine) {
        this.medicine = medicine;
    }

    ArrayList<MedicineModel> medicine = new ArrayList<>();
}
