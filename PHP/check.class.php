<?php
/**
 * Code it for students' signing , through bluetooth.
 *
 * We have functions like follows:
 * 
 * public:
 *   	check()
 *   	course()
 *   	get_class_id()
 *   	insert_blue_mac()
 *   	insert_end_blue_mac()
 *   	single_insert()
 * 
 * PHP version 5
 * @link http://github.com/mixmore/blue_check
 * @author MEX
 * @copyright  2012 South-Center University For Nationalities's 505 computer laboratory
 * SCUEC SIGNING WEBSERVICE
 *
 */

/**
 * BLUETOOTH SIGN IN
 *
 * @package default
 **/
class Check
{
	/**
	 * constructor
	 *
	 * @return void
	 **/
	function __construct()
	{
		// database setting
		$this->server   ='';  
		$this->username ='';  
		$this->password ='';  
		$this->database =''; 
		$this->connstr  = "Driver={SQL Server};Server=$this->server;Database=$this->database";
	}

	/**
	 * judge user whether login success
	 * TURE 1 
	 * FALSE 0
	 *
	 * @return int
	 **/
	public function check($ID ,$password)
	{
		// link database
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);
		// it likes no use here
		$ID       = $ID;
		$password = $password;

		$sql_check = "SELECT * FROM dbo.T_User WHERE UserID = $ID";
		$get_p = odbc_exec($conn, $sql_check);
		if (odbc_fetch_array($get_p)) 
		{
			$psw = $this->gbk2utf(odbc_result($get_p, 'Password'));
			if ($psw == $password) 
			{
				$result = "1_".$this->gbk2utf( odbc_result($get_p, 'UserName') );
			}
			else
			{
				$result = "0";
			}
		}
		else
		{
			$result = "0";
		}
		odbc_close ($conn);
		return $result;
	}

	/**
	 * GET course number
	 * RETURN course name & course teacher
 	 * GET SUCCSEE will have a flag .(1)
 	 * 		LIKE:
 	 * 	
 	 * 	<flag>1</flag>
 	 *	<CourseName>course name</CourseName>
 	 *	<CourseTeacher>course teacher</CourseTeacher>
 	 *  </coures_return>
	 *
	 * @return void
	 * @author mex
	 **/
	public function course($course_no)
	{
		// GET course number in GBK
		$course_no = $this->utf2gbk( $course_no );

		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);
		
		// GET class infomation
		$sql_course = "SELECT * from dbo.DIC_Course WHERE CourseClassNo = '$course_no'";
		$exe_course = odbc_exec( $conn, $sql_course);
		if($exe_course)
		{
			if (odbc_fetch_array($exe_course)) 
			{
				$course = "<course_return>";
				$course.= "<flag>"."1"."</flag>";
				$course.= "<CourseName>".$this->gbk2utf( odbc_result( $exe_course, 'CourseName'))."</CourseName>";
				$course.= "<CourseTeacher>".$this->gbk2utf( odbc_result( $exe_course, 'CourseTeacher'))."</CourseTeacher>";
				$course.= "</course_return>";
			}
			else
			{
				$course = "<course_return>";
				$course.= "<flag>"."0"."</flag>";
				$course.= "</course_return>";
			}
		}
		else
		{
			$course = "<course_return>";
			$course.= "<flag>"."0"."</flag>";
			$course.= "</course_return>";
		}
		return $course;
	}

	/*
	* GET sign serial number and use it in T_List to get student number
	* RETURN the student sum number and sing serial number
	*/
	public function get_class_id($class_id_xml)		
	{ 
		$stu_num = "0";
		// Insert in db to get serial number
		$insert_flag = $this->insert_check_id($class_id_xml);		
		if($insert_flag == 1)
		{
			// get serial number
			$CheckIn_ID = $this->get_check_id();
	
			// If the flag is 1, get the students to the table of information, and then insert T_list
			if($CheckIn_ID != '-1')
			{	
				// Get the number of students
				$stu_num = $this->get_stu_num($class_id_xml ,$CheckIn_ID);
			}
			// return students' sum and serial number
			$CheckIn_ID = $this->gbk2utf($CheckIn_ID);
			$stuinfo = "$stu_num"."_"."$CheckIn_ID";
		}
		else
		{	
			$stuinfo = "$stu_num"."_"."$CheckIn_ID";
		}
		return $stuinfo;
	}


	/*
	* Students will gain the bluetooth information into the database
	*/
	public function insert_blue_mac($bluetooth_xml)
	{
		$CheckIn_ID = $this->insert_blue_mac_info($bluetooth_xml);
		// GET the success num
		$a = "正在录入蓝牙信息";

		return $a;
	}

	/*
	* the last signing
	*/
	public function insert_end_blue_mac($bluetooth_end_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		// analyzing XML
		$bluetooth_s_xml = simplexml_load_string($bluetooth_end_xml);
		// detection the terminator
		if($bluetooth_s_xml->end == '1')
		{
			// Get basic information
			$CheckIn_ID       = $this->utf2gbk($bluetooth_s_xml->CheckIn_ID);
			$need_record_time = $bluetooth_s_xml->need_record_time;

			if ($need_record_time > '0')
			{
				for( $i=1 ; $i<=$need_record_time ; $i++ )
				{
					$BtMac   = "BtMac".$i;
					$get_mac = $this->utf2gbk($bluetooth_s_xml->$BtMac);
					$y = $this->utf2gbk('已到');
					$end_update_sql	= " UPDATE dbo.T_List 
										SET CheckInState = '$y' 
										WHERE CheckIn_ID = '$CheckIn_ID'
										AND BtMac = '$get_mac'";
					$end_check_query = odbc_exec($conn ,$end_update_sql);
				}
			}
			// Read the number of no signing student
			$n = $this->utf2gbk('未到');
			$not_sql     = "SELECT StuName , StuNo FROM dbo.T_List WHERE CheckInState = '$n' AND CheckIn_ID = '$CheckIn_ID'";
			$not_res     = odbc_exec( $conn ,$not_sql);

			$absent_info = "<absent_info>";
			$absent_info.= "<CheckIn_ID>".$this->gbk2utf($CheckIn_ID)."</CheckIn_ID>";
			$add         = 1;
			while (odbc_fetch_array($not_res))
			{
				$absent_info.= "<StuNo".$add.">".$this->gbk2utf( odbc_result($not_res,'StuNo') )."</StuNo".$add.">";
				$absent_info.= "<StuName".$add.">".$this->gbk2utf( odbc_result($not_res,'StuName') )."</StuName".$add.">";
				$add++;
			}
			$add = $add - 1;
			$absent_info.= "<NotSum>".$add."</NotSum>";
			$absent_info.= "</absent_info>";
		}
		odbc_close ($conn);
		return $absent_info;
	}

	/*
	* no bluetooth
	* sign by hand
	*/ 
	public function single_insert($single_insert_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		// analyzing XML
		$absent_add = simplexml_load_string($single_insert_xml);

		$CheckIn_ID = $this->utf2gbk($absent_add->CheckIn_ID);

		$absent_num = $absent_add->absent_num;
		
		$flag = 0;
		// do operation
		for($i=1 ; $i<=$absent_num ; $i++)
		{
			$StuNo = "StuNo".$i;
			$StuName = "StuName".$i;

			$StuNo   = $this->utf2gbk( $absent_add->$StuNo);
			$StuName = $this->utf2gbk( $absent_add->$StuName);
			$y = $this->utf2gbk('已到');
			$update_sql	=  "UPDATE  dbo.T_List 
							SET 	CheckInState = '$y' 
							WHERE 	StuNo = '$StuNo'
							AND 	StuName = '$StuName'
							AND 	CheckIn_ID = '$CheckIn_ID'
							";							
			odbc_exec($conn,$update_sql);

			$check = "SELECT CheckInState 
						FROM dbo.T_List 
						WHERE  StuNo = '$StuNo'
						AND 	StuName = '$StuName'
						AND 	CheckIn_ID = '$CheckIn_ID'";
			$exe = odbc_exec( $conn, $check);

			$flag_array = array();
			while(odbc_fetch_array($exe)) 
			{
				$res = odbc_result( $exe, 'CheckInState');

					$flag_array[] = $this->gbk2utf($res);
			}
		}
			if(  strstr('未到', $flag_array))
			{
				$flag = '0';
			}
			else
			{
				$flag = '1';
			}

		$success = $flag;
		odbc_close ($conn);
		return $success;
	}



	/**
	 * 获取数据，存入checkinid list中
	 *
	 * @return int
	 * @author mex
	 **/
	private function insert_check_id($class_id_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		$class_s_xml   = simplexml_load_string($class_id_xml);

		$CourseClassNo  = $this->utf2gbk($class_s_xml->CourseClassNo); 
		$WeekNo         = $this->utf2gbk($class_s_xml->WeekNo); 
		$WeekDay        = $this->utf2gbk($class_s_xml->WeekDay); 
		$ClassNo        = $this->utf2gbk($class_s_xml->ClassNo); 
		$Classroom      = $this->utf2gbk($class_s_xml->Classroom); 
		$CourseName     = $this->utf2gbk($class_s_xml->CourseName);
		$CourseTeacher  = $this->utf2gbk($class_s_xml->CourseTeacher); 
		$IdentiType     = $this->utf2gbk($class_s_xml->IdentiType); 
		$OperatorID     = $this->utf2gbk($class_s_xml->OperatorID); 
		$OperatorName   = $this->utf2gbk($class_s_xml->OperatorName); 
		$OperatorIdenti = $this->utf2gbk($class_s_xml->OperatorIdenti); 
		
		// 将获取到得解析信息，放入记录表，生成签到流水号
		$sql_id = odbc_prepare($conn,"INSERT INTO dbo.T_ID_list (
													CourseClassNo,WeekNo,WeekDay,ClassNo,Classroom,CourseName,CourseTeacher,IdentiType,OperatorID,OperatorName,OperatorIdenti
													)
													VALUES
													('".$CourseClassNo."','".$WeekNo."','".$WeekDay."','".$ClassNo."','".$Classroom."','".$CourseName."','".$CourseTeacher."','".$IdentiType."','".$OperatorID."','".$OperatorName."','".$OperatorIdenti."')"
		);

		$res = odbc_execute($sql_id);
		if ($res) 
		{
			$result = '1';
		}
		else
		{
			$result = '0';
			// $result = $res;
		}
		odbc_close($conn);
		return $result;
	}

	/**
	 * 获取流水号
	 *
	 * @return int
	 * @author 
	 **/
	private function get_check_id()
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		$get_id = odbc_exec($conn ,"SELECT Max(CheckIn_ID) FROM dbo.T_ID_list");

		if (odbc_fetch_array($get_id)) 
		{
			$CheckIn_ID = odbc_result($get_id,1);
		}
		else
		{
			$CheckIn_ID = '-1';
		}
		odbc_close($conn);
		return $CheckIn_ID; 
	}

	/**
	 * 得到应到学生人数
	 *
	 * @return int
	 * @author 
	 **/
	private function get_stu_num($class_id_xml ,$CheckIn_ID)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		$class_s_xml   = simplexml_load_string($class_id_xml);
		$CourseClassNo  = $this->utf2gbk($class_s_xml->CourseClassNo); 

		$insert_number = "0";
		// 得到系统时间
		$time = self::time();
		
		$sql = odbc_exec($conn,"SELECT * FROM StuInfo WHERE CourseClassNo = '$CourseClassNo'");
		while(odbc_fetch_array($sql))
		{
			
			
			$WeekNo         = $this->utf2gbk($class_s_xml->WeekNo); 
			$WeekDay        = $this->utf2gbk($class_s_xml->WeekDay); 
			$ClassNo        = $this->utf2gbk($class_s_xml->ClassNo); 
			$Classroom      = $this->utf2gbk($class_s_xml->Classroom); 
			$CourseName     = $this->utf2gbk($class_s_xml->CourseName);
			$CourseTeacher  = $this->utf2gbk($class_s_xml->CourseTeacher); 
			$IdentiType     = $this->utf2gbk($class_s_xml->IdentiType); 
			$OperatorID     = $this->utf2gbk($class_s_xml->OperatorID); 
			$OperatorName   = $this->utf2gbk($class_s_xml->OperatorName); 
			$OperatorIdenti = $this->utf2gbk($class_s_xml->OperatorIdenti); 
			$StuNo 			= odbc_result($sql,'StuNo');
			$StuName 		= odbc_result($sql,'StuName');
			$Grade 			= odbc_result($sql,'Grade');
			$DepName 		= odbc_result($sql,'DepName');
			$Major			= odbc_result($sql,'Major');
			$BigClassName	= odbc_result($sql,'BigClassName');
			$SmallClassName = odbc_result($sql,'SmallClassName');
			$BtMac 			= odbc_result($sql,'BtMac');
			$BtName 		= odbc_result($sql,'BtName');

			$n = $this->utf2gbk('未到');
			$sql_insert_list = "INSERT into 
								T_List
									(
										CheckIn_ID,StuNo,StuName,Grade,DepName,Major,BigClassName,SmallClassName,WeekNo,WeekDay,ClassNo,CourseClassNo,Classroom,CourseName,CourseTeacher,BtMac,BtName,IdentiType,OperatorID,OperatorName,OperatorIdenti,OperateDate,CheckInState
									)VALUES(
												'$CheckIn_ID','$StuNo','$StuName','$Grade','$DepName','$Major','$BigClassName','$SmallClassName','$WeekNo' ,'$WeekDay','$ClassNo','$CourseClassNo','$Classroom','$CourseName','$CourseTeacher','$BtMac','$BtName','$IdentiType','$OperatorID','$OperatorName','$OperatorIdenti','$time','$n'
											)";
			$query_insert = odbc_prepare($conn,$sql_insert_list);
			$res = odbc_execute($query_insert);
			if ($res == '1') 
			{	
				// 如果执行成功
				$insert_number++;
			}
			// $insert_number = $this->gbk2utf( $CourseName );
		}
		odbc_close($conn);
		return $insert_number;
	}

	/**
	 * 将数据放入list表中
	 *
	 * @return void
	 * @author 
	 **/
	private function insert_blue_mac_info($bluetooth_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		// 获取蓝牙mac地址并插入数据库
		$bluetooth_s_xml = simplexml_load_string($bluetooth_xml);
		
		$CheckIn_ID = $this->utf2gbk($bluetooth_s_xml->CheckIn_ID);

		$mac_num = $bluetooth_s_xml->Mac_Num;
		
		// 进行签到操作
		for($i=1 ; $i<=$mac_num ; $i++)
		{
			$BtMac = "BtMac".$i;

			$get_mac	= $this->utf2gbk($bluetooth_s_xml->$BtMac);
			$y = $this->utf2gbk('已到');
			$update_sql	=  "UPDATE  dbo.T_List 
							SET 	CheckInState = '$y' 
							WHERE 	CheckIn_ID = '$CheckIn_ID'
							AND 	BtMac = '$get_mac'
							";							
			odbc_exec($conn,$update_sql);
		}
		odbc_close($conn);
		// return $this->gbk2utf($get_mac);
		return $CheckIn_ID;
	}

	/**
	 * 获取已经签到的人数
	 *
	 * @return int
	 * @author mex
	 **/
	private function checked_num($CheckIn_ID)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		$checked_num = 0;
		$y = $this->utf2gbk('已到');

		$sql = "SELECT CheckInState
				FROM dbo.T_List 
				WHERE 	CheckIn_ID = '$CheckIn_ID'";
		$exe = odbc_exec( $conn, $sql);
		while(odbc_result( $exe ))
		{
			$res = odbc_result( $exe, 1);
			if($res === $y)
			{
				$checked_num++;
			}	
		}
		odbc_close($conn);
		return $checked_num;
	}

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

	/*
	* 供之后函数调用获取时间
	*/
	private function time()										// 获取系统时间
	{
		// 获取系统时间
		date_default_timezone_set('Asia/Shanghai');
		$time = date("Y-m-d H:i:s");
		// 返回时间字符串
		return $time;
	}
}
