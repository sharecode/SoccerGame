<?php
require_once "ext.php";
	con_mysql();
	session_pair();
	islogin();
?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="description" content="เว็บทายผลบอล">
	<meta name="keywords" content="ทายผลบอล">
	<meta name="robots" content="ALL">
	<title>สมัครสมาชิก</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css">
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
	
	var $_request;

	function send_member()
	{
		innerHTML("add_warn",""); innerHTML("s_pro","");

		$username=gValue("username_r"); 
		$pass1=gValue("pass1"); 
		$pass2=gValue("pass2"); 
		$name=gValue("name");
		$pat=/^[a-zA-Z0-9ก-ูเ-์]+$/;

		$warn_txt='';
		$check_f=0;
		if($username.length>5 && $pat.test($username))
		{ $check_f=1;		} else { $warn_txt="กรุณาตรวจสอบชื่อผู้ใช้งานอีกครั้ง";}

		if($check_f==1)
		{
			if($pass1.length>5)
			{
				if($pass1==$pass2) 
				{ $check_f=2;}
				else { $warn_txt="รหัสผ่านไม่เหมือนกัน";}
			}
			else{ $warn_txt="รหัสผ่านต้องยาว 6 ตัวอักษรขึ้นไป";}
		}

		if($check_f==2)
		{
			$pat=/^[a-zA-Z0-9ก-ูเ-์]+[ ][a-zA-Z0-9ก-ูเ-์]+$/;
			if($pat.test($name)) {$check_f=3;} 
			else{$warn_txt="กรุณาตรวจสอบ ชื่อ-นามสกุล";	}
		}
		if($check_f==3)
		{
			$pat_c=/^[a-zA-Z]+$/;
			$capt=gValue("capt");
			if($pat_c.test($capt)&&$capt.length==4)
			{
				$check_f=4;
			}else {$warn_txt="ตัวอักษรในรูปไม่ถูกต้อง";}
		}	
	
		if($check_f==4)
		{
						sDisable("send_but");
						innerHTML("s_pro",$_progress+" กำลังส่งข้อมูล");			

						$form= new FormData();
						$form.append("page","register");
						$form.append("addusername",$username);
						$form.append("pass",$pass1);
						$form.append("name",$name);
						$form.append("capt",$capt);

						$_request= new XMLHttpRequest();
						$_request.onreadystatechange=rsp_addmember;
						$_request.open("POST","agents.php",true);
						$_request.send($form);	
		}
		else{
		innerHTML("s_pro",$_caution+' '+$warn_txt); }
	}

	function rsp_addmember()
	{
		if($_request.readyState==4)
		{
			rDisable("send_but"); innerHTML("s_pro","");		
			$receive=$_request.responseText; 

			if($receive=='a')
			{ 	innerHTML("s_pro",$_ok+" การสมัครสมาชิกเสร็จเรียบร้อย"); }
			else
			{
				if($receive=='u') 	{innerHTML("s_pro",$_caution+" ชื่อผู้ใช้งานนี้มีอยู่แล้วในระบบ"); }
				if($receive=='n') 	{innerHTML("s_pro",$_caution+" ชื่อ-นามสกุลนี้มีอยู่แล้วในระบบ"); 	}
				if($receive=='c') 	{innerHTML("s_pro",$_caution+' ตัวอักษรในรูปไม่ถูกต้อง'); change_capt();}				
			}
		}
	}

function  change_capt()
{
	$img=document.getElementById("img_capt");
	$rand=randomNumber();
	$src=$_url+'piccapt.php?site=2&id='+$rand;
	$img.setAttribute("src",$src);
	sValue("capt","");
}

</script>
<style>

body {font-size:13px; font-family:tahoma;}

input,select {display:inline-block; width:200px; margin-right:10px;}
input { border: 2px solid #a9b6d9; background-color:white; padding:3px; border-radius:3px;}
</style>
</head>

<body style="background-color:rgba(126,152,204,0.8); color:white;">
<?php  top_head(); ?>
<center><div class="body_box">

				<center><div>					
							<table style="font-size:14px;" border="0">
							<tr>
									<td colspan="3"><h2>สมัครสมาชิก ทายผลบอล (สมัครฟรี ทายฟรี !)</h2></td>
							</tr>
							<tr>
								<td colspan="3">* อนุญาตให้สมัครได้ <u>1คน ต่อ 1ไอดี</u> เท่านั้น เพื่อความยุติธรรมในการแข่งขัน</td>
							</tr>
							<tr><td colspan="3">&nbsp;</td></tr>
							<tr>
								<td align="right" valign="top">ชื่อผู้ใช้งาน</td><td><input  type="txet" maxlength="64" id="username_r"><td>Username</td>
							</tr>
							<tr><td></td><td colspan="2"><span class="desc12">ไอดีสำหรับ เข้าสู่ระบบ ยาว 6 ตัวอักษรขึ้นไป A-Z a-z 0-9 </span></td>							
							</tr>
							<tr><td align="right" valign="top">รหัสผ่าน</td> <td><input  type="password" maxlength="255" id="pass1"><td>Password</td>
							</tr>
							<tr><td></td> <td colspan="2"><span class="desc12">ยาว 6 ตัวอักษรขึ้นไป ตัวอักษรหรือสัญลักษณ์พิเศษใดๆ</span></td>
							</tr>
							<tr><td valign="top">ยืนยันรหัสผ่าน</td> <td><input  type="password" maxlength="255" id="pass2"></td><td>Confirm password</td>
							</tr>
							<tr><td></td><td colspan="2"><span class="desc12">ยืนยันรหัสผ่านอีกครั้ง ให้เหมือนกับข้างบน</span></td>
							</tr>
							<tr><td valign="top">ชื่อ-นามสกุล</td> <td><input  type="txet" maxlength="510" id="name"></td><td>First name - Last name</td>
							</tr>
							<tr><td></td><td colspan="2"><span class="desc12">ไม่ต้องมีคำนำหน้า <strike>นาย นางสาว นาง ฯลฯ</strike> จำเป็นสำหรับติดต่อรับของรางวัล</span></td>
							</tr>
							<tr><td></td>
									<td align="left">
								<div style="width:92%; display:inline-block; border:1px solid white ;border-radius:2px; text-align:center;">
									<table border="0" style="width:100%;">
										<tr>
											<td align="right">
												<span style="display:inline-block;width:87px;height:42px; margin:2px;">
													<img id="img_capt" src="<?php echo $_url; ?>piccapt.php?site=2">
												</span>
												</td>
											<td align="left">
												<input type="text" id="capt" maxlength="4" style="width:40px; text-align:center;margin:0px 0px;"><br>
												<span class="link12" onclick="change_capt()">เปลี่ยนรูป</span>
											</td>
										</tr>
									</table>
									<span class="desc12">กรอกตัวอักษรที่เห็นในรูป</span>
								</div>
									</td><td></td>
							</tr>
							<tr><td>&nbsp;</td><td align="center"><button id="send_but" onclick="send_member()" class="yellow_button">สมัครสมาชิก</button></td><td></td>
							</tr>
							<tr><td>&nbsp;</td><td align="center"><span id="s_pro"></span><span class="warn" id="add_warn"></span></td><td></td></tr>
							<tr><td colspan="3"><span class="desc12"><h2>ร่วมสนุก กับกิจกรรมทายผลบอล ชิงของรางวัลมากมาย</h2></span></td></tr>
							
							</table>

</div></center>
<div class="hide">
	<img src="<?php echo $_url;?>images/progress.gif">
	<img src="<?php echo $_url;?>images/right_small.png">
</div>

</div></center> <!-----end body------>
<?php footer();  if($_con) { $_con=null;} ?>
</body>
</html>