package com.qdocs.smarthospital24.model;

public class StaffCommentModel {
    private String comment_staff;

    public String getComment_staff() {
        return comment_staff;
    }

    public void setComment_staff(String comment_staff) {
        this.comment_staff = comment_staff;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public String getStaffname() {
        return staffname;
    }

    public void setStaffname(String staffname) {
        this.staffname = staffname;
    }

    private String created_at;
    private String staffname;
}
