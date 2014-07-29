package com.blue;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;

public class SetActivity extends Activity {
	Button exit;
	Context myContext;
	SharedPreferences sp;
	Editor editor;
	BlueApp app;

	public boolean onKeyDown(int keyCode, KeyEvent event) {
		// TODO Auto-generated method stub
		return super.onKeyDown(keyCode, event);
	}

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.set);
		app = (BlueApp) this.getApplication();
		exit = (Button) findViewById(R.id.exit);
		exit.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				myContext = SetActivity.this;
				sp = myContext.getSharedPreferences("LoginData", MODE_PRIVATE);
				editor = sp.edit();
				editor.remove("ID");
				editor.remove("password");
				editor.commit();

				sp = myContext.getSharedPreferences("CourseData", MODE_PRIVATE);
				sp.edit().clear();
				sp.edit().commit();

				Intent intent = new Intent();
				intent.setClass(SetActivity.this, LoginActivity.class);
				// Çå³ýÊý¾Ý
				app.metro.finish();
				app.clean_data();
				startActivity(intent);
				finish();
			}
		});
	}

}
