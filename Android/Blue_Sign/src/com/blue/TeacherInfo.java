package com.blue;

import java.io.ByteArrayOutputStream;

import org.xmlpull.v1.XmlSerializer;

import com.util.System_Data_Interface;
import com.util.call_webservice;
import com.util.writeXML;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.util.Xml;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;
import android.widget.AdapterView.OnItemSelectedListener;

public class TeacherInfo extends Activity {
	/** Called when the activity is first created. */
	BlueApp app;
	EditText NotEditInfo;
	Spinner WeekNo;
	Spinner WeekDay;
	Spinner ClassNo;
	EditText Classroom;
	ArrayAdapter adapter_weekno;
	ArrayAdapter adapter_weekday;
	ArrayAdapter adapter_classno;

	ProgressDialog dialog;

	Button send;
	int choosed_weekno = 0;
	int choosed_weekday = 0;
	int choosed_classno = 0;

	msgHandler handler = new msgHandler();

	String result;

	String[] weekno = new String[30];
	String[] weekday = { "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日" };
	String[] classno = new String[11];

	String courseno;
	String coursename;
	String teachername;

	Intent intent;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.teacher_info);

		intent = getIntent();
		courseno = intent.getStringExtra("courseno");// 课程号
		coursename = intent.getStringExtra("coursename");// 课程名
		teachername = intent.getStringExtra("teachername");// 老师名

		NotEditInfo = (EditText) findViewById(R.id.NotEditInfo);
		WeekNo = (Spinner) findViewById(R.id.WeekNo);
		WeekDay = (Spinner) findViewById(R.id.WeekDay);
		ClassNo = (Spinner) findViewById(R.id.ClassNo);
		Classroom = (EditText) findViewById(R.id.Classroom);

		send = (Button) findViewById(R.id.send);

		app = (BlueApp) this.getApplication();

		NotEditInfo.setText("课程号   :   " + courseno + "\n");
		NotEditInfo.append("课程名称:   " + coursename + "\n");
		NotEditInfo.append("教师姓名:   " + teachername + "\n");
		NotEditInfo.append("签到方式:   " + "bluetooth" + "\n");
		NotEditInfo.append("登录    ID：      " + app.ID + "\n");
		NotEditInfo.append("操作人     :   " + teachername + "\n");
		NotEditInfo.append("身份          :   " + "teacher" + "\n");
		// weekno周次
		init_weekno();
		adapter_weekno = new ArrayAdapter<String>(TeacherInfo.this,
				android.R.layout.simple_spinner_item, weekno);
		adapter_weekno
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		WeekNo.setAdapter(adapter_weekno);
		WeekNo.setSelection(0);
		WeekNo.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3) {
				choosed_weekno = arg2;
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});
		// weekday
		adapter_weekday = new ArrayAdapter<String>(TeacherInfo.this,
				android.R.layout.simple_spinner_item, weekday);
		adapter_weekday
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		WeekDay.setAdapter(adapter_weekday);
		WeekDay.setSelection(0);
		WeekDay.setOnItemSelectedListener(new OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3) {
				choosed_weekday = arg2;
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});
		// classno节次
		init_classno();
		adapter_classno = new ArrayAdapter<String>(TeacherInfo.this,
				android.R.layout.simple_spinner_item, classno);
		adapter_classno
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		ClassNo.setAdapter(adapter_classno);
		ClassNo.setSelection(0);
		ClassNo.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3) {
				choosed_classno = arg2;
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});

		send.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (isConnect(TeacherInfo.this) == false) {
					Toast.makeText(TeacherInfo.this, "网络异常", Toast.LENGTH_LONG)
							.show();
				} else {
					dialog = new ProgressDialog(TeacherInfo.this);
					dialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);// 设置进度条风格，风格为原型，旋转的
					dialog.setMessage("正在发送...");
					dialog.setIcon(android.R.drawable.ic_dialog_map);// 设置标题图标
					dialog.setCancelable(false);// 按返回键取消
					dialog.show();// 显示
					new Thread() {
						public void run() {
							/*
							 * 写xml
							 */
							writeXML write = new writeXML();
							write.write_Parent_Start_Tag("class_s_xml");
							write.write_SonTag("CourseClassNo", courseno);
							write.write_SonTag("WeekNo", weekno[choosed_weekno]);//
							write.write_SonTag("WeekDay",
									weekday[choosed_weekday]);//
							write.write_SonTag("ClassNo",
									classno[choosed_classno]);//

							// write.write_SonTag("WeekNo", "周一");//
							// write.write_SonTag("WeekDay", "周一");//
							// write.write_SonTag("ClassNo", "周一");//

							write.write_SonTag("Classroom", Classroom.getText()
									.toString());
							write.write_SonTag("CourseName", coursename);
							write.write_SonTag("CourseTeacher", teachername);//

							// write.write_SonTag("CourseTeacher", "1");//

							write.write_SonTag("IdentiType", "bluetooth");
							write.write_SonTag("OperatorID", app.ID);
							write.write_SonTag("OperatorName", teachername);
							write.write_SonTag("OperatorIdenti", "teacher");
							write.write_Parent_End_Tag("class_s_xml");
							Log.v("tag", write.finish());

							result = write.finish();
							/*
							 * webservie
							 */
							System_Data_Interface data = new System_Data_Interface();
							String classNum_checkId = data.getResponse(
									"get_class_id", "class_id_xml", result);
							// Intent intent = new Intent();
							// intent.setClass(TeacherInfo.this,SignIn.class);
							// intent.putExtra("classNum_checkId",
							// classNum_checkId);
							// startActivity(intent);
							Message msg = new Message();
							if (classNum_checkId == null) {
								msg.arg1 = 2;// 异常
							} else {
								app.classNum_checkId = classNum_checkId;
								Log.v("tag", classNum_checkId);
								msg.arg1 = 1;
							}
							handler.sendMessage(msg);
						}
					}.start();
				}
			}
		});
	}

	// 节次
	private void init_classno() {
		// TODO Auto-generated method stub
		for (int i = 0; i < 9; i++) {
			classno[i] = "第	" + "0" + (i + 1) + "节";
		}
		classno[9] = "第10节";
		classno[10] = "第11节";
	}

	// 周次
	public void init_weekno() {
		for (int i = 0; i < 9; i++) {
			weekno[i] = "第" + "0" + (i + 1) + "周";
		}
		for (int i = 9; i < weekno.length; i++) {
			weekno[i] = "第" + (i + 1) + "周";
		}
	}

	public boolean isConnect(Context context) {
		try {
			ConnectivityManager connectivity = (ConnectivityManager) context
					.getSystemService(Context.CONNECTIVITY_SERVICE);
			if (connectivity != null) {
				// 获取网络连接管理的对象
				NetworkInfo info = connectivity.getActiveNetworkInfo();
				if (info != null && info.isConnected()) {
					// 判断当前网路是否已经连接
					if (info.getState() == NetworkInfo.State.CONNECTED)
						return true;
				}

			}
		} catch (Exception e) {
			Log.v("tag", e.toString());
		}
		return false;
	}

	class msgHandler extends Handler {
		public void handleMessage(Message msg) {
			switch (msg.arg1) {
			case 1:
				dialog.dismiss();
				Intent intent = new Intent();
				intent.setClass(TeacherInfo.this, SignIn.class);
				startActivity(intent);
				finish();
				break;
			case 2:
				dialog.dismiss();
				Toast.makeText(TeacherInfo.this, "数据异常", Toast.LENGTH_LONG)
				.show();
				break;
			}
		}
	}
}