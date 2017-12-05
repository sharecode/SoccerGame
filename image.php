<?php
require_once "ext.php";
con_mysql();

if(isset($_GET['lg']))
{
	$id=$_GET['lg'];
	if(isDecimal($id))
	{
		$get_logo=$_con->prepare("select blob_data,img_type from team_logo where id=?;");
		$get_logo->execute(array($id));
		if($get_logo->rowCount())
		{
			$data=$get_logo->fetchAll();
			$binary=$data[0]['blob_data'];
			$type=$data[0]['img_type'];

			 if($type==1){header('Content-Type: image/gif');}
             if($type==2){header('Content-Type: image/jpeg');}
             if($type==3){header('Content-Type: image/png');}
			 echo $binary;
		}
		else
		{
			header('Content-Type: image/png');
			readfile("images/nologo.png");
		}
	}
}

if(isset($_GET['p']))
{
	$id=$_GET['p'];
	if(isDecimal($id))
	{
		$get_avt=$_con->prepare("select data_blob,img_type from avatar_blob where owner=?;");
		$get_avt->execute(array($id));
		if($get_avt->rowCount())
		{
			$data=$get_avt->fetchAll();
			$binary=$data[0]['data_blob'];
			$type=$data[0]['img_type'];

			 if($type==1){header('Content-Type: image/gif');}
             if($type==2){header('Content-Type: image/jpeg');}
             if($type==3){header('Content-Type: image/png');}
			 echo $binary;
		}
	}
}


if($_con) { $_con=null;}
?>