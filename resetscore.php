<?php
require_once "ext.php";
con_mysql();
session_pair();
if(islogin() && isAdmin(0)){} else{ $_con=null; exit();}
//================================

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>Reset Score</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>


var $request1;
function reset_score()
{
		$form=new FormData();
		$form.append("reset_score",1);
		$form.append("page","reset");
		
		innerHTML("re_warn",$_progress+' กำลังดำเนินการ'); sDisable("reset_bt");
		$_request1= new XMLHttpRequest();
		$_request1.onreadystatechange=rsp_reset;
		$_request1.open("POST","manage.php",true);
		$_request1.send($form);
}
function rsp_reset()
{
		if($_request1.readyState==4)
		{	
			innerHTML("re_warn",''); rDisable("reset_bt");
			$receive=$_request1.responseText; alert($receive);	

			if($receive=='b'){ innerHTML("re_warn",$_caution+' มีการทายผลยังไม่ถูกตรวจสอบในระบบ');}
			if($receive=='o'){ innerHTML("re_warn",$_ok+' ระบบคะแนนถูกรีเซตแล้ว');}
		}
}

</script>
<style>
body {margin:30px; background:white;}
</style>
</head>
<body>
<h2>รีเซต คะแนนสมาชิก</h2>
<span class="desc">
* จำเป็นต้องตรวจการทายผลในระบบให้หมดก่อน<br>
* รีเซ็ตในวันที่ 1 ของเดือน เพื่อเริ่มต้นการแข่งขันใหม่
</span>
<br>
<button onclick="reset_score()" id="reset_bt">เริ่มต้นการรีเซต</button><span id="re_warn"></span>
</body>
</html>