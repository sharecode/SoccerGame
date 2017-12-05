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
	<title>Reset Password</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
	function body_click()
	{
		innerHTML("name_node","");
	}
	function body_press(event)
	{
		if(event.keyCode==27)
		{
		 innerHTML("name_node","");
		}
	}


var $center_pro='<center>'+$_progress+'</center>';
var $l_find_send=0;
var $_request2;

function find_name(event)
{
	event.stopPropagation();

	$txt=gValue("name_search");

	if($txt.length>2 && $l_find_send==0)
	{
		innerHTML("name_node",'<span id="n_g_node" class="guess_show"></span>');
		innerHTML("n_g_node",$center_pro);
		$l_find_send=1;
		$form= new FormData();
		$form.append("find_name",$txt);
		$form.append("page","resetpass");

		$_request2= new XMLHttpRequest();
		$_request2.onreadystatechange=rsp_find_name;
		$_request2.open("POST","manage.php",true);
		$_request2.send($form);	

	}
	if($txt.length<3 &&  $l_find_send==0)
	{
		innerHTML("name_node","");
	}
}

function rsp_find_name()
{
		if($_request2.readyState==4)
		{
			$l_find_send=0;
			$receive=$_request2.responseText;  alert($receive);
			if(document.getElementById("n_g_node")) {innerHTML("n_g_node",$receive); }
		}
}
var $cur_id,$_request1;
function choose_name($id,$user,$name,$phone)
{
	$cur_id=$id;
	innerHTML("cur_username",$user);
	innerHTML("cur_name",$name);
	innerHTML("phone",$phone);
}

function save_pass()
{
	$pass1=gValue("newpass1");
	$pass2=gValue("newpass2");
	$adminpass=gValue("addminpass");

	$check_f=0;
	if($cur_id>0)
	{
		if($pass1.length>5)
			{
				if($pass1==$pass2) 
				{ 
					if($adminpass.length>5)
					{ 					
						$check_f=1;
						
					}else { $warn_txt="กรุณาตรวจสอบรหัสผ่านผู้ดูแลระบบ";}
				}else { $warn_txt="รหัสผ่านไม่เหมือนกัน";}
			}else{ $warn_txt="รหัสผ่านต้องยาว 6 ตัวอักษรขึ้นไป";}
	}else { $warn_txt="กรุณาค้นหาและระบุชื่อผู้ใช้งาน";}

	if($check_f==1)
	{
		 innerHTML("np_warn",$_progress+" กำลังส่งข้อมูล"); sDisable("r_bt");
		$form= new FormData();
		$form.append("set_new_pass",$cur_id);
		$form.append("new_pass",$pass1);
		$form.append("admin_pass",$adminpass);
		$form.append("page","resetpass");

		$_request1= new XMLHttpRequest();
		$_request1.onreadystatechange=rsp_reset_pass;
		$_request1.open("POST","manage.php",true);
		$_request1.send($form);	

	}
	else{ innerHTML("np_warn",$_caution+" "+$warn_txt);}
}
function rsp_reset_pass()
{
		if($_request1.readyState==4)
		{	
			 innerHTML("np_warn",""); rDisable("r_bt");
			$receive=$_request1.responseText; alert($receive);

			if($receive=='r') { innerHTML("np_warn",$_ok+" รหัสผ่านใหม่ถูกบันทึกแล้ว");
			$cur_id=0;
			}
		}
}
</script>
<style>
		body {margin:30px;}
		.guess_row{padding:1px 3px; cursor:pointer;}
		.guess_row:hover { background-color:rgb(220,220,220);}
		.guess_show {position:absolute; left:0px; top:0px;background-color:white; width:302px;border:1px solid rgb(200,200,200);}

</style>
</head>
<body onclick="body_click()" onkeypress="body_press(event)" style="background:white;">
<h2>Reset Password สมาชิก</h2>
<table>
	<tr>
			<td valign="top">กรอกชื่อจริง</td>
			<td valign="top">
					<input type="text" id="name_search" oninput="find_name(event)" onclick="find_name(event)" style="width:300px;">
					<div id="name_node" style="position:relative; height:0px;width:100%;"></div>
					<span class="desc">กรอกชื่อจริงเพื่อค้นหาชื่อผู้ใช้งานในระบบ</span>
			</td>
			<td></td>
	</tr>
</table>
<br><br>
<table>
		<tr>
			<td>ชื่อผู้ใช้งาน</td><td><span id="cur_username"></span></td>
		</tr>
		<tr>
			<td>ชื่อ-นามสกุล</td><td><span id="cur_name"></span></td>
		</tr>
		<tr>
			<td>เบอร์มือถือ</td><td><span id="phone"></span></td>
		</tr>
		<tr>
			<td>รหัสผ่านใหม่</td><td><input type="text" id="newpass1"><br><span class="desc">ยาว 6 ตัวอักษรขึ้นไป ตัวอักษรหรือสัญลักษณ์พิเศษใดๆ</span></td>
		</tr>
		<tr>
			<td>ยืนยันรหัสผ่านอีกครั้ง</td><td><input type="text" id="newpass2"></td>
		</tr>
		<tr>
			<td>&nbsp;</td><td></td>
		</tr>
		<tr>
			<td>รหัสผ่านผู้ดูแลระบบ</td><td><input type="password" id="addminpass"></td>
		</tr>
		<tr>
			<td></td><td><button onclick="save_pass()" id="r_bt">บันทึกรหัสผ่าน</button></td>
		</tr>
		<tr>
			<td></td><td><span id="np_warn"></span></td>
		</tr>		
</table>
</body>
</html>