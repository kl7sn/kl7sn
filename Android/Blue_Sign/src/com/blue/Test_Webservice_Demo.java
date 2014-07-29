package com.blue;

import com.util.call_webservice;

import android.app.Activity;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

public class Test_Webservice_Demo extends Activity {
	call_webservice test;

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.test_webservice);

		test = new call_webservice();

		Button button = (Button) findViewById(R.id.test);
		button.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				test.initialzeWebservice("time_string");
//				test.addProperty("a", 111111);
//				test.addProperty("b", 111111);
				test.send_to_server();
				String result = test.getResponse();
				Toast.makeText(Test_Webservice_Demo.this, result,
						Toast.LENGTH_LONG).show();
			}
		});
	}
}
