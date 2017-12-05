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
	<title>สร้างตารางการแข่งขัน</title>

<link rel="stylesheet" href="<?php echo $_url; ?>/main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>/images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>/main.js"></script>
<script>

	window.addEventListener("resize", resize_td);

	function body_click()
	{	innerHTML("e_league_guess","");
		innerHTML("league_guess",""); innerHTML("team1_guess",""); innerHTML("team2_guess","");  innerHTML("e_team1_guess",""); innerHTML("e_team2_guess","");
	}
	function body_press(event)
	{
		if(event.keyCode==27)
		{	innerHTML("e_league_guess","");
		innerHTML("league_guess",""); innerHTML("e_team1_guess","");  innerHTML("e_team2_guess","");
		}
	}

var $_request2,$l_find_send=0;$t_find_send=0;
var $center_pro='<center>'+$_progress+'</center>';
var $league_id_keep,$home_id_keep,$away_id_keep;
function find_league(event)
{
	event.stopPropagation();
	$txt=gValue("league");
	if($txt.length>2 && $l_find_send==0)
	{
		innerHTML("league_guess",'<span id="l_g_node" class="guess_show"></span>');
		innerHTML("l_g_node",$center_pro);
		$l_find_send=1;
		$form= new FormData();
		$form.append("choose_league",$txt);
		$form.append("page","mtable");

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
			if(document.getElementById("l_g_node")){innerHTML("l_g_node",$receive);}
		}
}
function league_select($id,$en)
{
	$league_id_keep=$id;
	sValue("league",$en); 	innerHTML("league_guess","");
}

var $_request5,$field,$cur_find,$cur_node;
function find_team(event,$home,$input,$node)
{	
	$field=$home;
	$cur_find=$input;
	$cur_node=$node;

	event.stopPropagation();
	$txt=gValue($cur_find);
	if($txt.length>2 && $t_find_send==0)
	{
		innerHTML($cur_node,'<span id="t_g_node" class="guess_show"></span>');
		innerHTML("t_g_node",$center_pro);
		$t_find_send=1;
		$form= new FormData();
		$form.append("choose_team",$txt);
		$form.append("page","mtable");

		$_request5= new XMLHttpRequest();
		$_request5.onreadystatechange=rsp_guess_team;
		$_request5.open("POST","manage.php",true);
		$_request5.send($form);	

	}
	if($txt.length<3 &&  $t_find_send==0)
	{
		innerHTML($cur_node,"");
	}
}

function rsp_guess_team()
{
		if($_request5.readyState==4)
		{
			$t_find_send=0;
			$receive=$_request5.responseText; 
			if(document.getElementById("t_g_node")){innerHTML("t_g_node",$receive);}
		}
}

function team_select($id,$en)
{
	if($field==1) {$home_id_keep=$id;}
	if($field==2) {$away_id_keep=$id;}
	sValue($cur_find,$en); 	innerHTML($cur_node,"");
}

//-----------------------------------------------

var $_reqest;
function send_match()
{
	innerHTML("m_warn","");

	$t_day=gValue("table_day");
	$t_month=gValue("table_month");
	$t_year=gValue("table_year");
	
	$k_day=gValue("race_day");
	$k_month=gValue("race_month");
	$k_year=gValue("race_year");
	$k_time=gValue("race_time");

	$n_check=document.getElementById("no_home");
	if($n_check.checked) { $nfield=1;} else { $nfield=0;}

	$advan=document.odds.odd.value;
	$handicap=gValue("handicap");
	
	$check=0; $warn='';

	if($t_day>0 && $t_month>0 && $t_year>2016)
	{ $check=1; 	} else { $warn=$_caution+" กรุณาตรวจสอบวันเวลาของตารางการแข่งขัน";}
	if($check==1)
	{ 		if($k_day>0 && $k_month>0 && $k_year>2016) {$check=2;} else { $warn=$_caution+" กรุณาตรวจสอบวันที่เตะ";} 
	}
	if($check==2)
	{
		$pat=/^[0-2][0-9][0-5][0-9]$/;
		if($pat.test($k_time)) {$check=3;} else { $warn=$_caution+" กรุณาตรวจสอบเวลาที่เริ่มเตะ";}
	}
	if($check==3)
	{ if($league_id_keep>0 ) {$check=4; } else { $warn=$_caution+" กรุณาระบุลีกที่เตะ";}
	}
	if($check==4)
	{ if($home_id_keep>0  && $away_id_keep>0 && $home_id_keep != $away_id_keep) {$check=5; } else { $warn=$_caution+" กรุณาตรวจสอบทีมที่ลงเตะ";}
	}
	if($check==5)
	{
		if($advan>0) {$check=6;} else { $warn=$_caution+" กรุณาระบุทีมบอลต่อ";}
	} 
	if($check==6)
	{
		$pc=/^[0-9][0-9]*([\.](0|00|25|5|50|75))*$/;
		if($pc.test($handicap)) {$check=7;}  else { $warn=$_caution+" กรุณาตรวจสอบราคาบอล";}
	}
	if($check==7)
	{
		innerHTML("m_warn",$_progress+" กำลังส่งข้อมูล"); sDisable("bt_table");

		$form=new FormData();
		$form.append("t_day",$t_day);
		$form.append("t_month",$t_month);
		$form.append("t_year",$t_year);

		$form.append("k_day",$k_day);
		$form.append("k_month",$k_month);
		$form.append("k_year",$k_year);
		$form.append("k_time",$k_time);
		
		$form.append("nfield",$nfield);
		$form.append("l_id",$league_id_keep);
		$form.append("h_id",$home_id_keep);
		$form.append("a_id",$away_id_keep);

		$form.append("advan",$advan);
		$form.append("handicap",$handicap);
		$form.append("page","mtable");

		$_request= new XMLHttpRequest();
		$_request.onreadystatechange=rsp_send_match;
		$_request.open("POST","manage.php",true);
		$_request.send($form);	
	}
	else
	{ 	
		innerHTML("m_warn",$warn);
	}
}

function rsp_send_match()
{
		if($_request.readyState==4)
		{
			innerHTML("m_warn",""); rDisable("bt_table");
			$receive=$_request.responseText; 
			if($receive=='t') { innerHTML("m_warn",$_caution+" วันและเวลาไม่ถูกต้อง");}
			if($receive=='h') { innerHTML("m_warn",$_caution+" ข้อมูลทีมลงแข่งซ้ำ");}
			if($receive=='c') { innerHTML("m_warn",$_ok+" บันทึกลงตารางการแข่งขันแล้ว");
				sValue("race_time","");
				sValue("home_team",""); $home_id_keep=0;
				sValue("away_team","");  $away_id_keep=0;
				document.getElementById("no_home").checked=false;
				document.odds.reset();
					$t_day=gValue("table_day");
					$t_month=gValue("table_month");
					$t_year=gValue("table_year");
					sValue("s_day",$t_day);
					sValue("s_month",$t_month);
					sValue("s_year",$t_year);
					load_match();
			}		
		}
}

var $request1;

function load_match()
{
		$t_day=gValue("s_day");
		$t_month=gValue("s_month");
		$t_year=gValue("s_year");
		document.getElementById("load_node").style.opacity=0.3;	
		innerHTML("load_warn",$_progress+" กำลังโหลดข้อมูล"); sDisable("load_bt");

		$form=new FormData();
		$form.append("l_day",$t_day);
		$form.append("l_month",$t_month);
		$form.append("l_year",$t_year);
		$form.append("page","mtable");

		$_request1= new XMLHttpRequest();
		$_request1.onreadystatechange=rsp_load_match;
		$_request1.open("POST","manage.php",true);
		$_request1.send($form);
}
function rsp_load_match()
{
		if($_request1.readyState==4)
		{
			innerHTML("load_warn",""); rDisable("load_bt");
			document.getElementById("load_node").style.opacity=1.0;
			$receive=$_request1.responseText; 
			innerHTML("load_node",$receive);
		}
}
//========edit function====================================================================================

var $_cur_edit_id;
var $league_id_edit,$home_id_edit,$away_id_edit;

function edit_match($id)
{
	$_cur_edit_id=$id;
	$mess='emess'+$id;
	$tr=document.getElementById($mess);
	$data=$tr.getAttribute("data");
	$slice=$data.split('|');
	sValue("e_table_day",$slice[0]); 	sValue("e_table_month",$slice[1]); 	sValue("e_table_year",$slice[2]);
	sValue("e_race_day",$slice[3]); 	sValue("e_race_month",$slice[4]); 	sValue("e_race_year",$slice[5]);
	$league_id_edit=$slice[6]; sValue("e_league",$slice[7]); sValue("e_race_time",$slice[8]);
	if($slice[9]=='1'){ document.getElementById("e_no_home").checked=true;} else { document.getElementById("e_no_home").checked=false;}
	$home_id_edit=$slice[10]; sValue("e_home_team",$slice[11]); $away_id_edit=$slice[12]; sValue("e_away_team",$slice[13]);
	if($slice[14]=='1') { document.getElementById("e_odd1").checked=true;}
	if($slice[14]=='2') { document.getElementById("e_odd2").checked=true;}
	sValue("e_handicap",$slice[15]);
	
	$table=document.getElementById("mask_edit");
	$table.style.display='table-cell';
	resize_td();
}

function e_find_league(event)
{
	event.stopPropagation();
	$txt=gValue("e_league");
	if($txt.length>2 && $l_find_send==0)
	{
		innerHTML("e_league_guess",'<span id="e_l_g_node" class="guess_show"></span>');
		innerHTML("e_l_g_node",$center_pro);
		$l_find_send=1;
		$form= new FormData();
		$form.append("choose_league",$txt);
		$form.append("edit_league",1);
		$form.append("page","mtable");

		$_request2= new XMLHttpRequest();
		$_request2.onreadystatechange=rsp_e_guess_league;
		$_request2.open("POST","manage.php",true);
		$_request2.send($form);
	}
	if($txt.length<3 &&  $l_find_send==0)
	{
		innerHTML("e_league_guess","");
	}
}
function rsp_e_guess_league()
{
		if($_request2.readyState==4)
		{
			$l_find_send=0;
			$receive=$_request2.responseText; 
			if(document.getElementById("e_l_g_node")){innerHTML("e_l_g_node",$receive);}
		}
}
function e_league_select($id,$en)
{
	$league_id_edit=$id;
	sValue("e_league",$en); 	innerHTML("e_league_guess","");
}

function e_find_team(event,$home,$input,$node)
{	
	$field=$home;
	$cur_find=$input;
	$cur_node=$node;

	event.stopPropagation();
	$txt=gValue($cur_find);
	if($txt.length>2 && $t_find_send==0)
	{
		innerHTML($cur_node,'<span id="e_t_g_node" class="guess_show"></span>');
		innerHTML("e_t_g_node",$center_pro);
		$t_find_send=1;
		$form= new FormData();
		$form.append("choose_team",$txt);
		$form.append("team_edit",1);
		$form.append("page","mtable");
		$_request5= new XMLHttpRequest();
		$_request5.onreadystatechange=rsp_e_guess_team;
		$_request5.open("POST","manage.php",true);
		$_request5.send($form);	

	}
	if($txt.length<3 &&  $t_find_send==0)
	{
		innerHTML($cur_node,"");
	}
}

function rsp_e_guess_team()
{
		if($_request5.readyState==4)
		{
			$t_find_send=0;
			$receive=$_request5.responseText; 
			if(document.getElementById("e_t_g_node")){innerHTML("e_t_g_node",$receive);}
		}
}

function e_team_select($id,$en)
{
	if($field==1) {$home_id_edit=$id;}
	if($field==2) {$away_id_edit=$id;}
	sValue($cur_find,$en); 	innerHTML($cur_node,"");
}
//------------------------------------------------------------------

var $_reqest_e;
function e_send_match()
{
	innerHTML("e_m_warn","");

	$t_day=gValue("e_table_day");
	$t_month=gValue("e_table_month");
	$t_year=gValue("e_table_year");
	
	$k_day=gValue("e_race_day");
	$k_month=gValue("e_race_month");
	$k_year=gValue("e_race_year");
	$k_time=gValue("e_race_time");

	$n_check=document.getElementById("e_no_home");
	if($n_check.checked) { $nfield=1;} else { $nfield=0;}

	$advan=document.e_odds.e_odd.value;
	$handicap=gValue("e_handicap");
	
	$check=0; $warn='';

	if($t_day>0 && $t_month>0 && $t_year>2016)
	{ $check=1; 	} else { $warn=$_caution+" กรุณาตรวจสอบวันเวลาของตารางการแข่งขัน";}
	if($check==1)
	{ 		if($k_day>0 && $k_month>0 && $k_year>2016) {$check=2;} else { $warn=$_caution+" กรุณาตรวจสอบวันที่เตะ";} 
	}
	if($check==2)
	{
		$pat=/^[0-2][0-9][0-5][0-9]$/;
		if($pat.test($k_time)) {$check=3;} else { $warn=$_caution+" กรุณาตรวจสอบเวลาที่เริ่มเตะ";}
	}
	if($check==3)
	{ if($league_id_edit>0 ) {$check=4; } else { $warn=$_caution+" กรุณาระบุลีกที่เตะ";}
	}
	if($check==4)
	{ if($home_id_edit>0  && $away_id_edit>0 && $home_id_edit != $away_id_edit) {$check=5; } else { $warn=$_caution+" กรุณาตรวจสอบทีมที่ลงเตะ";}
	}
	if($check==5)
	{
		if($advan>0) {$check=6;} else { $warn=$_caution+" กรุณาระบุทีมบอลต่อ";}
	} 
	if($check==6)
	{
		$pc=/^[0-9][0-9]*([\.](0|00|25|5|50|75))*$/;
		if($pc.test($handicap)) {$check=7;}  else { $warn=$_caution+" กรุณาตรวจสอบราคาบอล";}
	}
	if($check==7)
	{
		innerHTML("e_m_warn",$_progress+" กำลังส่งข้อมูล"); sDisable("e_bt_table");

		$form=new FormData();
		$form.append("editmatch",$_cur_edit_id);
		$form.append("t_day_",$t_day);
		$form.append("t_month_",$t_month);
		$form.append("t_year_",$t_year);

		$form.append("k_day_",$k_day);
		$form.append("k_month_",$k_month);
		$form.append("k_year_",$k_year);
		$form.append("k_time_",$k_time);
		
		$form.append("nfield_",$nfield);
		$form.append("l_id_",$league_id_edit);
		$form.append("h_id_",$home_id_edit);
		$form.append("a_id_",$away_id_edit);

		$form.append("advan_",$advan);
		$form.append("handicap_",$handicap);
		$form.append("page","mtable");

		$_request= new XMLHttpRequest();
		$_request.onreadystatechange=rsp_e_send_match;
		$_request.open("POST","manage.php",true);
		$_request.send($form);	
	}
	else
	{ 	
		innerHTML("e_m_warn",$warn);
	}
}
function rsp_e_send_match()
{
		if($_request.readyState==4)
		{
			innerHTML("e_m_warn",""); rDisable("e_bt_table");
			$receive=$_request.responseText; 
			if($receive=='t') { innerHTML("e_m_warn",$_caution+" วันและเวลาไม่ถูกต้อง");}
			if($receive=='h') { innerHTML("e_m_warn",$_caution+" ข้อมูลทีมลงแข่งซ้ำ");}
			if($receive=='c') { innerHTML("e_m_warn",$_ok+" บันทึกการแก้ไขเสร็จสมบูรณ์");

					$t_day=gValue("e_table_day");
					$t_month=gValue("e_table_month");
					$t_year=gValue("e_table_year");
					sValue("s_day",$t_day);
					sValue("s_month",$t_month);
					sValue("s_year",$t_year);
					load_match();
			}
		}
}
//============================
function hide_edit()
{
	$table=document.getElementById("mask_edit");
	$table.style.display='none';
}

function resize_td()
{
	$td=document.getElementById("td_edit_node");
	$w=window.innerWidth; $td.style.width=$w+'px';
	$h=window.innerHeight;  $td.style.height=$h+'px';
}
var $cur_del=0,$_request_d,$del_send=0;

function del_match($id)
{		
	if($del_send==0)
	{
		$del_send=1;
		$cur_del=$id;
		$span=document.getElementById("ew"+$cur_del);
		$span.innerHTML=$_progress;
		$tr=document.getElementById("emess"+$cur_del);
		$tr.style.opacity=0.3;

		$form= new FormData();
		$form.append("del_match",$id);
		$form.append("page","mtable");
		$_request_d= new XMLHttpRequest();
		$_request_d.onreadystatechange=rsp_del;
		$_request_d.open("POST","manage.php",true);
		$_request_d.send($form);	
	}
}
function rsp_del()
{
		if($_request_d.readyState==4)
		{
			$span=document.getElementById("ew"+$cur_del);
			$span.innerHTML="";
			$tr=document.getElementById("emess"+$cur_del);
			$tr.style.opacity=1.0;
			$del_send=0;
			$receive=$_request_d.responseText; 
			if($receive=='d')
			{
				$tbody=document.getElementById("load_node");
				$tbody.removeChild($tr);
			}
		}
}
</script>

<style>
	body{ margin:30px;}
	.team{ width:350px;}
	.guess_row{padding:1px 3px; cursor:pointer;}
	.guess_row:hover { background-color:rgb(220,220,220);}
	.guess_show {position:absolute; left:0px; top:0px;background-color:white; width:352px; border:1px solid rgb(200,200,200);}
	.edit_home,.edit_away{ width:200px;}
	.edit_home{ text-align:right;}
	.edit_handicap{ width:50px; text-align:center;}
	.edit_lea{ font-weight:bold; padding-left:40px; background-color:rgb(230,230,230);}
	.edit_kick{ width:50px;}
	.edit_txt{ width:50px;}
	.ew{display:inline-block; width:30px;}

</style>
</head>

<body onclick="body_click()" onkeypress="body_press(event)" style="min-height:500px;background:white;">
<?php
$txt_day=date("j",time());
$txt_month=date("n",time());
$txt_year=date("Y",time());
?>

<h2>สร้างตารางการแข่งขันประจำวัน</h2>
<span class="desc">* จำเป็นต้องมีชื่อ ลีก และชื่อทีมอยู่ในระบบก่อน </span><br><br>

<table border="0">
	<tr>
		<td><b>ตารางของวันที่</b></td>
		<td colspan="3">		 
				<input type="text" id="table_day" size="2" value="<?php echo $txt_day ;?>">
				<input type="text" id="table_month" size="2" value="<?php echo $txt_month ;?>">
				<input type="text" id="table_year" size="4" value="<?php echo $txt_year ;?>">
				<span class="desc">วัน/เดือน/ค.ศ.</span>
		</td>
	</tr>
		<td>&nbsp;</td><td colspan="3">	</td>
	</tr>

	<tr>
		<td><div style="width:100px;">วันที่เตะ</div></td>
		<td>
			<input type="text" id="race_day" size="2" value="<?php echo $txt_day ;?>">
			<input type="text" id="race_month" size="2" value="<?php echo $txt_month ;?>">
			<input type="text" id="race_year" size="4" value="<?php echo $txt_year ;?>"> 
			<span class="desc">วัน/เดือน/ค.ศ.</span>
		</td><td></td><td></td>		
	</tr>
	<tr>
		<td>ลีก</td>
		<td colspan="3">
					<input type="text" id="league" oninput="find_league(event)" onclick="find_league(event)" class="team">				
					<div id="league_guess" style="position:relative; height:0px; width:100%;"></div>

		</td>
	</tr>
	<tr>
		<td>เวลาเตะ</td>
		<td><input type="text" size="4" maxlength="4" id="race_time"><span class="desc">  HHMM เช่น 0230</span> <input type="checkbox" id="no_home"> เตะสนามกลาง(N)
		</td><td></td><td></td>
	</tr>

	<tr>
			<td>ทีมเจ้าบ้าน</td><td>
			<input type="text" id="home_team" class="team" oninput="find_team(event,1,'home_team','team1_guess')" onclick="find_team(event,1,'home_team','team1_guess')">
				<div id="team1_guess" style="position:relative; height:0px; width:100%;"></div>

			</td>
			<td>ทีมเยือน</td><td>
			<input type="text" id="away_team" class="team" oninput="find_team(event,2,'away_team','team2_guess')" onclick="find_team(event,2,'away_team','team2_guess')">
				   <div id="team2_guess" style="position:relative; height:0px; width:100%;"></div>

			</td>
	</tr>
		<tr>
			<td>ราคาบอล</td>
			<td colspan="3">
				<form name="odds">
				<input type="radio"  name="odd" value="1"> เจ้าบ้านต่อ 				
				<input type="radio" name="odd" value="2">  ทีมเยือนต่อ 
				<input type="text" size="5" maxlength="5" id="handicap"> 
				<span class="desc">ราคาต่อ(ทศนิยม) 0.25 0.5 0.75 1.25 เป็นต้น</span>
			</form>
			</td>			
	</tr>
	<tr>
		<td></td><td colspan="3"><button id="bt_table" onclick="send_match()">ส่งข้อมูลการแข่งขัน</button>
			<span id="m_warn"></span>
		</td>
	</tr>

</table> <!-------end create table------------------>

<hr>
<br><br>
<h3>ค้นหาและแก้ไข</h3>
<table border="0">
	<tr>
		<td colspan="0">		 
				<input type="text" id="s_day" size="2" value="">
				<input type="text" id="s_month" size="2" value="<?php echo $txt_month ;?>">
				<input type="text" id="s_year" size="4" value="<?php echo $txt_year ;?>">
				<span class="desc">วัน/เดือน/ค.ศ.</span>
		</td>
		<td><button id="load_bt" onclick="load_match()">โหลดตารางแข่งขัน</button><span id="load_warn"></span></td>
	</tr>
</table>
<!------------------------------load node--------------------------------->
	<table border="0" >
		<tbody id="load_node"></tbody>
	</table>
<!------------------------------------------------------------------------->

	<table id="mask_edit" style="position:fixed; background-color:rgba(80,80,80,0.3);left:0px; z-index:10;left:0px;top:0px;display:table-cell;display:none;">
	<tr>
		<td id="td_edit_node" align="center">
		<!----------edit table------------------------------------->
<table border="0" style="width:70%; background-color:white;padding:40px;border-radius:3px;">
	<tr>
		<td><b>ตารางของวันที่</b></td>
		<td colspan="3" style="position:relative;">
				<input type="text" id="e_table_day" size="2" value="">
				<input type="text" id="e_table_month" size="2" value="">
				<input type="text" id="e_table_year" size="4" value="">
				<span class="desc">วัน/เดือน/ค.ศ.</span>
				<img src="<?php echo $_url; ?>images/red_close.png" style="position:absolute;right:-55px;top:-55px;cursor:pointer;" onclick="hide_edit()">
		</td>
	</tr>
		<td>&nbsp;</td><td colspan="3"></td>
	</tr>

	<tr>
		<td><div style="width:100px;">วันที่เตะ</div></td>
		<td>
			<input type="text" id="e_race_day" size="2" value="">
			<input type="text" id="e_race_month" size="2" value="">
			<input type="text" id="e_race_year" size="4" value=""> 
			<span class="desc">วัน/เดือน/ค.ศ.</span>
		</td><td></td><td></td>		
	</tr>
	<tr>
		<td>ลีก</td>
		<td colspan="3">
					<input type="text" id="e_league" oninput="e_find_league(event)" onclick="e_find_league(event)" class="team">				
					<div id="e_league_guess" style="position:relative; height:0px; width:100%;"></div>

		</td>
	</tr>
	<tr>
		<td>เวลาเตะ</td>
		<td><input type="text" size="4" maxlength="4" id="e_race_time"><span class="desc">  HHMM เช่น 0230</span> <input type="checkbox" id="e_no_home"> เตะสนามกลาง(N)
		</td><td></td><td></td>
	</tr>

	<tr>
			<td>ทีมเจ้าบ้าน</td><td>
			<input type="text" id="e_home_team" class="team" oninput="e_find_team(event,1,'e_home_team','e_team1_guess')" onclick="e_find_team(event,1,'e_home_team','e_team1_guess')">
				<div id="e_team1_guess" style="position:relative; height:0px; width:100%;"></div>

			</td>
			<td>ทีมเยือน</td><td>
			<input type="text" id="e_away_team" class="team" oninput="e_find_team(event,2,'e_away_team','e_team2_guess')" onclick="e_find_team(event,2,'e_away_team','e_team2_guess')">
				   <div id="e_team2_guess" style="position:relative; height:0px; width:100%;"></div>

			</td>
	</tr>
		<tr>
			<td>ราคาบอล</td>
			<td colspan="3">
				<form name="e_odds">
				<input type="radio"  name="e_odd" value="1" id="e_odd1"> เจ้าบ้านต่อ 				
				<input type="radio" name="e_odd" value="2" id="e_odd2">  ทีมเยือนต่อ 
				<input type="text" size="5" maxlength="5" id="e_handicap"> 
				<span class="desc">ราคาต่อ(ทศนิยม) 0.25 0.5 0.75 1.25 เป็นต้น</span>
			</form>
			</td>			
	</tr>
	<tr>
		<td></td><td colspan="3"><button id="e_bt_table" onclick="e_send_match()">บันทึกการแก้ไข</button>
			<span id="e_m_warn"></span>
		</td>
	</tr>

</table> 
<!---------------end table-------------------------------->
	</td>
	</tr>
	</table>
</body>
</html>
<?php
if($_con) { $_con=null;}
?>