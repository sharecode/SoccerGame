<?php
require_once "session.php";
// db connect=================
$_url='http://localhost/lotto/';
$_userdb="";
$_passdb="";
$_host="localhost";
$_db="betting_";


// initial=======================
date_default_timezone_set('Asia/Bangkok');
ini_set("short_open_tag","On");
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
$_tab=uniqid();

//==========================

$_uid=NULL;
$_now=time();
$_con=NULL;

$_jpeg_quality_out=90;

//==========================

$_s_handicap_pat="^([\+]|[\-])[0-9][0-9]*[\.](0|00|25|5|50|75)$";
$_odds_pat="^[0-9][0-9]*[\.][0-9][0-9]$";
$_handicap_pat="^[\-]*[0-9][0-9]*([\.](0|00|25|5|50|75))*$";
$_alias_pat="^[a-zA-Z0-9ก-ูเ-์]+([\ ][a-zA-Z0-9ก-ูเ-์]+)*$";

$_pat_en='^[0-9a-zA-Z\-\_\(\)\ ]+$';
$_pat_th='^[0-9ก-ูเ-์\-\_\(\)\ ]+$';


//=================
$_bill_limit_day=1; // สามารถทายได้กี่บิลต่อวัน
$_time_right=300; // เวลาที่สามารถทายได้ก่อน เตะ หน่วย วินาที

$_end_guess=5; //เวลา สิ้นสุดที่จะสามารถทายผลได้ กี่นาฬิกา
$_start_guess=12; // เวลาเปิดทายผลของวัน
$_tipster_show=10; // โชว์กี่อันเซียนในหน้าแรก


function con_mysql()
{  
	$send=0;
	global $_con,$_host,$_userdb,$_passdb,$_db;
	$_con= new PDO("mysql:host=$_host;dbname=$_db",$_userdb,$_passdb, array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8'));
	if($_con)
	{ $send=1;
	}
	return $send;
}

function get_user_id($user)
{
	global $_con;
	$id=null;

	$sql=$_con->prepare("select id from member_ where user=?;");
	$para=array($user);
	$sql->execute($para);
	if($sql->rowCount())
	{
		$data=$sql->fetchAll();
		$id=$data[0]['id'];
	}
	return $id;
}
/*
function isAdmin(0)
{
	$find=0;
	global $_admin,$_uid;
	$all_admin=mb_split(',',$_admin); $a_count=count($all_admin);
	for($ai=0;$ai<$a_count;$ai++)
	{
		if($all_admin[$ai]==$_uid){$find=1; break;}
	}
	return $find;
}
*/

function isAdmin($deep)
{
	
	global $_uid,$_con;
	$allow=0;
	$get_ad=$_con->query("select * from admin where id=$_uid;");
	if($get_ad->rowCount())
	{	
		$ad_data=$get_ad->fetchAll();
		$deep_ =$ad_data[0]['deep'];
		if($deep_<=$deep) {$allow=1;}
	}
	return $allow;
}

//=================================================
function islogin()
{
	global $_con,$_uid;  
    $ip=$_SERVER['REMOTE_ADDR'];
    $now=time();
	$sess_uid=session_read('uid');
    if($sess_uid)
    {
            $_uid=$sess_uid;
    }
    else
        { 
            if(isset($_COOKIE['MR'])&&isset($_COOKIE['SR']))
            {
                $mr=$_COOKIE['MR'];
                $sr=$_COOKIE['SR'];
                $pat1="^[1-9a-f]+[0-9a-f]*$";
                $pat2="^[0-9a-z]{64}$";
                if(mb_ereg($pat1,$mr)&&mb_ereg($pat2,$sr))
                {
                    $uuid=hexdec($mr);
					$sql_getsr=$_con->prepare("select user,password,login_serial from member_ where(id=?);");
					$uid_sr_in=array($uuid);
					$sql_getsr->execute($uid_sr_in);
					if($sql_getsr->rowCount())
					{
						$user_data=$sql_getsr->fetchAll();
						$username=$user_data[0]['user'];
						$serial=$user_data[0]['login_serial'];

                        if($serial==$sr)
							{          
									$_uid=$uuid;
									session_set('uid',$uuid);
									session_set('username',$username);                                     
                                     $now_insert=time();
                                     $_con->exec("update member_ set last_login=$now_insert where(id=$_uid)");                                                   
                           }
                      } 
                }
            }
        }
        return $_uid;
 }
 //===================================================
 function top_head()
 {
	global $_url,$_con,$_uid;
	if($_uid)
	 {
		$get_user=$_con->query("select user,round(score,2) as score,score as score2 from member_ where id=$_uid;");
		$u_data=$get_user->fetchAll();
		$name=$u_data[0]['user'];
		$score=$u_data[0]['score'];
		$score2=$u_data[0]['score2'];
		$get_bill=$_con->query("select id from bill where owner=$_uid and is_check=0;");
		$bill_count=$get_bill->rowCount();
		if($bill_count) {$bill='<span id="bill_node"><span class="bill_count">'.$bill_count.'</span></span>';} else { $bill='<span id="bill_node"></span>';}
		$market_link='<a href="'.$_url.'market.php">ทายผลบอล</a>';
		$bill_link='<a href="'.$_url.'bill.php" title="'.$score2.'">คะแนน '.$score.'</a>';
		$profile='<a href="'.$_url.'profile.php">'.$name.'</a>';
		$td_avt='';
		$td_log='<div class="name_frame">'.$profile.'<br>'.$bill_link.$bill.'<br>'.$market_link.'</div>';
		$td_end='<div class="avt_frame"><img src="'.$_url.'image.php?p='.$_uid.'" id="top_avt"></div>';
		$td_below_end='<button onclick="logout()" id="log_but" class="pink_button">ออกจากระบบ</button>';
	 }
	 else
	 {
		 $td_below_end='';
		 $td_avt='<button class="reg_link_but" onclick="register()">ลงทะเบียน</button>';
		 $td_end='<button onclick="login()" id="log_but" class="pink_button44">เข้าสู่ระบบ</button>';
		 $td_log='<table cellpadding="0">
					<tr><td valign="top"><input type="txt" id="username" class="in_log" onfocus="u_log_check(event)" onblur="u_log_check(event)"></td><td align="center"><span class="check_r" id="check_r" onclick="click_check()"></span></td></tr>
					<tr><td  valign="top"><input type="text" id="pass" class="in_log" onfocus="p_log_check(event)" onblur="p_log_check(event)" onkeypress="key_log_check(event)"></td><td>จดจำ</td></tr>
					</table>';


	 }		

	echo '<div class="top_head"><center><div class="top_head_f">
	<table class="tb_top_head" border="0" cellpadding="0" cellspacing="0">
		<tr>
				<td class="web_logo_f"><a href="'.$_url.'"><img src="'.$_url.'images/web_logo.png" class="web_logo"></a></td>
				<td class="td_top_avt">'.$td_avt.'</td>
				<td class="td_in_log"><div class="score_f">'.$td_log.'</div></td>
				<td class="end_td">'.$td_end.'</td>
		</tr>
	</table>
	</div></center>
	<div class="hide"><img src="'.$_url.'images/progress.gif"><img src="'.$_url.'images/right_small.png"><img src="'.$_url.'images/caution.png"><img src="'.$_url.'images/ok.png"><img src="'.$_url.'images/check.png"></div>
	</div>
	<div class="top_below">
		<center>
		<div class="inner_below">
			<table class="tb_below" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><a href="'.$_url.'">หน้าหลัก</a></td>					
					<td><a href="'.$_url.'match.php">ตารางวันนี้</a></td>
					<td><a href="'.$_url.'livescore.php">ผลบอลสด</a></td>
					<td><a href="'.$_url.'tipster.php">ทำเนียบเซียน</a></td>
					<td><a href="'.$_url.'rule.php">กติกาและเงื่อนไข</a></td>
					</td><td align="right">'.$td_below_end.'</td>
				</tr>
			</table>
		</div>
		</center>
	</div>
	';
 }
 function footer()
 {
	 echo '<div class="low_footer">
	 <table class="tb_mask_progress" id="tb_mask_progress">
		<tr>
			<td id="td_mask_progress" align="center"><span id="sp_mask_progress" style="color:black;"></span></td>
		</tr>
		</table>
		<script>window.addEventListener("resize", resize_mask_td);fill_u_log_check();</script>
	 </div>';
 }
//====================================
function rewrite_hdc($hdc)
{
	$handicap_pat="^[0-9]+[\.](25|75)$";
	if($hdc=='0.00'){ $hdc='0';}
	if(mb_ereg($handicap_pat,$hdc))
	{
		$first_h=$hdc-0.25;
		$second_h=$hdc+0.25;
		$handicap=$first_h.'-'.$second_h;
	}
	else {$handicap=$hdc*1;}
	return $handicap;
}

function rewrite_handicap($hdc)
{
	$hdc*=1;
	$handicap_pat="^[0-9]+[\.](25|75)$";
	if(mb_ereg($handicap_pat,$hdc))
	{
		$first_h=$hdc-0.25;
		$second_h=$hdc+0.25;
		$handicap=$first_h.'/'.$second_h;
	}
	else
	{
		$handicap=$hdc;
		if($hdc=='0.00'){ $handicap='เสมอ';}		
	}
	return $handicap;
}
//====================================
function mb_trim($str)
{
	$left_start=0;
	$right_end=0; $have_str=0;
	$len=mb_strlen($str);
	for($fi=0;$fi<$len;$fi++)
	{
		$digit=mb_substr($str,$fi,1);
		if($digit!=" "&&$digit!="\r"&&$digit!="\n"&&$digit!="\t"&&$digit!="\0"&&$digit!="\x0B")
		{
			$left_start=$fi; $have_str=1;
		    break;
			}
	}
	if($have_str==1)
	{
		for($ri=$len-1;$ri>=0;$ri--)
			{
				$digit=mb_substr($str,$ri,1);
				if($digit!=" "&&$digit!="\r"&&$digit!="\n"&&$digit!="\t"&&$digit!="\0"&&$digit!="\x0B")
				{ $right_end=$ri; break;}
			}
				$sub_len=($right_end-$left_start)+1;
				return mb_substr($str,$left_start,$sub_len);
	}
	else {return '';}
}

function gen_num($long)
{
	$buff='';
	for($c=0;$c<$long;$c++)
	{
		$buff.=rand(0,9);
	}
	return utf8_encode($buff);
}

function isDecimal($str)
{ $pat="^[1-9]+([0-9]+)*$";
	if(mb_ereg($pat,$str))
	{ return 1;} else {return 0;}
}
function isStrnum($str)
{ $pat="^[0-9]+$";
	if(mb_ereg($pat,$str))
	{ return 1;} else {return 0;}
}

function selectbank($num)
{
	switch($num)
	{
		case 1 : return 'กสิกรไทย'; break;
		case 2 : return 'ไทยพาณิชย์'; break;
		case 3 : return 'กรุงเทพ'; break;
		case 4 : return 'กรุงไทย'; break;
		case 5 : return 'ทหารไทย'; break;
		case 6 : return 'กรุงศรีอยุธยา'; break;
		case 7 : return 'ออมสิน'; break;
		case 8 : return 'ยูโอบี'; break;
		default :  return 'ไม่ระบุธนาคาร';
	}
}

function genstr($ln)
{
	$str='';
	$ln++;
for($i=1; $i<$ln;$i++)
    {
	$rsn=rand(1,2);
	if($rsn==1)
	{			
		$randchr=rand(97,122);
	}else
	{$randchr=rand(48,57);
	}
	$str.=chr($randchr);
   }
    return utf8_encode($str);
}

function genchr($ln)
{
	$str='';	$ln++;
for($i=1; $i<$ln;$i++)
    {
		$randchr=rand(97,122);		$str.=chr($randchr);
   }
    return utf8_encode($str);
}

function getip()
{
	return $_SERVER['REMOTE_ADDR'];
}

function number($num)
{
	return $num+0;
}
function sign_num($num)
{
	if($num>0){$num='+'.$num;}
	return $num;
}
//////////////////// gd function ///////////////////////////////////////
function smoothresize (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h,$quality = 3)
{
  if (empty($src_image) || empty($dst_image) || $quality <= 0) 
	{ return false; }
	imagecolortransparent($dst_image, imagecolorallocatealpha($dst_image, 0, 0, 0, 127));
    imagealphablending($dst_image, false);
    imagesavealpha($dst_image, true);
	imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	 return true;
}


function resize_y($file,$dest,$need_y)
{
	// input is $file output is $dest 
	$re=true;
	if(file_exists($file))
	{
				global $_jpeg_quality_out;
				$pathinfo=pathinfo($file);
				$fileinfo=getimagesize($file);
				$orgx=$fileinfo[0];
				$orgy=$fileinfo[1];
				$type=$fileinfo[2];

				if($orgy==$need_y)
				{
					if(copy($file,$dest)) {$re=true ;}
				}
				else
				{
					if($orgy>$need_y)
					{
						if($type==3) {$org=imagecreatefrompng($file);}
						if($type==2) {$org=imagecreatefromjpeg($file);}
						if($type==1) {$org=imagecreatefromgif($file);}						
					
						$dest_height=$need_y;
						$dest_width=($orgx*$need_y)/$orgy;
						$dest_width=ceil($dest_width);

						$dest_img=imagecreatetruecolor($dest_width,$dest_height);
						smoothresize($dest_img,$org,0,0,0,0,$dest_width,$dest_height,$orgx,$orgy);
						 if($type==3){imagepng($dest_img,$dest,9);}
						if($type==2){imagejpeg($dest_img,$dest,$_jpeg_quality_out);}
						if($type==1){imagegif($dest_img,$dest);}
						imagedestroy($org);
					    imagedestroy($dest_img);
						$re=true;
					}
					if($orgy<$need_y) { $re=false;}
				}	  
	 }else {$re=false;}	 
	 return $re;
}  /// end
function resize_avatar($file,$destfile,$wh) // ตัดรูปภาพเป็นสี่หลี่ยมจตุรัสที่ 200x200 pixel
{
	global $_jpeg_quality_out;
    $fileinfo=getimagesize($file);
    $orgx=$fileinfo[0];
    $orgy=$fileinfo[1];
	$type=$fileinfo[2];

	$dest_img=square_resize($wh,$file);

     if($type==3){imagepng($dest_img,$destfile,9);}
     if($type==2){imagejpeg($dest_img,$destfile,$_jpeg_quality_out);}
     if($type==1){imagegif($dest_img,$destfile);}
     return 1;      
}

function square_resize($wh,$file) // read from file return from memory
{
    $fileinfo=getimagesize($file);
    $orgx=$fileinfo[0];
    $orgy=$fileinfo[1];
	$type=$fileinfo[2];
    
    if($type==3) {$src_img=imagecreatefrompng($file);}
    if($type==2) {$src_img=imagecreatefromjpeg($file);}
    if($type==1) {$src_img=imagecreatefromgif($file);}

    if(($orgx>=$wh)&&($orgy>=$wh))
    {
        $dest_img=imagecreatetruecolor($wh,$wh);
        $dest_start_x=0;
        $dest_start_y=0;
        $dest_w=$wh;
        $dest_h=$wh;
         
        if($orgx==$orgy)
        {
             $src_start_x=0;
             $src_start_y=0;
             $src_x=$orgx;
             $src_y=$orgy;             
        }
        
        if($orgx>$orgy)
          {
            $src_start_x=floor(($orgx-$orgy)/2);
            $src_start_y=0;
            $src_x=$orgy;
            $src_y=$orgy;
          }
        if($orgx<$orgy)
        {
            $src_start_x=0;
            $src_start_y=floor(($orgy-$orgx)/2);
            $src_x=$orgx;
            $src_y=$orgx;
        }
           smoothresize($dest_img,$src_img,0,0,$src_start_x,$src_start_y,$dest_w,$dest_h,$src_x,$src_y);

		   return $dest_img;
    }
}
//==============================================
function ltrim_zero($str)
{
	$find=0;
	for($li=0;$li<mb_strlen($str);$li++)
	{
		$chr=mb_substr($str,$li,1);
		if($chr != '0') {$find=1; break;}
	}
	if($find==1) {$sub=mb_substr($str,$li); }
	if($find==0) {$sub =0;}
	return $sub;
}

function month_year_thai($num)
{
	$y=date("Y",$num)+543;
	$m=date("n",$num);
	 if($m==1){$mon='มกราคม';}
	 if($m==2){$mon='กุมภาพันธ์';}
	  if($m==3){$mon='มีนาคม';}
	 if($m==4){$mon='เมษายน';}
	 if($m==5){$mon='พฤษภาคม';}
	 if($m==6){$mon='มิถุนายน';}
	 if($m==7){$mon='กรกฎาคม';}
	 if($m==8){$mon='สิงหาคม';}
	 if($m==9){$mon='กันยายน';}
	 if($m==10){$mon='ตุลาคม';}
	 if($m==11){$mon='พฤศจิกายน';}
	 if($m==12){$mon='ธันวาคม';}
	$full=$mon.' '.$y;
	return $full;
}

function week_thai($num)
{
	$w=date("w",$num);
	if($w==0) { $day="อาทิตย์";}
	if($w==1) { $day="จันทร์";}
	if($w==2) { $day="อังคาร";}
	if($w==3) { $day="พุธ";}
	if($w==4) { $day="พฤหัสบดี";}
	if($w==5) { $day="ศุกร์";}
	if($w==6) { $day="เสาร์";}
	return $day;
}

function thaiday($num)
{
	$y=date("Y",$num)+543;
	$m=date("n",$num);
	 if($m==1){$mon='มกราคม';}
	 if($m==2){$mon='กุมภาพันธ์';}
	  if($m==3){$mon='มีนาคม';}
	 if($m==4){$mon='เมษายน';}
	 if($m==5){$mon='พฤษภาคม';}
	 if($m==6){$mon='มิถุนายน';}
	 if($m==7){$mon='กรกฎาคม';}
	 if($m==8){$mon='สิงหาคม';}
	 if($m==9){$mon='กันยายน';}
	 if($m==10){$mon='ตุลาคม';}
	 if($m==11){$mon='พฤศจิกายน';}
	 if($m==12){$mon='ธันวาคม';}
	$d=date("j",$num);
	$se=date("s",$num);
	$mi=date("i",$num);
	$ho=date("H",$num);
	$full=week_thai($num).' '.$d.' '.$mon.' '.$y;
	return $full;
}

function bet_type_txt($type)
{
	if($type==1) {return "เต็มเวลา : Handicap";}
	if($type==2) {return "เต็มเวลา : Over/Under";}
	if($type==3) {return "เต็มเวลา : 1X2";}
	if($type==4) {return "ครึ่งแรก : Handicap";}
	if($type==5) {return "ครึ่งแรก : Over/Under";}
	if($type==6) {return "ครึ่งแรก : 1X2";}
}

function invert_hdc($hdc)
{
	if($hdc !=0 || $hdc != 0.0 || $hdc != 0.00)
	{
		return $hdc*-1.00;
	}
	else { return $hdc;}
}

function hdc_to_floating($str)
{
	$handicap_pat="^[0-9\.]+[\-][0-9\.]+$";
	if(mb_ereg($handicap_pat,$str))
	{
		$array = mb_split('-',$str);
		$f_num=$array[0];
		$r_num=$array[1];
		return ($r_num+$f_num)/2;
	}
	else { return $str;}
}

function thaidate($num)
{
	$y=date("Y",$num)+543;
	$m=date("n",$num);
	 if($m==1){$mon='มกราคม';}
	 if($m==2){$mon='กุมภาพันธ์';}
	  if($m==3){$mon='มีนาคม';}
	 if($m==4){$mon='เมษายน';}
	 if($m==5){$mon='พฤษภาคม';}
	 if($m==6){$mon='มิถุนายน';}
	 if($m==7){$mon='กรกฎาคม';}
	 if($m==8){$mon='สิงหาคม';}
	 if($m==9){$mon='กันยายน';}
	 if($m==10){$mon='ตุลาคม';}
	 if($m==11){$mon='พฤศจิกายน';}
	 if($m==12){$mon='ธันวาคม';}
	$d=date("j",$num);
	$se=date("s",$num);
	$mi=date("i",$num);
	$ho=date("H",$num);
	$full=$d.' '.$mon.' '.$y.' '.$ho.':'.$mi;
	return $full;
}

function isHdc($str)
{
	$handicap_pat="^[\-]*[0-9][0-9]*([\.](0|00|25|5|50|75))*$";
	if(mb_ereg($handicap_pat,$str)) {return 1;} else { return 0;}
}
function isOdds($str)
{	$odds_pat="^[0-9][0-9]*[\.][0-9][0-9]$";
	if($str>0)
	{
		if(mb_ereg($odds_pat,$str)) {return 1;} else { return 0;}
	} else { return 0;}
}

function market_select()
{
	global $_end_guess,$_start_guess;
	$bill=0;
	$cur_day=date("j");
	$cur_month=date("n");
	$cur_year=date("Y");
	$cur_hour=date("G");
	$start_day=mktime(0,0,0,$cur_month,$cur_day,$cur_year);
	$start_yesterday=$start_day-86400;
	if($cur_hour<$_end_guess && $cur_hour < $_start_guess)	{$bill=$start_yesterday;	}
	if($cur_hour>$_end_guess && $cur_hour >=$_start_guess) { $bill=$start_day;}
	return $bill;
}

$_league_cache;
function get_league($id)
{
	global $_con,$_league_cache;
	
	if(isset($_league_cache[$id])) {}
	else
	{
		$get_league=$_con->query("select en_name,th_name from league where id=$id;");
		if($get_league->rowCount())
		{
			$l_data=$get_league->fetchAll(); 
			$_league_cache[$id][0]=$l_data[0]['en_name'];
			$_league_cache[$id][1]=$l_data[0]['th_name'];
		}
	}
	return $_league_cache[$id][0];
}

$_team_cache;
function get_team($id)
{
	global $_con,$_team_cache;
	
	if(isset($_team_cache[$id])) {}
	else
	{
		$get_team=$_con->query("select en_name,th_name from team where id=$id;");
		if($get_team->rowCount())
		{
			$l_data=$get_team->fetchAll(); 
			$_team_cache[$id][0]=$l_data[0]['en_name'];
			$_team_cache[$id][1]=$l_data[0]['th_name'];
		}
	}
	return $_team_cache[$id][0];
}

function invert_value($value)
{
	return $value*-1;
}

function start_today()
{
		$day=date("j");
		$month=date("n");
		$year=date("Y");
					
		$today=mktime(0,0,0,$month,$day,$year);
		return $today;
}

?>