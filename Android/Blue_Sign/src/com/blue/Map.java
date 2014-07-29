package com.blue;

import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;

import com.baidu.mapapi.BMapManager;
import com.baidu.mapapi.GeoPoint;
import com.baidu.mapapi.MKLocationManager;
import com.baidu.mapapi.MapActivity;
import com.baidu.mapapi.MapController;
import com.baidu.mapapi.MapView;
import com.baidu.mapapi.MyLocationOverlay;

public class Map extends MapActivity {
	
	BMapManager mBMapMan = null;
	MapController mapcontroller = null;
	MapView mapview = null;
	GeoPoint myloc = null;
	Handler handler;
	MyLocationOverlay mylocTest;
	MKLocationManager mLocationManager;
	
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.map);
		mBMapMan = new BMapManager(getApplication());
		// 地图初始化
		mBMapMan.init("E20910FD89B16B7DCC85A4D1DD0858D06EAC59E3", null);
		super.initMapActivity(mBMapMan);
		mapview = (MapView) findViewById(R.id.mapview);
		mapview.setBuiltInZoomControls(true);// 设置启用内嵌的缩放控件
		mapcontroller = mapview.getController();// 得到mapview的控制权，可以用它控制和驱动平移和缩放
		mapcontroller.setZoom(13);
		
		new Thread()
		{
			public void run()
			{
				mLocationManager = mBMapMan.getLocationManager();
				mylocTest = new MyLocationOverlay(Map.this, mapview);
				mylocTest.enableMyLocation(); // 启用定位
				mylocTest.enableCompass();    // 启用指南针
				while(mylocTest.getMyLocation() !=null)
				{
					Message msg = new Message();
					msg.arg1 =1 ;
					handler.sendMessage(msg);
				}
			}
		}.start();
		
		handler = new Handler()
		{
			 public void handleMessage(Message msg) 
			 {
			     if(msg.arg1 ==1 )
			     {
			    	 mapcontroller.setCenter(mylocTest.getMyLocation());
			     }
			 }
		};
	}

	@Override
	protected boolean isRouteDisplayed() {
		// TODO Auto-generated method stub
		return false;
	}

}
