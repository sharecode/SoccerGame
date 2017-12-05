<?php

/*CREATE TABLE `session` (
  `id` varchar(24) NOT NULL DEFAULT '',
  `data` varchar(20480) DEFAULT '',
  `ip_owner` int(11) unsigned DEFAULT NULL,
  `expire` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

$_session_id=null;
$_cookie_sess='client_pair'; // cookie name for client;
$_cookie_life=300; // second life


function session_pair()
{
	global $_session_id;
	global $_con;
	global $_cookie_sess;
	global $_cookie_life;

	$ip=getip();
	$now=time();

	$new_id=genchr(8);
	$expire=time()+$_cookie_life;
	$pat2="^[0-9a-z]{8}$";

	$_con->exec("delete from session where expire<$now;"); // ทำลาย เซสซชั่นขยะ

	if(isset($_COOKIE[$_cookie_sess]))
	{
		$name=$_COOKIE[$_cookie_sess];
		if(mb_ereg($pat2,$name))
		{
			$st_get=$_con->prepare("select * from session where id=?;");
			$in_sess=array($name);
			$st_get->execute($in_sess);
			if($st_get->rowCount()) // ถ้ามี cookie อยู่ ไคลเอน และ มีเซสชั่น อยู่ในดาต้าเบสแล้ว
			{
				$data=$st_get->fetchAll();
				$sess_ip=$data[0]['ip_owner'];
				if($sess_ip==$ip) // ต้องตรวจสอบ ว่า ip cookie นี้มาจาก client ที่เป็นเจ้าของ จริง
				{
					setcookie($_cookie_sess,$name,$expire);// เพิ่ม life ให้ cookie ที่ไคลเอน
					$_con->exec("update session set expire=$expire where id='$name';"); // เพิ่ม life ให้กับ session ใน ดาต้าเบสด้วย
					$_session_id=$name;
				}
				else { $_con->exec("delete from session where id='$name';"); } // ถ้า ip ที่ส่งคุกกี้มา ไม่ตรงกับที่สร้างไว้ตั้งแต่แรกให้ทำลาย คุกกี้ทิ้ง
			}
			else
			{
						if($_con->exec("insert into session(id,ip_owner,expire) values('$new_id','$ip',$expire);"))
						{
							if(setcookie($_cookie_sess,$new_id,$expire))
							{ $_session_id=$new_id;
							}
						}					
			}
		}
	}
	else
	{ // ไม่มี คุกกี้ ที่ ไคลเอน และ ดาต้าเบส ต้องสร้างขึ้นใหม่

		if($_con->exec("insert into session(id,ip_owner,expire) values('$new_id','$ip',$expire);"))
		{
			if(setcookie($_cookie_sess,$new_id,$expire))
			{ $_session_id=$new_id;
			}
		}
	}
} // end pair_sess

function session_read($name)
{
	
	global $_session_id;
	global $_con;
	$re=null;
	$st_check=$_con->query("select data from session where id='$_session_id';");
	if($st_check->rowCount())
	{
		$get=$st_check->fetchAll();
		$data=$get[0]['data'];
			$pat="^((.)+[;])+$";
			if(mb_ereg($pat,$data))
			{
				$data_split=mb_split(';',$data);
				$count=count($data_split);

				for($sesi=0;$sesi<$count;$sesi++)
				{
					$name_data=$data_split[$sesi];
					$pat2="^(.)+[:](.)+$";
					if(mb_ereg($pat2,$name_data))
					{
						$name_data_split=mb_split(':',$name_data);
						$s_name=$name_data_split[0];
						$s_data=base64_decode($name_data_split[1]);
						if($s_name==$name) 
						{ 
							if(isStrnum($s_data))
							{$re=number($s_data);
							}
							else{$re=$s_data;}							
						}
					}					
				}				
			}		
	}
	return $re;
}

function session_set($name,$value)
{
	global $_session_id;
	global $_con;
	$value=base64_encode($value);
	$data_buff='';
	$find=0;
	$new=$name.':'.$value.';';
	$st_check=$_con->query("select data from session where id='$_session_id';");
	if($st_check->rowCount() &&mb_strlen($name))
	{
		$get=$st_check->fetchAll();
		$data=$get[0]['data'];
			$pat="^((.)+[;])+$";
			if(mb_ereg($pat,$data))			
			{
				$data_split=mb_split(';',$data);
				$count=count($data_split);

				for($sesi=0;$sesi<$count;$sesi++)
				{
					$name_data=$data_split[$sesi];
					$pat2="^(.)+[:](.)+$";
					if(mb_ereg($pat2,$name_data))
					{
						$name_data_split=mb_split(':',$name_data);
						$s_name=$name_data_split[0];
						if($s_name==$name) 						
						{ 
							$find=1;
							if($value != null || $value != '')
							{
								$data_buff.=$new;							
							}
						}
						else
						{
							$data_buff.=$name_data.';';
						}
					}					
				}
				if($find==0)
				{					
					$data_buff.=$name.':'.$value.';';
				}
			}
			else
			{
				$data_buff.=$name.':'.$value.';';
			}
	}

	$st_new=$_con->prepare("update session set data=? where id=?;");
	$in=array($data_buff,$_session_id);
	$st_new->execute($in);
}

function session_clear()
{
		global $_session_id;
		global $_con;
		global $_cookie_sess;
		
		$_con->exec("delete from session where id='$_session_id';");
		setcookie($_cookie_sess,'',0);
}
?>