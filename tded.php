<?php
require_once "ext.php";
con_mysql();
session_pair();

if(islogin()&& isAdmin(15)){} else{ $_con=null; header('Location: '.$_url); exit();}

if(market_select()) { $show_day=thaiday(market_select());}
else{ $show_day='เปิดให้ทายผล เวลา '.$_start_guess.'.00 น.';}

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>ทีเด็ดวันนี้</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>

var $div_bill;
var $request1,$cur_sort=0;
function load_match($sort)
{
		mask_progress_show("กำลังโหลดตารางทายผลบอล");

		$form=new FormData();
		$form.append("market_day",1);
		$form.append("sort",$cur_sort);
		$form.append("page","market");

		$_request1= new XMLHttpRequest();
		$_request1.onreadystatechange=rsp_load_match;
		$_request1.open("POST","sess.php",true);
		$_request1.send($form);
		if($cur_sort==0) {$cur_sort=1;} else { if($cur_sort==1) {$cur_sort=0;}}
}
function rsp_load_match()
{
		if($_request1.readyState==4)
		{
			mask_progress_hide();
			$receive=$_request1.responseText; 
			innerHTML("load_node",$receive);
		}
}

var $request_s;
var $g_mid,$g_type,$g_team,$g_hdc,$g_odds;
function guess($mid,$type,$team_select,$hdc,$odds)
{
		$g_mid=$mid;
		$g_type=$type;
		$g_team=$team_select;
		$g_hdc=$hdc;
		$g_odds=$odds;
	if($g_mid>0)
	{
		set_y_bill();
		mask_progress_show("กำลังโหลดข้อมูล"); innerHTML("g_warn","");
		$form=new FormData();
		$form.append("market_check",$mid);
		$form.append("c_type",$type);
		$form.append("c_team",$team_select);
		$form.append("c_hdc",$hdc);
		$form.append("c_odds",$odds);
		$form.append("page","market");

		$request_s= new XMLHttpRequest();
		$request_s.onreadystatechange=rsp_load_odds;
		$request_s.open("POST","sess.php",true);
		$request_s.send($form);
	}
}
function rsp_load_odds()
{
		if($request_s.readyState==4)
		{
			mask_progress_hide();
			$div_bill.style.display='inline-block';
			$receive=$request_s.responseText; 
			if($receive=='m')
			{	innerHTML("g_warn",$_caution+" ราคานี้ถูกปิดไปแล้ว");
				load_market_team($g_mid);
				$g_mid=0;
			}

			$tbody=document.getElementById("load_node");
			$tr=$tbody.getElementsByTagName("tr");
			for($ri=0;$ri<$tr.length;$ri++)
			{
				$cur_tr=$tr[$ri];
				$cur_mid=$cur_tr.getAttribute("mid");
				if($cur_mid==$g_mid)		{	$data=$cur_tr.getAttribute("data"); break;		}
			}
			$slice=$data.split('|'); 
			innerHTML("bet_type",bet_type_txt($g_type));
			$home_en=$slice[11]; $away_en=$slice[13];
			if($g_type==1 || $g_type==4)
			{
				if($g_hdc=='0.00') {$home_hdc='+0'; $away_hdc='+0';}
				else{
					$sign=$g_hdc.substr(0,1);
					$num=$g_hdc.substr(1);
					if($sign=='-'){$home_hdc=$g_hdc; $away_hdc='+'+$num;}
					else{
							$home_hdc='+'+$g_hdc; $away_hdc='-'+$g_hdc;
						}
				}
				if($g_team==1){innerHTML("bet_choose",$home_en+' '+$home_hdc);}
				if($g_team==2){innerHTML("bet_choose",$away_en+' '+$away_hdc);}

			}
			if($g_type==2||$g_type==5)
			{
				if($g_team==1) { $show_txt='สูงกว่า '+$g_hdc;}
				if($g_team==2) { $show_txt='ต่ำกว่า '+$g_hdc;}
				innerHTML("bet_choose",$show_txt);
			}
			if($g_type==3||$g_type==6)
			{
				if($g_team==1) {$show_bet=$home_en+' ชนะ';}
				if($g_team==2) {$show_bet=$away_en+' ชนะ';}
				if($g_team==0) {$show_bet=' เสมอ';}
				innerHTML("bet_choose",$show_bet);
			}

			if($_odds_pat.test($receive)) /*-------------อับบเดท ออซ ใหม่ถ้าไม่ตรงกับฝั่งเซิฟเวอร์---------------------*/
			{
				innerHTML("bet_odds",' @ '+$receive+' <strike>'+$g_odds+'</strike>');
				$g_odds=$receive;
				innerHTML("g_warn",$_caution+" ค่าน้ำมีการเปลี่ยนแปลง");
				load_market_team($g_mid); /*----------------อับเดทตารางราคา ของทีมนั้นๆใหม่ด้วย----------------------*/
			}
			else
			{		innerHTML("bet_odds",' @ '+$g_odds);	}

			innerHTML("bet_team",$home_en+' พบกับ '+$away_en);

		}
}

var $_t_start_x,$_t_start_y,$_t_width,$bill_start_x;
function cal_bill_x_pos()
{
	$table=document.getElementById("market_tb");
	$_t_start_x=$table.offsetLeft;
	$_t_start_y=$table.offsetTop;
	$_t_width=$table.offsetWidth;
	$bill_start_x=$_t_width+$_t_start_x+1;
	$div_bill.style.left=$bill_start_x+'px';
	if($_position_type==0){$div_bill.style.top=$_t_start_y+'px';}
	if($_position_type==1){$div_bill.style.top='1px'}

}

var $_position_type=0;
var $_pos_top=1;
var $_time=null;
function set_y_bill()
{
	$cur_scroll=document.documentElement.scrollTop;
	$space_height=$_t_start_y-$cur_scroll;
	if($space_height<0 && $_position_type==0)
	{
		$_position_type=1;
		$_time=setTimeout("bill_fixed()",40);			
	}
	else
	{
		if($space_height>0 && $_position_type==1)
		{	$_position_type=0;	
			$_time=setTimeout("bill_absolute()",40);				
		}
	}
}
function bill_absolute()
{
		$div_bill.style.position='absolute';
		$div_bill.style.top=$_t_start_y+'px';
		
}
function bill_fixed()
{
		$div_bill.style.position='fixed';
		$div_bill.style.top='1px';	
}

function press_body($event)
{
	if($event.keyCode==27) { $div_bill.style.display='none';}
}
function hide_bill()
{ $div_bill.style.display='none';}

/*------------------------------------------------------------*/
var $request_t;
var $cur_load_match_id;
function load_market_team($id)
{
		$cur_load_match_id=$id;
		$form=new FormData();
		$form.append("market_team",$id);
		$form.append("page","market");

		 $request_t= new XMLHttpRequest();
		 $request_t.onreadystatechange=rsp_market_team;
		 $request_t.open("POST","sess.php",true);
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

var $_request_b;
function save_bill()
{
	if($g_mid>0)
	{
		mask_progress_show("กำลังส่งข้อมูล"); innerHTML("g_warn","");
		$form=new FormData();
		$form.append("market_bill",$g_mid);
		$form.append("b_type",$g_type);
		$form.append("b_team",$g_team);
		$form.append("b_hdc",$g_hdc);
		$form.append("b_odds",$g_odds);
		$form.append("page","tded");

		$_request_b= new XMLHttpRequest();
		$_request_b.onreadystatechange=rsp_bill;
		$_request_b.open("POST","manage.php",true);
		$_request_b.send($form);
	}
}
function rsp_bill()
{
		if($_request_b.readyState==4)
		{
			mask_progress_hide();
			$receive=$_request_b.responseText; 
			if($receive=='m')
			{	innerHTML("g_warn",$_caution+" ราคานี้ถูกปิดไปแล้ว");
				load_market_team($g_mid);
				$g_mid=0;
			}
			if($receive=='i')
			{	innerHTML("g_warn",$_ok+" บันทึกการทายผลเรียบร้อย");
				load_tded();
				$g_mid=0;
			}
			if($receive=='l')
			{	innerHTML("g_warn",$_caution+" ข้อจำกัดการทายผลต่อวัน");
			}
			if($receive=='t')
			{		innerHTML("g_warn",$_caution+" เลยเวลาที่สามารถทายผลได้");
					$tbody=document.getElementById("load_node");
					$tr=$tbody.getElementsByTagName("tr");
					for($ri=$tr.length-1;$ri>=0;$ri--)
					{	
						$cur_tr=$tr[$ri];
						$cur_mid=$cur_tr.getAttribute("mid");
						if($cur_mid==$g_mid) {$tbody.removeChild($cur_tr);}		
					}
					$g_mid=0;
			}
			if($_odds_pat.test($receive)) 
			{
				innerHTML("bet_odds",' @ '+$receive+' <strike>'+$g_odds+'</strike>');
				$g_odds=$receive;
				innerHTML("g_warn",$_caution+" ค่าน้ำมีการเปลี่ยนแปลง");
				load_market_team($g_mid); 
			}
		}
}
var $request_bc;
function load_bill_count($id)
{
		$form=new FormData();
		$form.append("bill_count",1);
		$form.append("page","market");

		 $request_bc= new XMLHttpRequest();
		 $request_bc.onreadystatechange=rsp_bill_count;
		 $request_bc.open("POST","sess.php",true);
		 $request_bc.send($form);
}
function rsp_bill_count()
{
		if($request_bc.readyState==4)
		{
			$receive=$request_bc.responseText; 
			if(num_test($receive))
			{
				$bill_count='<span class="bill_count">'+$receive+'</span>';
				innerHTML("bill_node",$bill_count);
			}
		}
}

var $request_t;
function load_tded()
{
		$form=new FormData();
		$form.append("l_tded",1);
		$form.append("page","tded");
		mask_progress_show("กำลังโหลด");
		 $request_t= new XMLHttpRequest();
		 $request_t.onreadystatechange=rsp_tded;
		 $request_t.open("POST","manage.php",true);
		 $request_t.send($form);
}
function rsp_tded()
{
		if($request_t.readyState==4)
		{
			mask_progress_hide();
			$receive=$request_t.responseText; 
			innerHTML("tded_node",$receive);
		}
}
/*-----------------------------------------------------*/
var $request_d;
var $cur_del;
function del_tded($id)
{
		$cur_del=$id;
		mask_progress_show("กำลังลบข้อมูล");
		$form=new FormData();
		$form.append("d_tded",$id);
		$form.append("page","tded");
		
		 $request_d= new XMLHttpRequest();
		 $request_d.onreadystatechange=rsp_d_tded;
		 $request_d.open("POST","manage.php",true);
		 $request_d.send($form);
}
function rsp_d_tded()
{
		if($request_d.readyState==4)
		{
			mask_progress_hide();
			$receive=$request_d.responseText; 
			if($receive=='d')
			{
				$name='tded'+$cur_del;
				$tbody=document.getElementById("tded_node");
				$tr=document.getElementById($name);
				$tbody.removeChild($tr);
			}
		}
}

</script>
<style>
	body{ background:white;}
	.league_group{ background-color: #436590;color: #E1E9FF; font-weight:bold; padding:2px 0px 2px 50px; border-left: solid 1px #b1b1b1;border-right: solid 1px #b1b1b1;}
	.kick_time{ width:50px; color:black; text-align:center; border-left: solid 1px #b1b1b1;}
	.team_en{ width:200px; color:black;padding:2px 4px; border-left: solid 1px #b1b1b1;border-right: solid 1px #b1b1b1;}
	.box_frame{ display:inline-block; height:48px; width:82px; vertical-align:top;padding:1px; text-align:left; position:relative;} 
	.tell_market{display:inline-block; width:82px; text-align:center;}
	.hdc_box { display:inline-block; width:48px; vertical-align:top; color:#505050;}
	.odds_box { display:inline-block; width:34px; vertical-align:top;}
	.odds_s {color: #031E55; cursor:pointer;}
	.odds_s:hover {text-decoration:underline;}
	.odds_x {color: #03a; cursor:pointer;}
	.odds_x:hover {text-decoration:underline;}
	.o1x2_box{ display:inline-block; height:48px;width:34px; vertical-align:top;padding:1px; text-align:right; position:relative;} 
	.m_td1,.m_td2,.m_td3,.m_td4,.m_td5,.m_td6{ text-align:center;}
	.m_td3,.m_td6{border-right: solid 1px #b1b1b1;}

	.market_tb thead tr td{background-color:#6986AC; border-bottom: solid 1px #B1B1B1; border-left: solid 1px #B1B1B1; padding:2px 0px;}
	.market_tb tbody tr td{border-bottom: solid 1px #b1b1b1;}

	.guess_select { display:inline-block; width:240px; color:rgb(100,100,100); min-height:100px; padding:20px 20px 10px 20px; border-radius:3px 11px 3px 3px; background-color:rgb(240,240,240); text-align:center;}

	/*--------------------------*/
		.bill_tb {width:100%;}
	.guess_detail{ color:rgb(120,120,120); padding:0px 8px;}
	.bill_tb thead tr td 
	{ background:#436590; color:white; padding:3px 0px; font-size:15px; text-align:center;
	border-top: solid 1px #b1b1b1;border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1;
	}
	.bill_tb tbody tr td{background:white; color:rgb(90,90,90);}
	.team_choose{font-size:15px; color:black;display:inline-block;padding:0px 0px;}
	.odds_choose{font-size:15px;color:#556B2F;}
	.guess_type{font-size:14px;}
	.desc_small{ font-size:11px; color:rgb(120,120,120);}
	.guess_result,.stake_result 
	{
		min-width:30px; text-align:center;
		border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1;
	}
	.guess_detail	{border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1;	}
	.guess_result{border-right: solid 1px #b1b1b1; width:80px;}
	.vs_team{font-size:12px; color:rgb(80,80,80);}
	.t_img_f{display:inline-block; width:74px; height:74px; overflow:hidden; border-radius:3px; border:1px solid white;}
	.t_img_f img{ width:100%;}
	.t_avt{ width:78px; text-align:center; padding:3px 0px; border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1;}
	.guess_his{width:258px; border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1; padding:0px 4px;}
	.tipster_link{font-size:15px;padding:4px 2px; display:inline-block;}
	.del_t{cursor:pointer;}


</style>
</head>
<body onkeypress="press_body(event)">
<center><div class="body_box">

<div class="hide"><table><tbody id="team_buffer"></tbody></table></div>

	<h2>เลือกทีมที่เด็ดที่สุดของวันนี้ 1 ทีม<br><?php echo $show_day;?></h2>
	<table border="0" style="font-size:12px; width:100%;" cellpadding="0" cellspacing="0" class="market_tb" id="market_tb">
			<thead>
			<tr>
				<td rowspan="2" align="center" style="border-top: solid 1px #b1b1b1;"><span class="link" onclick="load_match(1)">เวลาเตะ</span></td>
				<td rowspan="2" align="center" style="border-top: solid 1px #b1b1b1;">ทีมที่พบกัน</td>
				<td colspan="3" align="center" style="border-top: solid 1px #b1b1b1;">เต็มเวลา</td>
				<td colspan="3" align="center" style="border-top: solid 1px #b1b1b1;border-right: solid 1px #b1b1b1;">ครึ่งแรก</td>		
			</tr>

		<tr>
				<td align="center">Handicap</td>
				<td align="center">Over/Under</td>
				<td align="center">1x2</td>
				<td align="center">Handicap</td>
				<td align="center">Over/Under</td>
				<td align="center" style="border-right: solid 1px #b1b1b1;">1x2</td>
			</tr> 
			</thead>

		<tbody id="load_node" style="border:none;"></tbody>
	</table>

<h2>ที่เด็ดของวันนี้</h2>
	<table class="bill_tb">
	<tbody id="tded_node" style="border:none;"></tbody>
	</table>


</div></center>
<div class="guess_select" style="display:none;position:absolute" id="guess_select">
<img src="<?php echo $_url; ?>images/red_close.png" onclick="hide_bill()" style="position:absolute;top:0px;right:0px;cursor:pointer; width:22px;">
	<div id="cur_server_odds">
				<span id="bet_type" style="font-size:14px;display:inline-block; width:230px;background:gray; color:white; padding:2px 5px;font-weight:bold;border-radius:2px;"></span><br>
				<span id="bet_choose" style="font-size:14px; color:black;display:inline-block;padding:3px 0px;"></span>
				<span id="bet_odds" style="font-size:14px;color:#556B2F;"></span><br>
				<span id="bet_team" style="font-size:11px;"></span><br>
	</div>
	<center><button class="yellow_button" style="font-size:15px;" onclick="save_bill()">ยืนยันการให้ทีเด็ด</button></center>
	<span id="g_warn" style="display:inline-block; height:14px; width:100%; text-align:center;color:black; position:relative; top:2px;"></span>
</div>

<script>
 window.addEventListener("scroll", set_y_bill);  
window.addEventListener("resize", cal_bill_x_pos);
$div_bill=document.getElementById("guess_select");
cal_bill_x_pos();
</script>
<table class="tb_mask_progress" id="tb_mask_progress">
		<tr>
			<td id="td_mask_progress" align="center"><span id="sp_mask_progress" style="color:black;"></span></td>
		</tr>
		</table>
		<script>window.addEventListener("resize", resize_mask_td);</script>
		<script>load_match(0); load_tded();</script>
</body>
</html>