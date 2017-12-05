<?php
require_once "ext.php";
con_mysql();
session_pair();
if(islogin()){} else{ $_con=null; header('Location: '.$_url); exit();}
//================================
$bill_show=31;
?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>รายการทายผล บอลชุด</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
	.bill_tb {width:100%;}
	.guess_detail{ color:rgb(120,120,120); padding:3px 12px;}
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
		border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1
	}
	.stake_result {border-right: solid 1px #b1b1b1}
	.guess_detail
	{
		border-bottom: solid 1px #b1b1b1;border-left: solid 1px #b1b1b1
	}
	.vs_team{font-size:12px;}
	
</style>
</head>
<body>
<?php  top_head(); 
?>
<center><div class="body_box">

	<table border="0" style="width:100%;">
		<tr>
			<td valign="top"><h2>ประวัติทายผลบอลชุด</h2></td>
			<td align="right"><a href="bill.php"><span class="reg_link_but" style="color:black;font-size:15px;">สถิติทายผลบอลเดี่ยว</span></a></td>
		</tr>
	</table>
	<table class="bill_tb" border="0" cellpadding="0" cellspacing="0">
		<thead>
		<tr><td>รายการทาย</td><td>ผลทาย</td><td>คะแนน</td></tr>
		</thead>
		<tbody>
	<?php

	$get_bill=$_con->query("select * from bill where owner=$_uid and bill_type=1 order by id desc limit $bill_show;");
	if($get_bill->rowCount())
	{
		$bill_data=$get_bill->fetchAll();
		for($bi=0;$bi<$get_bill->rowCount();$bi++)
		{
			$id=$bill_data[$bi]['id'];
			$bill_type=$bill_data[$bi]['bill_type'];
			$mday=$bill_data[$bi]['mday'];
			$b_time=$bill_data[$bi]['g_time'];
			$b_stake=$bill_data[$bi]['stake'];
			$b_result=$bill_data[$bi]['g_result'];
			$b_check=$bill_data[$bi]['is_check'];
			$stake_result=$bill_data[$bi]['stake_result'];

			//===========get mix parlay========================
			$td_buff='';
			$get_all_bill=$_con->query("select * from mixparlay where bill=$id;");
			if($get_all_bill->rowCount())
			{
				$all_bill=$get_all_bill->fetchAll();
				for($ai=0;$ai<$get_all_bill->rowCount();$ai++)
				{

					$match_id=$all_bill[$ai]['match_id'];
					$g_type=$all_bill[$ai]['g_type'];
					$g_handicap=$all_bill[$ai]['g_handicap'];
					$g_team=$all_bill[$ai]['g_team'];
					$g_odds=$all_bill[$ai]['g_odds'];					
					$g_result=$all_bill[$ai]['g_result'];
					$is_check=$all_bill[$ai]['is_check'];

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
						$ft_show='<b>HT '.$fh_home_score.' : '.$fh_away_score.' FT '.$ft_home_score.' : '.$ft_away_score.'</b>';	
						}

						if($g_result==0.0) {$result_txt='<span class="d_draw">D</span>';}
						if($g_result==1.0) {$result_txt='<span class="w_win">W</span>';}
						if($g_result==0.5) {$result_txt='<span class="w_win">w</span>';}
						if($g_result==-0.5) {$result_txt='<span class="l_lost">l</span>';}
						if($g_result==-1.0) {$result_txt='<span class="l_lost">L</span>';}
					}
					else
					{ 
						$ft_show='';
						$result_txt='<span span class="r_wait">รอผล</span>';
						$show_stake='';
					}

					$td_buff.='<span class="guess_type">'.$guess_type.'</span></b><br>
							<span class="team_choose">'.$side_choose.'</span> <span class="odds_choose">@ '.$g_odds.'</span> '.$result_txt.'<br>
							<span class="vs_team">'.$home_name.$nfield_txt.' // '.$away_name.'</span> '.$ft_show.'<br>
							<span class="desc_small">'.$league_name.' '.thaidate($mkick).'</span><br><br>';							


				}// end for all bill
			}	//============end mixparlay===========================================

					if($b_check==1)	
					{ 

						if($b_result==0.0) {$b_result_txt='<span class="d_draw">D</span>';}
						if($b_result==1.0) {$b_result_txt='<span class="w_win">W</span>';}
						if($b_result==0.5) {$b_result_txt='<span class="w_win">w</span>';}
						if($b_result==-0.5) {$b_result_txt='<span class="l_lost">l</span>';}
						if($b_result==-1.0) {$b_result_txt='<span class="l_lost">L</span>';}

						if($stake_result>=0) {$show_stake='+'.round($stake_result,2);}
						if($stake_result<0) {$show_stake=round($stake_result,2);}
					}
					else
					{ 
						$b_result_txt='<span span class="r_wait">รอผล</span>';
						$show_stake='';
					}


							echo '<tr>
							<td class="guess_detail">'.$td_buff.'
							</td>
							<td class="guess_result" valign="top"><br>'.$b_result_txt.'</td>
							<td class="stake_result" title="'.$stake_result.'" valign="top"><br>'.$show_stake.'</td>
							</tr>';

			$td_buff='';
		} // end for bill
	}
?>
		</tbody>
	</table>
</tbody></table>

</div></center>
<?php footer(); if($_con) { $_con=null;} ?>
</body>
</html>