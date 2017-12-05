<?php
require_once "ext.php";

function hdc_test($str)
{
	$handicap_pat="^[0-9\.\-]+$";
	if(mb_ereg($handicap_pat,$str))
	{ return 1;} 
	else {return 0;}
}

con_mysql();
session_pair();
if(islogin() && isAdmin(0)){}
else{$_con=null; exit();}

$pre_check=0;

if(isset($_POST['dom_day']) && isset($_POST['month'])&& isset($_POST['month'])&& isset($_POST['year'])&&isset($_FILES['domfile']))
{
	if(isDecimal($_POST['dom_day']) && isDecimal($_POST['month'])&& isDecimal($_POST['month'])&& isDecimal($_POST['year']))
	{	
		$month=$_POST['month'];
		$day=$_POST['dom_day'];
		$year=$_POST['year'];
		if(checkdate($month,$day,$year))
		{
			$mday=mktime(0,0,0,$month,$day,$year);
				$nq_name=uniqid();
                $dest_file='tmp/'.$nq_name.'.dom';
				 if(move_uploaded_file($_FILES['domfile']['tmp_name'],$dest_file))
				{
					$dom = new DOMDocument();
					libxml_use_internal_errors(true);
					$dom->loadHTMLFile($dest_file);
					if($dom->getElementById("events")) {$pre_check=1;
					$_con->exec("delete from dom_buffer;");
					}
					unlink($dest_file);
				}
		}
	}
}


if($pre_check==1)
{
$tr_league="bggroup rows"; //class ลีก bggroup rows 
$tr_match0="bgAltRow"; // class คู่แข่งขัน
$tr_match1="bgRow";


//$league_td="bg headbtmleft lgName";
//headbtmleft bg lgNameHDP
$td_league_name_0="bg headbtmleft lgNameHDP";
$td_league_name_1="headbtmleft bg lgNameHDP";
//$time_td="brg headbtmleft";

$td_time_name="brg headbtmleft timeStyle";
$div_fav="eventFavDbl2"; // class ทีมต่อ
$div_un_fav="eventnonFavDbl2"; // class ทีมรอง

$class_t1_v1_1="dvhdpH";
$class_t1_v1_2="dvhdpA bg";
$class_t1_v2="dvoddsRight  oddsPos"; // คลาส ออซเจ้าบ้าน
$class_t1_v21="soccer dvoddsRight  oddsPos";
$class_t1_v3="dvoddsRight  oddsPos   bg";
$class_t1_v31="soccer dvoddsRight  oddsPos   bg";// คลาส ออซทีมเยือน


$class_t2_v1="dvouhdpH";
$class_t2_v2="dvoddsRightOU  oddsPos";
$class_t2_v3="dvoddsRightOU  oddsPos   bg";

$class_t3_v1="dvodds1x2H";
$class_t3_v2="dvodds1x2A";
$class_t3_v3="dvodds1x2D";

// id "levents คือ div ที่กำลังแข่ง 

// id "events" คือ div ที่ยังไม่ได้แข่ง

$events=$dom->getElementById("events");

// echo $events->getElementsByTagName("table")->length.'table length<br>'; นับจำนวนแถวของ ตาราง

$table=$events->getElementsByTagName("table");
$table0=$table->item(0);

$tbody=$table0->getElementsByTagName("tbody");

$tbody_len=$table0->getElementsByTagName("tbody")->length;

$cur_league='';


for($tb=0;$tb<$tbody_len;$tb++) // =============================วนลูป tbdoy
{
$cur_tbody=$tbody->item($tb);

$all_tr=$cur_tbody->getElementsByTagName("tr");

$tr_count= $all_tr->length; //========== tr ทั้งหมด

// echo $tr_count.' tr_count tbody '.$tb.'<br>';

for($tri=0;$tri<$tr_count;$tri++)
{
	$cur_tr=$all_tr->item($tri);
	$tr_class=mb_trim($cur_tr->getAttribute("class"));
	//echo $tr_class.'<br>';
	$tr_type=null;

	if($tr_class==$tr_league) {$tr_type=0;}  // tr มันจะมีอยู่ 3 คลาส คลาส ลีก 1 คลาส ทีม2
	if($tr_class==$tr_match0 || $tr_class==$tr_match1) {$tr_type=1;}

		$all_td=$cur_tr->getElementsByTagName("td"); //========== td td ทั้งหมด
		$td_count=$all_td->length;

						//$cur_td=$all_td->item($tdi);

								

								if($tr_type==0) //============จัดการ td ทั้งหมด ของ Tr ลีคก์ =====================
								{
									$td0=$all_td->item(0);
									$td0_class=mb_trim($td0->getAttribute("class"));
									if($td0_class==$td_league_name_0 || $td0_class==$td_league_name_1)
										{	
											$cur_league=mb_trim($td0->textContent); // <<<<<<<<<<<<<<<<league name<<<<<<<<<<<<<<<<<<<<<<<<										
										}
								}

								if($tr_type==1) //=============td tr ของ คู่แข่งขัน==============================
								{

									$td0=$all_td->item(0); // td0 td เวลา
									$td0_class=mb_trim($td0->getAttribute("class"));
									//>>>>>>>>>> ดึงเวลาเตะ
									if($td0_class==$td_time_name) 
									{
										$all_time_div=$td0->getElementsByTagName("div");
										$div0=$all_time_div->item(0);
										$div1=$all_time_div->item(1);

										$cur_kicktime=$div1->textContent;
									}
									//>>>>>>>>>>>>>>>>>>> ดึง ทีมคู่แข่ง
									$td1=$all_td->item(1); //td1 td ทีมที่แข่ง
									$all_div_team=$td1->getElementsByTagName("div");
									$team_div0=$all_div_team->item(0);
									$team_div1=$all_div_team->item(1);
									$team0_class=mb_trim($team_div0->getAttribute("class"));
									$team1_class=mb_trim($team_div1->getAttribute("class"));
									$advan_team=0;
									if($team0_class=$div_fav) { $advan_team=1;}
									if($team1_class=$div_fav) { $advan_team=2;}

									$home_en=mb_trim($team_div0->textContent);
									$away_en=mb_trim($team_div1->textContent);


									//>>>>>>>>>>>>>>>>>>>td
									$td2=$all_td->item(3);

									$all2_div=$td2->getElementsByTagName("div");
									$div_len=$td2->getElementsByTagName("div")->length;
									
									$t1_v1=0.00; $t1_v2=0.00; $t1_v3=0.00;$t2_v1=0.00; $t2_v2=0.00; $t2_v3=0.00;$t3_v1=0.00; $t3_v2=0.00; $t3_v3=0.00; $advant_team=0;
									for($di=0;$di<$div_len;$di++)
									{   
										$cur_div=$all2_div->item($di);
										$d_class=mb_trim($cur_div->getAttribute("class"));

										if($d_class==$class_t1_v1_1) 
										{$t1_v1_inner=mb_trim($cur_div->textContent);
											if(hdc_test($t1_v1_inner))
												{
													$advan_team=1; $t1_v1=$t1_v1_inner;
												}	
										}
										if($d_class==$class_t1_v1_2) 
										{
											$t1_v2_inner=mb_trim($cur_div->textContent); 
											if(hdc_test($t1_v2_inner))
												{
													$advan_team=2; $t1_v1=$t1_v2_inner;
												}
										}
 
										if($d_class==$class_t1_v2 ||$d_class==$class_t1_v21) { $t1_v2=mb_trim($cur_div->textContent); }
										if($d_class==$class_t1_v3 || $d_class==$class_t1_v31) { $t1_v3=mb_trim($cur_div->textContent); }
										$t1_v1=hdc_to_floating($t1_v1);
										if($advan_team==1) { $t1_v1=invert_hdc($t1_v1);}

										if($d_class==$class_t2_v1) { if(hdc_test(mb_trim($cur_div->textContent))){$t2_v1=hdc_to_floating(mb_trim($cur_div->textContent)); }}
										if($d_class==$class_t2_v2) { $t2_v2=mb_trim($cur_div->textContent); }
										if($d_class==$class_t2_v3) { $t2_v3=mb_trim($cur_div->textContent); }

										if($d_class==$class_t3_v1) {  if(isOdds(mb_trim($cur_div->textContent))) {$t3_v1=mb_trim($cur_div->textContent);} }
										if($d_class==$class_t3_v2) {  if(isOdds(mb_trim($cur_div->textContent))) {$t3_v2=mb_trim($cur_div->textContent);} }
										if($d_class==$class_t3_v3) {  if(isOdds(mb_trim($cur_div->textContent))) {$t3_v3=mb_trim($cur_div->textContent);} }										

									}

									unset($d_class);

									// >>>>>>>>>>>>>>td3
									$td3=$all_td->item(4);
									$all3_div=$td3->getElementsByTagName("div");
									$div_len=$td3->getElementsByTagName("div")->length;
									
									$t4_v1=0.00; $t4_v2=0.00; $t4_v3=0.00;$t5_v1=0.00; $t5_v2=0.00; $t5_v3=0.00;$t6_v1=0.00; $t6_v2=0.00; $t6_v3=0.00; $advan_team=0;
								
									for($di=0;$di<$div_len;$di++)
									{   
										$cur_div=$all3_div->item($di);
										$d_class=mb_trim($cur_div->getAttribute("class"));
										$f4_f1=0;
										if($d_class==$class_t1_v1_1) 
										{
											$t4_v1_inner=mb_trim($cur_div->textContent); 
											if(hdc_test($t4_v1_inner)){	 $t4_v1=$t4_v1_inner; $f4_f1=1; $advan_team=1;}	
										}
										if($d_class==$class_t1_v1_2) 
											{ 
												$t4_v2_inner=mb_trim($cur_div->textContent); 									
												if(hdc_test($t4_v2_inner)) {$t4_v1=$t4_v2_inner; $f4_f1=1; $advan_team=2;};
											}
										if($f4_f1==1)
										{
											$t4_v1=hdc_to_floating($t4_v1);
											if($advan_team==1) { $t4_v1=invert_hdc($t4_v1);}
										} 

										if($d_class==$class_t1_v2 || $d_class==$class_t1_v21) { if(isOdds(mb_trim($cur_div->textContent))){$t4_v2=mb_trim($cur_div->textContent);} }
										if($d_class==$class_t1_v3 || $d_class==$class_t1_v31) { if(isOdds(mb_trim($cur_div->textContent))) {$t4_v3=mb_trim($cur_div->textContent); }}


										if($d_class==$class_t2_v1) { if(hdc_test(mb_trim($cur_div->textContent))) {$t5_v1=hdc_to_floating(mb_trim($cur_div->textContent));} }
										if($d_class==$class_t2_v2) { if(isOdds(mb_trim($cur_div->textContent))){$t5_v2=mb_trim($cur_div->textContent); }}
										if($d_class==$class_t2_v3) { if(isOdds(mb_trim($cur_div->textContent))){$t5_v3=mb_trim($cur_div->textContent); }}

										if($d_class==$class_t3_v1) { if(isOdds(mb_trim($cur_div->textContent))) {$t6_v1=mb_trim($cur_div->textContent);} }
										if($d_class==$class_t3_v2) {  if(isOdds(mb_trim($cur_div->textContent))) {$t6_v2=mb_trim($cur_div->textContent);}}
										if($d_class==$class_t3_v3) {  if(isOdds(mb_trim($cur_div->textContent))) {$t6_v3=mb_trim($cur_div->textContent);}}										

									}
										//===========================
											$cur_kicktime=mb_trim($cur_kicktime);

											$k_hour=ltrim_zero(mb_substr($cur_kicktime,0,2));
											$k_min=ltrim_zero(mb_substr($cur_kicktime,3,2));
											$k_past=mb_substr($cur_kicktime,5,2);
											$k_past=mb_strtoupper($k_past);

											if($k_past=='PM')
											{
												$o_time=mktime($k_hour,$k_min,0,$month,$day,$year);
												$kick_time=$o_time+43200;
												$kick_time=$kick_time-3600;
											}
											if($k_past=='AM')
											{
												if($k_hour=='12')
												{
													$end_day=mktime(23,59,59,$month,$day,$year);
													$end_day=$end_day+1;
													$past_min=$k_min*60;
													$kick_time=$end_day+$past_min-3600;
												}
												else{
												$end_day=mktime(23,59,59,$month,$day,$year);
												$end_day=$end_day+1;
												$past_min=($k_hour*3600)+($k_min*60);
												$kick_time=$end_day+$past_min-3600;
												}
											}
										//=======	====================
										unset($d_class);
										$nfield=0;
										mb_ereg_search_init($home_en); $n_f1="\(n\)"; $n_f2="\(N\)";
										if(mb_ereg_search($n_f1) || mb_ereg_search($n_f2))
										{
											$home_en=mb_ereg_replace("\(n\)","",$home_en); $home_en=mb_ereg_replace("\(N\)","",$home_en); $home_en=mb_trim($home_en); $nfield=1;
										}

										

						$insert=$_con->prepare("insert into dom_buffer(mday,mkick,league,home_en,away_en,nfield,t1_v1,t1_v2,t1_v3,t2_v1,t2_v2,t2_v3,t3_v1,t3_v2,t3_v3,t4_v1,t4_v2,t4_v3,t5_v1,t5_v2,t5_v3,t6_v1,t6_v2,t6_v3) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
						$in=array($mday,$kick_time,$cur_league,$home_en,$away_en,$nfield,$t1_v1,$t1_v2,$t1_v3,$t2_v1,$t2_v2,$t2_v3,$t3_v1,$t3_v2,$t3_v3,$t4_v1,$t4_v2,$t4_v3,$t5_v1,$t5_v2,$t5_v3,$t6_v1,$t6_v2,$t6_v3);
						$insert->execute($in);
										
								}					
						
					
} // end access tr
} //end loop tbody

} // end if pre check

if(isset($_POST['load_dom']))
{
	$get_buffer=$_con->query("select * from dom_buffer order by id asc;");
	if($get_buffer->rowCount())
	{
		$cur_league='';
		$tr='';
		$data=$get_buffer->fetchAll();
		for($fi=0;$fi<$get_buffer->rowCount();$fi++)
		{
			$mday=$data[$fi]['mday'];
			$mkick=$data[$fi]['mkick'];
			$league=$data[$fi]['league'];
			$home_en=$data[$fi]['home_en'];
			$away_en=$data[$fi]['away_en'];
			$nfield=$data[$fi]['nfield'];
			
			$id=$data[$fi]['id'];
			$t1_v1=$data[$fi]['t1_v1'];
			$t1_v2=$data[$fi]['t1_v2'];
			$t1_v3=$data[$fi]['t1_v3'];

			if($t1_v1=='0.00') {$t1_v1=0;}

			$t2_v1=$data[$fi]['t2_v1'];
			$t2_v2=$data[$fi]['t2_v2'];
			$t2_v3=$data[$fi]['t2_v3'];

			$t3_v1=$data[$fi]['t3_v1'];
			$t3_v2=$data[$fi]['t3_v2'];
			$t3_v3=$data[$fi]['t3_v3'];

			$t4_v1=$data[$fi]['t4_v1'];
			$t4_v2=$data[$fi]['t4_v2'];
			$t4_v3=$data[$fi]['t4_v3'];
			if($t4_v1=='0.00') {$t4_v1=0;}
			if($t4_v1=='0.00' && $t4_v2=='0.00' && $t4_v2=='0.00') {$t4_v1='';}

			$t5_v1=$data[$fi]['t5_v1'];
			$t5_v2=$data[$fi]['t5_v2'];
			$t5_v3=$data[$fi]['t5_v3'];

			$t6_v1=$data[$fi]['t6_v1'];
			$t6_v2=$data[$fi]['t6_v2'];
			$t6_v3=$data[$fi]['t6_v3'];

			if($cur_league!=$league)
			{
				$cur_league=$league;
				$tr.='<tr><td colspan="13" align="left" class="league_b">'.$league.'</td></tr>';
			}
			if($nfield==1) {$home_en.=' (n)';}
				if($t1_v1<0){ $home_en='<span class="red_t"><b>'.$home_en.'</b></span>';}
				if($t1_v1>0){ $away_en='<span class="red_t"><b>'.$away_en.'</b></span>';}
				$tr.='<tr id="buff'.$id.'"><td valign="top" class="kick_b">'.thaidate($mkick).'</td><td valign="top" align="left" class="team_b">'.$home_en.'<br>'.$away_en.'</td>
				<td valign="top" align="right">'.$t1_v1.'</td><td valign="top">'.$t1_v2.'<br>'.$t1_v3.'</td><td valign="top">'.$t2_v1.'</td><td valign="top">'.$t2_v2.'<br>'.$t2_v3.'</td><td valign="top" class="t3_b">'.$t3_v1.'<br>'.$t3_v2.'<br>'.$t3_v3.'</td>
				<td valign="top" align="right">'.$t4_v1.'</td><td valign="top">'.$t4_v2.'<br>'.$t4_v3.'</td><td valign="top">'.$t5_v1.'</td><td valign="top">'.$t5_v2.'<br>'.$t5_v3.'</td><td valign="top">'.$t6_v1.'<br>'.$t6_v2.'<br>'.$t6_v3.'</td><td valign="top" class="end_b"><button onclick="add_odds('.$id.')">เพิ่มราคานี้เข้าในระบบ</button><br><button onclick="del_buff('.$id.')">ลบราคานี้ทั้งแถว</button><span id="pd'.$id.'"></span></td>


				</tr>';
			
		}
		$tr=mb_ereg_replace("0\.00","",$tr);
		$tr.='<tr><td colspan="13"></td></tr>';
		echo $tr;
	}
}

if(isset($_POST['del_buff']) && isDecimal($_POST['del_buff']))
{
	$del_id=$_POST['del_buff'];
	$del=$_con->prepare("delete from dom_buffer where id=?;");
	if($del->execute(array($del_id))) { echo 'd';}
}
//==========ดึงจากตาราง ชั่วคราว ลงตารางทายผลจริง====================
if(isset($_POST['add_odds']) && isStrnum($_POST['add_odds']))
{
		$aid=$_POST['add_odds'];
		if($aid==0)
		{
			$get_buffer=$_con->query("select * from dom_buffer order by id asc;");
		}
		else
		{
		$get_buffer=$_con->prepare("select * from dom_buffer where id=?;");
		$get_buffer->execute(array($aid));
		}

		$cur_match_id=0;
	if($get_buffer->rowCount())
	{
		$cur_league='';
		$data=$get_buffer->fetchAll();
		for($fi=0;$fi<$get_buffer->rowCount();$fi++)
		{
			$mday=$data[$fi]['mday'];

			$mkick=$data[$fi]['mkick'];
			$league=$data[$fi]['league'];
			$home_en=$data[$fi]['home_en'];
			$away_en=$data[$fi]['away_en'];
			$nfield=$data[$fi]['nfield'];
			
			$id=$data[$fi]['id'];
			$t1_v1=$data[$fi]['t1_v1'];
			$t1_v2=$data[$fi]['t1_v2'];
			$t1_v3=$data[$fi]['t1_v3'];

			$t2_v1=$data[$fi]['t2_v1'];
			$t2_v2=$data[$fi]['t2_v2'];
			$t2_v3=$data[$fi]['t2_v3'];

			$t3_v1=$data[$fi]['t3_v1'];
			$t3_v2=$data[$fi]['t3_v2'];
			$t3_v3=$data[$fi]['t3_v3'];

			$t4_v1=$data[$fi]['t4_v1'];
			$t4_v2=$data[$fi]['t4_v2'];
			$t4_v3=$data[$fi]['t4_v3'];

			$t5_v1=$data[$fi]['t5_v1'];
			$t5_v2=$data[$fi]['t5_v2'];
			$t5_v3=$data[$fi]['t5_v3'];

			$t6_v1=$data[$fi]['t6_v1'];
			$t6_v2=$data[$fi]['t6_v2'];
			$t6_v3=$data[$fi]['t6_v3'];

			if($cur_league!=$league)
			{
				$cur_league=$league;
			}
			$check_flow=0;

			if(mb_ereg($_pat_en,$league))
			{
				$l_check=$_con->prepare("select id from league where en_name=?;");
				$l_check->execute(array($league));
				if($l_check->rowCount())
				{ $l_data=$l_check->fetchAll(); $cur_league_id=$l_data[0]['id']; $check_flow=1;
				}
				else
				{
						$l_insert=$_con->prepare("insert into league(en_name,th_name) values(?,?);");
						if($l_insert->execute(array($league,''))) 
						{ $cur_league_id=$_con->lastInsertId(); $check_flow=1; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
						}
				}
			}
			if($check_flow==1)
			{
				if(mb_ereg($_pat_en,$home_en) && mb_ereg($_pat_en,$away_en))
				{
					$t_insert=$_con->prepare("insert into team(en_name,th_name) values(?,?);");
					$t_check=$_con->prepare("select id from team where en_name=?;");
					$t_check->execute(array($home_en));
					if($t_check->rowCount()) {$t_data=$t_check->fetchAll(); $home_id=$t_data[0]['id'];  $check_flow=2;}
					else
					{		$dest_logo="images/nologo.png";
							$fhand_small=fopen($dest_logo,"rb");		$small_size=filesize($dest_logo);		$logo_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
							$img_type=3; $logox=150; $logoy=150;
									
							if($t_insert->execute(array($home_en,'')))
								{
									$team_id=$_con->lastInsertId(); $home_id=$team_id; $check_flow=2;
									$insert_team=$_con->prepare("insert into team_logo(id,blob_data,img_type,img_x,img_y) values(?,?,?,?,?);");
									$insert_team->execute(array($team_id,$logo_binary,$img_type,$logox,$logoy));									
								}									
					}
				}
			}
			if($check_flow==2)
			{
					$t_check=$_con->prepare("select id from team where en_name=?;");
					$t_check->execute(array($away_en));
					if($t_check->rowCount()) {$t_data=$t_check->fetchAll(); $away_id=$t_data[0]['id'];  $check_flow=3;}
					else
					{		
							/*$dest_logo="images/nologo.png";
							$fhand_small=fopen($dest_logo,"rb");		$small_size=filesize($dest_logo);		$logo_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
							$img_type=3; $logox=150; $logoy=150;
							*/
									
							if($t_insert->execute(array($away_en,'')))
								{
									$team_id=$_con->lastInsertId(); $away_id=$team_id; $check_flow=3;
									/*
									$insert_team=$_con->prepare("insert into team_logo(id,blob_data,img_type,img_x,img_y) values(?,?,?,?,?);");
									$insert_team->execute(array($team_id,$logo_binary,$img_type,$logox,$logoy));			
									*/
								}									
					}				
			}
			if($check_flow==3)
			{
				$m_check=$_con->prepare("select id from matchday where mday=? and (home_team in(?,?) OR away_team in (?,?));");
				$m_check->execute(array($mday,$home_id,$home_id,$away_id,$away_id));
				if($m_check->rowCount())
				{	$m_data=$m_check->fetchAll(); $match_id=$m_data[0]['id']; $check_flow=4;					
				}
				else
				{
					$advan=0; $handicap=$t1_v1;
					if($t1_v1>0) {$advan=2;}
					if($t1_v1<0) {$advan=1; $handicap=invert_hdc($t1_v1);}
					$_save=$_con->prepare("insert into matchday(mday,league,mkick,nfield,home_team,away_team,advan,handicap) values(?,?,?,?,?,?,?,?);");
					$data_in=array($mday,$cur_league_id,$mkick,$nfield,$home_id,$away_id,$advan,$handicap);
					if($_save->execute($data_in))
					{
						$match_id=$_con->lastInsertId(); $check_flow=4;
					}
				}
			}			
			if($check_flow==4)
			{
				if($aid==0) // ลบราคาเก่าที่เปิดอยู่ทั้งหมด
				{
					if($match_id != $cur_match_id)
					{
						$_con->exec("delete from market where match_id=$match_id;");
						$cur_match_id=$match_id;
					}
				}

				//========t1v1==========
				if($t1_v2>0 && $t1_v3> 0 && isHdc($t1_v1) && isOdds($t1_v2)&& isOdds($t1_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
					$get_market->execute(array($match_id,1,$t1_v1));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
						$update->execute(array($t1_v2,$t1_v3,$match_id,1,$t1_v1));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,1,$t1_v1,$t1_v2,$t1_v3));
					}
				}
				//else {$_con->exec("insert into market(match_id,market_type,value1,value2,value3) values($match_id,1,$t1_v1,$t1_v2,$t1_v3);");}
				//==============t2=======================================
				if($t2_v1>0 && $t2_v2>0 && $t2_v3> 0 && isHdc($t2_v1) && isOdds($t2_v2)&& isOdds($t2_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
					$get_market->execute(array($match_id,2,$t2_v1));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
						$update->execute(array($t2_v2,$t2_v3,$match_id,2,$t2_v1));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,2,$t2_v1,$t2_v2,$t2_v3));
					}
				}
				//==============t3=======================================
				if($t3_v1>0 && $t3_v2>0 && $t3_v3> 0 && isOdds($t3_v1) && isOdds($t3_v2)&& isOdds($t3_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=?;");
					$get_market->execute(array($match_id,3));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value1=?,value2=?,value3=? where match_id=? and market_type=?;");
						$update->execute(array($t3_v1,$t3_v2,$t3_v3,$match_id,3));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,3,$t3_v1,$t3_v2,$t3_v3));
					}
				}
					//========t4v1==========
				if($t4_v2>0 && $t4_v3> 0 && isHdc($t4_v1) && isOdds($t4_v2)&& isOdds($t4_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
					$get_market->execute(array($match_id,4,$t4_v1));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
						$update->execute(array($t4_v2,$t4_v3,$match_id,4,$t4_v1));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,4,$t4_v1,$t4_v2,$t4_v3));
					}
				}
				//==============t5=======================================
				if($t5_v1>0 && $t5_v2>0 && $t5_v3> 0 && isHdc($t5_v1) && isOdds($t5_v2)&& isOdds($t5_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=? and value1=?;");
					$get_market->execute(array($match_id,5,$t5_v1));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value2=?,value3=? where match_id=? and market_type=? and value1=?;");
						$update->execute(array($t5_v2,$t5_v3,$match_id,5,$t5_v1));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,5,$t5_v1,$t5_v2,$t5_v3));
					}
				}
				//==============t6=======================================
				if($t6_v1>0 && $t6_v2>0 && $t6_v3> 0 && isOdds($t6_v1) && isOdds($t6_v2)&& isOdds($t6_v3))
				{
					$get_market=$_con->prepare("select match_id from market where match_id=? and market_type=?;");
					$get_market->execute(array($match_id,6));
					if($get_market->rowCount())
					{
						$update=$_con->prepare("update market set value1=?,value2=?,value3=? where match_id=? and market_type=?;");
						$update->execute(array($t6_v1,$t6_v2,$t6_v3,$match_id,6));
					}
					else
					{
						$insert=$_con->prepare("insert into market(match_id,market_type,value1,value2,value3) values(?,?,?,?,?);");
						$insert->execute(array($match_id,6,$t6_v1,$t6_v2,$t6_v3));
					}
				}
				
			}//end flow4



		//============end check flow====================
		}
	}
}

if($_con) {$_con=null;}
?>
