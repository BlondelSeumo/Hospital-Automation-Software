package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class IpdNurseNoteModel {

    private String name;
    private String date;
    private String note;
    private String id;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getNote() {
        return note;
    }

    public void setNote(String note) {
        this.note = note;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getComment() {
        return comment;
    }

    public void setComment(String comment) {
        this.comment = comment;
    }

    public ArrayList<StaffCommentModel> getStaffcomment() {
        return staffcomment;
    }

    public void setStaffcomment(ArrayList<StaffCommentModel> staffcomment) {
        this.staffcomment = staffcomment;
    }

    private String comment;


    ArrayList<StaffCommentModel> staffcomment = new ArrayList<>();

}