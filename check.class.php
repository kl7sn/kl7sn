<?php
/**
 * public:
 *   	check()
 *   	course()
 *   	get_class_id()
 *   	insert_blue_mac()
 *   	insert_end_blue_mac()
 *   	single_insert()
 *   	
 * private:
 * 		insert_check_id()
 *   	get_check_id()
 *    	get_stu_num($class_id_xml ,$CheckIn_ID)
 * 		insert_blue_mac_info($bluetooth_xml)
 *   	checked_num($CheckIn_ID)
 *    	utf2gbk($str)
 *     	gbk2utf($str)
 *     	time()
 */



/**
 * 被调用提供蓝牙检测的
 *
 * @package default
 * @author Mex
 **/
class Check
{
	/**
	 * 构造函数
	 *
	 * @return void
	 * @author mex
	 **/
	function __construct()
	{
		$this->server   ='210.42.151.55';  
		$this->username ='bluetooth';  
		$this->password ='blue@123';  
		$this->database ='Blue'; 
		$this->connstr  = "Driver={SQL Server};Server=$this->server;Database=$this->database";
	}

	/**
	 * 判断用户是否登陆成功
	 * 成功返回 1 
	 * 失败返回 0
	 *
	 * @return int
	 * @author Mex
	 **/
	public function check($ID ,$password)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

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
	 * 返回 课程名，老师姓名
	 * <course_return>
 	 * 	<flag></flag>
 	 *	<CourseName></CourseName>
 	 *	<CourseTeacher></CourseTeacher>
 	 * </coures_return>
	 *
	 * @return void
	 * @author mex
	 **/
	public function course($course_no)
	{
		$course_no = $this->utf2gbk( $course_no);

		// TODO 获取课程字典表中的信息并返回
		$conn     = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);
		
		// 取出所以的课程信息
		$sql_course = "SELECT * from dbo.DIC_Course WHERE CourseClassNo = $course_no";
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
	* 获取签到流水号，并将学生信息插入 T_List
	* 返回上课总人数和签到流水号
	*/
	public function get_class_id($class_id_xml)		
	{ 
		$stu_num = "0";
		// 获取到的数据插入数据库
		$insert_flag = $this->insert_check_id($class_id_xml);
		
		if($insert_flag == 1)
		{
			// 获取流水号
			$CheckIn_ID = $this->get_check_id();
	
			// 如果flag为1，获取学生表中的信息，然后插入T_list
			if($CheckIn_ID != '-1')
			{	
				// 得到应到学生人数
				$stu_num = $this->get_stu_num($class_id_xml ,$CheckIn_ID);
			}
			// 返回学生总数，签到流水号
			$CheckIn_ID = $this->gbk2utf($CheckIn_ID);
			$stuinfo = "$stu_num"."_"."$CheckIn_ID";
		}
		else
		{
			$stuinfo = "$stu_num"."_"."$CheckIn_ID";
			// $stuinfo = $insert_flag;
		}
		return $stuinfo;
	}


	/*
	* 将获得的学生的蓝牙信息插入数据库
	*/
	public function insert_blue_mac($bluetooth_xml)				// 将获得的学生的蓝牙信息插入数据库
	{
		$CheckIn_ID = $this->insert_blue_mac_info($bluetooth_xml);
		// 获取已经签到成功的人数
		$a = $this->checked_num($CheckIn_ID);
		
		return $a;
	}

	/*
	* 最后一次插入
	* 返回未到人数和流水号
	*/
	public function insert_end_blue_mac($bluetooth_end_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		// 解析获得信息
		$bluetooth_s_xml = simplexml_load_string($bluetooth_end_xml);
		// 检测终止符
		if($bluetooth_s_xml->end == '1')
		{
			// 取得基本信息
			$CheckIn_ID       = $this->utf2gbk($bluetooth_s_xml->CheckIn_ID);
			$need_record_time = $bluetooth_s_xml->need_record_time;

			for( $i=1 ; $i<=$need_record_time ; $i++ )
			{
				$BtMac   = "BtMac".$i;
				$get_mac = $this->utf2gbk($bluetooth_s_xml->$BtMac);
				$end_update_sql	= " UPDATE dbo.T_List 
									SET CheckInState = '1' 
									WHERE CheckIn_ID = '$CheckIn_ID'
									AND BtMac = '$get_mac'";
				$end_check_query = odbc_exec($conn ,$end_update_sql);
			}
			// 读出未到学生数量
			$not_sql    = "SELECT StuName , StuNo FROM dbo.T_List WHERE CheckInState = '0' AND CheckIn_ID = '$CheckIn_ID'";
			$not_res    = odbc_exec( $conn ,$not_sql);
			$not_mk_xml = "<absent_info>";
			$not_mk_xml.= "<CheckIn_ID>".$this->gbk2utf($CheckIn_ID)."</CheckIn_ID>";
			$add        = '0';
			while (odbc_fetch_array($not_res))
			{
				$not_mk_xml.= "<StuNo".$add.">".$this->gbk2utf( odbc_result($not_res,'StuNo') )."</StuNo".$add.">";
				$not_mk_xml.= "<StuName".$add.">".$this->gbk2utf( odbc_result($not_res,'StuName') )."</StuName".$add.">";
				$add++;
			}
			$not_mk_xml.= '</absent_info>';
			$not_mk_xml.= "<NotSum>".$add."</NotSum>";
			$absent_info = $not_mk_xml;
			// $absent_info = $CheckIn_ID;
			// 标签StuName0....到....StuName99此类标签提取未到人姓名StuNo1....到....StuNo99学号 通过NotSum提取未到总数
		}
		odbc_close ($conn);
		return $absent_info;
	}

	/*
	* 插入单一个人信息
	*/ 
	public function single_insert($single_insert_xml)
	{
		$conn = odbc_connect( $this->connstr, $this->username, $this->password,SQL_CUR_USE_ODBC);

		// 解析获得信息
		$single_insert_s = simplexml_load_string($single_insert_xml);

		$CheckIn_ID     = $this->utf2gbk($single_insert_s->CheckIn_ID);
		$single_StuNo   = $this->utf2gbk($single_insert_s->single_StuNo);
		$single_StuName = $this->utf2gbk($single_insert_s->single_StuName);

		$single_insert = "  UPDATE T_List 
							SET 
							CheckInState = '1' 
							WHERE 
							StuNo = '$single_StuNo'
							AND 
							StuName = '$single_StuName'
							AND
							CheckIn_ID = '$CheckIn_ID'
							";

		$single_update = odbc_exec($conn, $single_insert);

		// 判断update是否成功，这段代码很挫
		$check_update = "SELECT CheckIn_ID FROM dbo.T_List 
						 WHERE StuNo = '$single_StuNo' AND StuName = '$single_StuName' AND CheckIn_ID = '$CheckIn_ID'
							";
		$check = odbc_exec( $conn, $check_update);

		while(odbc_fetch_array($check)) 
		{
			$success = odbc_result( $check, 1);
			if( $success != 0)
			{
				$success = 1;
			}
		}

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
		$time = $this->time();
		
		$sql = odbc_exec($conn,"SELECT * FROM StuInfo WHERE CourseClassNo = $CourseClassNo");
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
			$sql_insert_list = "INSERT into 
								T_List
									(
										CheckIn_ID,StuNo,StuName,Grade,DepName,Major,BigClassName,SmallClassName,WeekNo,WeekDay,ClassNo,CourseClassNo,Classroom,CourseName,CourseTeacher,BtMac,BtName,IdentiType,OperatorID,OperatorName,OperatorIdenti,OperateDate,CheckInState
									)VALUES(
												'$CheckIn_ID','$StuNo','$StuName','$Grade','$DepName','$Major','$BigClassName','$SmallClassName','$WeekNo' ,'$WeekDay','$ClassNo','$CourseClassNo','$Classroom','$CourseName','$CourseTeacher','$BtMac','$BtName','$IdentiType','$OperatorID','$OperatorName','$OperatorIdenti','$time','0'
											)";
			$query_insert = odbc_prepare($conn,$sql_insert_list);
			$res = odbc_execute($query_insert);
			if ($res == 1) 
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

		$mac_num = $bluetooth_s_xml->MacNum;
		
		// 进行签到操作
		for($i=1 ; $i<=$mac_num ; $i++)
		{
			$BtMac = "BtMac".$i;

			$get_mac	= $this->utf2gbk($bluetooth_s_xml->$BtMac);

			$update_sql	=  "UPDATE  dbo.T_List 
							SET 	CheckInState = '1' 
							WHERE 	CheckIn_ID = '$CheckIn_ID'
							-- AND 	BtMac = '$get_mac'
							AND EXISTS
							(
								SELECT CourseClassNo FROM dbo.T_List WHERE BtMac = '$get_mac'
							)
							";


							
			odbc_exec($conn,$update_sql);
		}
		odbc_close($conn);
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

		$sql = "SELECT SUM(CheckInState)
				FROM dbo.T_List 
				WHERE 	CheckIn_ID = '$CheckIn_ID'";
		$exe = odbc_exec( $conn, $sql);
		$checked_num = 0;
		while( odbc_fetch_array($exe) )
		{
			$checked_num = odbc_result( $exe, 1);
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
