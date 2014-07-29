<? include_once "template/head.php" ?>
<? include_once "conn.php" ?>
</head>
<body>
	<?

	if(isset($_GET['submit']))
		{
			$id = $_GET['checkid'];
			// echo $url = "result.php?checkid=".$id;
			//echo "<META HTTP-EQUIV=REFRESH CONTENT='5';URL=$url>";
		
	?>
	<div class="container">
		<div class="row well">
			<h1>课堂签到结果显示</h1>		
		</div>
<div class="row">		
	<div class="row span3">
<pre>

<b> 本次签到信息：</b>
	
<?$info = odbc_exec($conn, "SELECT * FROM dbo.T_ID_list WHERE CheckIn_ID = '$id'");?>
<? e($info) ;?>
<span> <b>周次：</b><?=gbk2utf(odbc_result($info,'WeekNo' ));?></span>
<span> <b>星期：</b><?=gbk2utf(odbc_result($info,'WeekDay' ));?></span>
<span> <b>节次：</b><?=gbk2utf( rtrim( odbc_result($info,'ClassNo')));?></span>
<span> <b>教室：</b><?=gbk2utf( rtrim( odbc_result($info,'Classroom')));?></span>
<span> <b>课程名：</b><?=gbk2utf(odbc_result($info,'CourseName' ));?></span>
<span> <b>任课教师：</b><?=gbk2utf(odbc_result($info,'CourseTeacher' ));?></span>
<span> <b>签到人身份：</b><?=gbk2utf(odbc_result($info,'OperatorIdenti' ));?></span>
<span> <b>签到负责人：</b><?=gbk2utf(odbc_result($info,'OperatorName' ));?></span>
	

</pre>
	</div>
	
			<div class="row well span4">
				<div class="line">
					<h3>已到：</h3>
				</div>
				<div class="line">
					总计：
				</div>
					<?
					$s = utf2gbk('已到');
					$y = odbc_exec($conn, "SELECT * FROM dbo.T_List WHERE CheckIn_ID = '$id' AND CheckInState = '$s'");
					?>
					<table class="table table-striped">
						<tbody>
							<tr>
								<td>姓名</td>
								<td>学号</td>
							</tr>

							<? 
							while (odbc_fetch_array($y))
							{?>
							<tr>
								<td><?=gbk2utf(odbc_result($y, 'StuName')) ?></td>
								<td><?=gbk2utf(odbc_result($y, 'StuNo')) ?></td>
							</tr>
							<? } ?>
						</tbody>						
					</table>

			</div>
			<div class="row well span4">
				<div class="line">
					<h3>未到：</h3>
				</div>
				<div class="line">
					</h3>总计：
				</div>
					<?
					$s = utf2gbk('未到');
					$n = odbc_exec($conn, "SELECT * FROM dbo.T_List WHERE CheckIn_ID = '$id' AND CheckInState = '$s'");
					?>
					<table class="table table-striped">
						<tbody>
							<tr>
								<td>姓名</td>
								<td>学号</td>
							</tr>
							<? 
							while (odbc_fetch_array($n)) 
							{?>
							<tr>
								<td><?=gbk2utf(odbc_result($n, 'StuName'))?></td>
								<td><?=gbk2utf(odbc_result($n, 'StuNo')) ?></td>
							</tr>
							<? } ?>
						</tbody>
						
					</table>


			</div>
		</div>
	</div>
	<? } ?>
</body>
</html>
<?/**
 * "gbk" 转码 utf8
 *
 * @return str
 * @author mex
 **/
function gbk2utf($str)
{
	$string = iconv('GB2312//IGNORE', 'UTF-8', $str);
	return $string;
}


/**
 * utf8转码为gbk	
 *
 * @return string
 * @author mex
 **/
function utf2gbk($str)
{
	$string = iconv('utf-8', 'GB2312//IGNORE', $str);
	return $string;
}

/**
* 输入的字符串只能用utf-8编码
*
* @param  mixed 要输出的对象
* @param  bool 是否输出后直接退出
* @return void
* @author ys
**/
function e($s, $is_exit = false)
{
	echo "<pre>";

	if(is_object($s))
	{
		print_r($s);

		echo 'Function ';
		print_r(get_class_methods($s));
	}
	else
	{
		echo htmlspecialchars(print_r($s, true));
	}

	echo "</pre>";

	if($is_exit)
		exit;
}
?>