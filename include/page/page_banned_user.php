<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!isset($parent_page)) exit("<center><h3>Error: Direct access denied!</h3></center>");

if(!isset($index_dir)) $index_dir='';

?>

<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="0">
<title>Banned account</title>
<meta http-equiv="generator" content="Kate" />
<style>
h3 {
	padding: 0px;
	margin: 5px;
}
</style>
</head>
<body bgcolor="#D1D1E9" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000"><table width="100%" height="100%" style="border: 10px solid brown"><tr><td align="center">

<?php

echo '<h3 style="color: red">Your account has been banned by Admin!</h3>';
if($ban_reason!=='') echo '<h4>Ban reason: <span style="color: #84f;">', htmlspecialchars($ban_reason, ENT_QUOTES, 'UTF-8'), '</span></h4>';
if($ban_until!=1) {
	require_once $index_dir.'include/func/func_duration2msg.php';
	echo '<h4>Ban will be lifted at:  <span style="color: #84f;">', duration2friendly_str($ban_until-time(), 2), '</span> later.</h4>';
}
else echo '<h4>Ban will be lifted at:  <span style="color: #84f;">Not specified.</span></h4>';
echo '<br><a href="index.php">Login page</a>';

?>

</td></tr></table></body>
</html>
