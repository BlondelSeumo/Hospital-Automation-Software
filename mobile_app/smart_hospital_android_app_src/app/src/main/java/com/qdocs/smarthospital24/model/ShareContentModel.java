package com.qdocs.smarthospital24.model;

import java.util.ArrayList;

public class ShareContentModel {
    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getShare_date() {
        return share_date;
    }

    public void setShare_date(String share_date) {
        this.share_date = share_date;
    }

    public String getValid_upto() {
        return valid_upto;
    }

    public void setValid_upto(String valid_upto) {
        this.valid_upto = valid_upto;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getSharedby() {
        return sharedby;
    }

    public void setSharedby(String sharedby) {
        this.sharedby = sharedby;
    }

    String id;
    String share_date;
    String valid_upto;
    String title;
    String sharedby;

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    String date;


    public ArrayList<DownloadContentModel> getContent() {
        return content;
    }

    public void setContent(ArrayList<DownloadContentModel> content) {
        this.content = content;
    }

    ArrayList<DownloadContentModel> content = new ArrayList<>();

}
