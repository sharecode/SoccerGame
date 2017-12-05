	$_url='http://thaitipster.esy.es/';

	$_progress='<span class="pg_frame"><img src="'+$_url+'images/progress.gif" class="p_img"></span>';
	$_right0='<span class="r_frame"><img src="'+$_url+'images/right_small.png" class="r_small"></span>';
	$_ok='<span class="icon_frame"><img src="'+$_url+'images/ok.png" class="icon_small"></span>';
	$_caution='<span class="icon_frame"><img src="'+$_url+'images/caution.png" class="icon_small"></span>';
	$_check='<img src="'+$_url+'images/check.png">';

	$_s_handicap_pat=/^([\+]|[\-])[0-9][0-9]*[\.](0|00|25|5|50|75)$/;
	$_odds_pat=/^[0-9][0-9]*[\.][0-9][0-9]$/;
	$_handicap_pat=/^[0-9][0-9]*[\.](0|00|25|5|50|75)$/;
	$_alias_pat=/^[a-zA-Z0-9ก-ูเ-์]+([\ ][a-zA-Z0-9ก-ูเ-์]+)*$/;



	/*---------------------------------*/
	$u_str='ชื่อผู้ใช้งาน';
	$p_str='รหัสผ่าน';
	function u_log_check($event)
	{
		$user=gValue("username");
		if($user==$u_str){sValue("username","");}
		else{
			if($user==''){ sValue("username",$u_str);}
		}
	}
	function p_log_check($event)
	{
		$input=document.getElementById("pass");
		$pass=gValue("pass");
		if($pass==$p_str){sValue("pass",""); $input.setAttribute("type","password");}
		else{
			if($pass==''){ sValue("pass",$p_str); $input.setAttribute("type","text");}
		}
	}

	function fill_u_log_check()
	{
		if(document.getElementById("username")) {sValue("username",$u_str);}
		if(document.getElementById("pass")) {sValue("pass",$p_str);}
	}
	function register()
	{
		$new=$_url+'register.php'; window.location.assign($new);
	}
	function key_log_check($event)
	{ if($event.keyCode==13) {login();}
	}
	var $_log_re;
	function login()
	{
		$username=str_trim(gValue("username")); 
		$pass=str_trim(gValue("pass")); 

		if($username.length>5 && $pass.length>5 && $username != $u_str && $pass != $p_str)
		{
			sDisable("log_but"); mask_progress_show("กำลังตรวจสอบข้อมูลผู้ใช้งาน");
		
			$form= new FormData();
			$form.append("loguser",$username);
			$form.append("pass",$pass);
			$form.append("check",$_is_c_member);
			$form.append("page",'global');

			$_log_re= new XMLHttpRequest();
			$_log_re.onreadystatechange=rsp_login;
			$_log_re.open("POST","agents.php",true);
			$_log_re.send($form);				
		}
	}
	function rsp_login()
	{
		if($_log_re.readyState==4)
		{
			rDisable("log_but"); mask_progress_hide();
			$receive=$_log_re.responseText; 
			if($receive=='l') { location.reload(true); }
			if($receive=='c') 
			{window.location.assign($_url+"login.php");	
				localStorage.setItem("trylogin",gValue("username"));
			}
		}
	}
/*----------------------------------------------------*/
	var $_log_out;
	function logout()
	{		
			sDisable("log_but"); mask_progress_show("กำลังพาท่านออกจากระบบ");		
			$form= new FormData();
			$form.append("logout",1);
			$form.append("page",'global');

			$_log_out= new XMLHttpRequest();
			$_log_out.onreadystatechange=rsp_logout;
			$_log_out.open("POST","agents.php",true);
			$_log_out.send($form);			
	}
	function rsp_logout()
	{
		if($_log_out.readyState==4)
		{
			rDisable("log_but"); mask_progress_hide();
			 window.location.reload(true); 
		}
	}
	
	var $_is_c_member=0;
	function click_check()
	{
		if($_is_c_member==0) {innerHTML("check_r",$_check); $_is_c_member=1;}
		else{
		if($_is_c_member==1) {innerHTML("check_r",""); $_is_c_member=0;} }
	}
	/*---------------------------------*/
	function gValue($id)
	{
		return str_trim(document.getElementById($id).value);
	}
	function sValue($id,$value)
	{
		document.getElementById($id).value=$value
	}
	function gAttribute($id,$att)
	{
		return document.getElementById($id).getAttribute($att);
	}
	function sAttribute($id,$att,$value)
	{
		 document.getElementById($id).setAttribute($att,$value);
	}
	function rAttribute($id,$att)
	{
		 document.getElementById($id).removeAttribute($att);
	}

	function innerHTML($id,$str)
	{
		document.getElementById($id).innerHTML=$str;
	}
	function sDisable($id)
	{
		 document.getElementById($id).setAttribute("disabled","disabled");
	}
	function rDisable($id)
	{
		 document.getElementById($id).removeAttribute("disabled");
	}
	
	/*-----------------------------------------*/
	function str_trim($str)
	{
		$str_crop='';
		$str_len=$str.length;
		$have_str=0;
		$left_start=0;
		$right_end=0;
		for($fi=0;$fi<$str_len;$fi++)
		{
			$char=$str.substr($fi,1);
			if($char!=" " && $char!="\r" && $char!="\n" && $char!="\t" && $char!="\f")
			{
				$have_str=1;$left_start=$fi; break;
			}
		}
		if($have_str==1)
			{ 
				for($ei=$str_len-1;$ei>=0;$ei--)
				{
					$char=$str.substr($ei,1);
					if($char!=" " && $char!="\r" && $char!="\n" && $char!="\t" && $char!="\f")
					{
						$right_end=$ei; break
					}
				}
				$sub_len=($right_end-$left_start)+1;
				$str_crop=$str.substr($left_start,$sub_len);
			}
			return $str_crop;
	}

/*-------------------------------------------------------*/

function randomNumber()
{
	return Math.floor((Math.random()*10000000)+1); 
}
/*-------------------------------------------------------------------------- */
function num_test($str)
{
	$pat=/^[0-9]+$/;
	if($pat.test($str))
	{
		return 1;
	}
	else { return 0;}
}
/*-------------------------------------------------------------------------- */
function num_filter($id)
{
	$input=document.getElementById($id);
	$str=$input.value;
	$buffer='';
	$numopen=0;
	for($inc=0;$inc<$str.length;$inc++)
		{
			if(num_test($str[$inc]))
			{
				$buffer+=$str[$inc];				
			} 
			
		}
 $input.value=$buffer;
}

/*-------------------table progress mask------------------------------------*/
function resize_mask_td()
{
	$td=document.getElementById("td_mask_progress");
	$w=window.innerWidth; $td.style.width=$w+'px';
	$h=window.innerHeight;  $h-=20; $td.style.height=$h+'px';
}

function mask_progress_show($str)
{
	resize_mask_td();
	document.getElementById("sp_mask_progress").innerHTML=$_progress+' '+$str;
	document.getElementById("tb_mask_progress").style.display='table-cell';
}
function mask_progress_hide()
{
	resize_mask_td();
	document.getElementById("sp_mask_progress").innerHTML='';
	document.getElementById("tb_mask_progress").style.display='none';
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