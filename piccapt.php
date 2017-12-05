<?php
  {
require_once "ext.php";
con_mysql();
session_pair();

 
if(isset($_GET['site']))
{
	 if($_GET['site']==1) // login.php
		{
					$capstr=gen_num(4);
					session_set('trylogin',$capstr);
					$capstr=mb_strtoupper($capstr);
					header("Content-type: image/gif");
					$imggif=imagecreatefromgif("cap.gif");
					$color=imagecolorallocate($imggif,0,0,0);
					$font='SeeYouPoint.TTF';					
					imagettftext($imggif,22,0,8,32,$color,$font,$capstr);
					imagegif($imggif);
					imagedestroy($imggif);			
			 
		}
//=============================
	 if($_GET['site']==2) //register.php
		{
					$capstr=genchr(4);
					session_set('register',$capstr);
					$capstr=mb_strtoupper($capstr);
					header("Content-type: image/gif");
					$imggif=imagecreatefromgif("cap.gif");
					$color=imagecolorallocate($imggif,0,0,0);
					$font='SeeYouPoint.TTF';					
					imagettftext($imggif,22,0,8,32,$color,$font,$capstr);
					imagegif($imggif);
					imagedestroy($imggif);	
		}
//===========================
	}
}
  ?>