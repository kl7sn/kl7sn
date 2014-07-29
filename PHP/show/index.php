<? include_once "template/head.php" ?>

</head>
<body>
<div class="container">
	
	<div class="row well">
		<h1 class="offset2">中南民族大学教学签到系统web端</h1>
	</div>
	<div class="row span3 well">
		<blockquote>
			<b>操作方法：</b>
			<span>在框中输入课程id编号，即签到流水号，在安卓客户端开始签到后可以查看到</span>
		</blockquote>
	</div>
	<div class="row span7 well">
		<form action="result.php" method="GET">
			<label for="checkid" >输入签到号</label>
			<input type="text" name="checkid" class="span3">
			<br/>
			<button type="submit" class="btn" name="submit" value="submit">提交</button>
		</form>
	</div>
</div>
</body>
</html>