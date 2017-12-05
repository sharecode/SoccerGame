<?php
require_once "ext.php";
con_mysql();
session_pair();
islogin();
//================================

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content="ตารางแข่งขันฟุตบอล"/>
	<meta name="robots" content="ALL" />
	<title>ตารางแข่งวันนี้</title>

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
<?php  top_head(); 

	$get_last=$_con->query("select mday from matchday order by id desc limit 1;");
	$mk_load=null;
	if($get_last->rowCount())
	{
		$last_data=$get_last->fetchAll();
		$mk_load=$last_data[0]['mday'];
		$show_date=thaiday($mk_load);
	}
	else {$show_date='';}
?>
<center><div class="body_box">

<h2>ตารางแข่งขันวัน <?php echo $show_date; ?></h2>
| 
<?php
	$cur_day=start_today();
	for($bi=0;$bi<3;$bi++)
	{
		$cur_day-=86400;
		echo '<a href="'.$_url.'result.php?day='.$cur_day.'" style="color:white;">'.thaiday($cur_day).'</a> | ';
	}
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" style="background:white; color:rgb(80,80,80);width:100%;" class="tb_match">
<tbody>
<?php

		if($mk_load)
		{
			$get_l=$_con->prepare("select id,en_name,th_name from league where id in(select league from matchday where mday=?);");
			$get_l->execute(array($mk_load));
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
				$get_team->execute(array($mk_load,$mk_load));
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
				$get_match->execute(array($mk_load));
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
					$txt_day=date("j",$mk_load);
					$txt_month=date("n",$mk_load);
					$txt_year=date("Y",$mk_load);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);

					//$mess_edit=$txt_day.'|'.$txt_month.'|'.$txt_year.'|'.$k_day.'|'.$k_month.'|'.$k_year.'|'.$league.'|'.$league_en.'|'.$kick_edit.'|'.$nfield.'|'.$home_t.'|'.$h_en_e.'|'.$away_t.'|'.$a_en_e.'|'.$advan.'|'.$handicap;

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