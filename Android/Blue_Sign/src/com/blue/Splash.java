package com.blue;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.widget.Toast;

public class Splash extends Activity 
{

	@Override
	public void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		this.setContentView(R.layout.splash);
		Toast.makeText(this, "ÕýÔÚ™zœy¾W½j­h¾³...", Toast.LENGTH_SHORT).show();
		new Handler().postDelayed(new Runnable()
		{
			@Override
			public void run() {
				// TODO Auto-generated method stub
				Intent intent= new Intent();
				intent.setClass(Splash.this,LoginActivity.class);
				Splash.this.startActivity(intent);
				Splash.this.finish();
				overridePendingTransition(R.anim.in_from_right, R.anim.out_to_left);	
			}
			
		},3000);
	}

}
