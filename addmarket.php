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
	<title>เปิดราคาบอล</title>

<link rel="stylesheet" href="<?php echo $_url; ?>/main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>/images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>/main.js"></script>
<script>
	
window.addEventListener("resize", resize_td);
var $request1;
function load_match()
{
		$t_day=gValue("s_day");
		$t_month=gValue("s_month");
		$t_year=gValue("s_year");
		document.getElementById("load_node").style.opacity=0.3;	
		innerHTML("load_warn",$_progress+" กำลังโหลดข้อมูล"); sDisable("load_bt");

		$form=new FormData();
		$form.append("market_day",$t_day);
		$form.append("market_month",$t_month);
		$form.append("market_year",$t_year);
		$form.append("page","addmarket");

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
			$receive=$_request1.responseText;  //alert($receive);
			innerHTML("load_node",$receive);
		}
}

function resize_td()
{
	$td=document.getElementById("td_edit_node");
	$w=window.innerWidth; $td.style.width=$w+'px';
	$h=window.innerHeight;  $td.style.height=$h+'px';
}

function hide_edit()
{
	$table=document.getElementById("mask_edit");
	$table.style.display='none';
}

var $_cur_market_id;
function addmarket($id)
{
	document.market_form.reset();
	$_cur_market_id=$id;
	$tbody=document.getElementById("load_node");
	$tr=$tbody.getElementsByTagName("tr");
	for($ri=0;$ri<$tr.length;$ri++)
	{
		$cur_tr=$tr[$ri];
		$cur_mid=$cur_tr.getAttribute("mid");
		if($id==$cur_mid)
		{
			$data=$cur_tr.getAttribute("data"); break;
		}
	}

	$slice=$data.split('|');	
	innerHTML("home_en",$slice[11]); innerHTML("away_en",$slice[13]);
	innerHTML("m_day",$slice[0]);	innerHTML("m_month",$slice[1]);	innerHTML("m_year",$slice[2]);
	innerHTML("home_name",$slice[11]); 	innerHTML("away_name",$slice[13]);
	innerHTML("home_name_win",$slice[11]); 	innerHTML("away_name_win",$slice[13]);

	$table=document.getElementById("mask_edit");
	$table.style.display='table-cell';
	resize_td();
	s_m_type(0); 	s_m_type(1); 
}

function fill_h()
{
	$han=gValue("home_handicap");
	$f_home=document.getElementById("home_f"); $f_home.removeAttribute("style");
	$f_away=document.getElementById("away_f"); $f_away.removeAttribute("style");

	if($han=='0' || $han=='0.0' || $han== '0.00')
	{
		innerHTML("h_handicap","0.00"); 	innerHTML("a_handicap","0.00");
	}
	else
	{	
		if($_s_handicap_pat.test($han))
		{
			$sign=$han.substr(0,1);
			$num=$han.substr(1);
			if($sign=='+') { $away_han='-'+$num; $f_away.style.color='red';}
			if($sign=='-') { $away_han='+'+$num; $f_home.style.color='red';}
			innerHTML("h_handicap",$han); 	innerHTML("a_handicap",$away_han)
		}
		else { innerHTML("h_handicap",""); 	innerHTML("a_handicap","");}
	}
}
//====================
function fill_gold()
{
	$gold=gValue("gold");
	if($_handicap_pat.test($gold))
	{ 
		innerHTML("over_gold",$gold); innerHTML("under_gold",$gold);
	}
}
function over_odds()
{
	$o_odds=gValue("o_odds");
	if($_odds_pat.test($o_odds))
	{ innerHTML("fover_odds",$o_odds);
	}
}

function under_odds()
{
	$o_odds=gValue("u_odds");
	if($_odds_pat.test($o_odds))
	{ innerHTML("funder_odds",$o_odds);
	}
}
//===================
function home_win()
{
	$odd=gValue("home_win_odds");
	if($_odds_pat.test($odd))
	{		 innerHTML("h_win_odds",$odd);	}
	else
	{   innerHTML("h_win_odds",""); }
}
function away_win()
{
	$odd=gValue("away_win_odds");
	if($_odds_pat.test($odd))
	{		 innerHTML("a_win_odds",$odd);	}
	else
	{   innerHTML("a_win_odds",""); }
}
function draw()
{
	$odd=gValue("draw_odds");
	if($_odds_pat.test($odd))
	{		 innerHTML("d_odds",$odd);	}
	else
	{   innerHTML("d_odds",""); }
}

//===================
function h_odds()
{
	$odd=gValue("home_odds");
	if($_odds_pat.test($odd))
	{		 innerHTML("h_odds",$odd);	}
	else
	{   innerHTML("h_odds",""); }
}
function a_odds()
{
	$odd=gValue("away_odds");
	if($_odds_pat.test($odd))
	{		 innerHTML("a_odds",$odd);	}
	else
	{   innerHTML("a_odds","");}
}
//----------------------------
var $_request_m;
var $form0=new FormData();
function make_form()
{
	if($_cur_market_type==1 || $_cur_market_type==4)
	{
		$odd1=gValue("home_odds");
		$odd2=gValue("away_odds");
		$handicap=gValue("home_handicap");
	
		if($_s_handicap_pat.test($handicap) && $_odds_pat.test($odd1) && $_odds_pat.test($odd2))
		{		
		$form0.append("v1",$handicap);
		$form0.append("v2",$odd1);
		$form0.append("v3",$odd2);
		send_market();
		}
		else{	innerHTML("odds_w",$_caution+" โปรดตรวจสอบข้อมูลอีกครั้ง");}
	}
	if($_cur_market_type==2 || $_cur_market_type==5)
	{
		$gold=gValue("gold");
		$o_odds=gValue("o_odds");
		$u_odds=gValue("u_odds");
	
		if($_handicap_pat.test($gold) && $_odds_pat.test($o_odds) && $_odds_pat.test($u_odds))
		{		
		$form0.append("v1",$gold);
		$form0.append("v2",$o_odds);
		$form0.append("v3",$u_odds);
		send_market();
		}
		else{	innerHTML("odds_w",$_caution+" โปรดตรวจสอบข้อมูลอีกครั้ง");}
	}
	if($_cur_market_type==3 || $_cur_market_type==6)
	{
		$h_odds=gValue("home_win_odds");
		$a_odds=gValue("away_win_odds");
		$d_odds=gValue("draw_odds");
	
		if($_odds_pat.test($h_odds) && $_odds_pat.test($a_odds) && $_odds_pat.test($d_odds))
		{		
		$form0.append("v1",$h_odds);
		$form0.append("v2",$a_odds);
		$form0.append("v3",$d_odds);
		send_market();
		}
		else{	innerHTML("odds_w",$_caution+" โปรดตรวจสอบข้อมูลอีกครั้ง");}
	}
}

function send_market()
{
		sDisable("market_bt"); innerHTML("odds_w",$_progress+" กำลังส่งข้อมูล");
		
		$form0.append("create_market",$_cur_market_id);
		$form0.append("market_type",$_cur_market_type);
		$form0.append("page","addmarket");

		$_request_m= new XMLHttpRequest();
		$_request_m.onreadystatechange=rsp_send_market;
		$_request_m.open("POST","manage.php",true);
		$_request_m.send($form0);
}
function rsp_send_market()
{
	if($_request_m.readyState==4)
		{
			innerHTML("odds_w",""); rDisable("market_bt");
			$receive=$_request_m.responseText;
			if($receive=='i') {	innerHTML("odds_w",$_ok+" เพิ่มราคาเรียบร้อยแล้ว");}
			if($receive=='u') {	innerHTML("odds_w",$_ok+" อับเดทราคาเรียบร้อยแล้ว");} 
			if($receive=='i' || $receive=='u'){
			if($_cur_market_type==1 || $_cur_market_type==4) { 	sValue("home_odds","");	sValue("away_odds","");	sValue("home_handicap","");}
			if($_cur_market_type==2 || $_cur_market_type==5) { 	sValue("gold","");	sValue("o_odds","");	sValue("u_odds","");}
			if($_cur_market_type==3 || $_cur_market_type==6) { 	sValue("home_win_odds","");	sValue("draw_odds","");	sValue("away_win_odds","");}
			load_market_team($_cur_market_id);
			}
		}
}
var $_cur_market_type;

function s_m_type($id)
{	$_cur_market_type=$id;
	$max=6;
	for($ti=1;$ti<=$max;$ti++)
	{
		$span=document.getElementById("mt"+$ti);
		if($ti==$id) { $span.style.opacity=1.0; }
		if($ti !=$id) { $span.removeAttribute("style");}
	}
	for($di=1;$di<=3;$di++)
	{
		$div=document.getElementById("market_var"+$di); $div.style.display='none';
	}
	if($id==1 || $id==4) { $div=document.getElementById("market_var1"); $div.style.display='inline-block';}
	if($id==2 || $id==5) { $div=document.getElementById("market_var2"); $div.style.display='inline-block';}
	if($id==3 || $id==6) { $div=document.getElementById("market_var3"); $div.style.display='inline-block';}

	 innerHTML("odds_w","");
}
function edit_odds($mid,$type,$value1,$value2,$value3)
{
	$tbody=document.getElementById("load_node");
	$tr=$tbody.getElementsByTagName("tr");
	for($ri=0;$ri<$tr.length;$ri++)
	{
		$cur_tr=$tr[$ri];
		$cur_mid=$cur_tr.getAttribute("mid");
		if($mid==$cur_mid)
		{
			$data=$cur_tr.getAttribute("data"); break;
		}
	}
	addmarket($mid);
	if($type==1 || $type==4) { 	if($value1>0) {$value1='+'+$value1;} sValue("home_odds",$value2);	sValue("away_odds",$value3);	sValue("home_handicap",$value1); fill_h();h_odds();a_odds();}
	
	if($type==2 || $type==5) { 	sValue("gold",$value1);	sValue("o_odds",$value2);	sValue("u_odds",$value3); fill_gold();over_odds();under_odds();}
	if($type==3 || $type==6)  { 	sValue("home_win_odds",$value1);	sValue("draw_odds",$value3);	sValue("away_win_odds",$value2);home_win();draw(); away_win();}
	s_m_type($type)
}

var $request_t;
var $cur_load_match_id;
function load_market_team($id)
{
		$cur_load_match_id=$id;
		$form=new FormData();
		$form.append("market_team",$id);
		$form.append("page","addmarket");

		 $request_t= new XMLHttpRequest();
		 $request_t.onreadystatechange=rsp_market_team;
		 $request_t.open("POST","manage.php",true);
		 $request_t.send($form);
}
function rsp_market_team()
{
		if($request_t.readyState==4)
		{
			$receive=$request_t.responseText; 
			document.getElementById("team_buffer").innerHTML=$receive;
			move_match_market();
		}
}

function move_match_market()
{
	$tbody_buffer=document.getElementById("team_buffer");
	$tr_buffer=$tbody_buffer.getElementsByTagName("tr");
	$buffer_rows=$tr_buffer.length;

	$tbody=document.getElementById("load_node");
	$tr=$tbody.getElementsByTagName("tr");
	$old_tr_count=0;
	for($ri=0;$ri<$tr.length;$ri++)
	{	
		$cur_tr=$tr[$ri];
		$cur_mid=$cur_tr.getAttribute("mid");
		if($cur_load_match_id==$cur_mid) {$old_tr_count++;}		
	}

	if($buffer_rows<$old_tr_count)
	{
		for($ri=0;$ri<$tr.length;$ri++)
		{	
			$cur_tr_del=$tr[$ri];
			$cur_mid=$cur_tr_del.getAttribute("mid");
			if($cur_load_match_id==$cur_mid) 
			{	$tbody.removeChild($cur_tr_del); $old_tr_count--; if($buffer_rows==$old_tr_count) {break;}
			}		
		}
	}

	if($buffer_rows>$old_tr_count)
	{
		for($ri=0;$ri<$tr.length;$ri++)
		{
			$cur_tr=$tr[$ri];
			$cur_mid=$cur_tr.getAttribute("mid");
			if($cur_load_match_id==$cur_mid) 
			{
				$clone_tr=$cur_tr.cloneNode(true); $tbody.insertBefore($clone_tr,$cur_tr); $old_tr_count++;  if($buffer_rows==$old_tr_count) {break;}
			}
		}
	}
	$buffer_index=0;
	for($ri=0;$ri<$tr.length;$ri++)
		{				
					$cur_tr=$tr[$ri];
					$cur_mid=$cur_tr.getAttribute("mid");
					if($cur_load_match_id==$cur_mid)
					{
						$cur_buffer_tr=$tr_buffer[$buffer_index];
						$td_buffer=$cur_buffer_tr.getElementsByTagName("td");
						$old_td=$cur_tr.getElementsByTagName("td");
						for($ti=0;$ti<$td_buffer.length;$ti++)
						{
							$inner_buffer=$td_buffer[$ti].innerHTML;
							$old_td[$ti].innerHTML=$inner_buffer;
						}
						$buffer_index++;
					}
		}
		$tbody_buffer.innerHTML='';
}

var $request_d,$cur_del_market_id;
function del_market($mid,$type,$value,event)
{
		event.stopPropagation();
		$cur_del_market_id=$mid;
		mask_progress_show("กำลังลบข้อมูล");
		$form=new FormData();
		$form.append("market_del_id",$mid);
		$form.append("market_del_type",$type);
		$form.append("market_del_value",$value);
		$form.append("page","addmarket");

		 $request_d= new XMLHttpRequest();
		 $request_d.onreadystatechange=rsp_del_market;
		 $request_d.open("POST","manage.php",true);
		 $request_d.send($form);

}
function rsp_del_market()
{
		if($request_d.readyState==4)
		{
			$receive=$request_d.responseText; 
			if($receive=='d')				
			{load_market_team($cur_del_market_id); }
			mask_progress_hide();
		}
}

</script>

<style>
	body { margin:50px;}
	.team_en{ width:200px;padding-left:3px;}
	.edit_lea{ font-weight:bold; padding-left:40px; background-color:rgb(230,230,230);}
	.edit_kick{ width:50px;text-align:center;}
	.edit_txt{ width:50px;}
	.home_name{ display:inline-block;width:240px;} 	.home_name2{ display:inline-block;width:200px;} .draw_name {display:inline-block;width:38px;}
	.h_handicap{ display:inline-block;width:44px; color:#46a;}
	.h_odds{ display:inline-block;width:38px; font-weight:bold; text-align:right;}
	.market_type{ display:inline-block; width:148px; padding:3px 8px; margin:0px 8px 10px 0px; background-color:#ccc; border-radius:2px; text-align:center; cursor:pointer;opacity:0.2; font-weight:bold;}
	.market_type:hover{opacity:1.0;}
	.odds_f
	{
		background: #ebf1ff; padding:2px;
		border: 1px solid #fff;
		border-right: 1px solid #a7bdef;
		border-bottom: 1px solid #a7bdef;
		display: inline-block;
		margin: 5px 3px 0 0;
	}
	.td_fulltime{min-width:222px;}
	.box_frame{ display:inline-block; height:48px;width:72px; vertical-align:top;padding:1px;cursor:pointer; text-align:right; position:relative;} 
	.box_frame:hover{ background-color:rgb(230,230,220);}
	.hdc_box { display:inline-block; width:34px; vertical-align:top;}
	.odds_box { display:inline-block; width:34px; vertical-align:top;}
	.market_del {position:absolute; left:1px; bottom:1px;}
	.market_tb tbody tr td { border:1px solid white;}
</style>
</head>

<body style="background:white;">

<h2>สร้างราคาสำหรับทายผล</h2>
<span class="desc">
* ราคาน้ำ ต้องเป็น odds HK เท่านั้น<br>
ไม่จำเป็นต้องเปิดราคาให้แทงครบทุกคู่ในตารางแข่งวันนั้นก็ได้<br>
คู่แข่งขันคู่หนึ่ง อาจเปิด ราคาให้แทงหลายราคาได้ แต่ไม่ควรเกิน 3 ราคา
</span><br><br>

<table border="0">
	<tr>
		<td colspan="0">		 
				<input type="text" id="s_day" size="2" value="">
				<input type="text" id="s_month" size="2" value="<?php echo date("n",time()) ;?>">
				<input type="text" id="s_year" size="4" value="<?php echo date("Y",time());?>">
				<span class="desc">วัน/เดือน/ค.ศ.</span>
		</td>
		<td><button id="load_bt" onclick="load_match()">โหลดตารางแข่งขัน</button><span id="load_warn"></span></td>
	</tr>
</table>

<div class="hide"><table><tbody id="team_buffer"></tbody></table></div>

<!--------------cur table match ------------------------>
	<table border="0" style="font-size:12px;" cellpadding="0" cellspacing="0" class="market_tb">
		<tbody id="load_node"></tbody>
	</table>

<!--------------end table match ----------------------->

<!----------------------------------------------------------->
<table id="mask_edit" style="position:fixed; background-color:rgba(80,80,80,0.3); z-index:2;left:0px;top:0px;display:none;">
	<tr>
		<td id="td_edit_node" align="center">
			<div style="width:700px;background-color:white;text-align:left;padding:30px;position:relative;border-radius:3px;">
			
			<img src="<?php echo $_url; ?>images/red_close.png" style="position:absolute;right:-12px;top:-12px;cursor:pointer;" onclick="hide_edit()">

				<b><span id="home_en"></span></b> พบกับ <b><span id="away_en"></span></b>
					<span class="desc"><span id="m_day"></span>/<span id="m_month"></span>/<span id="m_year"></span></span>

				<br>

				<span class="desc">* handicap ต้องมี เครื่องหมาย + - นำหน้า <br> * ออดส์ ต้องเป็น HK เท่านั้น <br>* ต้องเป็นทศนิยม 2 ตำแหน่งทุกค่า</span>
				<div style="margin:8px 0px 14px 0px;">
					<span class="market_type" id="mt1" onclick="s_m_type(1)">เต็มเวลา: HANDICAP</span> 	
					<span class="market_type" id="mt2" onclick="s_m_type(2)">เต็มเวลา: OVER/UNDER</span> 
					<span class="market_type" id="mt3" onclick="s_m_type(3)">เต็มเวลา: 1X2</span><br>
					<span class="market_type" id="mt4" onclick="s_m_type(4)">ครึ่งแรก: HANDICAP</span> 	
					<span class="market_type" id="mt5" onclick="s_m_type(5)">ครึ่งแรก: OVER/UNDER</span> 
					<span class="market_type" id="mt6" onclick="s_m_type(6)">ครึ่งแรก: 1X2</span>

				</div>
				<form name="market_form">
				<div id="market_var1">
					<input type="text" size="2" maxlength="6" id="home_handicap" oninput="fill_h()"><span class="desc"> แฮนดิแคปเจ้าบ้าน</span>
					<input type="text" size="2" maxlength="5" id="home_odds" oninput="h_odds()"><span class="desc"> ราคาน้ำเจ้าบ้าน</span>
					<input type="text" size="2" maxlength="5" id="away_odds" oninput="a_odds()"><span class="desc"> ราคาน้ำทีมเยือน</span>				
					<div style="margin:5px 0px 10px 0px;">
						<span class="odds_f" id="home_f">
							<span id="home_name" class="home_name"></span>
							<span id="h_handicap" class="h_handicap"></span>
							<span id="h_odds" class="h_odds"></span>
						</span>					
						<span class="odds_f" id="away_f">
							<span id="away_name" class="home_name"></span>
							<span id="a_handicap" class="h_handicap"></span>
							<span id="a_odds" class="h_odds"></span>
						</span>
					</div>
				</div>

				<div id="market_var2">
					<input type="text" size="2" maxlength="6" id="gold" oninput="fill_gold()"><span class="desc"> จำนวนประตู</span>
					<input type="text" size="2" maxlength="5" id="o_odds" oninput="over_odds()"><span class="desc"> ราคาน้ำเมื่อสูงกว่า</span>
					<input type="text" size="2" maxlength="5" id="u_odds" oninput="under_odds()"><span class="desc"> ราคาน้ำเมื่อต่ำกว่า</span>				
					<div style="margin:5px 0px 10px 0px;">
						<span class="odds_f" id="home_f">
							<span id="home_name" class="home_name">สูงกว่า</span>
							<span id="over_gold" class="h_handicap"></span>
							<span id="fover_odds" class="h_odds"></span>
						</span>					
						<span class="odds_f" id="away_f">
							<span id="away_name" class="home_name">ต่ำกว่า</span>
							<span id="under_gold" class="h_handicap"></span>
							<span id="funder_odds" class="h_odds"></span>
						</span>
					</div>
					</div>

					<div id="market_var3">
						<input type="text" size="2" maxlength="6" id="home_win_odds" oninput="home_win()"><span class="desc"> ราคาน้ำเจ้าบ้านชนะ</span>
						<input type="text" size="2" maxlength="5" id="away_win_odds" oninput="away_win()"><span class="desc"> ราคาน้ำทีมเยือนชนะ</span>
						<input type="text" size="2" maxlength="5" id="draw_odds" oninput="draw()"><span class="desc"> ราคาน้ำเสมอ</span>

						<div style="margin:5px 0px 10px 0px;">
						<span class="odds_f" id="home_f">
							<span id="home_name_win" class="home_name2"></span>
							<span class="h_handicap">ชนะ</span>
							<span id="h_win_odds" class="h_odds"></span>
						</span>	
						<span class="odds_f" id="home_f">							
							<span class="draw_name">เสมอ</span>
							<span id="d_odds" class="h_odds"></span>
						</span>					

						<span class="odds_f" id="away_f">
							<span id="away_name_win" class="home_name2"></span>
							<span class="h_handicap">ชนะ</span>
							<span id="a_win_odds" class="h_odds"></span>
						</span>
					</div>
					</div>
					</form>
				<button onclick="make_form()" id="market_bt">บันทึกราคา</button><span id="odds_w"></span>
			</div>
		</td>
	</tr>
</table>
<!---------------------------------------------------------->
<table class="tb_mask_progress" id="tb_mask_progress"><tr><td id="td_mask_progress" align="center"><span id="sp_mask_progress"></span></td></tr></table><script>window.addEventListener("resize", resize_mask_td);</script>		

</body>
</html>
<?php
if($_con) { $_con=null;}
?>