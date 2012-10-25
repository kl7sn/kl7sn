<?php
/**
 * 用户登录验证的webserver
 *
 * @package default
 * @author 
 **/
class Login
{	
	/**
	 * utf8转码为gbk	
	 *
	 * @return string
	 * @author mex
	 **/
	private function utf2gbk($str)
	{
		$string = iconv('utf-8', 'GB2312//IGNORE', $str);
		return $string;
	}

	/**
	 * "gbk" 转码 utf8
	 *
	 * @return str
	 * @author mex
	 **/
	private function gbk2utf($str)
	{
		$string = iconv('GB2312//IGNORE', 'UTF-8', $str);
		return $string;
	}

	/**
	 * 判断用户是否登陆成功
	 * 成功返回 1 
	 * 失败返回 0
	 *
	 * @return int
	 * @author Mex
	 **/
	public function check($login_xml)
	{
		$server   ='210.42.151.55';  
		$username ='bluetooth';  
		$password ='blue@123';  
		$database ='Blue'; 
		$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
		$conn     =odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
		odbc_exec($conn,"set names gb2312");

		$login_s_xml   = simplexml_load_string($login_xml);

		$user     = $login_s_xml->user;
		$password = $login_s_xml->password;

		$user = $this->gbk2utf($user);
		
		$sql_check = "select Password from T_User where UserName = $user";

		$get_p = odbc_exec($conn, $sql_check);
		while ($get_p) 
		{
			$psw = $this->gbk2utf(odbc_result($get_p, Password));
			if ($psw = $password) 
			{
				$flag = "1";
			}
			else
			{
				$flag = "0";
			}
		}
// <user_info>
// 	<CourseTeacher></CourseTeacher>
// 	<IdentiType></IdentiType>
// 	<OperatorID></OperatorID>
// 	<OperatorName></OperatorName>
// 	<OperatorIdenti></OperatorIdenti>
// </user_info>
		if($flag == "1")
		{
			$result = "<user_info>";
			$result.= "<CourseTeacher>".$CourseTeacher."</CourseTeacher>";
			$result.= "<IdentiType>".$IdentiType."</IdentiType>";
			$result.= "<OperatorID>".$OperatorID."</OperatorID>";
			$result.= "<OperatorName>".$OperatorName."</OperatorName>";
			$result.= "<OperatorIdenti>".$OperatorIdenti."</OperatorIdenti>";
			$result.= "</user_info>";
		}
		else
		{
			$result = "0";
		}

		return $result;
	}
} // END public class Login()