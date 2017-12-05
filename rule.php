<?php
require_once "ext.php";
con_mysql();
session_pair();
islogin();
//================================

?>
<!DOCTYPE html>
<html lang="th">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="ALL" />
	<title>กติกาและเงื่อนไขการทายผลบอล</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
</style>
</head>
<body>
<?php  top_head(); ?>
<center><div class="body_box">
<h2>กติกาการทายผลบอล</h2>

</div></center>
<?php footer(); if($_con) { $_con=null;} ?>

</body>
</html>