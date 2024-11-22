package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class AppointmentModel {
    String id;
    String date;
    String name;
    String surname;
    String message;
    String appointment_status;
    String specialist_name;

    public String getAppointment_serial_no() {
        return appointment_serial_no;
    }

    public void setAppointment_serial_no(String appointment_serial_no) {
        this.appointment_serial_no = appointment_serial_no;
    }

    String appointment_serial_no;

    public String getLive_consult() {
        return live_consult;
    }

    public void setLive_consult(String live_consult) {
        this.live_consult = live_consult;
    }

    String live_consult;

    public String getSource() {
        return source;
    }

    public void setSource(String source) {
        this.source = source;
    }

    String source;

    public String getEmployee_id() {
        return employee_id;
    }

    public void setEmployee_id(String employee_id) {
        this.employee_id = employee_id;
    }

    String employee_id;

    public String getPriority() {
        return priority;
    }

    public void setPriority(String priority) {
        this.priority = priority;
    }

    String priority;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
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

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getAppointment_status() {
        return appointment_status;
    }

    public void setAppointment_status(String appointment_status) {
        this.appointment_status = appointment_status;
    }

    public String getSpecialist_name() {
        return specialist_name;
    }

    public void setSpecialist_name(String specialist_name) {
        this.specialist_name = specialist_name;
    }

    public ArrayList<CustomFieldModel> getCustomfield() {
        return customfield;
    }

    public void setCustomfield(ArrayList<CustomFieldModel> customfield) {
        this.customfield = customfield;
    }

    ArrayList<CustomFieldModel> customfield = new ArrayList<>();
}
