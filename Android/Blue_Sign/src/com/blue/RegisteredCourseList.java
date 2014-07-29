package com.blue;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.List;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.SimpleAdapter;

public class RegisteredCourseList extends Activity implements
		OnItemClickListener {
	ListView course_list;
	String[] namearray = new String[] { "coursename_teachername", "courseno" };
	int[] idarray = { R.id.coursename_teachername, R.id.courseno };
	Map allData;
	List<Map<String, Object>> list;
	Button exit;

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.registered_course_list);
		// 返回键
		exit = (Button) findViewById(R.id.back_courseSelect);
		exit.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				finish();
			}
		});
		Log.v("tag", "RegisteredCourseList here！");
		course_list = (ListView) findViewById(R.id.listView);
		SimpleAdapter adapter = new SimpleAdapter(this, getData(),
				R.layout.registered_course_list_item, namearray, idarray);
		course_list.setAdapter(adapter);
		course_list.setOnItemClickListener(RegisteredCourseList.this);
	}

	private List<Map<String, Object>> getData() {
		list = new ArrayList<Map<String, Object>>();

		Map<String, Object> map;// listview的map

		Context myContext = RegisteredCourseList.this;
		SharedPreferences sp = myContext.getSharedPreferences("CourseData",
				MODE_PRIVATE);
		allData = sp.getAll();
		if (allData.size() != 0) {

			Iterator it = allData.entrySet().iterator();// allData
														// (key:课程号----->value:课程名_老师名)

			while (it.hasNext()) {
				// 从it取出课程号，课程名_老师名
				Map.Entry entry = (Map.Entry) it.next();
				String key = (String) entry.getKey();// 课程号
				String value = (String) entry.getValue();// 课程名_老师名
				// 取完
				// 向listview的map赋值
				map = new HashMap<String, Object>();
				Log.v("tag", value);// 测试
				Log.v("tag", key);// 测试
				map.put("coursename_teachername", value);
				map.put("courseno", key);
				list.add(map);
			}
		}
		return list;
	}

	@Override
	public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
		// arg2表示被选择项
		Log.v("tag", "listview被选择");
		Map map = list.get(arg2);
		String coursename_teachername = (String) map
				.get("coursename_teachername");
		String coursename = coursename_teachername.substring(0,
				coursename_teachername.indexOf("_"));
		String teachername = coursename_teachername
				.substring(coursename_teachername.indexOf("_") + 1);
		String courseno = (String) map.get("courseno");
		Intent intent = new Intent();
		intent.putExtra("coursename", coursename);
		intent.putExtra("teachername", teachername);
		intent.putExtra("courseno", courseno);
		intent.setClass(RegisteredCourseList.this, TeacherInfo.class);
		startActivity(intent);
	}

}
