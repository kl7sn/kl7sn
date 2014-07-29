package com.blue;

import java.io.IOException;

import org.xmlpull.v1.XmlPullParserException;

import com.util.System_Data_Interface;
import com.util.parseXML;
import com.util.writeXML;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

/*
 * Date 2012/10/12
 * Author robin-xue
 * 
 */
public class LoginActivity extends Activity {

	EditText ID;
	EditText password;
	Button submit;
	ProgressDialog dialog;

	String Id;
	String Password;
	String OperatorName;
	myHandler handler = new myHandler();
	// sharedPreferences
	Context myContext;
	SharedPreferences sp;
	Editor editor;

	String[] user_info;

	BlueApp app;
	Thread thread;

	Boolean stopThread = false;

	public boolean onKeyDown(int keyCode, KeyEvent event) {
		// TODO Auto-generated method stub
		stopThread = true;
		finish();
		return super.onKeyDown(keyCode, event);
	}

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		app = (BlueApp) LoginActivity.this.getApplication();// 获取application对象
		// 从sharedPreferences读数据
		myContext = LoginActivity.this;
		sp = myContext.getSharedPreferences("LoginData", MODE_PRIVATE);
		editor = sp.edit();
		Id = sp.getString("ID", "none");
		Password = sp.getString("password", "none");

		/*
		 * 监测网络环境
		 */
		if (isConnect(this) == false) {
			new AlertDialog.Builder(this)
					.setTitle("網絡錯誤")
					.setMessage("網路連接失敗，請確認網絡連接")
					.setPositiveButton("确定",
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									// TODO Auto-generated method stub
									finish();
								}
							}).show();
			setContentView(R.layout.login);// 登录界面
		} else {
			/*
			 * if(第一次登录） {...} else() {...}
			 */
			if (Id.equals("none") || Password.equals("none")) {
				// 第一次登录
				setContentView(R.layout.login);// 登录界面
				ID = (EditText) findViewById(R.id.formlogin_userid);
				password = (EditText) findViewById(R.id.formlogin_pwd);
				submit = (Button) findViewById(R.id.formlogin_btsubmit);
				submit.setOnClickListener(new OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub

						Id = ID.getText().toString();// 获取登录密码
						Password = password.getText().toString();
						// 进度条
						dialog = new ProgressDialog(LoginActivity.this);
						dialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);// 设置进度条风格，风格为原型，旋转的
						dialog.setMessage("正在验证...");
						dialog.setIcon(android.R.drawable.ic_dialog_map);// 设置标题图标
						dialog.setCancelable(false);// 按返回键取消
						dialog.show();// 显示
						// 验证登录
						thread = new Thread() {
							public void run() {
								/*
								 * webservice验证登录
								 */
								System_Data_Interface data_Interface = new System_Data_Interface();
								String result = data_Interface
										.getResponseForCheck("check", "ID", Id,
												"password", Password);
								// <user_info>
								// <CourseTeacher></CourseTeacher>
								// <IdentiType></IdentiType>
								// <OperatorID></OperatorID>
								// <OperatorName></OperatorName>
								// <OperatorIdenti></OperatorIdenti>
								// </user_info>
								Message msg = new Message();
								if (result == null) 
								{
									msg.arg1 = 4;// 网络超时
									Log.v("tag", "result is null");
								}else if (result.equals("0")) 
								{// 登录验证失败
									msg.arg1 = 0;
								} else 
								{
									OperatorName = result.substring(2);// "1_OperatorName"
									app.OperatorName = OperatorName;
									msg.arg1 = 1;
								}
								handler.sendMessage(msg);
							}
						};
						thread.start();
					}

				});
			} else {
				// 之前登录过
				Log.v("login", "login before");
				setContentView(R.layout.login_progress);
				dialog = new ProgressDialog(LoginActivity.this);
				dialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);// 设置进度条风格，风格为原型，旋转的
				dialog.setMessage("正在验证...");
				dialog.setIcon(android.R.drawable.ic_dialog_map);// 设置标题图标
				dialog.setCancelable(false);// 按返回键取消
				dialog.show();// 显示
				thread = new Thread() {
					public void run() {
						/*
						 * webservice验证登录
						 */
						System_Data_Interface data_Interface = new System_Data_Interface();
						String result = data_Interface.getResponseForCheck(
								"check", "ID", Id, "password", Password);
						Message msg = new Message();
						if (result == null) {
							msg.arg1 = 4;// 网络异常
							Log.v("tag", "result is null");
						} else {
							/*
							 * 解析result
							 */
							OperatorName = result.substring(2);
							app.OperatorName = OperatorName;
							msg.arg1 = 3;
						}
						handler.sendMessage(msg);
					}
				};
				thread.start();
			}
		}
	}

	class myHandler extends Handler {
		@Override
		public void handleMessage(Message msg) {
			// TODO Auto-generated method stub
			super.handleMessage(msg);
			dialog.dismiss();
			if (msg.arg1 == 1) {
				/*
				 * 登录成功 将用户输入的账户和密码使用sharedPreferences写入xml里
				 */
				// 存入数据
				editor = sp.edit();
				editor.putString("ID", Id);
				editor.putString("password", Password);
				editor.commit();
				// 返回
				Log.v("log", sp.getString("ID", "none"));
				Log.v("log", sp.getString("password", "none"));
				app.ID = Id;
				// 跳转
				Intent intent = new Intent();
				intent.setClass(LoginActivity.this, MetroActivity.class);
				startActivity(intent);
				overridePendingTransition(R.anim.in_from_bottom,
						R.anim.out_to_top);
				finish();// important！！！销毁当前activity！！！
			} else if (msg.arg1 == 0) {
				Toast.makeText(LoginActivity.this, "登录失败，请输入正确的账户和密码",
						Toast.LENGTH_LONG).show();
			} else if (msg.arg1 == 3) {
				dialog.dismiss();
				Intent intent = new Intent();
				intent.setClass(LoginActivity.this, MetroActivity.class);
				app.ID = Id;
				startActivity(intent);
				finish();// important！！！销毁当前activity！！！
			} else if (msg.arg1 == 4) {
				dialog.dismiss();
				Toast.makeText(LoginActivity.this, "网络超时，请检查您的网络",
						Toast.LENGTH_LONG).show();
			}
		}
	}

	@Override
	protected void onRestart() {
		// TODO Auto-generated method stub
		// the activiy is no longer visible

		super.onRestart();
	}

	@Override
	protected void onResume() {
		// TODO Auto-generated method stub
		// Another Activity comes in front of the activity

		super.onResume();
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
}
