package com.blue;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;

public class course_return_info extends Activity {
	EditText coursename;
	EditText courseteacher;
	
	public String[] course_return_info;//course_return_info[0]_course_return_info[1] 课程名_老师
	
	Button send;
	Button cancel;
	Builder dialog;
//	BlueApp app;
	String CourseClassNo;
	
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
//		app = (BlueApp) this.getApplication();
		setContentView(R.layout.course_return_info);
		coursename = (EditText) findViewById(R.id.CourseName);
		courseteacher = (EditText) findViewById(R.id.CourseTeacher);
		
		Intent intent = getIntent();
		course_return_info = intent.getStringArrayExtra("course_return");
		CourseClassNo = intent.getStringExtra("CourseClassNo");
		
		coursename.setText(course_return_info[0]);
		courseteacher.setText(course_return_info[1]);

		send = (Button) findViewById(R.id.send);
		cancel = (Button) findViewById(R.id.cancel);
		/*
		 * Android弹出对话框时，默认情况下无论点击哪个button， 触发事件后对话框都会自动关闭
		 */
		send.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				dialog = new AlertDialog.Builder(course_return_info.this);
				dialog.setTitle("警告");
				dialog.setIcon(android.R.drawable.btn_star);
				dialog.setMessage("确定提交信息么？");
				dialog.setPositiveButton("确定",
						new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,
									int which) {
								/*
								 * 正确信息
								 */
								//将课程号，老师名，课程名存入sharedpreference文件
								Context myContext = course_return_info.this;
								SharedPreferences sp = myContext.getSharedPreferences("CourseData", MODE_PRIVATE);
								Editor editor = sp.edit();
								//key:课程号 value：课程名_老师
								Log.v("tag",CourseClassNo+course_return_info[0]+"_"+course_return_info[1]);//测试
								editor.putString(CourseClassNo,course_return_info[0]+"_"+course_return_info[1]);//课程名_老师
								editor.commit();
								Intent intent = new Intent();
								finish();
							}
						});
				dialog.setNegativeButton("取消", null);
				dialog.show();
			}
		});
		cancel.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				// 返回之前的metro 原因是courseno输入错误
				finish();
			}
		});
	}
}
