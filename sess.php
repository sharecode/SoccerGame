<?php
	require_once "ext.php";
	con_mysql();
	session_pair();
	if(islogin()){} else {$_con=null; exit();}

if(isset($_POST['page'])){	$page=$_POST['page'];}
else{  $_con=null; exit();}

if($page=='profile')
{
	$get_user=$_con->query("select password,aliasname,phone from member_ where id=$_uid;");
	$u_data=$get_user->fetchAll();
	$old_pass=$u_data[0]['password'];
	$alias=$u_data[0]['aliasname'];
	$phone=$u_data[0]['phone'];

	$pass_check=0;
	$flow_c=0;

	if(isset($_POST['cur_pass']))
	{
		$curpass=$_POST['cur_pass'];
		$sha_c=sha1($curpass);
		if($sha_c==$old_pass)
		{ $pass_check=1;	
		} 
		else{ echo 'p';}
	}
$has_change=0;

if($pass_check==1)
{
	 if(isset($_FILES['new_avt']['name']) )
		{
				$nq_name=uniqid();
                 $dest_file='tmp/'.$nq_name.'.avt';
				  if(move_uploaded_file($_FILES['new_avt']['tmp_name'],$dest_file))
					{
										$image_info=getimagesize($dest_file);
										$orgx=$image_info[0];
										$orgy=$image_info[1];
										$img_type=$image_info[2];
										if(($img_type==1 || $img_type==2 || $img_type==3)&&($orgx>=200)&&($orgy>=200))
										{
											$avt_name=uniqid().'.pic';
											$dest_avt='tmp/'.$avt_name;
											resize_avatar($dest_file,$dest_avt,200);
											
											$fileinfo=getimagesize($dest_avt);
											$fhand_small=fopen($dest_avt,"rb");		$small_size=filesize($dest_avt);		$avt_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
											unlink($dest_avt);
											
											$update_avt=$_con->prepare("update avatar_blob set data_blob=?,img_type=? where owner=?;");
											if($update_avt->execute(array($avt_binary,$img_type,$_uid))){$has_change=1;}
										
											$flow_c=1;
										}
										else { echo 's';}
						unlink($dest_file);
				   }
	} else {$flow_c=1;}
	//end if file
	if($flow_c==1)
	{
		if(isset($_POST['newalias']))
		{
			$new_alias=$_POST['newalias'];
			if($new_alias != $alias && mb_ereg($_alias_pat,$new_alias))
			{
				$check_a=$_con->prepare("select id from member_ where aliasname=?;");
				$check_a->execute(array($new_alias));
				if($check_a->rowCount()) { echo 'a';}
				else
				{
					$flow_c=2;
					$u_alias=$_con->prepare("update member_ set aliasname=? where id=?;");
					if($u_alias->execute(array($new_alias,$_uid))){ $has_change=1;}
				}
			}
			if($new_alias==$alias) {$flow_c=2;}
		}
		else {$flow_c=2;}
	}
	if($flow_c==2)
	{
		if(isset($_POST['newpass']))
		{
			$newpass=$_POST['newpass'];
			if(mb_strlen($newpass)>5)
			{
				$flow_c=3; $newpass_sha=sha1($newpass);
				$u_pass=$_con->prepare("update member_ set password=? where id=?;");
				if($u_pass->execute(array($newpass_sha,$_uid))){$has_change=1;}
			}
		}
		else {$flow_c=3;}
	}
	if($flow_c==3)
	{
		if(isset($_POST['newphone']))
		{
			$newphone=$_POST['newphone'];
			if(mb_strlen($newphone)==10 && isStrnum($newphone) && $newphone != $phone)
			{
				$c_phone=$_con->prepare("select id from member_ where phone=?;");
				$c_phone->execute(array($newphone));
				if($c_phone->rowCount()) { echo 'm';}
				else{
				$u_phone=$_con->prepare("update member_ set phone=? where id=?;");
					if($u_phone->execute(array($newphone,$_uid))){ $has_change=1;}
				}
			}
		}
	}
	if($has_change==1) { echo 'c';}
} // end pass check

}// end page profile
//==============================================
if($page=='market')
{
	if(isset($_POST['market_day']) && isDecimal($_POST['market_day']) && market_select() && isset($_POST['sort']))
	{
			$sort=$_POST['sort'];
			$mk_load=market_select();
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
				$end_guess=time()+$_time_right;
			
				if($sort==0){	$get_match=$_con->prepare("select * from matchday where mday=? and mkick>? order by league asc,mkick asc;"); }
				if($sort==1){	$get_match=$_con->prepare("select * from matchday where mday=? and mkick>? order by mkick asc;"); }

				$get_match->execute(array($mk_load,$end_guess));
				$m_data=$get_match->fetchAll();
				$cur_l=0;

				$get_market=$_con->prepare("select * from market where match_id in (select id from matchday where mday=?) order by match_id asc,market_type asc;");
				$get_market->execute(array($mk_load));
				$m_rows=$get_market->rowCount();				
				$market_data=$get_market->fetchAll();
				
				$team_bg_class=0;
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
					
					$kick_time=date("H",$mkick).':'.date("i",$mkick);
					$league_en=$l_en_buffer[$league]; 
					$home_en=$t_en_buffer[$home_t]; $h_en_e=$home_en;
					$away_en=$t_en_buffer[$away_t];  $a_en_e=$away_en;

					if($hadicap=0.00) {$handicap=0;}
					if($nfield==1) {$home_en.=' (n)';}

					if($cur_l != $league)
					{
						$cur_l=$league;
						$tr.='<tr><td colspan="8" class="league_group">'.$league_en.'</td></tr>';
					}
					//==========for edit data============

					$txt_day=date("j",$mk_load);
					$txt_month=date("n",$mk_load);
					$txt_year=date("Y",$mk_load);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);

					$mess_edit=$txt_day.'|'.$txt_month.'|'.$txt_year.'|'.$k_day.'|'.$k_month.'|'.$k_year.'|'.$league.'|'.$league_en.'|'.$kick_edit.'|'.$nfield.'|'.$home_t.'|'.$h_en_e.'|'.$away_t.'|'.$a_en_e.'|'.$advan.'|'.$handicap;

					//================scan market=======================
					//$market1=array();$market2= array();$market3=array();$market4=new array();$market5=new array();$market6=new array();
					$keep_index1=0; $keep_index2=0; $keep_index3=0; $keep_index4=0; $keep_index5=0; $keep_index6=0;
					$max_count=0; $cur_type=0; $cur_count=0;
					
					
					for($ki=0;$ki<$m_rows;$ki++) // แสกน ทั้ง อาเรย์ $market_data หาราคาที่เปิดให้แทง ของแมทนี้
						{
							$match_id=$market_data[$ki]['match_id'];
							if($mid==$match_id)
							{								
								$market_type= $market_data[$ki]['market_type'];
								$value1= $market_data[$ki]['value1'];
								$value2= $market_data[$ki]['value2'];
								$value3= $market_data[$ki]['value3'];						
								
									if($market_type !=$cur_type)
									{ 										
										$cur_type=$market_type;
										$cur_count=0;
									}
								$cur_count++;
								if($market_type==1) {$market1[$keep_index1][0]=$value1; $market1[$keep_index1][1]=$value2; $market1[$keep_index1][2]=$value3; $keep_index1++;}
								if($market_type==2) {$market2[$keep_index2][0]=$value1; $market2[$keep_index2][1]=$value2; $market2[$keep_index2][2]=$value3; $keep_index2++;}
								if($market_type==3) {$market3[$keep_index3][0]=$value1; $market3[$keep_index3][1]=$value2; $market3[$keep_index3][2]=$value3; $keep_index3++;}
								if($market_type==4) {$market4[$keep_index4][0]=$value1; $market4[$keep_index4][1]=$value2; $market4[$keep_index4][2]=$value3; $keep_index4++;}
								if($market_type==5) {$market5[$keep_index5][0]=$value1; $market5[$keep_index5][1]=$value2; $market5[$keep_index5][2]=$value3; $keep_index5++;}
								if($market_type==6) {$market6[$keep_index6][0]=$value1; $market6[$keep_index6][1]=$value2; $market6[$keep_index6][2]=$value3; $keep_index6++;}
								
								if($cur_count>$max_count) {$max_count=$cur_count;}
							}							
						}
						
						for($make_row=0;$make_row<$max_count;$make_row++)
						{
							$home_en2=$home_en; $away_en2=$away_en;
							if(isset($market1[$make_row]))  //ft:handicap
							{ $m1_v1=$market1[$make_row][0]; $m1_v2=$market1[$make_row][1]; $m1_v3=$market1[$make_row][2];	
								$m1_v1_=$m1_v1; 
								if($m1_v1_<0){ $m1_v1=invert_value($m1_v1);} $m1_v1=rewrite_hdc($m1_v1);
								if($m1_v1_<0) { $home_en2='<span class="red_t">'.$home_en2.'</span>';}
								if($m1_v1_>0) { $m1_v1='<br>'.$m1_v1; $away_en2='<span class="red_t">'.$away_en2.'</span>'; }

								$guess1_o1='<span onclick="guess('.$mid.',1,1,\''.$m1_v1_.'\',\''.$m1_v2.'\')" class="odds_s">'.$m1_v2.'</span>';
								$guess1_o2='<span onclick="guess('.$mid.',1,2,\''.$m1_v1_.'\',\''.$m1_v3.'\')" class="odds_s">'.$m1_v3.'</span>';

								$m1_box='<span class="box_frame"><span class="hdc_box">'.$m1_v1.'</span><span class="odds_box">'.$guess1_o1.'<br>'.$guess1_o2.'</span></span>';
							}else {$m1_box='<span class="box_frame"></span>';}

							if(isset($market2[$make_row])) //ft:over/under
							{ $m2_v1=$market2[$make_row][0]; $m2_v2=$market2[$make_row][1]; $m2_v3=$market2[$make_row][2];	

								$guess2_o1='<span onclick="guess('.$mid.',2,1,\''.$m2_v1.'\',\''.$m2_v2.'\')" class="odds_s">'.$m2_v2.'</span>';
								$guess2_o2='<span onclick="guess('.$mid.',2,2,\''.$m2_v1.'\',\''.$m2_v3.'\')" class="odds_s">'.$m2_v3.'</span>';
								$m2_box='<span class="box_frame"><span class="hdc_box">'.rewrite_hdc($m2_v1).'</span><span class="odds_box">'.$guess2_o1.'<br>'.$guess2_o2.'</span></span>';
							}else {$m2_box='<span class="box_frame"></span>';}

							if(isset($market3[$make_row])) //ft:1x2
							{ $m3_v1=$market3[$make_row][0]; $m3_v2=$market3[$make_row][1]; $m3_v3=$market3[$make_row][2];	

								$guess3_o1='<span onclick="guess('.$mid.',3,1,\'0\',\''.$m3_v1.'\')" class="odds_x">'.$m3_v1.'</span>';
								$guess3_o2='<span onclick="guess('.$mid.',3,2,\'0\',\''.$m3_v2.'\')" class="odds_x">'.$m3_v2.'</span>';
								$guess3_o0='<span onclick="guess('.$mid.',3,0,\'0\',\''.$m3_v3.'\')" class="odds_x">'.$m3_v3.'</span>';

								$m3_box='<span class="o1x2_box">'.$guess3_o1.'<br>'.$guess3_o2.'<br>'.$guess3_o0.'</span>';
							}else {$m3_box='<span class="o1x2_box"></span>';}
							//-----------------------------------------------------------
							if(isset($market4[$make_row]))  //fh:handicap
							{ $m4_v1=$market4[$make_row][0]; $m4_v2=$market4[$make_row][1]; $m4_v3=$market4[$make_row][2];		
								$m4_v1_=$m4_v1;
								if($m4_v1_<0){$m4_v1= invert_value($m4_v1);} $m4_v1=rewrite_hdc($m4_v1);
								if($m4_v1_>0) { $m4_v1='<br>'.$m4_v1; }


								$guess4_o1='<span onclick="guess('.$mid.',4,1,\''.$m4_v1_.'\',\''.$m4_v2.'\')" class="odds_s">'.$m4_v2.'</span>';
								$guess4_o2='<span onclick="guess('.$mid.',4,2,\''.$m4_v1_.'\',\''.$m4_v3.'\')" class="odds_s">'.$m4_v3.'</span>';

								$m4_box='<span class="box_frame"><span class="hdc_box">'.$m4_v1.'</span><span class="odds_box">'.$guess4_o1.'<br>'.$guess4_o2.'</span></span>';

							}else {$m4_box='<span class="box_frame"></span>';}

							if(isset($market5[$make_row])) //fh:over/under
							{ $m5_v1=$market5[$make_row][0]; $m5_v2=$market5[$make_row][1]; $m5_v3=$market5[$make_row][2];	

								$guess5_o1='<span onclick="guess('.$mid.',5,1,\''.$m5_v1.'\',\''.$m5_v2.'\')" class="odds_s">'.$m5_v2.'</span>';
								$guess5_o2='<span onclick="guess('.$mid.',5,2,\''.$m5_v1.'\',\''.$m5_v3.'\')" class="odds_s">'.$m5_v3.'</span>';
								$m5_box='<span class="box_frame"><span class="hdc_box">'.rewrite_hdc($m5_v1).'</span><span class="odds_box">'.$guess5_o1.'<br>'.$guess5_o2.'</span></span>';
							}else {$m5_box='<span class="box_frame"></span>';}

							if(isset($market6[$make_row])) //fh:1x2
							{ $m6_v1=$market6[$make_row][0]; $m6_v2=$market6[$make_row][1]; $m6_v3=$market6[$make_row][2];	

								$guess6_o1='<span onclick="guess('.$mid.',6,1,\'0\',\''.$m6_v1.'\')" class="odds_x">'.$m6_v1.'</span>';
								$guess6_o2='<span onclick="guess('.$mid.',6,2,\'0\',\''.$m6_v2.'\')" class="odds_x">'.$m6_v2.'</span>';
								$guess6_o0='<span onclick="guess('.$mid.',6,0,\'0\',\''.$m6_v3.'\')" class="odds_x">'.$m6_v3.'</span>';

								$m6_box='<span class="o1x2_box">'.$guess6_o1.'<br>'.$guess6_o2.'<br>'.$guess6_o0.'</span>';
							}else {$m6_box='<span class="o1x2_box"></span>';}

							$cur_match_td='<td class="team_en" valign="top">'.$home_en2.'<br>'.$away_en2.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="kick_time">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="m_td1">'.$m1_box.'</td><td class="m_td2">'.$m2_box.'</td><td class="m_td3">'.$m3_box.'</td>
							<td class="m_td4">'.$m4_box.'</td><td class="m_td5">'.$m5_box.'</td><td class="m_td6">'.$m6_box.'</td>
							</tr>';

						}
						unset($market1);unset($market2);unset($market3);unset($market4);unset($market5);unset($market6);
						
						if($team_bg_class==0){$team_bg_class=1;} 
						else{ 	if($team_bg_class==1){$team_bg_class=0;}  }
					
					//==============end scan market======================
					}
				echo $tr;
			}
	}
//==========อับเดทราคาน้ำ แต่ละทีม================

	if(isset($_POST['market_team']) && isDecimal($_POST['market_team']))
	{
				$match_id=$_POST['market_team'];
				$tr='';

				$get_team=$_con->prepare("select id,en_name,th_name from team where id in(select home_team from matchday where id=?) or id in(select away_team from matchday where id=?)");
				$get_team->execute(array($match_id,$match_id));
				$get_team->rowCount();

				$t_en_buffer=array();
				$t_data=$get_team->fetchAll();
				for($ti=0;$ti<$get_team->rowCount();$ti++)
					{	$t_id=$t_data[$ti]['id'];
						$t_en=$t_data[$ti]['en_name'];
						$t_th= $t_data[$ti]['th_name'];
						$t_en_buffer[$t_id]=$t_en;
					}
				$get_match=$_con->prepare("select * from matchday where id=?;");
				$get_match->execute(array($match_id));
				$m_data=$get_match->fetchAll();

				$get_market=$_con->prepare("select * from market where match_id =? order by market_type asc;");
				$get_market->execute(array($match_id));
				$m_rows=$get_market->rowCount();				
				$market_data=$get_market->fetchAll();
				
				$team_bg_class=0;
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
					
					$kick_time=date("H",$mkick).':'.date("i",$mkick);
					$league_en=$l_en_buffer[$league]; 
					$home_en=$t_en_buffer[$home_t]; $h_en_e=$home_en;
					$away_en=$t_en_buffer[$away_t];  $a_en_e=$away_en;

					if($hadicap=0.00) {$handicap=0;}
					if($nfield==1) {$home_en.=' (n)';}

					//==========for edit data============
					$txt_day=date("j",$mk_load);
					$txt_month=date("n",$mk_load);
					$txt_year=date("Y",$mk_load);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);

					$mess_edit=$txt_day.'|'.$txt_month.'|'.$txt_year.'|'.$k_day.'|'.$k_month.'|'.$k_year.'|'.$league.'|'.$league_en.'|'.$kick_edit.'|'.$nfield.'|'.$home_t.'|'.$h_en_e.'|'.$away_t.'|'.$a_en_e.'|'.$advan.'|'.$handicap;

					//================scan market=======================
					//$market1=array();$market2= array();$market3=array();$market4=new array();$market5=new array();$market6=new array();
					$keep_index1=0; $keep_index2=0; $keep_index3=0; $keep_index4=0; $keep_index5=0; $keep_index6=0;
					$max_count=0; $cur_type=0; $cur_count=0;
					
					
					for($ki=0;$ki<$m_rows;$ki++) // แสกน ทั้ง อาเรย์ $market_data หาราคาที่เปิดให้แทง ของแมทนี้
						{
							$match_id=$market_data[$ki]['match_id'];
							if($mid==$match_id)
							{								
								$market_type= $market_data[$ki]['market_type'];
								$value1= $market_data[$ki]['value1'];
								$value2= $market_data[$ki]['value2'];
								$value3= $market_data[$ki]['value3'];						
								
									if($market_type !=$cur_type)
									{ 										
										$cur_type=$market_type;
										$cur_count=0;
									}
								$cur_count++;
								if($market_type==1) {$market1[$keep_index1][0]=$value1; $market1[$keep_index1][1]=$value2; $market1[$keep_index1][2]=$value3; $keep_index1++;}
								if($market_type==2) {$market2[$keep_index2][0]=$value1; $market2[$keep_index2][1]=$value2; $market2[$keep_index2][2]=$value3; $keep_index2++;}
								if($market_type==3) {$market3[$keep_index3][0]=$value1; $market3[$keep_index3][1]=$value2; $market3[$keep_index3][2]=$value3; $keep_index3++;}
								if($market_type==4) {$market4[$keep_index4][0]=$value1; $market4[$keep_index4][1]=$value2; $market4[$keep_index4][2]=$value3; $keep_index4++;}
								if($market_type==5) {$market5[$keep_index5][0]=$value1; $market5[$keep_index5][1]=$value2; $market5[$keep_index5][2]=$value3; $keep_index5++;}
								if($market_type==6) {$market6[$keep_index6][0]=$value1; $market6[$keep_index6][1]=$value2; $market6[$keep_index6][2]=$value3; $keep_index6++;}
								
								if($cur_count>$max_count) {$max_count=$cur_count;}
							}							
						}
						
						for($make_row=0;$make_row<$max_count;$make_row++)
						{
							$home_en2=$home_en; $away_en2=$away_en;
							if(isset($market1[$make_row]))  //ft:handicap
							{ $m1_v1=$market1[$make_row][0]; $m1_v2=$market1[$make_row][1]; $m1_v3=$market1[$make_row][2];	
								$m1_v1_=$m1_v1;
								if($m1_v1_<0){ $m1_v1=invert_value($m1_v1);} $m1_v1=rewrite_hdc($m1_v1);
								if($m1_v1_<0) { $home_en2='<span class="red_t">'.$home_en2.'</span>';}
								if($m1_v1_>0) {  $m1_v1='<br>'.$m1_v1; $away_en2='<span class="red_t">'.$away_en2.'</span>'; }

								$guess1_o1='<span onclick="guess('.$mid.',1,1,\''.$m1_v1_.'\',\''.$m1_v2.'\')" class="odds_s">'.$m1_v2.'</span>';
								$guess1_o2='<span onclick="guess('.$mid.',1,2,\''.$m1_v1_.'\',\''.$m1_v3.'\')" class="odds_s">'.$m1_v3.'</span>';

								$m1_box='<span class="box_frame"><span class="hdc_box">'.$m1_v1.'</span><span class="odds_box">'.$guess1_o1.'<br>'.$guess1_o2.'</span></span>';
							}else {$m1_box='<span class="box_frame"></span>';}

							if(isset($market2[$make_row])) //ft:over/under
							{ $m2_v1=$market2[$make_row][0]; $m2_v2=$market2[$make_row][1]; $m2_v3=$market2[$make_row][2];	

								$guess2_o1='<span onclick="guess('.$mid.',2,1,\''.$m2_v1.'\',\''.$m2_v2.'\')" class="odds_s">'.$m2_v2.'</span>';
								$guess2_o2='<span onclick="guess('.$mid.',2,2,\''.$m2_v1.'\',\''.$m2_v3.'\')" class="odds_s">'.$m2_v3.'</span>';
								$m2_box='<span class="box_frame"><span class="hdc_box">'.rewrite_hdc($m2_v1).'</span><span class="odds_box">'.$guess2_o1.'<br>'.$guess2_o2.'</span></span>';
							}else {$m2_box='<span class="box_frame"></span>';}

							if(isset($market3[$make_row])) //ft:1x2
							{ $m3_v1=$market3[$make_row][0]; $m3_v2=$market3[$make_row][1]; $m3_v3=$market3[$make_row][2];	

								$guess3_o1='<span onclick="guess('.$mid.',3,1,\'0\',\''.$m3_v1.'\')" class="odds_x">'.$m3_v1.'</span>';
								$guess3_o2='<span onclick="guess('.$mid.',3,2,\'0\',\''.$m3_v2.'\')" class="odds_x">'.$m3_v2.'</span>';
								$guess3_o0='<span onclick="guess('.$mid.',3,0,\'0\',\''.$m3_v3.'\')" class="odds_x">'.$m3_v3.'</span>';

								$m3_box='<span class="o1x2_box">'.$guess3_o1.'<br>'.$guess3_o2.'<br>'.$guess3_o0.'</span>';
							}else {$m3_box='<span class="o1x2_box"></span>';}
							//-----------------------------------------------------------
							if(isset($market4[$make_row]))  //fh:handicap
							{ $m4_v1=$market4[$make_row][0]; $m4_v2=$market4[$make_row][1]; $m4_v3=$market4[$make_row][2];		
								$m4_v1_=$m4_v1;
								
								if($m4_v1_<0){ $m4_v1=invert_value($m4_v1);} $m4_v1=rewrite_hdc($m4_v1);
								if($m4_v1_>0) { $m4_v1='<br>'.$m4_v1; }


								$guess4_o1='<span onclick="guess('.$mid.',4,1,\''.$m4_v1_.'\',\''.$m4_v2.'\')" class="odds_s">'.$m4_v2.'</span>';
								$guess4_o2='<span onclick="guess('.$mid.',4,2,\''.$m4_v1_.'\',\''.$m4_v3.'\')" class="odds_s">'.$m4_v3.'</span>';

								$m4_box='<span class="box_frame"><span class="hdc_box">'.$m4_v1.'</span><span class="odds_box">'.$guess4_o1.'<br>'.$guess4_o2.'</span></span>';

							}else {$m4_box='<span class="box_frame"></span>';}

							if(isset($market5[$make_row])) //fh:over/under
							{ $m5_v1=$market5[$make_row][0]; $m5_v2=$market5[$make_row][1]; $m5_v3=$market5[$make_row][2];	

								$guess5_o1='<span onclick="guess('.$mid.',5,1,\''.$m5_v1.'\',\''.$m5_v2.'\')" class="odds_s">'.$m5_v2.'</span>';
								$guess5_o2='<span onclick="guess('.$mid.',5,2,\''.$m5_v1.'\',\''.$m5_v3.'\')" class="odds_s">'.$m5_v3.'</span>';
								$m5_box='<span class="box_frame"><span class="hdc_box">'.rewrite_hdc($m5_v1).'</span><span class="odds_box">'.$guess5_o1.'<br>'.$guess5_o2.'</span></span>';
							}else {$m5_box='<span class="box_frame"></span>';}

							if(isset($market6[$make_row])) //fh:1x2
							{ $m6_v1=$market6[$make_row][0]; $m6_v2=$market6[$make_row][1]; $m6_v3=$market6[$make_row][2];	

								$guess6_o1='<span onclick="guess('.$mid.',6,1,\'0\',\''.$m6_v1.'\')" class="odds_x">'.$m6_v1.'</span>';
								$guess6_o2='<span onclick="guess('.$mid.',6,2,\'0\',\''.$m6_v2.'\')" class="odds_x">'.$m6_v2.'</span>';
								$guess6_o0='<span onclick="guess('.$mid.',6,0,\'0\',\''.$m6_v3.'\')" class="odds_x">'.$m6_v3.'</span>';

								$m6_box='<span class="o1x2_box">'.$guess6_o1.'<br>'.$guess6_o2.'<br>'.$guess6_o0.'</span>';
							}else {$m6_box='<span class="o1x2_box"></span>';}

							$cur_match_td='<td class="team_en" valign="top">'.$home_en2.'<br>'.$away_en2.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="kick_time">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="m_td1">'.$m1_box.'</td><td class="m_td2">'.$m2_box.'</td><td class="m_td3">'.$m3_box.'</td>
							<td class="m_td4">'.$m4_box.'</td><td class="m_td5">'.$m5_box.'</td><td class="m_td6">'.$m6_box.'</td>
							</tr>';

						}
						unset($market1);unset($market2);unset($market3);unset($market4);unset($market5);unset($market6);
						
						if($team_bg_class==0){$team_bg_class=1;} 
						else{ 	if($team_bg_class==1){$team_bg_class=0;}  }
					
					//==============end scan market======================
					}
				echo $tr;
			}
	

//===============เช็ค ออดซ์ ตอนแทง=================
if(isset($_POST['market_check']) &&isset($_POST['c_type']) &&isset($_POST['c_team']) &&isset($_POST['c_hdc']) &&isset($_POST['c_odds']))
	{
		$mid=$_POST['market_check'];
		$m_type=$_POST['c_type'];
		$select_team=$_POST['c_team'];
		$handicap=$_POST['c_hdc'];
		$odds=$_POST['c_odds'];
		if(isDecimal($mid) && isDecimal($m_type) && isStrnum($select_team) && ($handicap==0.00 || mb_ereg($_handicap_pat,$handicap)) && mb_ereg($_odds_pat,$odds))
		{
			if($m_type==1 || $m_type==4)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {echo $value2;}
					}
					if($select_team==2) 
					{ if($odds!=$value3) {echo $value3;}					
					}
				}	else{echo 'm';}
			}
			if($m_type==2 || $m_type==5)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {echo $value2;}
					}
					if($select_team==2) 
					{ if($odds!=$value3) {echo $value3;}					
					}
				}	else{echo 'm';}
			}
			if($m_type==3 || $m_type==6)
			{
				$get_odds=$_con->prepare("select value1,value2,value3 from market where match_id=? and market_type=?;");
				$get_odds->execute(array($mid,$m_type));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value1=$h_data[0]['value1'];
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value1) {echo $value1;}
					}
					if($select_team==2) 
					{ if($odds!=$value2) {echo $value2;}					
					}
					if($select_team==0) 
					{ if($odds!=$value3) {echo $value3;}
					}

				}	else{echo 'm';}
			}
		}
	}//end market_check
//=================เก็บการทายผล========================

if(isset($_POST['market_bill']) &&isset($_POST['b_type']) &&isset($_POST['b_team']) &&isset($_POST['b_hdc']) &&isset($_POST['b_odds']))
	{
		$mid=$_POST['market_bill'];
		$m_type=$_POST['b_type'];
		$select_team=$_POST['b_team'];
		$handicap=$_POST['b_hdc'];
		$odds=$_POST['b_odds'];
		if(isDecimal($mid) && isDecimal($m_type) && isStrnum($select_team) && ($handicap==0.00 || mb_ereg($_handicap_pat,$handicap)) && mb_ereg($_odds_pat,$odds))
		{
			$can_bill=0;
		
			$get_match=$_con->prepare("select mday,mkick from matchday where id=?;");
			$get_match->execute(array($mid));
			if($get_match->rowCount())
				{			
					$h_data=$get_match->fetchAll();
					$mday=$h_data[0]['mday'];
					$mkick=$h_data[0]['mkick'];
					$count_bill=$_con->query("select id from bill where owner=$_uid and bill_type=0 and mday=$mday;"); $bill_count=$count_bill->rowCount();
					if($bill_count<$_bill_limit_day)
					{
						if(market_select())
						{
							$time_wait=$mkick-$_now;						
							if($time_wait>$_time_right)
							{
								$can_bill=1;
								$month=date("n",$mday);
								$year=date("Y",$mday);
								$mmonth=mktime(0,0,0,$month,1,$year);
							} else { echo 't';}
						}else { echo 't';}
					} else { echo 'l';}
				}
	if($can_bill==1)
		{
			$ip=getip();
			$stake=1; // วางเงินเดิมพัน กรณีใช้ ใช้เครดิตแทง

			$insert=$_con->prepare("insert into bill(owner,mday,mmonth,match_id,g_type,g_handicap,g_team,g_odds,stake,g_time,g_ip) values(?,?,?,?,?,?,?,?,?,?,?);");

			if($m_type==1 || $m_type==4)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{
						if($odds!=$value2) {echo $value2;}
						if($odds==$value2) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,$handicap,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
					if($select_team==2) 
					{ if($odds!=$value3) {echo $value3;}	
						
						if($odds==$value3) { 
							$handicap=invert_hdc($handicap); 
							if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,$handicap,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
				}	else{echo 'm';}
			}
			if($m_type==2 || $m_type==5)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {echo $value2;}
						if($odds==$value2) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,$handicap,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
					if($select_team==2) 
					{ if($odds!=$value3) {echo $value3;}	
						if($odds==$value3) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,$handicap,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
				}	else{echo 'm';}
			}
			if($m_type==3 || $m_type==6)
			{
				$get_odds=$_con->prepare("select value1,value2,value3 from market where match_id=? and market_type=?;");
				$get_odds->execute(array($mid,$m_type));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value1=$h_data[0]['value1'];
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value1) {echo $value1;}
						if($odds==$value1) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,0,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
					if($select_team==2) 
					{ if($odds!=$value2) {echo $value2;}
						if($odds==$value2) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,0,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}
					if($select_team==0) 
					{ if($odds!=$value3) {echo $value3;}
						if($odds==$value3) {if($insert->execute(	array($_uid,$mday,$mmonth,$mid,$m_type,0,$select_team,$odds,$stake,$_now,$ip))) {echo 'i'; }}
					}

				}	else{echo 'm';}
			}
		}// end can bill
		}
	}//end market_check

//====================
if(isset($_POST['bill_count']))
	{
		$get_bill=$_con->query("select id from bill where owner=$_uid and is_check=0;");
		$bill_count=$get_bill->rowCount();
		if($bill_count){echo $bill_count;}
	}
}// end market page

//==============page mixparlay=======================================
if($page=='mixparlay')
{
	if(isset($_POST['parlay_check']) &&isset($_POST['c_type']) &&isset($_POST['c_team']) &&isset($_POST['c_hdc']) &&isset($_POST['c_odds']))
	{
		
		$mid=$_POST['parlay_check'];
		$m_type=$_POST['c_type'];
		$select_team=$_POST['c_team'];
		$handicap=$_POST['c_hdc'];
		$odds=$_POST['c_odds'];
		if(isDecimal($mid) && isDecimal($m_type) && isStrnum($select_team) && ($handicap==0.00 || mb_ereg($_handicap_pat,$handicap)) && mb_ereg($_odds_pat,$odds))
		{
			$old_odds='';
			$new_odds=$odds;
			$find=1;

			if($m_type==1 || $m_type==4)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2; }
					}
					if($select_team==2) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3; }					
					}
				}	else{$find=0;}
			}
			if($m_type==2 || $m_type==5)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2; }
					}
					if($select_team==2) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3; }					
					}
				}	else{$find=0;}
			}
			if($m_type==3 || $m_type==6)
			{
				$get_odds=$_con->prepare("select value1,value2,value3 from market where match_id=? and market_type=?;");
				$get_odds->execute(array($mid,$m_type));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value1=$h_data[0]['value1'];
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value1) {$old_odds=$odds; $new_odds=$value1; }
					}
					if($select_team==2) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2; }					
					}
					if($select_team==0) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3; }
					}

				}	else{$find=0;}
			}
		//---------------------------------------
			if($find==0) {echo 'm';} 
			else
			{
				$get_team=$_con->query("select home_team,away_team from matchday where id=$mid;");
				$t_data=$get_team->fetchAll();
				$home_id=$t_data[0]['home_team'];
				$away_id=$t_data[0]['away_team'];
				$home_name=get_team($home_id);
				$away_name=get_team($away_id);
		
				if($m_type==1 || $m_type==4)
				{
					if($handicap=='0.00') 
					{
						$home_hdc='+0'; $away_hdc='+0';
					}
					else
					{
						$handicap*=1;
						if($handicap>0)
						{
							$home_hdc='+'.$handicap; $away_hdc='-'.$handicap;
						}						
						if($handicap<0)
						{
							$home_hdc=$handicap; $away_hdc='+'.invert_value($handicap);
						}						
					}
					if($select_team==1){$bet_choose=$home_name.' '.$home_hdc;}
					if($select_team==2){$bet_choose=$away_name.' '.$away_hdc;}
				}

			if($m_type==2||$m_type==5)
			{
				if($select_team==1) { $show_txt='สูงกว่า '.$handicap;}
				if($select_team==2) { $show_txt='ต่ำกว่า '.$handicap;}
				$bet_choose=$show_txt;
			}
			if($m_type==3||$m_type==6)
			{
				if($select_team==1) {$show_bet=$home_name.' ชนะ';}
				if($select_team==2) {$show_bet=$away_name.' ชนะ';}
				if($select_team==0) {$show_bet=' เสมอ';}

				$bet_choose=$show_bet;
			}
			
				$img='<img src="'.$_url.'images/del.png" onclick="del_b('.$mid.')" class="del_bi">';
				$tr='<tr id="b'.$mid.'" bill="'.$mid.','.$m_type.','.$select_team.','.$handicap.','.$new_odds.'">
					<td>
						<span class="bet_type">'.bet_type_txt($m_type).'</span>'.$img.'<br>
						<span class="bet_choose">'.$bet_choose.'</span><span class="bet_odds"> @ '.$new_odds.' <strike>'.$old_odds.'</strike></span><br>
						<span class="bet_team">'.$home_name.' กับ '.$away_name.'</span>
					</td>
				</tr>';

				echo $tr;
			} // end find=1;
		} //end isDecimal
		//--------------------------------------------------------------

	}//end market_check
//==================ตรวจสอบ สเต็บ 5=====================

	if(isset($_POST['mix_bill']))
	{
			$bill_mix=mb_trim($_POST['mix_bill']);

			$bill=mb_split(';',$bill_mix);
			$bill_count=count($bill);

			$check_flag=0;
			$message="o";
			$tr_buff='';
			
			/* ตรวจสอบ บิล เท่ากับ 5 คู่หรือไม่
				ตรวจสอบ มีคู่แข่งที่เลือกมาซ้ำกันหรือไม่
				ตรวจสอบ แทง บิลเดี่ยวหรือยัง
				ตรวจสอบ แทง บอลชุดแล้วหรือยัง

			*/	
		
		$mday=market_select();

		 if($bill_count==5 && $mday)
			{
			 	for($bi=0;$bi<$bill_count;$bi++)
				{
					$cur_bill=$bill[$bi];
					$slice=mb_split(',',$cur_bill);

					$mid=$slice[0];
					$all_mid[$bi]=$mid;			
				}

				$ble=0;$double=0;
				
				for($bi=0;$bi<$bill_count;$bi++)
				{
					$cur_mid=$all_mid[$bi];
					for($bii=0;$bii<$bill_count;$bii++)
					{
						$c_mid=$all_mid[$bii];
						if($c_mid==$cur_mid){$ble++;}
					}
					if($ble>1){$double=1;}
					$ble=0;
				}
				if($double==1) {}
				else
				{
					$count_bill=$_con->query("select id from bill where owner=$_uid and bill_type=0 and mday=$mday;"); $bill0_count=$count_bill->rowCount();
					if($bill0_count)
					{
						$count_bill=$_con->query("select id from bill where owner=$_uid and bill_type=1 and mday=$mday;"); $bill1_count=$count_bill->rowCount();
						if($bill1_count){$message='h';}
						else
						{
							$check_flag=1;
						}
					}else {$message='b';}
				}
			}

		if($check_flag==1)
		{

		for($bi=0;$bi<$bill_count;$bi++)
			{
			$cur_bill=$bill[$bi];
			$slice=mb_split(',',$cur_bill);

			$mid=$slice[0];
			$m_type=$slice[1];
			$select_team=$slice[2];
			$handicap=$slice[3];
			$odds=$slice[4];

		if(isDecimal($mid) && isDecimal($m_type) && isStrnum($select_team) && ($handicap==0.00 || mb_ereg($_handicap_pat,$handicap)) && mb_ereg($_odds_pat,$odds))
		{

			$can_bill=0;
		
			$get_match=$_con->prepare("select mday,mkick from matchday where id=?;");
			$get_match->execute(array($mid));
			if($get_match->rowCount())
				{			
					$h_data=$get_match->fetchAll();
					$mday=$h_data[0]['mday'];
					$mkick=$h_data[0]['mkick'];
							$time_wait=$mkick-$_now;						
							if($time_wait>$_time_right)
							{
								$can_bill=1;
								$month=date("n",$mday);
								$year=date("Y",$mday);
								$mmonth=mktime(0,0,0,$month,1,$year);
							} else { $message='C';}					
				}else { $message='C';}


			if($can_bill==1)
			{
				$old_odds='';
				$new_odds=$odds;
				$find=1;

			if($m_type==1 || $m_type==4)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2; $message='u';}
					}
					if($select_team==2) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3; $message='u';}					
					}
				}	else{$find=0;}
			}
			if($m_type==2 || $m_type==5)
			{
				$get_odds=$_con->prepare("select value2,value3 from market where match_id=? and market_type=? and value1=?;");
				$get_odds->execute(array($mid,$m_type,$handicap));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2;$message='u'; }
					}
					if($select_team==2) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3;$message='u'; }					
					}
				}	else{$find=0;}
			}
			if($m_type==3 || $m_type==6)
			{
				$get_odds=$_con->prepare("select value1,value2,value3 from market where match_id=? and market_type=?;");
				$get_odds->execute(array($mid,$m_type));
				if($get_odds->rowCount())
				{
					$h_data=$get_odds->fetchAll();
					$value1=$h_data[0]['value1'];
					$value2=$h_data[0]['value2'];
					$value3=$h_data[0]['value3'];
					if($select_team==1) 
					{ if($odds!=$value1) {$old_odds=$odds; $new_odds=$value1;$message='u'; }
					}
					if($select_team==2) 
					{ if($odds!=$value2) {$old_odds=$odds; $new_odds=$value2;$message='u'; }					
					}
					if($select_team==0) 
					{ if($odds!=$value3) {$old_odds=$odds; $new_odds=$value3;$message='u'; }
					}

				}	else{$find=0;}
			}
		//---------------------------------------
			if($find==0) {$message='c';} 
			else
			{
				$get_team=$_con->query("select home_team,away_team from matchday where id=$mid;");
				$t_data=$get_team->fetchAll();
				$home_id=$t_data[0]['home_team'];
				$away_id=$t_data[0]['away_team'];
				$home_name=get_team($home_id);
				$away_name=get_team($away_id);
		
				if($m_type==1 || $m_type==4)
				{
					if($handicap=='0.00') 
					{
						$home_hdc='+0'; $away_hdc='+0';
					}
					else
					{
						$handicap*=1;
						if($handicap>0)
						{
							$home_hdc='+'.$handicap; $away_hdc='-'.$handicap;
						}						
						if($handicap<0)
						{
							$home_hdc=$handicap; $away_hdc='+'.invert_value($handicap);
						}						
					}
					if($select_team==1){$bet_choose=$home_name.' '.$home_hdc;}
					if($select_team==2){$bet_choose=$away_name.' '.$away_hdc;}
				}

			if($m_type==2||$m_type==5)
			{
				if($select_team==1) { $show_txt='สูงกว่า '.$handicap;}
				if($select_team==2) { $show_txt='ต่ำกว่า '.$handicap;}
				$bet_choose=$show_txt;
			}
			if($m_type==3||$m_type==6)
			{
				if($select_team==1) {$show_bet=$home_name.' ชนะ';}
				if($select_team==2) {$show_bet=$away_name.' ชนะ';}
				if($select_team==0) {$show_bet=' เสมอ';}

				$bet_choose=$show_bet;
			}
				$img='<img src="'.$_url.'images/del.png" onclick="del_b('.$mid.')" class="del_bi">';
				$tr_buff.='<tr bi="'.$bi.'" id="b'.$mid.'" bill="'.$mid.','.$m_type.','.$select_team.','.$handicap.','.$new_odds.'">
					<td>
						<span class="bet_type">'.bet_type_txt($m_type).'</span>'.$img.'<br>
						<span class="bet_choose">'.$bet_choose.'</span><span class="bet_odds"> @ '.$new_odds.' <strike>'.$old_odds.'</strike></span><br>
						<span class="bet_team">'.$home_name.' กับ '.$away_name.'</span>
					</td>
				</tr>';

				
			} // end find=1;
		} // end can_bill
		} else {$message='w';} //end isDecimal
		}  // end for bill
		} //end if check_flag
		//--------------------------------------------------------------

		if($message !='o') {echo $message.$tr_buff;}

		if($message=='o')
		{
			$ip=getip();
			$stake=1;
			$month=date("n",$mday);
			$year=date("Y",$mday);
			$mmonth=mktime(0,0,0,$month,1,$year);

			$insert=$_con->prepare("insert into bill(owner,bill_type,mday,mmonth,stake,g_time,g_ip) values(?,?,?,?,?,?,?);");

			if($insert->execute(array($_uid,1,$mday,$mmonth,$stake,$_now,$ip)))
			{
				$last_bill=$_con->lastInsertId();
				$suc_count=0;
				for($bim=0;$bim<$bill_count;$bim++)
				{
					$cur_bill=$bill[$bim];
					$slice=mb_split(',',$cur_bill);

					$mid=$slice[0];
					$m_type=$slice[1];
					$select_team=$slice[2];
					$handicap=$slice[3];
					$odds=$slice[4];
					if(($m_type==1 || $m_type==4) && $select_team==2)
					{ $handicap=invert_value($handicap);}

					$insert_mix=$_con->prepare("insert into mixparlay(bill,owner,match_id,g_type,g_handicap,g_team,g_odds) values(?,?,?,?,?,?,?);");
					if($insert_mix->execute(array($last_bill,$_uid,$mid,$m_type,$handicap,$select_team,$odds))) { $suc_count++;}
				}// end for bill

				if($suc_count==5) {echo 's5';}
				else
				{
					$_con->exec("delete from mixparlay where bill=$last_bill;");
					$_con->exec("delete from bill where id=$last_bill;"); 
					echo 'del'.$last_bill;
				}
			}
		}// end write bill mixparlay
	}
//===================end step 5=======================

}// end page mixparlay
	
	if($_con) {$_con=null;}
?>