<?php
/**
 * 被调用提供蓝牙检测的类
 *
 * @package default
 * @author Mex
 **/
class Check
{
	/*
	* webserver测试函数
	* 调用time_string()
	* 返回 $time 字符串
	*/
	public function time_string()
	{
		// 获取系统时间
		date_default_timezone_set('Asia/Shanghai');
		$time_now = date("Y-m-d H:i:s");
		$time = $time_now;
		// 返回时间字符串
		return $time;
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

	/*
	* 获取签到流水号，并将学生信息插入 T_list
	* 返回上课总人数和签到流水号
	*/
	public function get_class_id($class_id_xml)		
	{ 
		$server   ='210.42.151.55';  
		$username ='bluetooth';  
		$password ='blue@123';  
		$database ='Blue'; 
		$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
		$conn     =odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
		odbc_exec($conn,"set names gb2312");	
		// 初始化记录上课人数数量
		$insert_number = "0";
		// 得到系统时间
		$time          = $this->time();
		// 获取客户端传入信息并解析
		$class_s_xml   = simplexml_load_string($class_id_xml);
		// 课程id

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
		$sql_id = odbc_exec($conn,"insert into T_ID_list (
													CourseClassNo,
													WeekNo,
													WeekDay,
													ClassNo,
													Classroom,
													CourseName,
													CourseTeacher,
													IdentiType,
													OperatorID,
													OperatorName,
													OperatorIdenti
													)
													values
													(
													'".$CourseClassNo."',
													'".$WeekNo."',
													'".$WeekDay."',
													'".$ClassNo."',
													'".$Classroom."',
													'".$CourseName."',
													'".$CourseTeacher."',
													'".$IdentiType."',
													'".$OperatorID."',
													'".$OperatorName."',
													'".$OperatorIdenti."')"
		);
		
		$get_id = odbc_exec($conn ,"SELECT Max(CheckIn_ID) FROM dbo.T_ID_list");

		if (odbc_fetch_array($get_id)) 
		{

			$CheckIn_ID = odbc_result($get_id,1);
			// 执行成功后，将$flag变成 1
			$flag = '1';
		}
		else
		{
			$flag = '0';
		}

		// 如果flag为1，获取学生表中的信息，然后插入T_list
		if($flag == '1')
		{	
			$sql = odbc_exec($conn,"select * from StuInfo where CourseClassNo = $CourseClassNo");
			while(odbc_fetch_array($sql))
			{
				$StuNo 			= odbc_result($sql,'StuNo');
				$StuName 		= odbc_result($sql,'StuName');
				$Grade 			= odbc_result($sql,'Grade');
				$DepName 		= odbc_result($sql,'DepName');
				$Major			= odbc_result($sql,'Major');
				$BigClassName	= odbc_result($sql,'BigClassName');
				$SmallClassName = odbc_result($sql,'SmallClassName');
				$BtMac 			= odbc_result($sql,'BtMac');
				$BtName 		= odbc_result($sql,'BtName');

				$sql_insert_list = "insert into 
									T_list
										(
										CheckIn_ID,
										StuNo,
										StuName,
										Grade,
										DepName,
										Major,
										BigClassName,
										SmallClassName,
										WeekNo,
										WeekDay,
										ClassNo,
										CourseClassNo,
										Classroom,
										CourseName,
										CourseTeacher,
										BtMac,
										BtName,
										IdentiType,
										OperatorID,
										OperatorName,
										OperatorIdenti,
										OperateDate,
										CheckInState
										)values(
												'$CheckIn_ID',
												'$StuNo',
												'$StuName',
												'$Grade',
												'$DepName',
												'$Major',
												'$BigClassName',
												'$SmallClassName',
												'$WeekNo' ,
												'$WeekDay',
												'$ClassNo',
												'$CourseClassNo',
												'$Classroom',
												'$CourseName',
												'$CourseTeacher',
												'$BtMac',
												'$BtName',
												'$IdentiType',
												'$OperatorID',
												'$OperatorName',
												'$OperatorIdenti',
												'$time',
												'0'
												)";
					$query_insert = odbc_prepare($conn,$sql_insert_list);
					while (odbc_execute($query_insert)) 
					{	
						// 如果执行成功
						$insert_number++;
					}
			}
		}
		// 返回学生总数，签到流水号
			odbc_close ($conn);
			$CheckIn_ID = $this->gbk2utf($CheckIn_ID);
			$stuinfo = "$insert_number"."_"."$CheckIn_ID";
		
		return $stuinfo;
		// return $flag;
	}


	/*
	* 将获得的学生的蓝牙信息插入数据库
	*/
	public function insert_blue_mac($bluetooth_xml)				// 将获得的学生的蓝牙信息插入数据库
	{
		$server   ='210.42.151.55';  
		$username ='bluetooth';  
		$password ='blue@123';  
		$database ='Blue'; 
		$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
		$conn     = odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
		odbc_exec($conn,"set names gb2312");
		// 获取蓝牙mac地址并插入数据库
		$bluetooth_s_xml = simplexml_load_string($bluetooth_xml);
		$CheckIn_ID      = $this->utf2gbk($bluetooth_s_xml->CheckIn_ID);

		$a = '0';
		for($i='1' ; $i<'11' ; $i++)
		{
			$BtMac = "BtMac".$i;
			$get_mac	= $this->utf2gbk($bluetooth_s_xml->$BtMac);
			$check_sql	= "update T_list 
							set 	CheckInState = '1' 
							where 	CheckIn_ID = '$CheckIn_ID'
									and 
									BtMac = '$get_mac'
							";
			$check_query = odbc_prepare($conn,$check_sql);
			if (odbc_execute($check_query) == 1) 
			{
				$a = $a + 1;
			}
		}
		$success = $a;
		odbc_close ($conn);
		return $success;
	}

	/*
	* 最后一次插入
	* 返回未到人数和流水号
	*/
	public function insert_end_blue_mac($bluetooth_end_xml)
	{
		$server   ='210.42.151.55';  
		$username ='bluetooth';  
		$password ='blue@123';  
		$database ='Blue'; 
		$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
		$conn     =odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
		odbc_exec($conn,"set names gb2312");

		// 解析获得信息
		$bluetooth_s_xml = simplexml_load_string($bluetooth_end_xml);
		// 检测终止符
		if($bluetooth_s_xml->end == '1')
		{
			// 取得基本信息
			$CheckIn_ID       = $this->utf2gbk($bluetooth_s_xml->CheckIn_ID);
			$need_record_time = $bluetooth_s_xml->need_record_time;
			for($i='0' ; $i<$need_record_time ; $i++)
			{
				$BtMac   = "BtMac".$i;
				$get_mac = $this->utf2gbk($bluetooth_s_xml->$BtMac);
				$end_check_sql	= "update T_list 
								set 
								CheckInState = '1' 
								where 
								CheckIn_ID = $CheckIn_ID
								and 
								BtMac = $get_mac
								";
				$end_check_query = odbc_exec($conn,$end_check_sql);
			}
			// 读出未到学生数量
			$not_sql    = "select StuName and StuNo from T_list where CheckInState = '0'";
			$not_query  = odbc_prepare($conn,$not_sql);
			$not_res    = odbc_execute($not_query);
			$not_mk_xml = '<absent_info>';
			$not_mk_xml .= "<CheckIn_ID>".$this->gbk2utf($CheckIn_ID)."</CheckIn_ID>";
			$add        = '0';
			while ($not_res)
			{
				$not_mk_xml.= "<StuNo".$add.">";
				$not_mk_xml.= odbc_result($not_query,'StuNo');
				$not_mk_xml.= "</StuNo".$add.">";
				$not_mk_xml.= "<StuName".$add.">";
				$not_mk_xml.= odbc_result($not_query,'StuName');
				$not_mk_xml.= "</StuName".$add.">";	
				$add++;
			}
			$not_mk_xml.= '</absent_info>';
			$not_mk_xml.= "<NotSum>".$add."</NotSum>";
			$absent_info = $not_mk_xml;
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
		$server   ='210.42.151.55';  
		$username ='bluetooth';  
		$password ='blue@123';  
		$database ='Blue'; 
		$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
		$conn     =odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
		odbc_exec($conn,"set names gb2312");
		// 解析获得信息
		$single_insert_s = simplexml_load_string($single_insert_xml);
		$single_StuNo    = $this->utf2gbk($single_insert_s->single_StuNo);
		$single_StuName  = $this->utf2gbk($single_insert_s->single_StuName);

		$single_insert = "update T_list 
								set 
								CheckInState = '1' 
								where 
								StuNo = $single_StuNo
								and 
								StuName = $single_StuName
								";

		$single_update_query = odbc_prepare($conn, $single_insert);
		$single_update_res = odbc_execute($single_update_query);
		
		if($single_update_res)
		{
			$success 	= "1";
		}
		else
		{
			$success = "0";
		}
		odbc_close ($conn);
		return $success;
	}
}
