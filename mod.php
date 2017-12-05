<?php
require_once "ext.php";
con_mysql();
session_pair();
if(islogin() && isAdmin(15)){} else{ $_con=null;header('Location: '.$_url); exit(); }
//================================

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>จัดการหลังบ้าน</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
body { margin:50px; background:white;}
</style>
</head>
<body>
<a href="<?php echo $_url; ?>addteam.php">เพิ่มทีมและลีกเข้าในระบบ</a><br>
<a href="<?php echo $_url; ?>matchtable.php">เพิ่มตารางแข่งขันประจำวัน</a><br>
<a href="<?php echo $_url; ?>addmarket.php">สร้างราคาสำหรับทายผล</a><br>
<a href="<?php echo $_url; ?>import_dom.php">import html file</a><br>
<a href="<?php echo $_url; ?>ft_score.php">สรุปสกอร์แข่งขัน</a><br>
<a href="<?php echo $_url; ?>resetpass.php">เปลี่ยนรหัสผ่านสมาชิก</a><br>
<a href="<?php echo $_url; ?>tded.php">ให้ทีเด็ดประจำวัน</a><br>
<a href="<?php echo $_url; ?>resetscore.php">รีเซตคะแนน</a><br>




</body>
</html>