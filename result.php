<?php
require_once "ext.php";
con_mysql();
session_pair();
islogin();
//================================

		$day=date("j");
		$month=date("n");
		$year=date("Y");
					
		$today=mktime(0,0,0,$month,$day,$year);
		$yday=$today-86400; // แมทเมื่อวาน

		$result_day=$yday; // ค่าดีฟอลด์ แมทที่ต้องการค้นหา คือเมื่อวาน


		// หรือต้องการ เลือกผลการแข่งขัน วันใดๆ ให้ส่ง ผ่าน ตัวแปรเข้ามา
		if(isset($_GET['day']) && isDecimal($_GET['day']))
		{
			$get_day=$_GET['day'];
			$gday=date("j"); $gmonth=date("n"); $gyear=date("Y");
			if(checkdate($gmonth,$gday,$gyear))
			{
				$result_day=$get_day;
			}
		}

		$has_match=$_con->query("select id from matchday where mday=$result_day limit 1;");
		if($has_match->rowCount()){}
		else
		{
			$_con=null; header('Location: match.php');
			exit();
		}


		$result_title='ผลการแข่งขันฟุตบอล '.thaiday($result_day);

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content="<?php echo $result_title; ?>"/>
	<meta name="robots" content="ALL" />
	<title><?php echo $result_title; ?></title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
	.team{ width:350px;}
	.guess_row{padding:1px 3px; cursor:pointer;}
	.guess_row:hover { background-color:rgb(220,220,220);}
	.guess_show {position:absolute; left:0px; top:0px;background-color:white; width:352px; border:1px solid rgb(200,200,200);}
	.edit_home,.edit_away{ width:200px; padding:4px 8px; border-bottom:1px solid rgb(230,230,230); border-left:1px solid rgb(230,230,230);}
	.edit_home{ text-align:right;}
	.edit_handicap{ width:50px; text-align:center;  border-bottom:1px solid rgb(230,230,230); border-left:1px solid rgb(230,230,230);}
	.edit_lea{  padding:2px 2px 2px 40px; background-color:#436590; color:white;}
	.edit_kick{ width:50px; text-align:center; border-bottom:1px solid rgb(230,230,230); border-left:1px solid rgb(230,230,230);}
	.edit_txt{ width:50px;}
	.ew{display:inline-block; width:30px;}
	.score_warn{display:inline-block;width:20px;}
	.td_score{width:40px;  border-bottom:1px solid rgb(230,230,230); border-left:1px solid rgb(230,230,230); text-align:center; font-weight:bold;}
	.td_score_ft{width:40px;  border-bottom:1px solid rgb(230,230,230); border-left:1px solid rgb(230,230,230); border-right:1px solid rgb(230,230,230);text-align:center; font-weight:bold;}
	.tb_match tbody tr:hover{ background-color:rgb(230,230,230);}

</style>
</head>
<body>
<?php  top_head(); ?>

<center><div class="body_box">

<h2><?php echo $result_title; ?></h2>
| <a href="match.php" style="color:white;">ผลการแข่งขันวันนี้</a> | 
<?php // สร้างลิ้งค์ ย้อนหลังไปอีก 3 วัน
	
	$cur_day=$result_day;
	for($bi=0;$bi<3;$bi++)
	{
		$cur_day-=86400;
		echo '<a href="'.$_url.'result.php?day='.$cur_day.'" style="color:white;">'.thaiday($cur_day).'</a> | ';
	}

?>

<table border="0" cellpadding="0" cellspacing="0" style="background:white; color:rgb(80,80,80);width:100%;" class="tb_match">
<tbody>
<?php

		if($result_day)
		{
			$get_l=$_con->prepare("select id,en_name,th_name from league where id in(select league from matchday where mday=?);");
			$get_l->execute(array($result_day));
			if($get_l->rowCount())
			{
				$tr='';
				$l_en_buffer=array();
				$l_data=$get_l->fetchAll();
				for($li=0;$li<$get_l->rowCount();$li++)
				{	$l_id=$l_data[$li]['id'];
					$l_en=$l_data[$li]['en_name'];
					$l_th= $l_data[$li]['th_name'];
					$l_en_buffer[$l_id]=$l_en;
				}

				$get_team=$_con->prepare("select id,en_name,th_name from team where id in(select home_team from matchday where mday=?) or id in(select away_team from matchday where mday=?)");
				$get_team->execute(array($result_day,$result_day));
				$get_team->rowCount();

				$t_en_buffer=array();
				$t_data=$get_team->fetchAll();
				for($ti=0;$ti<$get_team->rowCount();$ti++)
					{	$t_id=$t_data[$ti]['id'];
						$t_en=$t_data[$ti]['en_name'];
						$t_th= $t_data[$ti]['th_name'];
						$t_en_buffer[$t_id]=$t_en;
					}
				$get_match=$_con->prepare("select * from matchday where mday=? order by mkick asc;");
				$get_match->execute(array($result_day));
				$m_data=$get_match->fetchAll();
				$cur_l=0;
				for($mi=0;$mi<$get_match->rowCount();$mi++)
				{
					$mid=$m_data[$mi]['id'];
					$mkick=$m_data[$mi]['mkick'];
					$league=$m_data[$mi]['league'];
					$nfield=$m_data[$mi]['nfield'];
					$home_t=$m_data[$mi]['home_team'];
					$away_t=$m_data[$mi]['away_team'];
					$advan=$m_data[$mi]['advan'];
					$handicap=$m_data[$mi]['handicap'];	

					$fh_h_score=$m_data[$mi]['fh_h_score'];	
					$fh_a_score=$m_data[$mi]['fh_a_score'];	
					$ft_h_score=$m_data[$mi]['ft_h_score'];	
					$ft_a_score=$m_data[$mi]['ft_a_score'];	
					
					$m_finish=$m_data[$mi]['m_finish'];	
					$m_delay=$m_data[$mi]['m_delay'];	
					
					$kick_time=date("H",$mkick).':'.date("i",$mkick);
					$league_en=$l_en_buffer[$league]; 
					$home_en=$t_en_buffer[$home_t]; $h_en_e=$home_en;
					$away_en=$t_en_buffer[$away_t];  $a_en_e=$away_en;

					if($advan==1) {$home_en='<span class="advan_team">'.$home_en.'</span>';}
					if($advan==2) {$away_en='<span class="advan_team">'.$away_en.'</span>';}
					if($hadicap=0.00) {$handicap=0;}
					if($nfield==1) {$home_en.=' (n)';}

					if($cur_l != $league)
					{
						$cur_l=$league;
						$tr.='<tr><td colspan="9" class="edit_lea">'.$league_en.'</td></tr>';
					}
					//==========for edit data============
					$txt_day=date("j",$result_day);
					$txt_month=date("n",$result_day);
					$txt_year=date("Y",$result_day);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);


					if($m_finish==1)
					{
						if($m_delay==0)
						{
						$kick_time='FT';
						$ft_score=$ft_h_score.' - '.$ft_a_score;
						$fh_score=$fh_h_score.' - '.$fh_a_score;
						}
						if($m_delay==1){ $ft_score='เลื่อน'; $fh_score='';}
					}
					else {$ft_score=''; $fh_score='';}

					$tr.='<tr>
					<td class="edit_kick">'.$kick_time.'</td>					
					<td class="edit_home">'.$home_en.'</td>
					<td class="edit_handicap">'.rewrite_handicap($handicap).'</td>
					<td class="edit_away">'.$away_en.'</td>
					<td class="td_score">'.$fh_score.'</td>
					<td class="td_score_ft">'.$ft_score.'</td>
					</tr>';
				}
				echo $tr;
			}
		}
	?>
</tbody>
</table>
</div></center>
<?php footer(); if($_con) { $_con=null;} ?>

</body>
</html>