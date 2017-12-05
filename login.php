<?php
require_once "ext.php";
con_mysql();
session_pair();
if(islogin()){ $_con=null; header('Location: '.$_url); exit();}

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>เข้าสู่ระบบ</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
	
	var $_log_re;
	function try_login()
	{
		innerHTML("login_pg","");
		$username=str_trim(gValue("username_t")); 
		$pass=str_trim(gValue("pass_t")); 
		$capt=str_trim(gValue("capt"));
		$remember=0;
		$check=document.getElementById("remember");
		if($check.checked){$remember=1;}

		if($username.length>5 && $pass.length>5)
		{
			if($capt.length==4 && num_test($capt))
			{
			sDisable("log_but"); mask_progress_show('ตรวจสอบข้อมูล');
		
			$form= new FormData();
			$form.append("trylog",$username);
			$form.append("pass",$pass);
			$form.append("check",$remember);
			$form.append("capt",$capt);
			$form.append("page",'login');

			$_log_re= new XMLHttpRequest();
			$_log_re.onreadystatechange=rsp_login;
			$_log_re.open("POST","agents.php",true);
			$_log_re.send($form);		
			}
			else{ innerHTML("login_pg","ตัวเลขในรูปไม่ถูกต้อง");}
		}
		else
		{innerHTML("login_pg","ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง");
		}
	}

	function rsp_login()
	{
		if($_log_re.readyState==4)
		{
			rDisable("log_but"); mask_progress_hide();
			$receive=$_log_re.responseText; //alert($receive);
			if($receive=='r')
			{
				localStorage.removeItem("trylogin");
				window.location.assign($_url);	 				
			}
			else
			{
				change_capt();
				if($receive=='w'){ innerHTML("login_pg","ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง"); }
				if($receive=='c'){ innerHTML("login_pg","ตัวเลขในรูปไม่ถูกต้อง"); }
			}

		}
	}

function  change_capt()
{
	$img=document.getElementById("img_capt");
	$rand=randomNumber();
	$src=$_url+'piccapt.php?site=1&id='+$rand;
	$img.setAttribute("src",$src);
}
</script>
<style>
input { border: 2px solid #a9b6d9; background-color:white; padding:3px; border-radius:3px; }

</style>
</head>
<body style="background-color:rgba(126,152,204,0.8); color:white;">
<?php top_head(); ?>
<center><div class="body_box">

<table style="width:100%; font-size:14px;">
	<tr>
		<td align="center">
		<h2>คุณอาจจะลืม ว่ารหัสผ่านของคุณคืออะไร</h2>
			<table border="0">
					<tr>
						<td align="right">ชื่อผู้ใช้งาน</td><td style="width:180px;">
						<input type="txt" id="username_t" style="width:94%;"> </td><td>Username</td>
					</tr>
					<tr>
						<td align="right">รหัสผ่าน</td><td><input type="password" id="pass_t" style="width:94%;"></td><td>Password</td>
					</tr>
					<tr>
						<td></td><td><input type="checkbox" id="remember">จดจำฉันไว้ในระบบ</td><td></td>
					</tr>
					<tr>
						<td valign="top"></td>
						<td align="center">
							<div style="width:92%; display:inline-block; border:1px solid white;border-radius:3px;">
									<span style="display:inline-block;width:87px;height:42px; margin:2px;">
										<img id="img_capt" src="<?php echo $_url; ?>/piccapt.php?site=1">
									</span>	<br>
								
							<span class="link12" onclick="change_capt()">เปลี่ยนรูป</span><br>
							<input type="text" id="capt" maxlength="4" style="width:40px; text-align:center;margin:3px 0px;"><br>
							<span class="desc12">กรอกตัวเลขที่เห็นในรูป</span>
							</div>
						</td><td></td>
					</tr>

					<tr>
						<td></td><td align="center"><button onclick="try_login()" id="log_but" style="width:94%;" class="gray_button">เข้าสู่ระบบ</button>
						</td><td></td>
					</tr>
					<tr>
						<td>&nbsp;</td><td align="left" colspan="2"><span id="login_pg" class="warn"></span></td>
					</tr>
					<tr>
							<td></td><td>&nbsp;</td><td></td>
					</tr>
			</table>
			<h2>หากคุณยังไม่ลืมชื่อตัวเอง ให้ติดต่อผู้ดูแลเว็บไซต์</h2>
		</td>
	</tr>
	</table>
<script>
if(localStorage.getItem("trylogin")) {sValue("username_t",localStorage.getItem("trylogin"));}
</script>
</div></center>
<?php footer(); if($_con) { $_con=null;} ?>
</body>
</html>
<?php
if($_con) { $_con=null;}
?>