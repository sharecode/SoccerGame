<?php
require_once "ext.php";
con_mysql();
session_pair();

if(islogin()){} else{ $_con=null; header('Location: '.$_url); exit();}
//================================

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>ข้อมูลส่วนตัว</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>

var $_request;
function save_profile()
{
	$alias=gValue("p_alias");
	$pass1=gValue("p_pass1");
	$pass2=gValue("p_pass2");
	$phone=gValue("p_phone");
	$file=document.getElementById("file_avt");
	$cur_pass=gValue("cur_pass");

	$warn_txt=''; innerHTML("pro_warn","");
	$check_f=0;
	$form = new FormData();

	if($alias.length)
	{
		if($alias.length>5 && $_alias_pat.test($alias)) { $check_f=1; $form.append("newalias",$alias);}
		else { $warn_txt='โปรดตรจสอบฉายาอีกครั้ง';}
	}else {$check_f=1;}
	if($check_f==1)
	{
		if($pass1.length)
		{
			if($pass1.length>5 && $pass1==$pass2) { $check_f=2; $form.append("newpass",$pass1);}
			else { $warn_txt="โปรดตรวจสอบรหัสผ่านที่ต้องการเปลี่ยนอีกครั้ง"}
		} else{$check_f=2;}
	}
	if($check_f==2)
	{
		if($phone.length)
		{ if(num_test($phone) && $phone.length==10) {$check_f=3; $form.append("newphone",$phone);}
		 else {$warn_txt="โปรดตรวจสอบเบอร์โทรอีกครั้ง";}
		}
		else{ $check_f=3;}		
	}
	if($check_f==3)
	{
		if($file.files.length)
		{
			$file_send=$file.files[0]
				if($file_send.name.length)	{	$form.append("new_avt",$file_send);	$check_f=4;}
		}else {$check_f=4;}
	}
	if($check_f==4)
	{
		if($cur_pass.length>5){ $check_f=5; $form.append("cur_pass",$cur_pass);}
		else{$warn_txt='โปรดตรวจสอบรหัสผ่านปัจจุบัน';}
	}
	if($check_f==5)
	{					innerHTML("pro_warn",$_progress+" กำลังส่งข้อมูล");
						$form.append("page","profile");
						$_request= new XMLHttpRequest();
						$_request.onreadystatechange=rsp_send_profile;
						$_request.open("POST","sess.php",true);
						$_request.send($form);	

	}
	else{
	innerHTML("pro_warn",$_caution+" "+$warn_txt);}
}
function rsp_send_profile()
{	
		if($_request.readyState==4)
		{
				innerHTML("pro_warn","");
				$receive=$_request.responseText;
				if($receive=='s') {innerHTML("pro_warn",$_caution+" ขนาดรูปภาพเล็กกว่ากำหนด"); }
				if($receive=='a') {innerHTML("pro_warn",$_caution+" ฉายานี้มีคนตั้งไปแล้ว"); }
				if($receive=='m') {innerHTML("pro_warn",$_caution+" เบอร์โทรนี้มีอยู่แล้วในระบบ"); }
				if($receive=='p') {innerHTML("pro_warn",$_caution+" รหัสผ่านปัจจุบันไม่ถูกต้อง"); }
				if($receive=='c') 
				{
					innerHTML("pro_warn",$_ok+" บันทึกการเปลี่ยนแปลงเรียบร้อย"); 
					$src=document.getElementById("pro_avt").getAttribute("src");
					$id=randomNumber();
					$new_src=$src+'&id='+$id;
					sAttribute("pro_avt","src",$new_src);
					sAttribute("top_avt","src",$new_src);
				}
		}	
}

</script>
<style>

.p_avt_f {display:inline-block; width:100px; height:100px;border:3px solid white; border-radius:3px;}
.p_avt_f img { width:100px;}
</style>
</head>
<body>
<?php  top_head();

	$get_user=$_con->query("select user,name,aliasname,phone from member_ where id=$_uid;");
	$u_data=$get_user->fetchAll();

	$user=$u_data[0]['user'];
	$name=$u_data[0]['name'];
	$alias=$u_data[0]['aliasname'];
	$phone=$u_data[0]['phone'];
	$avt='<img src="'.$_url.'image.php?p='.$_uid.'" id="pro_avt">';
?>
<center><div class="body_box">

<center>
<div style="text-align:left;width:452px;">
<h2>ข้อมูลส่วนตัวของคุณ</h2>

<table border="0" style="font-size:14px;">
			<tr>
					<td align="right">ชื่อผู้ใช้งาน</td><td><input type="text" value="<?php echo $user;?>" disabled="disabled" class="de_input"></td>
					<td rowspan="4"><div class="p_avt_f" id="avt_node"><?php echo $avt; ?></div></td>
			</tr>
			<tr>
					<td>ชื่อจริง-นามสกุล</td><td><input type="text" value="<?php echo $name;?>" disabled="disabled" class="de_input"></td>
			</tr>
			<tr><td>&nbsp;</td><td></td></tr>
			<tr>
					<td align="right">ฉายา</td><td><input type="text" id="p_alias" value="<?php echo $alias;?>" maxlength="128" class="de_input"></td>
			</tr>
			<tr><td></td><td colspan="2"><span class="desc12">ฉายา ยาว 6 ตัวขึ้นไป ห้ามมีอักขระพิเศษ</span</td></tr>

			<tr>
					<td align="right">รหัสผ่าน(ใหม่)</td><td><input type="password" value="" id="p_pass1" class="de_input"></td><td></td>
			</tr>
			<tr>
					<td align="right">ยืนยันรหัสผ่าน</td><td><input type="password" value="" id="p_pass2" class="de_input"></td><td></td>
			</tr>
						<tr><td></td><td colspan="2"><span class="desc12">ยาว 6 ตัวขึ้นไป ตัวอักษรใดๆก็ได้</span</td></tr>

			<tr>
					<td align="right">เบอร์มือถือ</td><td><input type="text" id="p_phone" value="<?php echo $phone; ?>" id="phone" maxlength="10" class="de_input"></td><td></td>
			</tr>	
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			<tr>	<td align="right">รูปภาพ</td><td colspan="2"><form name="new_avt"><input type="file" id="file_avt" style="width:200px; overflow:hidden;"></form></td>
			</tr>
				<tr><td></td><td colspan="2"><span class="desc12">รูปภาพต้องมีขนาดไม่เล็กว่า 200x200 Pixel</span</td></tr>

			<tr>
					<td>รหัสผ่านปัจจุบัน</td><td><input type="password" value="" id="cur_pass" class="de_input"></td><td>*จำเป็น</td>
			</tr>
			<tr><td></td><td colspan="2"><span class="desc12">จำเป็นต้องยืนยันเสมอเมื่อแก้ไขข้อมูล</span</td></tr>
			<tr><td></td><td align="center"><button class="gray_button" onclick="save_profile()" style="width:98%;">บันทึก</button></td><td></td></tr>
			<tr><td colspan="3" align="center"><span id="pro_warn"></span></td></tr>



</table>

</div></center>
</div></center>
<?php footer(); if($_con) { $_con=null;} ?>

</body>
</html>