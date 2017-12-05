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
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="description" content="ทายผลบอล ออนไลน์ รับของรางวัล">
	<meta name="keywords" content="ทายผลบอล">
	<meta name="robots" content="ALL">
	<title>ทายผลบอล2017</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css">
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
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

	
</style>
</head>
<body>
<?php  top_head(); 
?>
<center><div class="body_box">

<!------------------------------------------ทีเด็ดแอดมินเว็บ----------------------------------------------------------------->
<span class="h2_frame"><span class="h2">[VIP] ทีเด็ดจากแอดมิน</span></span>
	<table class="bill_tb" border="0" cellpadding="0" cellspacing="0">
		<thead>
		<tr><td>เซียน</td><td>สถิติการทาย</td><td>ทีเด็ดวันนี้</td><td style="border-right: solid 1px #b1b1b1;">ผลทาย</td></tr>
		</thead>
		<tbody>
	<?php

$get_tipster=$_con->query("select id,aliasname,round(score,2) as score,score as score2 from member_ where id in(select id from zean);");
$ti_rows=$get_tipster->rowCount();
if($ti_rows)
{

$t_data=$get_tipster->fetchAll();
for($ti=0;$ti<$ti_rows;$ti++)
{
	$tid=$t_data[$ti]['id'];
	$t_alias=$t_data[$ti]['aliasname'];
	$link_alias='<a href="'.$_url.'zeanlog.php?id='.$tid.'" class="tipster_link">'.$t_alias.'</a>';
	$t_score=$t_data[$ti]['score'];
	$t_score2=$t_data[$ti]['score2'];

	$guess_his='';
	$get_his=$_con->query("select g_result from bill_tded where owner=$tid and is_check=1 order by id desc limit 12;"); // ดึงสถิตการทายผลของ เซียนนั้น ย้อนหลัง 10วัน
	if($get_his->rowCount())
	{
		$h_data=$get_his->fetchAll();
		for($hi=$get_his->rowCount()-1;$hi>=0;$hi--)
		{
			$w_result=$h_data[$hi]['g_result'];
			if($w_result==0.0) {$win_txt='<span class="d_draw">D</span>';}
			if($w_result==1.0) {$win_txt='<span class="w_win">W</span>';}
			if($w_result==0.5) {$win_txt='<span class="w_win">w</span>';}
			if($w_result==-0.5) {$win_txt='<span class="l_lost">l</span>';}
			if($w_result==-1.0) {$win_txt='<span class="l_lost">L</span>';}
			$guess_his.=$win_txt;
		}
	}
	//===================
		$td1='<td class="t_avt"><div class="t_img_f"><img src="'.$_url.'image.php?p='.$tid.'"></div></td>';
		$td2='<td valign="top" class="guess_his">'.$link_alias.'<br><span class="his_guess">'.$guess_his.'</span></td>';
	//==================
	
	$bill_select=market_select();
	if($bill_select) {$get_bill=$_con->query("select * from bill_tded where owner=$tid and mday=$bill_select order by id desc;");}
	else{ $get_bill=$_con->query("select * from bill_tded where owner=$tid and is_check=0 order by id desc;");}

	
	if($get_bill->rowCount())
	{
		$bill_data=$get_bill->fetchAll();
		for($bi=0;$bi<$get_bill->rowCount();$bi++)
		{
			$id=$bill_data[$bi]['id'];
			$bill_type=$bill_data[$bi]['bill_type'];
			$mday=$bill_data[$bi]['mday'];
			$match_id=$bill_data[$bi]['match_id'];
			$g_type=$bill_data[$bi]['g_type'];
			$g_handicap=$bill_data[$bi]['g_handicap'];
			$g_team=$bill_data[$bi]['g_team'];
			$g_odds=$bill_data[$bi]['g_odds'];
			$g_stake=$bill_data[$bi]['stake'];
			$g_result=$bill_data[$bi]['g_result'];
			$is_check=$bill_data[$bi]['is_check'];
			$g_time=$bill_data[$bi]['g_time'];
			$stake_result=$bill_data[$bi]['stake_result'];

			$get_matchday=$_con->query("select * from matchday where id = $match_id;");
			$match_data=$get_matchday->fetchAll();

				$league_id=$match_data[0]['league'];
				$mkick=$match_data[0]['mkick'];
				$nfield=$match_data[0]['nfield'];
				$home_id=$match_data[0]['home_team'];
				$away_id=$match_data[0]['away_team'];

				$fh_home_score=$match_data[0]['fh_h_score'];
				$fh_away_score=$match_data[0]['fh_a_score'];
				$ft_home_score=$match_data[0]['ft_h_score'];
				$ft_away_score=$match_data[0]['ft_a_score'];

				$is_end=$match_data[0]['m_finish'];
				$is_delay=$match_data[0]['m_delay'];

				$league_name=get_league($league_id);
				$home_name=get_team($home_id);
				$away_name=get_team($away_id);

					if($nfield==1){ $nfield_txt='(n)';} else { $nfield_txt='';}

					$guess_type=bet_type_txt($g_type);

					if($g_type==1 || $g_type==4)
					{
						if($g_team==1){$choose_team_name=$home_name;}
						if($g_team==2){$choose_team_name=$away_name;}
						if($g_handicap=='0.00'){$g_handicap='+0';}
						if($g_handicap>0){ $g_handicap='+'.$g_handicap;}
						$side_choose=$choose_team_name.' '.$g_handicap;
					}
					if($g_type==2 || $g_type==5)
					{
						if($g_team==1){$choose_team_name='สูงกว่า';}
						if($g_team==2){$choose_team_name='ต่ำกว่า';}
						$side_choose=$choose_team_name.' '.$g_handicap;
					}
					if($g_type==3 || $g_type==6)
					{
						if($g_team==1){$choose_team_name=$home_name.' ชนะ';}
						if($g_team==2){$choose_team_name=$away_name.' ชนะ';}
						if($g_team==0){$choose_team_name='เสมอ';}
						$side_choose=$choose_team_name;
					}

					if($is_check==1)	
					{ 
						if($is_delay==1) { $ft_show='<span>เลื่อนแข่ง</span>';}
						else 
						{
						$ft_show='HT '.$fh_home_score.' : '.$fh_away_score.'<br> FT '.$ft_home_score.' : '.$ft_away_score;	
						}

						if($g_result==0.0) {$result_txt='<span class="d_draw">D</span>';}
						if($g_result==1.0) {$result_txt='<span class="w_win">W</span>';}
						if($g_result==0.5) {$result_txt='<span class="w_win">w</span>';}
						if($g_result==-0.5) {$result_txt='<span class="l_lost">l</span>';}
						if($g_result==-1.0) {$result_txt='<span class="l_lost">L</span>';}
						if($stake_result>=0) {$show_stake='+'.round($stake_result,2);}
						if($stake_result<0) {$show_stake=round($stake_result,2);}
					}
					else
					{ 
						$ft_show='';
						$result_txt='รอผล';
						$show_stake='';
					}

				
							$td3='<td class="guess_detail">
							<span class="guess_type">'.$guess_type.'</span></b><br>
							<span class="team_choose">'.$side_choose.'</span><br>
							<span class="vs_team">'.$home_name.$nfield_txt.' // '.$away_name.'</span><br>
							<span class="desc_small">'.$league_name.' '.thaidate($mkick).'</span>							
							</td>';

							$td4='<td class="guess_result">'.$result_txt.'<br>'.$ft_show.'</td>';						
							echo '<tr>'.$td1.$td2.$td3.$td4.'</tr>';

		} // end for bill
	}
	else 
	{
		$td3='<td class="guess_detail"></td>'; $td4='<td td class="guess_result"></td>';
		echo '<tr>'.$td1.$td2.$td3.$td4.'</tr>';
	}

}// end for ti
} // end have tripster

?>
</tbody></table><br><br>
<!----------------------------------------------------------------------------------------------------------->


<!----------------------------------------------------------------------------------------------------------->
<span class="h2">ทีเด็ดวันนี้จากเซียน</span>
	<table class="bill_tb" border="0" cellpadding="0" cellspacing="0">
		<thead>
		<tr><td>เซียน</td><td>สถิติการทาย</td><td>ทีเด็ดวันนี้</td><td style="border-right: solid 1px #b1b1b1;">ผลทาย</td></tr>
		</thead>
		<tbody>
	<?php

$get_tipster=$_con->query("select id,aliasname,round(score,2) as score,score as score2 from member_ order by score desc limit $_tipster_show;");
$ti_rows=$get_tipster->rowCount();
if($ti_rows)
{

$t_data=$get_tipster->fetchAll();
for($ti=0;$ti<$ti_rows;$ti++)
{
	$tid=$t_data[$ti]['id'];
	$t_alias=$t_data[$ti]['aliasname'];
	$link_alias='<a href="'.$_url.'guesslog.php?id='.$tid.'" class="tipster_link">'.$t_alias.'</a>';
	$t_score=$t_data[$ti]['score'];
	$t_score2=$t_data[$ti]['score2'];

	$guess_his='';
	$get_his=$_con->query("select g_result from bill where owner=$tid and bill_type=0 and is_check=1 order by id desc limit 12;"); // ดึงสถิตการทายผลของ เซียนนั้น ย้อนหลัง 10วัน
	if($get_his->rowCount())
	{
		$h_data=$get_his->fetchAll();
		for($hi=$get_his->rowCount()-1;$hi>=0;$hi--)
		{
			$w_result=$h_data[$hi]['g_result'];
			if($w_result==0.0) {$win_txt='<span class="d_draw">D</span>';}
			if($w_result==1.0) {$win_txt='<span class="w_win">W</span>';}
			if($w_result==0.5) {$win_txt='<span class="w_win">w</span>';}
			if($w_result==-0.5) {$win_txt='<span class="l_lost">l</span>';}
			if($w_result==-1.0) {$win_txt='<span class="l_lost">L</span>';}
			$guess_his.=$win_txt;
		}
	}
	//===================
		$td1='<td class="t_avt"><div class="t_img_f"><img src="'.$_url.'image.php?p='.$tid.'"></div></td>';
		$td2='<td valign="top" class="guess_his">'.$link_alias.'<br><span class="his_guess">'.$guess_his.'</span><br><span title="'.$t_score2.'">คะแนน '.$t_score.'</span></td>';
	//==================
	
	$bill_select=market_select();
	if($bill_select) {$get_bill=$_con->query("select * from bill where owner=$tid and bill_type=0 and mday=$bill_select order by id desc limit 1;");}
	else{ $get_bill=$_con->query("select * from bill where owner=$tid and bill_type=0 and is_check=0 order by id desc limit 1;");}

/*
	$bill_select=market_select();
	if($bill_select) 
	{	
		$bill_select-=86400;
		$get_bill=$_con->query("select * from bill where owner=$tid and bill_type=0 and mday=$bill_select order by id desc limit 1;");
	}
	else
	{ 
		$day=date("j");
		$month=date("n");
		$year=date("Y");
					
		$today=mktime(0,0,0,$month,$day,$year);
		$yday=$today-(86400*2); 

		$get_bill=$_con->query("select * from bill where owner=$tid and bill_type=0 and mday=$yday order by id desc limit 1;");
	}

*/
	
	if($get_bill->rowCount())
	{
		$bill_data=$get_bill->fetchAll();
		for($bi=0;$bi<$get_bill->rowCount();$bi++)
		{
			$id=$bill_data[$bi]['id'];
			$bill_type=$bill_data[$bi]['bill_type'];
			$mday=$bill_data[$bi]['mday'];
			$match_id=$bill_data[$bi]['match_id'];
			$g_type=$bill_data[$bi]['g_type'];
			$g_handicap=$bill_data[$bi]['g_handicap'];
			$g_team=$bill_data[$bi]['g_team'];
			$g_odds=$bill_data[$bi]['g_odds'];
			$g_stake=$bill_data[$bi]['stake'];
			$g_result=$bill_data[$bi]['g_result'];
			$is_check=$bill_data[$bi]['is_check'];
			$g_time=$bill_data[$bi]['g_time'];
			$stake_result=$bill_data[$bi]['stake_result'];

			$get_matchday=$_con->query("select * from matchday where id = $match_id;");
			$match_data=$get_matchday->fetchAll();

				$league_id=$match_data[0]['league'];
				$mkick=$match_data[0]['mkick'];
				$nfield=$match_data[0]['nfield'];
				$home_id=$match_data[0]['home_team'];
				$away_id=$match_data[0]['away_team'];

				$fh_home_score=$match_data[0]['fh_h_score'];
				$fh_away_score=$match_data[0]['fh_a_score'];
				$ft_home_score=$match_data[0]['ft_h_score'];
				$ft_away_score=$match_data[0]['ft_a_score'];

				$is_end=$match_data[0]['m_finish'];
				$is_delay=$match_data[0]['m_delay'];

				$league_name=get_league($league_id);
				$home_name=get_team($home_id);
				$away_name=get_team($away_id);

					if($nfield==1){ $nfield_txt='(n)';} else { $nfield_txt='';}

					$guess_type=bet_type_txt($g_type);

					if($g_type==1 || $g_type==4)
					{
						if($g_team==1){$choose_team_name=$home_name;}
						if($g_team==2){$choose_team_name=$away_name;}
						if($g_handicap=='0.00'){$g_handicap='+0';}
						if($g_handicap>0){ $g_handicap='+'.$g_handicap;}
						$side_choose=$choose_team_name.' '.$g_handicap;
					}
					if($g_type==2 || $g_type==5)
					{
						if($g_team==1){$choose_team_name='สูงกว่า';}
						if($g_team==2){$choose_team_name='ต่ำกว่า';}
						$side_choose=$choose_team_name.' '.$g_handicap;
					}
					if($g_type==3 || $g_type==6)
					{
						if($g_team==1){$choose_team_name=$home_name.' ชนะ';}
						if($g_team==2){$choose_team_name=$away_name.' ชนะ';}
						if($g_team==0){$choose_team_name='เสมอ';}
						$side_choose=$choose_team_name;
					}

					if($is_check==1)	
					{ 
						if($is_delay==1) { $ft_show='<span>เลื่อนแข่ง</span>';}
						else 
						{
						$ft_show='HT '.$fh_home_score.' : '.$fh_away_score.'<br> FT '.$ft_home_score.' : '.$ft_away_score;	
						}

						if($g_result==0.0) {$result_txt='<span class="d_draw">D</span>';}
						if($g_result==1.0) {$result_txt='<span class="w_win">W</span>';}
						if($g_result==0.5) {$result_txt='<span class="w_win">w</span>';}
						if($g_result==-0.5) {$result_txt='<span class="l_lost">l</span>';}
						if($g_result==-1.0) {$result_txt='<span class="l_lost">L</span>';}
						if($stake_result>=0) {$show_stake='+'.round($stake_result,2);}
						if($stake_result<0) {$show_stake=round($stake_result,2);}
					}
					else
					{ 
						$ft_show='';
						$result_txt='รอผล';
						$show_stake='';
					}

				
							$td3='<td class="guess_detail">
							<span class="guess_type">'.$guess_type.'</span></b><br>
							<span class="team_choose">'.$side_choose.'</span><br>
							<span class="vs_team">'.$home_name.$nfield_txt.' // '.$away_name.'</span><br>
							<span class="desc_small">'.$league_name.' '.thaidate($mkick).'</span>							
							</td>';

							$td4='<td class="guess_result">'.$result_txt.'<br>'.$ft_show.'</td>';						
							

		} // end for bill
	}else {$td3='<td class="guess_detail"></td>'; $td4='<td td class="guess_result"></td>';}

	echo '<tr>'.$td1.$td2.$td3.$td4.'</tr>';
}// end for ti
} // end have tripster

?>
</tbody></table>
<!----------------------------------------------------------------------------------------------------------->

</div></center>
<?php footer(); if($_con) { $_con=null;} ?>
</body>
</html>