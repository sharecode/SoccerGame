<?php
require_once "ext.php";
con_mysql();
session_pair();

if(islogin()){} else{ $_con=null; header('Location: '.$_url); exit();}

if(market_select()) { $show_day=thaiday(market_select()). '<br>ปิดให้ทายผล เวลา '.$_end_guess.'.00 น.';}
else{ $show_day='เปิดให้ทายผล เวลา '.$_start_guess.'.00 น.';}

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>ทายผลบอลชุด</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>

var $div_bill;
var $request1,$cur_sort=0;
function load_match()
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
}

function sort_match()
{
		mask_progress_show("กำลังโหลดตารางทายผลบอล");
		if($cur_sort==0) {$cur_sort=1;} else { if($cur_sort==1) {$cur_sort=0;}}
		$form=new FormData();
		$form.append("market_day",1);
		$form.append("sort",$cur_sort);
		$form.append("page","market");

		$_request1= new XMLHttpRequest();
		$_request1.onreadystatechange=rsp_load_match;
		$_request1.open("POST","sess.php",true);
		$_request1.send($form);
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
		$form.append("parlay_check",$mid);
		$form.append("c_type",$type);
		$form.append("c_team",$team_select);
		$form.append("c_hdc",$hdc);
		$form.append("c_odds",$odds);
		$form.append("page","mixparlay");

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
			else
			{
				innerHTML("bill_buff",$receive);
				$r_1=$receive.substr(0,1);
				$r_2=$receive.substr(1);

				$tbody1=document.getElementById("bill_buff");
				$tbody2=document.getElementById("bill_mix");
			
				if($tbody1.getElementsByTagName("TR"))
				{
					$tr1=$tbody1.getElementsByTagName("TR")[0];
					$mid=$tr1.getAttribute("id");
					$all_tr2=$tbody2.getElementsByTagName("TR");
					
					for($ri=0;$ri<$all_tr2.length;$ri++)
					{
						$cur_tr=$all_tr2[$ri];
						$cur_mid=$cur_tr.getAttribute("id");
						if($cur_mid==$mid) {$tbody2.removeChild($cur_tr);}
					} 
					
					if($all_tr2.length<5)
					{
						$tr1_c=$tr1.cloneNode(true);
						$tbody2.appendChild($tr1_c);
					}
					$tbody1.innerHTML='';
				}
			}	
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

	$tbody2=document.getElementById("bill_mix");			
	$all_tr2=$tbody2.getElementsByTagName("TR");
	 innerHTML("g_warn","");

	if($all_tr2.length==5)
	{
		for($ti=0;$ti<$all_tr2.length;$ti++)
		{
			$cur_tr=$all_tr2[$ti];
			$bill=$cur_tr.getAttribute("bill");
			if($ti==0) { $all_bill=$bill;}
			else{$all_bill+=';'+$bill;}
		}
		mask_progress_show("กำลังส่งข้อมูล");
		$form=new FormData();
		$form.append("mix_bill",$all_bill);
		$form.append("page","mixparlay");

		$_request_b= new XMLHttpRequest();
		$_request_b.onreadystatechange=rsp_bill;
		$_request_b.open("POST","sess.php",true);
		$_request_b.send($form);

	} else {innerHTML("g_warn",$_caution+" ต้องทายผล 5 คู่เท่านั้น");}
	
}
function rsp_bill()
{
		if($_request_b.readyState==4)
		{
			mask_progress_hide();
			$receive=$_request_b.responseText; 
			$head=$receive.substr(0,1);
			$rear=$receive.substr(1);

			if($head=='b')
			{	innerHTML("g_warn",$_caution+" กรุณาทายผลบอลเดี่ยวก่อน");
			}
			if($head=='h')
			{	innerHTML("g_warn",$_caution+" ข้อจำกัดการทายผลต่อวัน");
			}
			if($head=='C')
			{	innerHTML("g_warn",$_caution+" บอลบางคู่ถูกปิดให้ทายผล");
			}
			if($head=='c')
			{	innerHTML("g_warn",$_caution+" บางราคาถูกปิดให้ทายผล");
			}
			if($head=='u')
			{	innerHTML("g_warn",$_caution+" ค่าน้ำมีการเปลี่ยนแปลง");
			}
			if($head=='s')
			{	innerHTML("g_warn",$_ok+" การทายผลเสร็จสมบูรณ์");
				load_bill_count(1);
			}
			if($head=='C' || $head=='c' || $head=='u')
			{
				innerHTML("bill_mix",$rear);
				load_match();
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
/*------------------------------------------------*/
function del_b($id)
{
	$name='b'+$id;
	$tbody=document.getElementById("bill_mix");
	$tr=document.getElementById($name);
	$tbody.removeChild($tr);	
}
</script>
<style>

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

	.guess_select { display:inline-block; width:280px; color:rgb(100,100,100); min-height:100px; padding:20px 20px 10px 20px; border-radius:3px 11px 3px 3px; background-color:rgb(240,240,240);}
	.bet_type{font-size:14px;display:inline-block; width:230px;background:rgb(170,180,170); color:white; padding:2px 10px;font-weight:bold;border-radius:2px; text-align:center;}
	.bet_choose{font-size:14px; color:black;display:inline-block;padding:3px 0px; margin-left:2px;}
	.bet_odds{font-size:14px;color:#556B2F;}
	.bet_team{font-size:11px; margin-left:2px;}
	.del_bi{cursor:pointer; margin-left:5px;}

</style>
</head>
<body onkeypress="press_body(event)">
<?php  top_head(); ?>
<center><div class="body_box">

<div class="hide"><table><tbody id="team_buffer"></tbody></table></div>

		<table border="0" style="width:100%;">
		<tr>
			<td valign="top"><h2>(บอลชุด) ตารางทายผลบอล <?php echo $show_day;?></h2></td>
			<td align="right"><a href="market.php"><span class="reg_link_but" style="color:black;font-size:20px;">บอลเดี่ยว</span></a></td>
		</tr>
	</table>

	<table border="0" style="font-size:12px;width:100%;" cellpadding="0" cellspacing="0" class="market_tb" id="market_tb">
			<thead>
			<tr>
				<td rowspan="2" align="center" style="border-top: solid 1px #b1b1b1; min-width:40px;"><span class="cursor" onclick="sort_match()">เวลา&#9207;</span></td>
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


</div></center>
<div class="hide"><table><tbody id="bill_buff"></tbody></table></div>
<div class="guess_select" style="display:none;position:absolute" id="guess_select">	
<img src="http://localhost/lotto/images/red_close.png" onclick="hide_bill()" style="position:absolute;top:0px;right:0px;cursor:pointer; width:22px;">
		<table>
			<tbody id="bill_mix"></tbody>
		</table>

	<center><button class="yellow_button" style="font-size:15px;" onclick="save_bill()">ยืนยันการทายผล</button></center>
	<span id="g_warn" style="display:inline-block; height:14px; width:100%; text-align:center;color:black; position:relative; top:2px;"></span>
</div>

<script>
 window.addEventListener("scroll", set_y_bill);  
window.addEventListener("resize", cal_bill_x_pos);
$div_bill=document.getElementById("guess_select");
cal_bill_x_pos();
</script>
<?php footer(); if($_con) { $_con=null;} ?>
<script>load_match();</script>
</body>
</html>