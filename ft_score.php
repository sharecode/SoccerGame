<?php
require_once "ext.php";
con_mysql();
session_pair();
if(islogin() && isAdmin(0)){} else{ $_con=null; header('Location: '.$_url); exit();}
//================================
$txt_day=date("j");
$txt_month=date("n");
$txt_year=date("Y");

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>สรุปผลการแข่งขัน</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>

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
		$form.append("page","score");

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

var $request2;
var $cur_send;
function send_score($id)
{		
		$cur_send=$id;

		$score1=gValue('fh_h_score'+$id);
		$score2=gValue('fh_a_score'+$id);
		$score3=gValue('ft_h_score'+$id);
		$score4=gValue('ft_a_score'+$id);

		if(num_test($score1)&& num_test($score2) && num_test($score3) && num_test($score4))
		{
			innerHTML("score_warn"+$id,$_progress); sDisable("score_bt"+$id);

			$form=new FormData();
			$form.append("save_score",$id);
			$form.append("fh_h",$score1);
			$form.append("fh_a",$score2);
			$form.append("ft_h",$score3);
			$form.append("ft_a",$score4);

			$form.append("page","score");

			$request2= new XMLHttpRequest();
			$request2.onreadystatechange=rsp_save_score;
			$request2.open("POST","manage.php",true);
			$request2.send($form);
		}
}
function rsp_save_score()
{
		if($request2.readyState==4)
		{
			$receive=$request2.responseText; 
			innerHTML("score_warn"+$cur_send,''); rDisable("score_bt"+$cur_send);
			if($receive=='f')
			{
				$tr=document.getElementById('emess'+$cur_send);
				$tr.setAttribute("class","a_finish");
			}
		}
}

var $request3;
var $cur_send;
function delay($id)
{		
		$cur_send=$id;

			innerHTML("score_warn"+$id,$_progress); sDisable("score_bt"+$id);

			$form=new FormData();
			$form.append("set_delay",$id);
			$form.append("page","score");

			$request2= new XMLHttpRequest();
			$request2.onreadystatechange=rsp_set_delay;
			$request2.open("POST","manage.php",true);
			$request2.send($form);
		
}
function rsp_set_delay()
{
		if($request2.readyState==4)
		{
			$receive=$request2.responseText; 
			innerHTML("score_warn"+$cur_send,''); rDisable("score_bt"+$cur_send);
		}
			if($receive=='f')
			{
				$tr=document.getElementById('emess'+$cur_send);
				$tr.setAttribute("class","a_finish");
			}

}

</script>
<style>
	body {margin:50px; background:white;}
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
	.score_tb tbody tr td input { width:20px;}
	.score_warn{display:inline-block;width:20px;}
	.a_finish{background-color:LemonChiffon;}

</style>
</head>

<body>

<h2>สรุปผลการแข่งขัน</h2>
<span class="desc">สรุปประตูเมื่อจบเกมส์ ระบบจะเช็คการทายผลอัตโนมัติ</span><br>

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
	<table border="0" class="score_tb">
		<tbody id="load_node"></tbody>
	</table>

</body>
</html>