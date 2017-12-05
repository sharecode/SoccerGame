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
	<title>สร้างราคาบอล จาก HTML</title>

<link rel="stylesheet" href="<?php echo $_url; ?>/main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>/images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>/main.js"></script>
<script>

	var $_request1;
	function send_file()
	{
		innerHTML("send_warn","");

		$day=gValue("s_day");
		$month=gValue("s_month");
		$year=gValue("s_year");
		$check=0;
		if(num_test($day) && num_test($month) && num_test($year))
		{
					$file=document.getElementById("d_file");
						if($file.files.length)
							{
							$file_send=$file.files[0]
							if($file_send.name.length)
								{
									$check=1;
								}
							}else{ innerHTML("send_warn",$_caution+' โปรดเลือกไฟล์ที่ต้องการส่ง'); }

		}
		else { innerHTML("send_warn",$_caution+' โปรดระบุวันให้ตารางแข่งขัน');}
		if($check==1)
		{
						mask_progress_show("กำลังอับโหลดไฟล์");
						$form= new FormData();
						$form.append("dom_day",$day);
						$form.append("month",$month);
						$form.append("year",$year);
						$form.append("domfile",$file_send);
						$form.append("page","addteam");
						
						$_request1= new XMLHttpRequest();
						$_request1.onreadystatechange=rsp_add_team;
						$_request1.open("POST","dom.php",true);
						$_request1.send($form);	
		}
	}

	function rsp_add_team()
	{
		if($_request1.readyState==4)
		{
			mask_progress_hide();			
			innerHTML("send_warn",""); document.buff_file.reset();
			$receive=$_request1.responseText;  
			load_buffer();
		}
	}
	
	var $request_b;
	function load_buffer()
	{
						$form= new FormData();
						$form.append("load_dom",1);
						mask_progress_show("กำลังโหลดตาราง");
						$request_b= new XMLHttpRequest();
						$request_b.onreadystatechange=rsp_load_buffer;
						$request_b.open("POST","dom.php",true);
						$request_b.send($form);	

	}
	function rsp_load_buffer()
	{
		if($request_b.readyState==4)
		{
			mask_progress_hide();
			$receive=$request_b.responseText; 
			innerHTML("buffer_node",$receive);
		}
	}
/*-------------------------------------------------------*/
	var $request_d,$cur_del;
	function del_buff($id)
	{
			$cur_del=$id;
						$form= new FormData();
						$form.append("del_buff",$id);
						mask_progress_show("กำลังลบข้อมูล");
						$request_d= new XMLHttpRequest();
						$request_d.onreadystatechange=rsp_del_buffer;
						$request_d.open("POST","dom.php",true);
						$request_d.send($form);	

	}
	function rsp_del_buffer()
	{
		if($request_d.readyState==4)
		{
			mask_progress_hide();
			$receive=$request_d.responseText; 
			$tr=document.getElementById('buff'+$cur_del);
			$tbody=document.getElementById("buffer_node"); $tbody.removeChild($tr);
		}
	}
	
	var $request_a,$cur_add;
	function add_odds($id)
	{
			$cur_add=$id;
						$form= new FormData();
						$form.append("add_odds",$id);
						mask_progress_show("กำลังส่งข้อมูล");
						$request_a= new XMLHttpRequest();
						$request_a.onreadystatechange=rsp_add_odds;
						$request_a.open("POST","dom.php",true);
						$request_a.send($form);	

	}
	function rsp_add_odds()
	{
		if($request_a.readyState==4)
		{
			mask_progress_hide();
			$receive=$request_a.responseText;
			innerHTML('pd'+$cur_add,$_ok);
		}
	}
	$_request_all;
	function add_all()
	{
						innerHTML('p_add_all',"");
						$form= new FormData();
						$form.append("add_odds",0);
						mask_progress_show("กำลังส่งข้อมูล อาจต้องใช้เวลานาน");
						$_request_all= new XMLHttpRequest();
						$_request_all.onreadystatechange=rsp_add_all;
						$_request_all.open("POST","dom.php",true);
						$_request_all.send($form);	

	}
	function rsp_add_all()
	{
		if($_request_all.readyState==4)
		{
			mask_progress_hide();
			$receive=$_request_all.responseText;
			innerHTML('p_add_all',$_ok+" เพิ่มราคาเสร็จสมบูรณ์ โปรดตรวจสอบความถูกต้องอีกครั้ง");
		}
	}


</script>
<style>

.bf_node tbody tr td{border-top:1px solid gray; min-width:48px; text-align:left; padding:2px;}
.league_b { background:gray; color:white; font-weight:bold; padding-left:30px !important;}
.kick_b{border-left:1px solid gray;}
.team_b{ padding:0px 5px;  border-left:1px solid gray; border-right:1px solid gray;}
.t3_b{ padding:0px 5px;  border-right:1px solid gray;}

.end_b{ padding:0px 5px;  border-right:1px solid gray; border-right:1px solid gray;}


</style>
</head>

<body style="background:white;margin:50px;">
<h2>สร้างตารางแข่งขันจาก ไฟล์ HTML</h2>

<table border="0">
	<tr>
		<td colspan="0">		 
				<input type="text" id="s_day" size="2" value="">
				<input type="text" id="s_month" size="2" value="<?php echo date("n",time()) ;?>">
				<input type="text" id="s_year" size="4" value="<?php echo date("Y",time());?>">
				<span class="desc">วัน/เดือน/ค.ศ.</span><br><span class="desc">ต้องระบุวันของตารางทายผล</span>
		<td></td>
	</tr>
	<tr><td><form name="buff_file"><input type="file" id="d_file"></form></td><td></td><tr>
	<tr><td><button id="load_bt" onclick="send_file()">ส่งไฟล์</button><span id="send_warn"></span></td><td></td><tr>

</table>
<button onclick="load_buffer()">โหลดราคาบอล</button>

<table border="0" cellpadding="0" cellspacing="0" class="bf_node"><tbody id="buffer_node"></tbody></table><br>
<button onclick="add_all()">เพิ่มราคาทั้งหมดลงตารางทายผล</button><span id="p_add_all"></span>

<table class="tb_mask_progress" id="tb_mask_progress"><tr><td id="td_mask_progress" align="center"><span id="sp_mask_progress"></span></td></tr></table><script>window.addEventListener("resize", resize_mask_td);</script>		

</body>
</html>
<?php
if($_con) { $_con=null;}
?>