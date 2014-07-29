package com.blue;

import android.app.Application;
import android.content.Context;

public class BlueApp extends Application {

	String ID;
	String password;
	String OperatorName;
	String CourseClassNo;
	String CourseName;
	String CourseTeacher;
	String classNum_checkId;
	Boolean isAlive = true; //
	MetroActivity metro;

	@Override
	public void onCreate() {
		// TODO Auto-generated method stub
		super.onCreate();
	}

	public void clean_data() {
		ID = null;
		password = null;
		OperatorName = null;
		CourseClassNo = null;
		CourseName = null;
		CourseTeacher = null;
		classNum_checkId = null;
		metro = null;
		isAlive = true;
	}

}
