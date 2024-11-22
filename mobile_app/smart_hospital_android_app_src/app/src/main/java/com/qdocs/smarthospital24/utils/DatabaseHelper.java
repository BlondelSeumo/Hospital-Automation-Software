package com.qdocs.smarthospital24.utils;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import com.qdocs.smarthospital24.NotificationModel;
import java.util.ArrayList;

public class DatabaseHelper extends SQLiteOpenHelper {
    private static final int DB_VERSION = 1;
    private static final String DB_NAME = "usersdb";
    private static final String TABLE_Users = "userdetails";
    private static final String KEY_ID = "id";
    private static final String KEY_STATUS = "status";
    private static final String KEY_NAME = "name";
    private static final String KEY_LOC = "location";
    private static final String KEY_DES = "description";
    public DatabaseHelper(Context context){
        super(context,DB_NAME, null, DB_VERSION);
    }
    @Override
    public void onCreate(SQLiteDatabase db){
        String CREATE_TABLE = "CREATE TABLE " + TABLE_Users + "(" + KEY_ID + "  INTEGER PRIMARY KEY AUTOINCREMENT, "
                + KEY_NAME + " TEXT, " + KEY_LOC + " TEXT , " + KEY_STATUS + " TEXT, "+ KEY_DES + " DATETIME DEFAULT CURRENT_TIMESTAMP);";
        db.execSQL(CREATE_TABLE);
        System.out.println("CREATE_TABLE=="+CREATE_TABLE);

    }
    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion){
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_Users);
        onCreate(db);
    }
    void insertUserDetails(String name, String location, String status, String description){
        //Get the Data Repository in write mode
        SQLiteDatabase db = this.getWritableDatabase();
        //Create a new map of values, where column names are the keys
        ContentValues cValues = new ContentValues();
        cValues.put(KEY_NAME, name);
        cValues.put(KEY_LOC, location);
        cValues.put(KEY_STATUS, status);
        cValues.put(KEY_DES, description);
        // Insert the new row, returning the primary key value of the new row
        long newRowId = db.insert(TABLE_Users,null, cValues);
        System.out.println("cValues=="+cValues);
        db.close();
    }
    // Get User Details
    public ArrayList<NotificationModel> GetUsers(){
        SQLiteDatabase db = this.getReadableDatabase();
        ArrayList<NotificationModel> userList = new ArrayList<>();
        String query =  " SELECT  * FROM "+ TABLE_Users + " ORDER BY " + KEY_ID + " DESC ";
        Cursor cursor = db.rawQuery(query,null);
        while (cursor.moveToNext()){
            NotificationModel model = new NotificationModel();
            model.setId(cursor.getString(0));
            model.setName(cursor.getString(1));
            model.setLocation(cursor.getString(2));
            model.setDate(cursor.getString(4));
            userList.add(model);
        }
        return  userList;
    }
    public int updatestatus(String oldName , String newName) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues contentValues = new ContentValues();
        contentValues.put(KEY_STATUS,newName);
        String[] whereArgs= {oldName};
        int count =db.update(TABLE_Users,contentValues, KEY_STATUS+" = ?",whereArgs );
        return count;
    }
    public int getProfilesCount() {
        String countQuery = "SELECT  * FROM " + TABLE_Users + " WHERE " +KEY_STATUS+" = 0" ;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count = cursor.getCount();
        cursor.close();
        return count;
    }

    public int deleteAll() {
        SQLiteDatabase db = this.getWritableDatabase();
        return db.delete(TABLE_Users, null, null);
    }

    public void deletenotification(String id){
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("DELETE FROM "+ TABLE_Users + " WHERE " + KEY_ID + " = "+id+"");
        db.close();
    }


}