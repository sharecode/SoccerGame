<?php
require_once "ext.php";

if(isset($_POST['page']))
{
	$page=$_POST['page'];
}
else{ exit();}

con_mysql();
session_pair();
if(islogin() && isAdmin(15)){}
else{$_con=null; exit();}

//=====================add_team.php===========================

$pat_en='^[0-9a-zA-Z\-\_\(\)\ ]+$';
$pat_th='^[0-9ก-ูเ-์\-\_\(\)\ ]+$';


if($page=='addteam')
{
	if(isset($_POST['addleague']) && isset($_POST['th_l']))
	{
		$en=mb_trim($_POST['addleague']);
		$th=mb_trim($_POST['th_l']);

		$check=0;
		if(mb_strlen($en)>2 && mb_ereg($pat_en,$en))
		{ 
			if($th=='') {$check=1;}
			else
			{
				if(mb_strlen($th)>2 && mb_ereg($pat_th,$th)) { $check=1; }				
			}
		}

		if($check==1)
		{
			$l_check=$_con->prepare("select id from league where en_name=?;");
			$l_check->execute(array($en));
			if($l_check->rowCount()) { echo 'h';}
			else{ $check = 2;}
		}
		if($check==2)
		{
			$l_insert=$_con->prepare("insert into league(en_name,th_name) values(?,?);");
			if($l_insert->execute(array($en,$th))) { echo 'o'; }
		}
	}

	//-----------------------------------------

	if(isset($_POST['addteam']) && isset($_POST['th_t']))
	{
		$en=mb_trim($_POST['addteam']);
		$th=mb_trim($_POST['th_t']);
		
		$check_file=0;
		$has_logo=0;
		 if(isset($_FILES['logo']['name']))
		{
			
				 $nq_name=uniqid();
                 $dest_file='tmp/'.$nq_name.'.tmp';
				  if(move_uploaded_file($_FILES['logo']['tmp_name'],$dest_file))
					{
										$image_info=getimagesize($dest_file);
										$orgx=$image_info[0];
										$orgy=$image_info[1];
										$img_type=$image_info[2];
										if(($img_type==1 || $img_type==2 || $img_type==3)&&($orgx>=150)&&($orgy>=150))
										{
											$logo_name=uniqid().'.logo';
											$dest_logo='tmp/'.$logo_name;
											resize_y($dest_file,$dest_logo,150);
											
											$fileinfo=getimagesize($dest_logo);
											$logox=$fileinfo[0];
											$logoy=$fileinfo[1];
											$fhand_small=fopen($dest_logo,"rb");		$small_size=filesize($dest_logo);		$logo_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
											$check_file=1;
											$has_logo=1;
											unlink($dest_logo);
										}						
						unlink($dest_file);
				   }
		}
		else
		{
			/*
			$dest_logo="images/nologo.png";
			$fhand_small=fopen($dest_logo,"rb");		$small_size=filesize($dest_logo);		$logo_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
			$img_type=3; $logox=150; $logoy=150;
			*/
			$check_file=1;			
		}
	
		$check=0;
		if($check_file==1)
		{			
			if(mb_strlen($en)>2 && mb_ereg($pat_en,$en))
			{ 
				if($th=='') {$check=1;}
				else
				{
					if(mb_strlen($th)>2 && mb_ereg($pat_th,$th)) { $check=1; }				
				}
			}
		}

		if($check==1)
		{
			$l_check=$_con->prepare("select id from team where en_name=?;");
			$l_check->execute(array($en));
			if($l_check->rowCount()) { echo 'h';}
			else{ $check = 2;}
		}
		if($check==2)
		{
			$l_insert=$_con->prepare("insert into team(en_name,th_name) values(?,?);");
			if($l_insert->execute(array($en,$th)))
			{
				$team_id=$_con->lastInsertId();
				if($has_logo==1)
				{					
					$insert_team=$_con->prepare("insert into team_logo(id,blob_data,img_type,img_x,img_y) values(?,?,?,?,?);");
					$insert_team->execute(array($team_id,$logo_binary,$img_type,$logox,$logoy));
				}				
				
				echo $team_id; 
			}
		}
		
	}
//------------------------------------------------

if(isset($_POST['find_league']))
{
	$find=mb_trim($_POST['find_league']);
	if(mb_strlen($find)>2 && mb_ereg($pat_en,$find))
	{
		$key_find='%'.$find.'%';
		$league=$_con->prepare("select id,en_name,th_name from league where en_name like ? limit 10;");
		$league->execute(array($key_find));
		$rows=$league->rowCount();
		if($rows)
		{
			
			$buffer='';
			$data=$league->fetchAll();
			for($l=0;$l<$rows;$l++)
			{
				$en_l=$data[$l]['en_name'];
				$th_l=$data[$l]['th_name'];
				$l_id=$data[$l]['id'];

				$buffer.='<div class="guess_row" onclick="league_edit('.$l_id.',\''.$en_l.'\',\''.$th_l.'\')">'.$en_l.'</div>';
			}

			echo $buffer;
		}
	}
}

if(isset($_POST['find_team']))
{
	$find=mb_trim($_POST['find_team']);
	if(mb_strlen($find)>2 && mb_ereg($pat_en,$find))
	{
		$key_find='%'.$find.'%';
		$league=$_con->prepare("select id,en_name,th_name from team where en_name like ? limit 10;");
		$league->execute(array($key_find));
		$rows=$league->rowCount();
		if($rows)
		{
			
			$buffer='';
			$data=$league->fetchAll();
			for($l=0;$l<$rows;$l++)
			{
				$en_l=$data[$l]['en_name'];
				$th_l=$data[$l]['th_name'];
				$l_id=$data[$l]['id'];

				$buffer.='<div class="guess_row" onclick="team_edit('.$l_id.',\''.$en_l.'\',\''.$th_l.'\')">'.$en_l.'</div>';
			}

			echo $buffer;
		}
	}
}
//==============================

if(isset($_POST['edit_league']) && isset($_POST['e_en_l']) && isset($_POST['e_th_l']))
	{
		$l_id=$_POST['edit_league'];
		$new_en=mb_trim($_POST['e_en_l']);
		$new_th=mb_trim($_POST['e_th_l']);
		
		$update_detail='';

		if(isDecimal($l_id))
		{
			$get_l=$_con->prepare("select en_name,th_name from league where id=?;");
			$get_l->execute(array($l_id));
			if($get_l->rowCount())
			{
				$data=$get_l->fetchAll();
				$old_en=$data[0]['en_name'];
				$old_th=$data[0]['th_name'];

				if(mb_strlen($new_en)>2 && mb_ereg($pat_en,$new_en) && $new_en != $old_en)
				{
					$check_l=$_con->prepare("select id from league where en_name=?;");
					$check_l->execute(array($new_en));
					if($check_l->rowCount()) { echo 'h'; }
					else
					{					
						$update_en=$_con->prepare("update league set en_name=? where id=?;");
						if($update_en->execute(array($new_en,$l_id)))
						{
						$update_detail.='<span class="icon_frame"><img src="'.$_url.'images/ok.png" class="icon_small"></span> อับเดท '.$old_en.' => '.$new_en.'<br>';
						}
					}
				}
				if(mb_strlen($new_th)>2 && mb_ereg($pat_th,$new_th) && $new_th != $old_th)
				{
					$update_en=$_con->prepare("update league set th_name=? where id=?;");
					if($update_en->execute(array($new_th,$l_id)))
					{
						$update_detail.='<span class="icon_frame"><img src="'.$_url.'images/ok.png" class="icon_small"></span> อับเดท '.$old_th.' => '.$new_th.'<br>';
					}
				}

			}
		}

		echo $update_detail;
	}

//========================
if(isset($_POST['edit_team']) && isset($_POST['e_en_t']) && isset($_POST['e_th_t']))
	{
		$t_id=$_POST['edit_team'];
		$new_en=mb_trim($_POST['e_en_t']);
		$new_th=mb_trim($_POST['e_th_t']);
		
		$update_detail='';

		if(isDecimal($t_id))
		{
			//-------------------------------------------------
			 if(isset($_FILES['e_logo']['name']))
			{
				 $nq_name=uniqid();
                 $dest_file='tmp/'.$nq_name.'.tmp';
				  if(move_uploaded_file($_FILES['e_logo']['tmp_name'],$dest_file))
					{
										$image_info=getimagesize($dest_file);
										$orgx=$image_info[0];
										$orgy=$image_info[1];
										$img_type=$image_info[2];
										if(($img_type==1 || $img_type==2 || $img_type==3)&&($orgx>=150)&&($orgy>=150))
										{
											$logo_name=uniqid().'.logo';
											$dest_logo='tmp/'.$logo_name;
											resize_y($dest_file,$dest_logo,150);
											
											$fileinfo=getimagesize($dest_logo);
											$logox=$fileinfo[0];
											$logoy=$fileinfo[1];
											$fhand_small=fopen($dest_logo,"rb");		$small_size=filesize($dest_logo);		$logo_binary=fread($fhand_small,$small_size);			fclose($fhand_small);											
											unlink($dest_logo);
										}						
						unlink($dest_file);

						$update_logo=$_con->prepare("update team_logo set blob_data=?,img_type=?,img_x=?,img_y=? where id=?;");
						if($update_logo->execute(array($logo_binary,$img_type,$logox,$logoy,$t_id)))
						{
							$update_detail.='<span class="icon_frame"><img src="'.$_url.'images/ok.png" class="icon_small"></span> อับเดท โลโก้ทีม<br>';
						}
				   }
			}
			//-------------------------------------------------

			$get_l=$_con->prepare("select en_name,th_name from team where id=?;");
			$get_l->execute(array($t_id));
			if($get_l->rowCount())
			{
				$data=$get_l->fetchAll();
				$old_en=$data[0]['en_name'];
				$old_th=$data[0]['th_name'];

				if(mb_strlen($new_en)>2 && mb_ereg($pat_en,$new_en) && $new_en != $old_en)
				{
					$check_t=$_con->prepare("select id from team where en_name=?;");
					$check_t->execute(array($new_en));
					if($check_t->rowCount()) { echo 'h'; }
					else
					{	
						$update_en=$_con->prepare("update team set en_name=? where id=?;");
						if($update_en->execute(array($new_en,$t_id)))
						{
							$update_detail.='<span class="icon_frame"><img src="'.$_url.'images/ok.png" class="icon_small"></span> อับเดท '.$old_en.' => '.$new_en.'<br>';
						}
					}
				}
				if(mb_strlen($new_th)>2 && mb_ereg($pat_th,$new_th) && $new_th != $old_th)
				{
					$update_en=$_con->prepare("update team set th_name=? where id=?;");
					if($update_en->execute(array($new_th,$t_id)))
					{
						$update_detail.='<span class="icon_frame"><img src="'.$_url.'images/ok.png" class="icon_small"></span> อับเดท '.$old_th.' => '.$new_th.'<br>';
					}
				}
			}
		}
		echo $update_detail;
	}
	//============================
if(isset($_POST['del_team_logo']))
	{
		$del_id=$_POST['del_team_logo'];
		if(isDecimal($del_id))
		{
			$del=$_con->prepare("delete from team_logo where id=?;");
			$del->execute(array($del_id));
		}
	}
} //end page addteam

if($page=='mtable') //============== matchtable.php==================
{

if(isset($_POST['choose_league']))
{
	$find=mb_trim($_POST['choose_league']);
	if(mb_strlen($find)>2 && mb_ereg($pat_en,$find))
	{
		$key_find='%'.$find.'%';
		$league=$_con->prepare("select id,en_name,th_name from league where en_name like ? limit 10;");
		$league->execute(array($key_find));
		$rows=$league->rowCount();
		if($rows)
		{			
			$buffer='';
			$data=$league->fetchAll();
			for($l=0;$l<$rows;$l++)
			{
				$en_l=$data[$l]['en_name'];
				$th_l=$data[$l]['th_name'];
				$l_id=$data[$l]['id'];
				
				if(isset($_POST['edit_league']))
				{
					$buffer.='<div class="guess_row" onclick="e_league_select('.$l_id.',\''.$en_l.'\')">'.$en_l.'</div>'; 
				}else {
				$buffer.='<div class="guess_row" onclick="league_select('.$l_id.',\''.$en_l.'\')">'.$en_l.'</div>'; 
				}
			}
			echo $buffer;
		}
	}
}

if(isset($_POST['choose_team']))
{
	$find=mb_trim($_POST['choose_team']);
	if(mb_strlen($find)>2 && mb_ereg($pat_en,$find))
	{
		$key_find='%'.$find.'%';
		$league=$_con->prepare("select id,en_name,th_name from team where en_name like ? limit 10;");
		$league->execute(array($key_find));
		$rows=$league->rowCount();
		if($rows)
		{			
			$buffer='';
			$data=$league->fetchAll();
			for($l=0;$l<$rows;$l++)
			{
				$en_l=$data[$l]['en_name'];
				$th_l=$data[$l]['th_name'];
				$l_id=$data[$l]['id'];
				
				if(isset($_POST['team_edit']))
				{	$buffer.='<div class="guess_row" onclick="e_team_select('.$l_id.',\''.$en_l.'\')">'.$en_l.'</div>';
				}else{
				$buffer.='<div class="guess_row" onclick="team_select('.$l_id.',\''.$en_l.'\')">'.$en_l.'</div>';
				}
			}
			echo $buffer;
		}
	}
}
//==========================เพิ่มตารางแข่งในแต่ละวัน==================================

if(isset($_POST['t_day']) && isset($_POST['t_month'])  && isset($_POST['t_year'])  && isset($_POST['k_day'])  && isset($_POST['k_month'])  && isset($_POST['k_year'])  && isset($_POST['k_time'])  && isset($_POST['nfield'])  && isset($_POST['l_id'])  && isset($_POST['h_id'])  && isset($_POST['a_id'])  && isset($_POST['advan'])  && isset($_POST['handicap'] ))
	{
		
	if(isStrnum($_POST['t_day']) && isStrnum($_POST['t_month'])  && isStrnum($_POST['t_year'])  && isStrnum($_POST['k_day'])  && isStrnum($_POST['k_month'])  && isStrnum($_POST['k_year'])  && isStrnum($_POST['k_time'])  && isStrnum($_POST['nfield'])  && isStrnum($_POST['l_id'])  && isStrnum($_POST['h_id'])  && isStrnum($_POST['a_id']))
		{

		$check=0;
		if(checkdate($_POST['t_month'],$_POST['t_day'],$_POST['t_year']))
		{
			$mk_day=mktime(0,0,0,$_POST['t_month'],$_POST['t_day'],$_POST['t_year']);
			if(checkdate($_POST['k_month'],$_POST['k_day'],$_POST['k_year']))
			{
				$min=ltrim_zero(mb_substr($_POST['k_time'],2));
				$hour=ltrim_zero(mb_substr($_POST['k_time'],0,2));
				if($min >=0 && $min<60 && $hour >=0 && $hour <24)
				{
					$mk_kick=mktime($hour,$min,0,$_POST['k_month'],$_POST['k_day'],$_POST['k_year']);
					$check=1;
				}
			}
		}

		if($check==1)
		{
			$nfield=$_POST['nfield'];
			$l_id=$_POST['l_id'];
			$h_id=$_POST['h_id'];
			$a_id=$_POST['a_id'];
			if(($nfield==0 || $nfield==1) && isDecimal($l_id) && isDecimal($h_id) && isDecimal($a_id) && $h_id != $a_id)
			{
				
				$have_l=$_con->prepare("select id from league where id=?;");
				$have_l->execute(array($l_id));
				if($have_l->rowCount())
				{ 
					$have_t=$_con->prepare("select id from team where id=?;");
					$have_t->execute(array($h_id));
					$have_home=$have_t->rowCount();
					$have_t->execute(array($a_id));
					$have_away=$have_t->rowCount();
					if($have_home==1 && $have_away==1) { $check=2;}
				}
			}
		}
		else { echo 't';}

		if($check==2)
		{
			$ta=$_con->prepare("select id from matchday where mday=? and (home_team in(?,?) OR away_team in (?,?));");
			$ta->execute(array($mk_day,$h_id,$a_id,$h_id,$a_id));
			if($ta->rowCount()) { echo 'h';} else { $check=3;}
		}
		if($check==3)
		{
			$advan=$_POST['advan'];
			if($advan==1 || $advan==2)
			{
				$handicap=$_POST['handicap'];
				$pat="^[0-9][0-9]*([\.](0|00|25|5|50|75))*$";
				if(mb_ereg($pat,$handicap))
				{
					$check=4;
				}
			}
		}
		if($check==4)
		{
			$_save=$_con->prepare("insert into matchday(mday,league,mkick,nfield,home_team,away_team,advan,handicap) values(?,?,?,?,?,?,?,?);");
			$data_in=array($mk_day,$l_id,$mk_kick,$nfield,$h_id,$a_id,$advan,$handicap);
			if($_save->execute($data_in))
			{
				echo 'c';
			}
		}
	}
	}//end เพิ่ม match
//=============================
if(isset($_POST['l_day']) &&isset($_POST['l_month']) && isset($_POST['l_year']) && isStrnum($_POST['l_day']) && isStrnum($_POST['l_month']) && isStrnum($_POST['l_year']))
	{
		$mk_load=mktime(0,0,0,$_POST['l_month'],$_POST['l_day'],$_POST['l_year']);
		if(checkdate($_POST['l_month'],$_POST['l_day'],$_POST['l_year']))
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
				$get_match=$_con->prepare("select * from matchday where mday=? order by league asc,mkick asc;");
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
						$tr.='<tr><td colspan="6" class="edit_lea">'.$league_en.'</td></tr>';
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
					$tr.='<tr id="emess'.$mid.'" data="'.$mess_edit.'"><td class="edit_kick">'.$kick_time.'</td><td class="edit_home">'.$home_en.'</td><td class="edit_handicap">'.$handicap.'</td><td class="edit_away">'.$away_en.'</td><td class="edit_txt"><span class="link" onclick="edit_match('.$mid.')">แก้ไข</span></td><td><span class="linkred" onclick="del_match('.$mid.')">ลบ</span><span id="ew'.$mid.'" class="ew"></span></td></tr>';
				}
				echo $tr;
			}
		}
	}
	//==========================แก้ไข match==================================

if(isset($_POST['editmatch']) && isset($_POST['t_day_']) && isset($_POST['t_month_'])  && isset($_POST['t_year_'])  && isset($_POST['k_day_'])  && isset($_POST['k_month_'])  && isset($_POST['k_year_'])  && isset($_POST['k_time_'])  && isset($_POST['nfield_'])  && isset($_POST['l_id_'])  && isset($_POST['h_id_'])  && isset($_POST['a_id_'])  && isset($_POST['advan_'])  && isset($_POST['handicap_'] ))
	{
		
	if(isStrnum($_POST['editmatch']) &&isStrnum($_POST['t_day_']) && isStrnum($_POST['t_month_'])  && isStrnum($_POST['t_year_'])  && isStrnum($_POST['k_day_'])  && isStrnum($_POST['k_month_'])  && isStrnum($_POST['k_year_'])  && isStrnum($_POST['k_time_'])  && isStrnum($_POST['nfield_'])  && isStrnum($_POST['l_id_'])  && isStrnum($_POST['h_id_'])  && isStrnum($_POST['a_id_']))
	{
		$editmatch=$_POST['editmatch'];
		$check=0;
		if(checkdate($_POST['t_month_'],$_POST['t_day_'],$_POST['t_year_']))
		{
			$mk_day=mktime(0,0,0,$_POST['t_month_'],$_POST['t_day_'],$_POST['t_year_']);
			if(checkdate($_POST['k_month_'],$_POST['k_day_'],$_POST['k_year_']))
			{
				$min=ltrim_zero(mb_substr($_POST['k_time_'],2));
				$hour=ltrim_zero(mb_substr($_POST['k_time_'],0,2));
				if($min >=0 && $min<60 && $hour >=0 && $hour <24)
				{
					$mk_kick=mktime($hour,$min,0,$_POST['k_month_'],$_POST['k_day_'],$_POST['k_year_']);
					$check=1;
				}
			}
		}

		if($check==1)
		{
			$nfield=$_POST['nfield_'];
			$l_id=$_POST['l_id_'];
			$h_id=$_POST['h_id_'];
			$a_id=$_POST['a_id_'];
			if(($nfield==0 || $nfield==1) && isDecimal($l_id) && isDecimal($h_id) && isDecimal($a_id) && $h_id != $a_id)
			{
				
				$have_l=$_con->prepare("select id from league where id=?;");
				$have_l->execute(array($l_id));
				if($have_l->rowCount())
				{ 
					$have_t=$_con->prepare("select id from team where id=?;");
					$have_t->execute(array($h_id));
					$have_home=$have_t->rowCount();
					$have_t->execute(array($a_id));
					$have_away=$have_t->rowCount();
					if($have_home==1 && $have_away==1) { $check=2;}
				}
			}
		}
		else { echo 't';}

		if($check==2)
		{
			$editmatch+=0;$h_id+=0;$l_id+=0;$mk_day+=0;
			$ta=$_con->prepare("select id from matchday where id <> ? and mday=? and (home_team in(?,?) OR away_team in (?,?));");
			$ta->execute(array($editmatch,$mk_day,$h_id,$a_id,$h_id,$a_id));
			if($ta->rowCount()) { echo 'h';} else { $check=3; }
		}
		if($check==3)
		{
			$advan=$_POST['advan_'];
			if($advan==1 || $advan==2)
			{
				$handicap=$_POST['handicap_'];
				$pat="^[0-9][0-9]*([\.](0|00|25|5|50|75))*$";
				if(mb_ereg($pat,$handicap))
				{
					$check=4;
				}
			}
		}
		if($check==4)
		{
			$_save=$_con->prepare("update matchday set mday=?,league=?,mkick=?,nfield=?,home_team=?,away_team=?,advan=?,handicap=? where id=?;");
			$data_in=array($mk_day,$l_id,$mk_kick,$nfield,$h_id,$a_id,$advan,$handicap,$editmatch);
			if($_save->execute($data_in))
			{
				echo 'c';
			}
		}
	}
	}//end edit match

if(isset($_POST['del_match']))
	{
		$del_id=$_POST['del_match'];
		if(isDecimal($del_id))
		{
			$get_bill0=$_con->prepare("select id from bill where match_id=?;");
			$get_bill0->execute(array($del_id));
			$get_bill1=$_con->prepare("select id from bill_tded where match_id=?;");
			$get_bill1->execute(array($del_id));
			$get_bill2=$_con->prepare("select bill from mixparlay where match_id=?;");
			$get_bill2->execute(array($del_id));

			if($get_bill0->rowCount() || $get_bill1->rowCount() || $get_bill2->rowCount()){} // ไม่สามารถลบได้ หากมีการทายผลเกิดขึ้นในระบบ
			else
			{
				$del=$_con->prepare("delete from matchday where id=?;");
				if($del->execute(array($del_id)))
				{
					echo 'd';
				}
			}
		}
	}

} // end matchtable.php


//============maket.php=================
if($page=='addmarket')
{

if(isset($_POST['market_day']) &&isset($_POST['market_month']) && isset($_POST['market_year']) && isStrnum($_POST['market_day']) && isStrnum($_POST['market_month']) && isStrnum($_POST['market_year'])) // โหลดตาราง ตลาด
	{
		$mk_load=mktime(0,0,0,$_POST['market_month'],$_POST['market_day'],$_POST['market_year']);
		if(checkdate($_POST['market_month'],$_POST['market_day'],$_POST['market_year']))
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
				$get_match=$_con->prepare("select * from matchday where mday=? order by league asc,mkick asc;");
				$get_match->execute(array($mk_load));
				$m_data=$get_match->fetchAll();
				$cur_l=0;

				$get_market=$_con->prepare("select * from market where match_id in (select id from matchday where mday=?) order by match_id asc,market_type asc;");
				$get_market->execute(array($mk_load));
				$m_rows=$get_market->rowCount();				
				$market_data=$get_market->fetchAll();
				
				$del_img=$_url.'images/del.png';
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
						$tr.='<tr><td colspan="6" class="edit_lea">'.$league_en.'</td></tr>';
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
								if($m1_v1<0) { $m1_v1=mb_ereg_replace('-','',$m1_v1);$m1_v1='<br>'.$m1_v1; $home_en2='<span class="red_t">'.$home_en2.'</span>';}
								if($m1_v1>0) { $away_en2='<span class="red_t">'.$away_en2.'</span>'; }
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',1,\''.$m1_v1_.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',1,\''.$m1_v1_.'\',\''.$m1_v2.'\',\''.$m1_v3.'\')"';
								$m1_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m1_v1.'</span><span class="odds_box">'.$m1_v2.'<br>'.$m1_v3.'</span>'.$del_f.'</span>';
							}else {$m1_box='<span class="box_frame"></span>';}

							if(isset($market2[$make_row])) //ft:over/under
							{ $m2_v1=$market2[$make_row][0]; $m2_v2=$market2[$make_row][1]; $m2_v3=$market2[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',2,\''.$m2_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',2,\''.$m2_v1.'\',\''.$m2_v2.'\',\''.$m2_v3.'\')"';
								$m2_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m2_v1.'</span><span class="odds_box">'.$m2_v2.'<br>'.$m2_v3.'</span>'.$del_f.'</span>';
							}else {$m2_box='<span class="box_frame"></span>';}

							if(isset($market3[$make_row])) //ft:1x2
							{ $m3_v1=$market3[$make_row][0]; $m3_v2=$market3[$make_row][1]; $m3_v3=$market3[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',3,\''.$m3_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',3,\''.$m3_v1.'\',\''.$m3_v2.'\',\''.$m3_v3.'\')"';
								$m3_box='<span class="box_frame"'.$edit_odds.'><span class="odds_box">'.$m3_v1.'<br>'.$m3_v2.'<br>'.$m3_v3.'</span>'.$del_f.'</span>';
							}else {$m3_box='<span class="box_frame"></span>';}
							//-----------------------------------------------------------
							if(isset($market4[$make_row]))  //fh:handicap
							{ $m4_v1=$market4[$make_row][0]; $m4_v2=$market4[$make_row][1]; $m4_v3=$market4[$make_row][2];		
								$m4_v1_=$m4_v1;
								if($m4_v1<0) { $m4_v1=mb_ereg_replace('-','',$m4_v1);$m4_v1='<br>'.$m4_v1;}
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',4,\''.$m4_v1_.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',4,\''.$m4_v1_.'\',\''.$m4_v2.'\',\''.$m4_v3.'\')"';
								$m4_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m4_v1.'</span><span class="odds_box">'.$m4_v2.'<br>'.$m4_v3.'</span>'.$del_f.'</span>';
							}else {$m4_box='<span class="box_frame"></span>';}

							if(isset($market5[$make_row])) //fh:over/under
							{ $m5_v1=$market5[$make_row][0]; $m5_v2=$market5[$make_row][1]; $m5_v3=$market5[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',5,\''.$m5_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',5,\''.$m5_v1.'\',\''.$m5_v2.'\',\''.$m5_v3.'\')"';
								$m5_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m5_v1.'</span><span class="odds_box">'.$m5_v2.'<br>'.$m5_v3.'</span>'.$del_f.'</span>';
							}else {$m5_box='<span class="box_frame"></span>';}

							if(isset($market6[$make_row])) //fh:1x2
							{ $m6_v1=$market6[$make_row][0]; $m6_v2=$market6[$make_row][1]; $m6_v3=$market6[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',6,\''.$m6_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',6,\''.$m6_v1.'\',\''.$m6_v2.'\',\''.$m6_v3.'\')"';
								$m6_box='<span class="box_frame"'.$edit_odds.'><span class="odds_box">'.$m6_v1.'<br>'.$m6_v2.'<br>'.$m6_v3.'</span>'.$del_f.'</span>';
							}else {$m6_box='<span class="box_frame"></span>';}

							$cur_match_td='<td class="team_en" valign="top">'.$home_en2.'<br>'.$away_en2.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="edit_kick">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="td_fulltime" valign="top">'.$m1_box.$m2_box.$m3_box.'</td>
							<td class="td_fulltime" valign="top">'.$m4_box.$m5_box.$m6_box.'</td>
							<td class="edit_txt"><span class="link" onclick="addmarket('.$mid.')">เพิ่มราคา</span></td>	</tr>';

						}
						unset($market1);unset($market2);unset($market3);unset($market4);unset($market5);unset($market6);
						
						if($max_count==0) 
						{
							$cur_match_td='<td class="team_en" valign="top">'.$home_en.'<br>'.$away_en.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="edit_kick">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="td_fulltime" valign="top"><span class="box_frame"></span></td>
							<td class="td_fulltime" valign="top"></td>
							<td class="edit_txt"><span class="link" onclick="addmarket('.$mid.')">เพิ่มราคา</span></td>
						</tr>';

						}
						if($team_bg_class==0){$team_bg_class=1;} 
						else{ 	if($team_bg_class==1){$team_bg_class=0;}  }
					
					//==============end scan market======================
					}
				echo $tr;
			}
		}
	} // end load market

//===============เพิ่มราคาบอล====================
if(isset($_POST['create_market']) && isset($_POST['market_type']) && isset($_POST['v1'])&& isset($_POST['v2'])&& isset($_POST['v3']))
	{
		$type=$_POST['market_type'];
		$match_id=$_POST['create_market'];
		
		$have_m=0;
		if(isDecimal($match_id) && isDecimal($type))
		{
			$cm=$_con->prepare("select id from matchday where id=?;");
			$cm->execute(array($match_id));
			if($cm->rowCount()) { $have_m=1;}
		}
		//-----------------------------------------------------------------------------------------------------------------
		if(($type==1 || $type==4) && $have_m==1) // FT:Handicap
		{ 
				$handicap=$_POST['v1'];
				$home_odds=$_POST['v2'];
				$away_odds=$_POST['v3'];

			if(mb_ereg($_s_handicap_pat,$handicap) && mb_ereg($_odds_pat,$home_odds) && mb_ereg($_odds_pat,$away_odds))
			{
				$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
				$get_market->execute(array($match_id,$type,$handicap));
				if($get_market->rowCount())
				{
					$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
					if($update->execute(array($home_odds,$away_odds,$match_id,$type,$handicap))) { echo 'u';}
				}
				else
				{
					$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
					if($insert->execute(array($match_id,$type,$handicap,$home_odds,$away_odds))) {echo 'i'; }
				}				
			}
		}
		//-------------------------------------------------------------------------------------------------------------------
		if(($type==2 || $type==5) && $have_m==1) // FT:Over/under
		{
				$gold=$_POST['v1'];
				$o_odds=$_POST['v2'];
				$u_odds=$_POST['v3'];

			if(mb_ereg($_handicap_pat,$gold) && mb_ereg($_odds_pat,$o_odds) && mb_ereg($_odds_pat,$u_odds))
			{				
				$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
				$get_market->execute(array($match_id,$type,$gold));
				if($get_market->rowCount())
				{
					$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
					if($update->execute(array($o_odds,$u_odds,$match_id,$type,$gold))) { echo 'u';}
				}
				else
				{
					$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
					if($insert->execute(array($match_id,$type,$gold,$o_odds,$u_odds))) {echo 'i'; }
				}				
			}
		}
		//-------------------------------------------------------------------------------------------------------------------
		if(($type==3 || $type==6) && $have_m==1) // FT:1x2
		{
				$h_odds=$_POST['v1'];
				$a_odds=$_POST['v2'];
				$d_odds=$_POST['v3'];

			if(mb_ereg($_odds_pat,$h_odds) && mb_ereg($_odds_pat,$a_odds) && mb_ereg($_odds_pat,$d_odds))
			{				
				$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=?;");
				$get_market->execute(array($match_id,$type));
				if($get_market->rowCount())
				{
					$update=$_con->prepare("update market set value1=?,value2=?,value3=? where match_id=? and market_type=?;");
					if($update->execute(array($h_odds,$a_odds,$d_odds,$match_id,$type))) { echo 'u';}
				}
				else
				{
					$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
					if($insert->execute(array($match_id,$type,$h_odds,$a_odds,$d_odds))) {echo 'i'; }
				}				
			}
		}
	}

//==================โหลด market ทีมเดียว================================
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
				$get_match=$_con->prepare("select * from matchday where id=? order by league asc,mkick asc;");
				$get_match->execute(array($match_id));
				$m_data=$get_match->fetchAll();

				$get_market=$_con->prepare("select * from market where match_id =? order by market_type asc;");
				$get_market->execute(array($match_id));
				$m_rows=$get_market->rowCount();				
				$market_data=$get_market->fetchAll();
				$del_img=$_url.'images/del.png';
				$team_bg_class=0;
				for($mi=0;$mi<$get_match->rowCount();$mi++)
				{
					$mid=$m_data[$mi]['id'];
					$mday=$m_data[$mi]['mday'];
					$mkick=$m_data[$mi]['mkick'];
					$league=$m_data[$mi]['league'];
					$nfield=$m_data[$mi]['nfield'];
					$home_t=$m_data[$mi]['home_team'];
					$away_t=$m_data[$mi]['away_team'];
					$advan=$m_data[$mi]['advan'];
					$handicap=$m_data[$mi]['handicap'];	
					
					$kick_time=date("H",$mkick).':'.date("i",$mkick);
					
					$home_en=$t_en_buffer[$home_t]; $h_en_e=$home_en;
					$away_en=$t_en_buffer[$away_t];  $a_en_e=$away_en;

					if($hadicap=0.00) {$handicap=0;}
					if($nfield==1) {$home_en.=' (n)';}

					//==========for edit data============
					$txt_day=date("j",$mday);
					$txt_month=date("n",$mday);
					$txt_year=date("Y",$mday);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);

					$mess_edit=$txt_day.'|'.$txt_month.'|'.$txt_year.'|'.$k_day.'|'.$k_month.'|'.$k_year.'|||'.$kick_edit.'|'.$nfield.'|'.$home_t.'|'.$h_en_e.'|'.$away_t.'|'.$a_en_e.'|'.$advan.'|'.$handicap;

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
								if($m1_v1<0) { $m1_v1=mb_ereg_replace('-','',$m1_v1);$m1_v1='<br>'.$m1_v1; $home_en2='<span class="red_t">'.$home_en2.'</span>';}
								if($m1_v1>0) { $away_en2='<span class="red_t">'.$away_en2.'</span>'; }
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',1,\''.$m1_v1_.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',1,\''.$m1_v1_.'\',\''.$m1_v2.'\',\''.$m1_v3.'\')"';
								$m1_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m1_v1.'</span><span class="odds_box">'.$m1_v2.'<br>'.$m1_v3.'</span>'.$del_f.'</span>';
							}else {$m1_box='<span class="box_frame"></span>';}

							if(isset($market2[$make_row])) //ft:over/under
							{ $m2_v1=$market2[$make_row][0]; $m2_v2=$market2[$make_row][1]; $m2_v3=$market2[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',2,\''.$m2_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',2,\''.$m2_v1.'\',\''.$m2_v2.'\',\''.$m2_v3.'\')"';
								$m2_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m2_v1.'</span><span class="odds_box">'.$m2_v2.'<br>'.$m2_v3.'</span>'.$del_f.'</span>';
							}else {$m2_box='<span class="box_frame"></span>';}

							if(isset($market3[$make_row])) //ft:1x2
							{ $m3_v1=$market3[$make_row][0]; $m3_v2=$market3[$make_row][1]; $m3_v3=$market3[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',3,\''.$m3_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',3,\''.$m3_v1.'\',\''.$m3_v2.'\',\''.$m3_v3.'\')"';
								$m3_box='<span class="box_frame"'.$edit_odds.'><span class="odds_box">'.$m3_v1.'<br>'.$m3_v2.'<br>'.$m3_v3.'</span>'.$del_f.'</span>';
							}else {$m3_box='<span class="box_frame"></span>';}
							//-----------------------------------------------------------
							if(isset($market4[$make_row]))  //fh:handicap
							{ $m4_v1=$market4[$make_row][0]; $m4_v2=$market4[$make_row][1]; $m4_v3=$market4[$make_row][2];		
								$m4_v1_=$m4_v1;
								if($m4_v1<0) { $m4_v1=mb_ereg_replace('-','',$m4_v1);$m4_v1='<br>'.$m4_v1;}
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',4,\''.$m4_v1_.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',4,\''.$m4_v1_.'\',\''.$m4_v2.'\',\''.$m4_v3.'\')"';
								$m4_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m4_v1.'</span><span class="odds_box">'.$m4_v2.'<br>'.$m4_v3.'</span>'.$del_f.'</span>';
							}else {$m4_box='<span class="box_frame"></span>';}

							if(isset($market5[$make_row])) //fh:over/under
							{ $m5_v1=$market5[$make_row][0]; $m5_v2=$market5[$make_row][1]; $m5_v3=$market5[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',5,\''.$m5_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',5,\''.$m5_v1.'\',\''.$m5_v2.'\',\''.$m5_v3.'\')"';
								$m5_box='<span class="box_frame"'.$edit_odds.'><span class="hdc_box">'.$m5_v1.'</span><span class="odds_box">'.$m5_v2.'<br>'.$m5_v3.'</span>'.$del_f.'</span>';
							}else {$m5_box='<span class="box_frame"></span>';}

							if(isset($market6[$make_row])) //fh:1x2
							{ $m6_v1=$market6[$make_row][0]; $m6_v2=$market6[$make_row][1]; $m6_v3=$market6[$make_row][2];	
								$del_f='<img src="'.$del_img.'" class="market_del" onclick="del_market('.$mid.',6,\''.$m6_v1.'\',event)">';
								$edit_odds=' onclick="edit_odds('.$mid.',6,\''.$m6_v1.'\',\''.$m6_v2.'\',\''.$m6_v3.'\')"';
								$m6_box='<span class="box_frame"'.$edit_odds.'><span class="odds_box">'.$m6_v1.'<br>'.$m6_v2.'<br>'.$m6_v3.'</span>'.$del_f.'</span>';
							}else {$m6_box='<span class="box_frame"></span>';}

							$cur_match_td='<td class="team_en" valign="top">'.$home_en2.'<br>'.$away_en2.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="edit_kick">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="td_fulltime" valign="top">'.$m1_box.$m2_box.$m3_box.'</td>
							<td class="td_fulltime" valign="top">'.$m4_box.$m5_box.$m6_box.'</td>
							<td class="edit_txt"><span class="link" onclick="addmarket('.$mid.')">เพิ่มราคา</span></td>	</tr>';

						}
						unset($market1);unset($market2);unset($market3);unset($market4);unset($market5);unset($market6);
						
						if($max_count==0) 
						{
							$cur_match_td='<td class="team_en" valign="top">'.$home_en.'<br>'.$away_en.'</td>';
							$tr.='<tr mid="'.$mid.'" data="'.$mess_edit.'" class="league_bg'.$team_bg_class.'">
							<td class="edit_kick">'.$kick_time.'</td>'.$cur_match_td.'
							<td class="td_fulltime" valign="top"><span class="box_frame"></span></td>
							<td class="td_fulltime" valign="top"></td>
							<td class="edit_txt"><span class="link" onclick="addmarket('.$mid.')">เพิ่มราคา</span></td>
						</tr>';

						}
						if($team_bg_class==0){$team_bg_class=1;} 
						else{ 	if($team_bg_class==1){$team_bg_class=0;}  }
					
					//==============end scan market======================
					}
				echo $tr;
			
		
	} // end load market

	if(isset($_POST['market_del_id']) && isset($_POST['market_del_type']) && isset($_POST['market_del_value']))
	{
		$del_id=$_POST['market_del_id'];
		$del_type=$_POST['market_del_type'];
		$del_value=$_POST['market_del_value'];

		//echo $del_id,'delid';
		if(isDecimal($del_id))
		{
			if($del_type==1 || $del_type==4)
			{				
				if(mb_ereg($_handicap_pat,$del_value))
				{	
					
					$del=$_con->prepare("delete from market where match_id=? and market_type=? and value1=?;");
					if($del->execute(array($del_id,$del_type,$del_value))) {echo 'd';}
				}
			}
			if($del_type==2 || $del_type==5)
			{
				if(mb_ereg($_handicap_pat,$del_value))
				{
					$del=$_con->prepare("delete from market where match_id=? and market_type=? and value1=?;");
					if($del->execute(array($del_id,$del_type,$del_value))) {echo 'd';}
				}
			}
			if($del_type==3 || $del_type==6)
			{
					$del=$_con->prepare("delete from market where match_id=? and market_type=?;");
					if($del->execute(array($del_id,$del_type))) {echo 'd';}				
			}
		}
	}
} //end page addmarket

//===================โหลดตารางใส่ สกอร์====================
if($page=='score')
{
	if(isset($_POST['l_day']) &&isset($_POST['l_month']) && isset($_POST['l_year']) && isStrnum($_POST['l_day']) && isStrnum($_POST['l_month']) && isStrnum($_POST['l_year']))
	{
		$mk_load=mktime(0,0,0,$_POST['l_month'],$_POST['l_day'],$_POST['l_year']);
		if(checkdate($_POST['l_month'],$_POST['l_day'],$_POST['l_year']))
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

					$get_bill0=$_con->query("select id from bill where match_id=$mid limit 1;");
					$get_bill1=$_con->query("select id from bill_tded where match_id=$mid limit 1;");
					$get_bill2=$_con->query("select bill from mixparlay where match_id=$mid limit 1;");
			
					if($get_bill0->rowCount() || $get_bill1->rowCount() ||$get_bill2->rowCount()) { $h_style=' style="background:green;"';} 
					else {$h_style='';}

					//==========for edit data============
					$txt_day=date("j",$mk_load);
					$txt_month=date("n",$mk_load);
					$txt_year=date("Y",$mk_load);
					
					$k_day=date("j",$mkick);
					$k_month=date("n",$mkick);
					$k_year=date("Y",$mkick);
					
					$kick_edit=date("H",$mkick).date("i",$mkick);


					if($m_finish==1)
					{
						$value1=' value="'.$fh_h_score.'"'; $value2=' value="'.$fh_a_score.'"'; $value3=' value="'.$ft_h_score.'"'; $value4=' value="'.$ft_a_score.'"'; $tr_class=' class="a_finish"';
					}
					else { $value1=$value2=$value3=$value4=''; $tr_class="";}

					$tr.='<tr id="emess'.$mid.'"'.$tr_class.'>
					<td class="edit_kick">'.$kick_time.'</td>
					<td><button onclick="delay(\''.$mid.'\')">เลื่อน</button></td>
					<td class="edit_home">'.$home_en.'</td>
					<td class="edit_handicap"'.$h_style.'>'.$handicap.'</td>
					<td class="edit_away">'.$away_en.'</td>
					<td>HT <input type="text" id="fh_h_score'.$mid.'"'.$value1.'> <input type="text" id="fh_a_score'.$mid.'"'.$value2.'></td>
					<td>FT <input type="text" id="ft_h_score'.$mid.'"'.$value3.'> <input type="text" id="ft_a_score'.$mid.'"'.$value4.'></td>
					<td><button onclick="send_score(\''.$mid.'\')" id="score_bt'.$mid.'">บันทึก</button></td>
					<td><span id="score_warn'.$mid.'" class="score_warn"></span></td>
					</tr>';
				}
				echo $tr;
			}
		}
	}

if(isset($_POST['save_score']) && isset($_POST['fh_a'])&& isset($_POST['fh_h'])&& isset($_POST['fh_h'])&& isset($_POST['ft_a']) && isStrnum($_POST['save_score']) && isStrnum($_POST['fh_a'])&& isStrnum($_POST['fh_h'])&& isStrnum($_POST['fh_h'])&& isStrnum($_POST['ft_a']))
	{
	
		$mid=$_POST['save_score'];
		$fh_h=$_POST['fh_h'];
		$fh_a=$_POST['fh_a'];
		$ft_h=$_POST['ft_h'];
		$ft_a=$_POST['ft_a'];

		$has_bill=0;
		if($ft_h>=$fh_h && $ft_a>=$fh_a)
		{
			$update_score=$_con->prepare("update matchday set fh_h_score=?,fh_a_score=?,ft_h_score=?,ft_a_score=?,m_finish=1,m_delay=0 where id=?;");
			if($update_score->execute(array($fh_h,$fh_a,$ft_h,$ft_a,$mid)))
			{	echo 'f';
				$get_bill=$_con->prepare("select * from bill where match_id=?;");
				$get_bill->execute(array($mid));
				if($get_bill->rowCount()) 
				{
					$has_bill=$get_bill->rowCount();
					$bill_data=$get_bill->fetchAll();
				}
			}
		}

		if($has_bill)
		{
			for($bi=0;$bi<$has_bill;$bi++)
			{
				$bill_id=$bill_data[$bi]['id'];
				$bill_owner=$bill_data[$bi]['owner'];
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

				if($g_type==1) //=====ft hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$ft_h; 
						$a_sum_goal=$ft_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$ft_h;
						$a_sum_goal=$g_handicap+$ft_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================
				if($g_type==4) //=====fh hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$fh_h; 
						$a_sum_goal=$fh_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$fh_h;
						$a_sum_goal=$g_handicap+$fh_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================

				//=============ft o/u===============
				if($g_type==2)
				{
					$total_goal=$ft_h+$ft_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 2
				if($g_type==5)
				{
					$total_goal=$fh_h+$fh_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 5

				if($g_type==3)
				{
					if($g_team==1)
					{
						if($ft_h>$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($ft_h<$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($ft_h==$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}
				if($g_type==6)
				{
					if($g_team==1)
					{
						if($fh_h>$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($fh_h<$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($fh_h==$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}

					if($win_result==0){$new_stake_result=0;}
					if($win_result>0){$new_stake_result=$win_result*$g_stake*$g_odds;}
					if($win_result<0){$new_stake_result=$win_result*$g_stake*1;}

					if($is_check==0){$stake_to_member=$new_stake_result;}
					if($is_check==1){$stake_to_member=$new_stake_result+invert_value($stake_result);}

					$update_bill=$_con->prepare("update bill set g_result=?,is_check=?,stake_result=? where id=?;");
					if($update_bill->execute(array($win_result,1,$new_stake_result,$bill_id)))
					{
						$_con->exec("update member_ set score=score+$stake_to_member where id=$bill_owner;");	
					}


			} ///end for bill
		}

		//============เช็คบิล ของเซียนประจำเว็บ=================================
		$get_bill=$_con->prepare("select * from bill_tded where match_id=?;");
		$get_bill->execute(array($mid));
		$has_bill=$get_bill->rowCount();
		$bill_data=$get_bill->fetchAll();

		if($has_bill)
		{
			for($bi=0;$bi<$has_bill;$bi++)
			{
				$bill_id=$bill_data[$bi]['id'];
				$bill_owner=$bill_data[$bi]['owner'];
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

				if($g_type==1) //=====ft hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$ft_h; 
						$a_sum_goal=$ft_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$ft_h;
						$a_sum_goal=$g_handicap+$ft_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================
				if($g_type==4) //=====fh hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$fh_h; 
						$a_sum_goal=$fh_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$fh_h;
						$a_sum_goal=$g_handicap+$fh_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================

				//=============ft o/u===============
				if($g_type==2)
				{
					$total_goal=$ft_h+$ft_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 2
				if($g_type==5)
				{
					$total_goal=$fh_h+$fh_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 5

				if($g_type==3)
				{
					if($g_team==1)
					{
						if($ft_h>$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($ft_h<$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($ft_h==$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}
				if($g_type==6)
				{
					if($g_team==1)
					{
						if($fh_h>$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($fh_h<$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($fh_h==$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}

					if($win_result==0){$new_stake_result=0;}
					if($win_result>0){$new_stake_result=$win_result*$g_stake*$g_odds;}
					if($win_result<0){$new_stake_result=$win_result*$g_stake*1;}

					$update_bill=$_con->prepare("update bill_tded set g_result=?,is_check=?,stake_result=? where id=?;");
					$update_bill->execute(array($win_result,1,$new_stake_result,$bill_id));


			} ///end for bill_tded
		} //end has bill_tded
				
		//============เช็คบิล mixparlay=====================

		$cur_bill=0;
		$bill_change=null;
		$get_bill=$_con->prepare("select * from mixparlay where match_id=?;");
		$get_bill->execute(array($mid));
		$has_bill=$get_bill->rowCount();
		$bill_data=$get_bill->fetchAll();

		if($has_bill)
		{
			for($bi=0;$bi<$has_bill;$bi++)
			{
				$bid=$bill_data[$bi]['id'];
				$bill_id=$bill_data[$bi]['bill'];
				if($cur_bill != $bill_id)
				{
					$bill_change[]=$bill_id;
					$cur_bill=$bill_id;
				}

				$bill_owner=$bill_data[$bi]['owner'];
				$match_id=$bill_data[$bi]['match_id'];
				$g_type=$bill_data[$bi]['g_type'];
				$g_handicap=$bill_data[$bi]['g_handicap'];
				$g_team=$bill_data[$bi]['g_team'];
				$g_odds=$bill_data[$bi]['g_odds'];
				$g_result=$bill_data[$bi]['g_result'];
				$is_check=$bill_data[$bi]['is_check'];
				$stake_result=$bill_data[$bi]['stake_result'];

				if($g_type==1) //=====ft hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$ft_h; 
						$a_sum_goal=$ft_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$ft_h;
						$a_sum_goal=$g_handicap+$ft_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================
				if($g_type==4) //=====fh hdc===========
				{
					if($g_team==1)
					{ 
						$h_sum_goal=$g_handicap+$fh_h; 
						$a_sum_goal=$fh_a;
						$diff_goal=$h_sum_goal-$a_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{ 
						$h_sum_goal=$fh_h;
						$a_sum_goal=$g_handicap+$fh_a;
						$diff_goal=$a_sum_goal-$h_sum_goal;
						if($diff_goal==0){ $win_result=0;}
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					
				}//============================

				//=============ft o/u===============
				if($g_type==2)
				{
					$total_goal=$ft_h+$ft_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 2
				if($g_type==5)
				{
					$total_goal=$fh_h+$fh_a;
					$diff_goal=$total_goal-$g_handicap;
					if($diff_goal==0){$win_result=0;}

					if($g_team==1)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=0.5;}
							if($diff_goal>=0.5) {$win_result=1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=-0.5;}
							if($diff_goal<=-0.5) {$win_result=-1.0;}
						}
					}
					if($g_team==2)
					{
						if($diff_goal>0)
						{	
							if($diff_goal==0.25) {$win_result=-0.5;}
							if($diff_goal>=0.5) {$win_result=-1.0;}
						}
						if($diff_goal<0)
						{
							if($diff_goal==-0.25) {$win_result=0.5;}
							if($diff_goal<=-0.5) {$win_result=1.0;}
						}
					}

				} //======= end g_type 5

				if($g_type==3)
				{
					if($g_team==1)
					{
						if($ft_h>$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($ft_h<$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($ft_h==$ft_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}
				if($g_type==6)
				{
					if($g_team==1)
					{
						if($fh_h>$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==2)
					{
						if($fh_h<$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
					if($g_team==0)
					{
						if($fh_h==$fh_a){ $win_result=1.0;} else{$win_result=-1.0;}
					}
				}

					if($win_result==0){$new_stake_result=0;}
					if($win_result>0){$new_stake_result=$win_result*$g_odds;}
					if($win_result<0){$new_stake_result=$win_result*1;}

					$update_bill=$_con->prepare("update mixparlay set g_result=?,is_check=?,stake_result=? where id=?;");
					$update_bill->execute(array($win_result,1,$new_stake_result,$bid));


			} ///end for mixparlay
		} //end has mixparlay

			$c_count=count($bill_change); // ตรวจสอบบอล ชุด ว่าแข่งขันจบทุกคู่หรือยัง
			for($ci=0;$ci<$c_count;$ci++)
			{
				$bill_index=$bill_change[$ci];
				$get_mix=$_con->query("select * from mixparlay where bill=$bill_index;");
				$mix_rows=$get_mix->rowCount();
				if($mix_rows)
				{
					$mix_data=$get_mix->fetchAll();
					$all_check=1;
					$sum_stake=0;
					$cur_win=1;
					for($mi=0;$mi<$mix_rows;$mi++)
					{
						$m_check=$mix_data[$mi]['is_check'];
						$m_result=$mix_data[$mi]['stake_result'];
						$m_win=$mix_data[$mi]['g_result'];
						if($m_check==0) {$all_check=0; break;}
						$sum_stake+=$m_result;
						if($m_win<$cur_win){$cur_win=$m_win;}
						
					}
					
					if($all_check==1)
					{
						$bill_parent=$_con->query("select * from bill where id=$bill_index;");
						$p_rows=$bill_parent->rowCount();
						if($p_rows)
						{
							$p_data=$bill_parent->fetchAll();
							$p_owner=$p_data[0]['owner'];
							$p_check=$p_data[0]['is_check'];
							$p_stake=$p_data[0]['stake_result'];

							if($cur_win<0){$p_win=-1.0;}
							if($cur_win==0 || $cur_win==0.5) {$p_win=0.5;}
							if($cur_win==1){$p_win=1.0;}

							if($p_check==0){$stake_to_member=$sum_stake;}
							if($p_check==1){$stake_to_member=$sum_stake+invert_value($p_stake);}
							echo $stake_to_member;
							//echo $p_check.'|'.$p_stake.'|'.$stake_to_member;
							$update_bill=$_con->prepare("update bill set g_result=?,is_check=?,stake_result=? where id=?;");
							$update_bill->execute(array($p_win,1,$sum_stake,$bill_index));
							$stake_to_member+=0;
							echo $stake_to_member;
							$up_user=$_con->prepare("update member_ set score_mix=score_mix+? where id=?;");	
							$up_user->execute(array($stake_to_member,$p_owner));
							
						}
					}
				}
			}
	}

//========================================
if(isset($_POST['set_delay']) && isDecimal($_POST['set_delay'])) // เซตแมท เมื่อมีการเลื่อนการแข่งขัน
	{
			$mid=$_POST['set_delay'];
			
			$has_bill=0;
			$update_score=$_con->prepare("update matchday set fh_h_score=0,fh_a_score=0,ft_h_score=0,ft_a_score=0,m_finish=1,m_delay=1 where id=?;");
			if($update_score->execute(array($mid)))
			{	
				echo 'f';
				$get_bill=$_con->prepare("select * from bill where match_id=?;");
				$get_bill->execute(array($mid));
				if($get_bill->rowCount()) 
				{
					$has_bill=$get_bill->rowCount();
					$bill_data=$get_bill->fetchAll();
				}
			}

			for($bi=0;$bi<$has_bill;$bi++)
			{
				$bill_id=$bill_data[$bi]['id'];
				$bill_owner=$bill_data[$bi]['owner'];
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

				
					if($is_check==0){$stake_to_member=0;}
					if($is_check==1){$stake_to_member=invert_value($stake_result);}

					$update_bill=$_con->prepare("update bill set g_result=?,is_check=?,stake_result=? where id=?;");
					if($update_bill->execute(array(0,1,0,$bill_id)))
					{
						$_con->exec("update member_ set score=score+$stake_to_member where id=$bill_owner;");	
					}

			}// end for bill	
	}
}// end page score

if($page=='resetpass')
{
	if(isset($_POST['find_name']))
	{
		$find_name=mb_trim($_POST['find_name']);
		
		$pat="^[a-zA-Z0-9ก-ูเ-์\ ]+$";
		if(mb_ereg($pat,$find_name) && mb_strlen($find_name)>2)
		{			
			$key_find='%'.$find_name.'%';
			$get_name=$_con->prepare("select id,user,name,phone from member_ where name like ? limit 10;");
			$get_name->execute(array($key_find));
			$rows=$get_name->rowCount();
			if($rows)
			{			
			$buffer='';
			$data=$get_name->fetchAll();
				for($l=0;$l<$rows;$l++)
				{
					$id=$data[$l]['id'];
					$user=$data[$l]['user'];
					$name=$data[$l]['name'];
					$phone=$data[$l]['phone'];
					$buffer.='<div class="guess_row" onclick="choose_name('.$id.',\''.$user.'\',\''.$name.'\',\''.$phone.'\')">'.$name.'</div>';
				}
				echo $buffer;
			}
		}
	}
//=====================================
if(isset($_POST['set_new_pass']) && isset($_POST['new_pass']) && isset($_POST['admin_pass']) && isDecimal($_POST['set_new_pass']) &&isAdmin(0))
	{
		$adminpass=sha1($_POST['admin_pass']);
		$uid=$_POST['set_new_pass'];

		$new_pass=sha1($_POST['new_pass']);

		$get_pass=$_con->query("select password from member_ where id=$_uid;");
		$p_data=$get_pass->fetchAll();
		$a_pass=$p_data[0]['password'];
		if($adminpass==$a_pass && mb_strlen($_POST['new_pass'])>5)
		{
			$re_pass=$_con->prepare("update member_ set password=? where id=?;");
			if($re_pass->execute(array($new_pass,$uid))) {echo 'r';}
		}
	}
}// end page resetpass

if($page=='reset')
{
	if(isset($_POST['reset_score'])&&isAdmin(0))
	{
		
		$check_bill=$_con->query("select id from bill where is_check=0;");
		if($check_bill->rowCount()){ echo 'b';}
		else
		{
			$day=date("j");
			if($day==1)
			{
				$_con->exec("update member_ set sum_score=sum_score+score;");
				$_con->exec("update member_ set score=0,score_mix=0;");
				echo 'o';
			}
		}
	}
}

//====================page tded ========================================
if($page=='tded')
{
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
					$count_bill=$_con->query("select id from bill_tded where owner=$_uid and mday=$mday;"); $bill_count=$count_bill->rowCount();
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
				}
	if($can_bill==1)
		{
			$ip=getip();
			$stake=1; // วางเงินเดิมพัน กรณีใช้ ใช้เครดิตแทง

			$insert=$_con->prepare("insert into bill_tded(owner,mday,mmonth,match_id,g_type,g_handicap,g_team,g_odds,stake,g_time,g_ip) values(?,?,?,?,?,?,?,?,?,?,?);");

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
//===========================================
if(isset($_POST['l_tded']))
{
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

	//===================
		$td1='<td class="t_avt"><div class="t_img_f"><img src="'.$_url.'image.php?p='.$tid.'"></div></td>';
		$td2='<td valign="top" class="guess_his">'.$link_alias.'</td>';
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
							$img='<img src="'.$_url.'images/del.png" onclick="del_tded('.$id.')" class="del_t">';
							$td4='<td class="guess_result">'.$result_txt.'<br>'.$ft_show.$img.'</td>';						
							
			echo '<tr id="tded'.$id.'">'.$td1.$td2.$td3.$td4.'</tr>';
		} // end for bill
	}else {$td3='<td class="guess_detail"></td>'; $td4='<td td class="guess_result"></td>';
		echo '<tr>'.$td1.$td2.$td3.$td4.'</tr>';
	}

	
}// end for ti
} // end have tripster

}
//==================================
if(isset($_POST['d_tded']) && isDecimal($_POST['d_tded']))
	{
		$del=$_POST['d_tded'];
		$pre=$_con->prepare("delete from bill_tded where id=?;");
		if($pre->execute(array($del))){echo 'd';}
	}
} // end page tded

$_con=null;
?>