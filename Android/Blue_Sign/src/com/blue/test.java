package com.blue;

import com.util.call_webservice;
import com.util.writeXML;

public class test {

	public static void main(String args[])
	{
		call_webservice call = new call_webservice();
		call.initialzeWebservice("get_id");
		call.addProperty("name", "xueyufan");
		call.send_to_server();
		String  result = call.getResponse();
		
		call.initialzeWebservice("get_name");
	}
}
