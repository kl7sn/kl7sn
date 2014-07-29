package com.blue;

import android.app.Activity;
import android.os.Bundle;
import android.view.KeyEvent;

public class AboutActivity extends Activity{

	
	
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.about);
		
	}
	
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		// TODO Auto-generated method stub
		finish();
		return super.onKeyDown(keyCode, event);
	}

}
