<?php
require_once "ext.php";

con_mysql();
session_pair();
if(islogin() && isAdmin(0)){}
else{$_con=null;  header('Location: '.$_url); exit();}

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>เพิ่มทีม เข้าในระบบ</title>

<link rel="stylesheet" href="<?php echo $_url; ?>/main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>/images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>/main.js"></script>
<script>

	function body_click()
	{
		innerHTML("league_guess",""); innerHTML("team_guess","");
	}
	function body_press(event)
	{
		if(event.keyCode==27)
		{
		innerHTML("league_guess",""); innerHTML("team_guess","");
		}
	}


	var $_request;
	var $_l_name;
	function add_league()
	{
		sDisable("l_button"); innerHTML("l_warn",$_progress+" กำลังส่งข้อมูล");
		$en_l=gValue("en_league");
		$th_l=gValue("th_league");
		$_l_name=$en_l;
						$form= new FormData();
						$form.append("addleague",$en_l);
						$form.append("th_l",$th_l);
						$form.append("page","addteam");

						$_request= new XMLHttpRequest();
						$_request.onreadystatechange=rsp_add_league;
						$_request.open("POST","manage.php",true);
						$_request.send($form);	
	}

	function rsp_add_league()
	{
		if($_request.readyState==4)
		{
			rDisable("l_button"); innerHTML("l_warn","");
			$receive=$_request.responseText; 
			if($receive=='h') {  innerHTML("l_warn",$_caution+" มีชื่อลีกนี้อยู่แล้วในระบบ");}
			if($receive=='o') {  innerHTML("l_warn",$_ok+" เพิ่มลีก "+$_l_name+" เข้าในระบบแล้ว");
				sValue("en_league",""); sValue("th_league","");
			}			
		}
	}
	//=======================================
	var $_request1;
	var $_team_name;
	function add_team()
	{
		sDisable("t_button"); innerHTML("t_warn",$_progress+" กำลังส่งข้อมูล");  innerHTML("logo_node","");
		$en_t=gValue("en_team");
		$th_t=gValue("th_team");
		$_team_name=$en_t;

						$form= new FormData();
						$form.append("addteam",$en_t);
						$form.append("th_t",$th_t);
						$form.append("page","addteam");
						
						$file=document.getElementById("logo_team");
						if($file.files.length)
							{
							$file_send=$file.files[0]
							if($file_send.name.length)
								{
									$form.append("logo",$file_send);
								}
							}
						$_request1= new XMLHttpRequest();
						$_request1.onreadystatechange=rsp_add_team;
						$_request1.open("POST","manage.php",true);
						$_request1.send($form);	
	}

	function rsp_add_team()
	{
		if($_request1.readyState==4)
		{
			rDisable("t_button"); innerHTML("t_warn","");
			$receive=$_request1.responseText;  alert($receive);
			if($receive=='h') {  innerHTML("t_warn",$_caution+" มีชื่อทีมนี้อยู่แล้วในระบบ");}
			if(num_test($receive)) 
			{  innerHTML("t_warn",$_ok+" เพิ่มทีม "+$_team_name+" เข้าในระบบแล้ว");
				sValue("en_team",""); sValue("th_team",""); document.logo_team.reset();
				$img='<img src="'+$_url+'image.php?lg='+$receive+'">';
				innerHTML("logo_node",$img);
			}			
		}
	}
//----------------------------------------------------------------------------------
var $_request2,$l_find_send=0;$t_find_send=0;
var $center_pro='<center>'+$_progress+'</center>';
function find_league(event)
{
	event.stopPropagation();
	$txt=gValue("old_league");
	if($txt.length>2 && $l_find_send==0)
	{
		innerHTML("league_guess",'<span id="l_g_node" class="guess_show"></span>');
		innerHTML("l_g_node",$center_pro);
		$l_find_send=1;
		$form= new FormData();
		$form.append("find_league",$txt);
		$form.append("page","addteam");

		$_request2= new XMLHttpRequest();
		$_request2.onreadystatechange=rsp_guess_league;
		$_request2.open("POST","manage.php",true);
		$_request2.send($form);	

	}
	if($txt.length<3 &&  $l_find_send==0)
	{
		innerHTML("league_guess","");
	}
}

function rsp_guess_league()
{
		if($_request2.readyState==4)
		{
			$l_find_send=0;
			$receive=$_request2.responseText; 
			if(document.getElementById("l_g_node")) {innerHTML("l_g_node",$receive); }
		}
}
//==============================
var $_request5;
function find_team(event)
{
	event.stopPropagation();
	$txt=gValue("old_team");
	if($txt.length>2 && $t_find_send==0)
	{
		innerHTML("team_guess",'<span id="t_g_node" class="guess_show"></span>');
		innerHTML("t_g_node",$center_pro);
		$t_find_send=1;
		$form= new FormData();
		$form.append("find_team",$txt);
		$form.append("page","addteam");

		$_request5= new XMLHttpRequest();
		$_request5.onreadystatechange=rsp_guess_team;
		$_request5.open("POST","manage.php",true);
		$_request5.send($form);	

	}
	if($txt.length<3 &&  $t_find_send==0)
	{
		innerHTML("team_guess","");
	}
}

function rsp_guess_team()
{
		if($_request5.readyState==4)
		{
			$t_find_send=0;
			$receive=$_request5.responseText; 
			if(document.getElementById("t_g_node")) {innerHTML("t_g_node",$receive); }
		}
}

var $cur_l_id,$cur_team_id;

function league_edit($id,$en,$th)
{
	innerHTML("league_guess","");
	sValue("edit_en_league",$en);
	sValue("edit_th_league",$th);
	$cur_l_id=$id;
}

function team_edit($id,$en,$th)
{
	innerHTML("team_guess","");
	sValue("edit_en_team",$en);
	sValue("edit_th_team",$th);
	$cur_team_id=$id;
	$num=randomNumber();
	$img='<img src="'+$_url+'image.php?lg='+$id+'&pic='+$num+'">';
	innerHTML("team_logo_node",$img);
	$del_in='<button onclick="del_logo()" id="del_b_logo">ลบโลโก้</button>';
	innerHTML("del_logo_node",$del_in)

}
//==========================================================

var $_request3;
	function save_league()
	{
		sDisable("e_l_button"); innerHTML("e_l_warn",$_progress+" กำลังส่งข้อมูล");
		 innerHTML("l_update_detail","");		
		$en_l=gValue("edit_en_league");
		$th_l=gValue("edit_th_league");
		
						$form= new FormData();
						$form.append("edit_league",$cur_l_id);
						$form.append("e_en_l",$en_l);
						$form.append("e_th_l",$th_l);
						$form.append("page","addteam");

						$_request3= new XMLHttpRequest();
						$_request3.onreadystatechange=rsp_save_league;
						$_request3.open("POST","manage.php",true);
						$_request3.send($form);	
	}

	function rsp_save_league()
	{
		if($_request3.readyState==4)
		{
			rDisable("e_l_button"); innerHTML("e_l_warn","");
			$receive=$_request3.responseText;
			if($receive=='h')
			{ innerHTML("e_l_warn",$_caution+" มีชื่อลีกนี้อยู่แล้วในระบบ");
			}else
			{
			 innerHTML("l_update_detail",$receive);	
			 sValue("old_league","");
			}
		}
	}
//=============================================
	var $_request4;
	function save_team()
	{
		sDisable("e_t_button"); innerHTML("e_t_warn",$_progress+" กำลังส่งข้อมูล");
		 innerHTML("t_update_detail","");		
		$en_l=gValue("edit_en_team");
		$th_l=gValue("edit_th_team");
		
						$form= new FormData();
						$form.append("edit_team",$cur_team_id);
						$form.append("e_en_t",$en_l);
						$form.append("e_th_t",$th_l);
						$form.append("page","addteam");

						$file=document.getElementById("edit_logo_team");						
						if($file.files.length)
							{
								$file_send=$file.files[0];
								$form.append("e_logo",$file_send);
							}
						$_request4= new XMLHttpRequest();
						$_request4.onreadystatechange=rsp_save_team;
						$_request4.open("POST","manage.php",true);
						$_request4.send($form);	
	}

	function rsp_save_team()
	{
		if($_request4.readyState==4)
		{
			rDisable("e_t_button"); innerHTML("e_t_warn","");
			$receive=$_request4.responseText;
			if($receive=='h')
			{ innerHTML("e_t_warn",$_caution+" มีชื่อทีมนี้อยู่แล้วในระบบ");
			}
			else
			{
			 innerHTML("t_update_detail",$receive);	
			 sValue("old_team","");	document.edit_logo.reset();		 	
			$num=randomNumber();
			$img='<img src="'+$_url+'image.php?lg='+$cur_team_id+'&pic='+$num+'">';
			innerHTML("team_logo_node",$img);
			}
		}
	}

	
	function del_logo()
	{
		sDisable("e_t_button"); sDisable("del_b_logo"); innerHTML("e_t_warn",$_progress+" กำลังลบข้อมูล");
		
						$form= new FormData();
						$form.append("del_team_logo",$cur_team_id);
						$form.append("page","addteam");

						$_request4= new XMLHttpRequest();
						$_request4.onreadystatechange=rsp_del_logo;
						$_request4.open("POST","manage.php",true);
						$_request4.send($form);	

	}
	function rsp_del_logo()
	{
		if($_request4.readyState==4)
		{
			rDisable("e_t_button"); rDisable("del_b_logo"); innerHTML("e_t_warn","");
			$receive=$_request4.responseText; alert($receive);
			$num=randomNumber();
			$img='<img src="'+$_url+'image.php?lg='+$cur_team_id+'&pic='+$num+'">';
			innerHTML("team_logo_node",$img);
		}
	}

</script>

<style>
input { width:400px;}
body { margin:30px;}
.guess_row{padding:1px 3px; cursor:pointer;}
.guess_row:hover { background-color:rgb(220,220,220);}
.guess_show {position:absolute; left:0px; top:0px;background-color:white; width:402px; border:1px solid rgb(200,200,200);}
</style>

</head>

<body onclick="body_click()" onkeypress="body_press(event)" style="background:white;">
<h2>เพิ่มข้อมูล ลีก - ทีม </h2><br>

<table border="0">
			<tr>
				<td colspan="2"><b>เพิ่มชื่อลีก</b></td>
			</tr>
			<tr>
				<td>ชื่อลีกภาษาอังกฤษ</td><td><input type="text" id="en_league">*</td>
			</tr>
			<tr>
				<td>ชื่อลีกภาษาไทย</td><td><input type="text" id="th_league"></td>
			</tr>

			<tr>
				<td></td><td><button onclick="add_league()" id="l_button">เพิ่มลีกเข้าในระบบ</button>
						<span id="l_warn"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><b>เพิ่มชื่อทีม</b></td>
			</tr>
			<tr>
				<td>ชื่อทีมภาษาอังกฤษ</td><td><input type="text" id="en_team">*<br><span class="desc">ถ้าเป็นบอลหญิง ให้ใส่ (w) ไว้ท้ายชื่อทีม</span></td>
			</tr>
			<tr>
				<td>ชื่อทีมภาษาไทย</td><td><input type="text" id="th_team"></td>
			</tr>
			<tr>
				<td valign="top">โลโก้ทีม <br>
					<div id="logo_node" style="display:block;width:170px; height:152px;">
					</div>
				</td>
				<td valign="top">
					<form name="logo_team"><input type="file" id="logo_team"></form>
					<span class="desc">* รูปภาพ  ขนาดไม่น้อยกว่า 150x150 pixel <br>แนะนำรูปภาพ ชนิด png แบบไม่มีพื้นหลัง </span><br>
					<button onclick="add_team()" id="t_button">เพิ่มทีมเข้าในระบบ</button>
					<span id="t_warn"></span>
				</td>
			</tr>

			<tr>
				<td></td><td>				</td>
			</tr>

</table>
<!------------------------------------------------------------------->
<hr>
<table border="0">
			<tr>
				<td colspan="2"><b>แก้ไขชื่อลีก</b></td>
			</tr>
			<tr>
				<td>ค้นหาชื่อลีก(ภาษาอังกฤษ)</td>
				<td>
			
					<input type="text" id="old_league" oninput="find_league(event)" onclick="find_league(event)">*
					<div id="league_guess" style="position:relative; height:0px; width:100%;"></div>
				</td>
			</tr>

			<tr>
				<td>ชื่อใหม่ภาษาอังกฤษ</td><td><input type="text" id="edit_en_league"> </td>
			</tr>
			<tr>
				<td>ชื่อใหม่ภาษาไทย</td><td><input type="text" id="edit_th_league"></td>
			</tr>

			<tr>
				<td></td><td><button onclick="save_league()" id="e_l_button">บันทึกการแก้ไข</button>
						<span id="e_l_warn"></span>
				</td>
			</tr>
			<tr>
			<td></td><td><div  id="l_update_detail" style="display:block; height:40px;"></div></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>			
			<tr>
				<td colspan="2"><b>แก้ไขชื่อทีม</b></td>
			</tr>
			<tr>
				<td>ค้นหาชื่อทีม(ภาษาอังกฤษ)</td>
				<td>			
					<input type="text" id="old_team" oninput="find_team(event)" onclick="find_team(event)">*
					<div id="team_guess" style="position:relative; height:0px; width:100%;"></div>
				</td>
			</tr>
			<tr>
				<td>ชื่อทีมภาษาอังกฤษ</td><td><input type="text" id="edit_en_team"></td>
			</tr>
			<tr>
				<td>ชื่อทีมภาษาไทย</td><td><input type="text" id="edit_th_team"></td>
			</tr>
			<tr>
				<td valign="top">โลโก้ทีม <br>
					<div id="team_logo_node" style="display:block;width:170px; height:152px;">
					</div>
					<div id="del_logo_node"></div>
				</td>
				<td valign="top">
					<form name="edit_logo"><input type="file" id="edit_logo_team"></form>
					<span class="desc">* รูปภาพ  ขนาดไม่น้อยกว่า 150x150 pixel <br>แนะนำรูปภาพ ชนิด png แบบไม่มีพื้นหลัง </span><br>
					<button onclick="save_team()" id="e_t_button">บันทึกการแก้ไข</button>
					<span id="e_t_warn"></span>
					<div  id="t_update_detail" style="display:block; height:40px;"></div>
				</td>
			</tr>
			<tr>
			<td></td><td></td>
			</tr>


</table>

<div class="hide">
		<img src="<?php echo $_url; ?>images/progress.gif">
		<img src="<?php echo $_url; ?>images/ok.png">
		<img src="<?php echo $_url; ?>images/caution.png">
</div>
</body>
</html>
<?php
if($_con) { $_con=null;}
?>