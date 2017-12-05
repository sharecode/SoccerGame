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
	<title>ผลบอลสด</title>

<link rel="stylesheet" href="<?php echo $_url; ?>main.css" type="text/css" />
<link rel="icon" type="image/png" href="<?php echo $_url; ?>images/ball.png">
<script language="javascript" src="<?php echo $_url; ?>main.js"></script>
<script>
</script>
<style>
iframe {min-height:2800px; display:inline-block;}
</style>
</head>
<body>
<?php  top_head(); ?>
<center><div class="body_box">
<h2>ผลบอลสดทุกคู่</h2>
<script language='javascript'> var timeZone ='%2B0700'; var dstbox =''; var cpageBgColor = 'FFFFFF'; var wordAd=''; var wadurl='http://'; var width='680'; var tableFontSize='11'; var cborderColor='DDDDDD'; var ctdColor1='FFFFFF'; var ctdColor2='E0E9F6'; var clinkColor='0044DD'; var cdateFontColor='333333'; var cdateBgColor='FFFFFF'; var scoreFontSize='12'; var cteamFontColor='000000'; var cgoalFontColor='FF0000'; var cgoalBgColor='FFFFE1'; var cremarkFontColor='0000FF'; var mark = 'th'; var cremarkBgColor='F7F8F3'; var Skins='10'; var teamWeight='400'; var scoreWeight='700'; var goalWeight='400'; var fontWeight='700'; document.write("<iframe src='http://freelive.7m.cn/live.aspx?mark="+ mark +"&TimeZone=" + timeZone + "&wordAd=" + wordAd + "&cpageBgColor="+ cpageBgColor +"&wadurl=" + wadurl + "&width=" + width + "&tableFontSize=" + tableFontSize + "&cborderColor=" + cborderColor + "&ctdColor1=" + ctdColor1 + "&ctdColor2=" + ctdColor2 + "&clinkColor=" + clinkColor + "&cdateFontColor="+ cdateFontColor +"&cdateBgColor=" + cdateBgColor + "&scoreFontSize=" + scoreFontSize + "&cteamFontColor=" + cteamFontColor + "&cgoalFontColor=" + cgoalFontColor + "&cgoalBgColor=" + cgoalBgColor + "&cremarkFontColor=" + cremarkFontColor + "&cremarkBgColor=" + cremarkBgColor + "&Skins=" + Skins + "&teamWeight=" + teamWeight + "&scoreWeight=" + scoreWeight + "&goalWeight=" + goalWeight +"&fontWeight="+ fontWeight +"&DSTbox="+ dstbox +"'  height='100%' width='700' scrolling='yes' border='0' frameborder='0'></iframe>")</script>

</div></center>
<?php footer(); if($_con) { $_con=null;} ?>

</body>
</html>