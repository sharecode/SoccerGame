<?php
require_once "ext.php";

if(isset($_POST['page']))
{
	$page=$_POST['page'];
}
else{ exit();}

con_mysql();
session_pair();
//=================================================
if($page=='register')
{
	
	if(isset($_POST['addusername']) && isset($_POST['pass']) && isset($_POST['name'])&&isset($_POST['capt']))
	{
		$username=mb_trim($_POST['addusername']);
		$pass=mb_trim($_POST['pass']);
		$name=mb_trim($_POST['name']);
		$capt=mb_strtolower(mb_trim($_POST['capt']));
		
		$check=0;
		$u_len=mb_strlen($username);
		$u_pat="^[a-zA-Z0-9ก-ูเ-์]+$";
		if($u_len>5 && mb_ereg($u_pat,$username))
		{
			if(get_user_id($username)) {echo 'u';}
			else { $check=1;}
		}
		if($check==1)
		{
			$p_len=mb_strlen($pass);
			if($p_len>5) 
			{$sha_pass=sha1($pass);
				$check=2;
			}	
		}
		if($check==2)
		{
			$pat="^[a-zA-Z0-9ก-ูเ-์]+[ ][a-zA-Z0-9ก-ูเ-์]+$";
			if(mb_ereg($pat,$name))
			{
				$get_name=$_con->prepare("select id from member_ where name=?;");
				$get_name->execute(array($name));
				if($get_name->rowCount()){echo 'n';}
				else {$check=3;}
			}
		}
		if($check==3)
		{
			$ses_capt=session_read("register");
			if($capt==$ses_capt) {$check=4;} 
			else
			{
				$new_str=genchr(4); session_set("register",$new_str); echo 'c';
			}
		}
		if($check==4)
		{
			$now=time();
			$ip=getip();
			$insert=$_con->prepare("insert into member_(user,password,name,aliasname,reg_time,reg_ip) values(?,?,?,?,?,?);");
			$value=array($username,$sha_pass,$name,$username,$now,$ip);
			if($insert->execute($value))
			{
				$user_id=$_con->lastInsertId();
				$avt_file="images/tipster.png";  // รูปภาพ ดีฟอล 200x200px
				$fhand_small=fopen($avt_file,"rb");		$small_size=filesize($avt_file);		$avt_binary=fread($fhand_small,$small_size);			fclose($fhand_small);
				$insert_avt=$_con->prepare("insert into avatar_blob(owner,data_blob,img_type) values(?,?,?);");
				$insert_avt->execute(array($user_id,$avt_binary,3));
				echo 'a';

				$new_str=genchr(4); session_set("register",$new_str);
			}
		}
		
	}
} // end page register

//===============================================
if($page=='global')
{
if(isset($_POST['loguser']) && isset($_POST['pass']) && isset($_POST['check']))
{
	$username=$_POST['loguser'];
	$pass=$_POST['pass'];
	$mem=$_POST['check'];
		$u_len=mb_strlen($username);
		$u_pat="^[a-zA-Z0-9ก-ูเ-์]+$";
		if($u_len>5 && mb_ereg($u_pat,$username))
			{			
			$sql=$_con->prepare("select id,password,login_count from member_ where user=?;");
			$para=array($username);
			$sql->execute($para);
			if($sql->rowCount())
				{
					$data=$sql->fetchAll();
					$id=$data[0]['id'];
					$log_count=$data[0]['login_count'];
					$password=$data[0]['password'];					
					$log_count++;
					if($log_count<6) /// ล็อคอินได้ 5 ครั้ง ก่อนจะบังคับให้ไปป้อน captcha
					{
						$client_pass=sha1($pass);
						
						if($client_pass==$password)
						{
							session_set("uid",$id);
							session_set("username",$username);
							$_con->exec("update member_ set login_count=0 where id=$id;");

							if($mem==1)
							{
								$expire=time()+30*24*60*60;
								$uid_mem=dechex($id);
								$se_mem=genstr(64);
								 setcookie("MR",$uid_mem,$expire);
								 setcookie("SR",$se_mem,$expire);									 
								 $_con->exec("update member_ set login_serial='$se_mem' where id=$id;");
							}
							else
							{
								if(isset($_COOKIE['MR'])) { setcookie("MR",'',0);}
								 if(isset($_COOKIE['SR'])) { setcookie("SR",'',0);}
								  $_con->exec("update member_ set login_serial='' where id=$id;");
							}
							$_con->exec("update member_ set last_login=$_now where(id=$id)");                                                   

							echo 'l';
						}
						else
						{
							$_con->exec("update member_ set login_count=(login_count +1) where id=$id;");
						}
					}
					else { echo 'c'; }
			}
		}
	}
//==============================================
if(isset($_POST['logout']))
{
	$uid=session_read("uid");
	if($uid)
		{
				session_clear();
                 $_con->exec("update member_ set login_serial='',last_login=$_now where(id=$uid)");
                 if(isset($_COOKIE['MR'])) { setcookie("MR",'',0);}
                 if(isset($_COOKIE['SR'])) { setcookie("SR",'',0);}
		}
	else
	{
			session_clear();
			if(isset($_COOKIE['MR'])) { setcookie("MR",'',0);}
			 if(isset($_COOKIE['SR'])) { setcookie("SR",'',0);}
	}
}

}/// end page global

//======================login.php=============================
if($page=='login')
{
	if(isset($_POST['trylog']) && isset($_POST['pass']) && isset($_POST['check'])&& isset($_POST['capt']))
	{
	$username=$_POST['trylog'];
	$pass=$_POST['pass'];
	$mem=$_POST['check'];
	$capt=$_POST['capt'];

		$login='w';
		$u_len=mb_strlen($username);
		$u_pat="^[a-zA-Z0-9ก-ูเ-์]+$";
		if($u_len>5 && mb_ereg($u_pat,$username))
		{
			
			$sql=$_con->prepare("select id,password,login_count from member_ where user=?;");
			$para=array($username);
			$sql->execute($para);
			if($sql->rowCount())
				{
					$data=$sql->fetchAll();
					$id=$data[0]['id'];
					$log_count=$data[0]['login_count'];
					$password=$data[0]['password'];	
					
					if($log_count>4) 
					{
						$sess_capt=session_read("trylogin");
						if($sess_capt==$capt)
						{
						$client_pass=sha1($pass);
						
						if($client_pass==$password)
						{
							session_set("uid",$id);
							session_set("username",$username);

							$_con->exec("update member_ set login_count=0 where id=$id;");

							if($mem==1)
							{
								$expire=time()+30*24*60*60;
								$uid_mem=dechex($id);
								$se_mem=genstr(64);
								 setcookie("MR",$uid_mem,$expire);
								 setcookie("SR",$se_mem,$expire);									 
								 $_con->exec("update member_ set login_serial='$se_mem' where id=$id;");
							}
							else
							{
								if(isset($_COOKIE['MR'])) { setcookie("MR",'',0);}
								 if(isset($_COOKIE['SR'])) { setcookie("SR",'',0);}
								  $_con->exec("update member_ set login_serial='' where id=$id;");
							}	
							$_con->exec("update member_ set last_login=$_now where(id=$id)");                                                  
								$capstr=gen_num(4);
								session_set('trylogin',$capstr);								
								$login='r';
						}
					} else { $login='c';}
						// end check capt
					}				
			}
		}
		if($login != 'r') 
		{	$capstr=gen_num(4);
			session_set('trylogin',$capstr);
		}
		echo $login;
	}
} //end login page
?>