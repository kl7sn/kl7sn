package com.blue;

import java.util.ArrayList;

import com.bean.student;
import com.util.System_Data_Interface;
import com.util.call_webservice;
import com.util.writeXML;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class AbsentStudentInfo extends Activity {
	Button send;
	ListView lv;
	MyListAdapter adapter;
	String[] absent_stu_no;
	String[] absent_stu_name;
	String ID;
	int absent_num = 0;
	Context mContext;

	public Boolean[] check;
	CheckBox checkbox;
	ArrayList<student> students;
	ProgressDialog dialog;
	MsgHandler myHandler;

	public boolean onKeyDown(int keyCode, KeyEvent event) {
		// TODO Auto-generated method stub
		// if (keyCode == KeyEvent.KEYCODE_BACK) {
		finish();
		// }
		return super.onKeyDown(keyCode, event);
	}

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.absent_student_info);
		mContext = getApplicationContext();
		send = (Button) findViewById(R.id.send);
		lv = (ListView) findViewById(R.id.lvperson);
		Intent intent = this.getIntent();

		myHandler = new MsgHandler();

		absent_stu_no = intent.getStringArrayExtra("absent_stu_no");
		absent_stu_name = intent.getStringArrayExtra("absent_stu_name");
		absent_num = intent.getIntExtra("absent_num", 0);
		Log.v("tag", absent_num + "absent_num");
		ID = intent.getStringExtra("ID");

		check = new Boolean[absent_num];
		for (int i = 0; i < absent_num; i++) {
			check[i] = false;
		}
		students = new ArrayList<student>(absent_num);// 把absentstudent的信息装到ArrayList<student>里去
		for (int i = 0; i < absent_num; i++) {
			student s = new student();
			s.setStuName(absent_stu_name[i]);
			// test
			Log.v("tag", absent_stu_name[i]);
			// test

			s.setStuNo(absent_stu_no[i]);

			// test
			Log.v("tag", absent_stu_no[i]);
			// test
			students.add(s);
		}
		adapter = new MyListAdapter(students);
		lv.setAdapter(adapter);
		lv.setOnItemClickListener(new OnItemClickListener() {

			public void onItemClick(AdapterView<?> arg0, View v, int arg2,
					long arg3) {
				// TODO Auto-generated method stub
				checkbox = (CheckBox) v.findViewById(R.id.list_select);
				checkbox.setChecked(!checkbox.isChecked());
				check[arg2] = checkbox.isChecked();
			}
		});

		send.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				dialog = new ProgressDialog(AbsentStudentInfo.this);
				dialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);// 设置进度条风格，风格为原型，旋转的
				dialog.setMessage("正在发送中");
				dialog.setIcon(android.R.drawable.ic_dialog_map);// 设置标题图标
				dialog.setCancelable(false);// 按返回键取消
				dialog.show();// 显示
				/*
				 * 把选中的学生(签到的学生)发送到webservice
				 */
				new Thread() {
					public void run() {
						// 写xml
						writeXML write = new writeXML();
						write.write_Parent_Start_Tag("absent_add");
						write.write_SonTag("CheckIn_ID", ID);
						write.write_SonTag("absent_num",
								String.valueOf(absent_num));
						for (int i = 0; i < absent_num; i++) {
							Log.v("tag", String.valueOf(check[i]));
							if (check[i]) {
								write.write_SonTag("StuNo" + (i + 1),
										absent_stu_no[i]);
								write.write_SonTag("StuName" + (i + 1),
										absent_stu_name[i]);
							}
						}
						write.write_Parent_End_Tag("absent_add");
						String result = write.finish();
						Log.v("tag", result);
						System_Data_Interface data_interface = new System_Data_Interface();
						data_interface.getResponse("single_insert",
								"single_insert_xml", result);

						Message msg = new Message();
						msg.arg1 = 1;
						myHandler.sendMessage(msg);
					}
				}.start();
			}
		});
	}

	class MyListAdapter extends BaseAdapter {
		ArrayList<student> list;
		CheckBox checkbox;
		TextView stu_name;
		TextView stu_no;

		public MyListAdapter(ArrayList<student> list) {
			this.list = list;
		}

		public int getCount() {
			// TODO Auto-generated method stub
			return list.size();
		}

		public Object getItem(int position) {
			// TODO Auto-generated method stub
			return list.get(position);
		}

		public long getItemId(int position) {
			// TODO Auto-generated method stub
			return position;
		}

		public View getView(int position, View convertView, ViewGroup parent) {
			// TODO Auto-generated method stub
			View view;
			LayoutInflater mInflater = (LayoutInflater) mContext
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			view = mInflater.inflate(R.layout.absent_student_item, null);
			checkbox = (CheckBox) view.findViewById(R.id.list_select);
			stu_name = (TextView) view.findViewById(R.id.list_name);
			stu_no = (TextView) view.findViewById(R.id.list_no);
			checkbox.setChecked(check[position]);// important!!
			stu_name.setText(list.get(position).getStuName());
			stu_no.setText(list.get(position).getStuNo());
			return view;
		}
	}

	class MsgHandler extends android.os.Handler {
		Dialog alertDialog;

		public void handleMessage(Message msg) {
			dialog.dismiss();
			alertDialog = new AlertDialog.Builder(AbsentStudentInfo.this)
					.setMessage("已发送至服务器")
					.setIcon(R.drawable.ic_launcher)
					.setPositiveButton("确定",
							new DialogInterface.OnClickListener() {
								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									// TODO Auto-generated method stub
									alertDialog.dismiss();
									finish();
								}
							}).create();
			alertDialog.show();
		}
	}
}