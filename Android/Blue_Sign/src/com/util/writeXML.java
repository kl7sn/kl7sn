package com.util;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import org.xmlpull.v1.XmlSerializer;

import android.util.Xml;
import android.widget.Toast;

public class writeXML {

	ByteArrayOutputStream output;
	String result;
	XmlSerializer serializer;

	public writeXML() {
		serializer = Xml.newSerializer();
		output = new ByteArrayOutputStream();
		try {
			serializer.setOutput(output, "utf-8");
			serializer.startDocument("utf-8", Boolean.valueOf(true));
			serializer.setFeature(
					"http://xmlpull.org/v1/doc/features.html#indent-output",
					true);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	//写子标签
	public void write_SonTag(String Tag, String text) {
		try {
			serializer.startTag(null, Tag);
			serializer.text(text);
			serializer.endTag(null, Tag);

		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	//写根标签头
	public void write_Parent_Start_Tag(String Tag)
	{
		try {
			serializer.startTag(null, Tag);
		}catch(Exception e)
		{
			e.printStackTrace();
		}
	}
	//写跟标签尾
	public void write_Parent_End_Tag(String Tag)
	{
		try {
			serializer.endTag(null, Tag);
		}catch(Exception e)
		{
			e.printStackTrace();
		}
	}
   //完成
	public String finish() {
		try {
			serializer.endDocument();
			serializer.flush();
			result = output.toString();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return result;
	}
	

}
