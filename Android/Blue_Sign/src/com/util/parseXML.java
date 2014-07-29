package com.util;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;

import com.bean.student;

import android.util.Log;
import android.util.Xml;

public class parseXML {
	String content;
	InputStream is;
	XmlPullParser parser;
	ArrayList<student> students;
	student s;
	String CheckIn_ID;

	Boolean course_return_success = true;

	String[] course_return;

	public parseXML() {
		parser = Xml.newPullParser();
	}

	public ArrayList<student> Parse_Xml(String XmlString) {
		Log.v("tag", XmlString);
		try {
			is = new ByteArrayInputStream(XmlString.getBytes());
			parser.setInput(is, null);
			int event = parser.getEventType();
			int i = 1;
			while (event != XmlPullParser.END_DOCUMENT) {
				String nodeName = parser.getName();// 获取当前标签 头标签?尾标签
				Log.v("tag", "i = " + i);
				switch (event) {

				case XmlPullParser.START_DOCUMENT:
					students = new ArrayList<student>();
					Log.v("tag", "startDocument");
					break;

				case XmlPullParser.START_TAG:
					if ("CheckIn_ID".equals(nodeName)) {
						CheckIn_ID = parser.nextText();
						Log.v("tag", "CheckIn_ID:" + CheckIn_ID);
					} else if (("StuNo" + i).equals(nodeName)) {
						s = new student();
						String tmp = parser.nextText();
						s.setStuNo(tmp);
						Log.v("tag", "StuNo:" + tmp);
					} else if (("StuName" + i).equals(nodeName)) {
						String temp1 = parser.nextText();
						s.setStuName(temp1);
						Log.v("tag", "endTag");
						students.add(s);
						i++;
						Log.v("tag", "StuName:" + temp1);
					}
					break;

				// case XmlPullParser.END_TAG:
				// Log.v("tag", "endTag");
				// Log.v("tag", nodeName);
				// if (("StuName" + i).equals(nodeName)) {
				//
				// }
				// break;

				default:
					break;
				}
				event = parser.next();
			}
		} catch (Exception e) {
			e.printStackTrace();
			students =null;
		}
		return students;
	}

	public void Parse_Xml_course_return(String xml) {
		try {
			System.out.println("start parse");
			is = new ByteArrayInputStream(xml.getBytes());
			parser.setInput(is, null);
			int event = parser.getEventType();
			int i = 1;
			int pointer = 0;
			course_return = new String[2];

			while (event != XmlPullParser.END_DOCUMENT) {
				String nodeName = parser.getName();// 获取当前标签 头标签?尾标签
				Log.v("tag", "i = " + i);
				switch (event) {
				case XmlPullParser.START_DOCUMENT:
					Log.v("tag", "startDocument");
					break;
				case XmlPullParser.START_TAG:
					if (nodeName.equals("flag")) {
						Log.v("tag", nodeName);
						if (parser.nextText().equals("0")) {
							course_return_success = false;
						}
					} else if (nodeName.equals("CourseName")) {
						Log.v("tag", nodeName);
						course_return[pointer] = parser.nextText();
						pointer++;
					} else if (nodeName.equals("CourseTeacher")) {
						Log.v("tag", nodeName);
						course_return[pointer] = parser.nextText();
						pointer++;
					}
					break;
				default:
					break;
				}
				if (!course_return_success)
					break;
				event = parser.next();
				i++;
			}
		} catch (Exception e) {
			e.printStackTrace();
			course_return_success = false;;
		}
	}

	public String[] getcourse_return() {
		return course_return;
	}

	public Boolean isCourse_return() {
		return course_return_success;
	}
}
