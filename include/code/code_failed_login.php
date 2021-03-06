<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!isset($parent_page)) exit("<center><h3>Error: Direct access denied!</h3></center>");

if(!isset($index_dir)) $index_dir='';

if($lockdown_threshold==-1  and $captcha_threshold==-1) return;

$req_time=time();

require $index_dir.'include/info/info_register.php';

if(!$username_exists and $registeration_enabled and $ajax_check_username) {
$no_pretend_user=true;
return;
}

require_once $index_dir.'include/class/class_cookie.php';

require_once $index_dir.'include/code/code_db_object.php';

$cookie=new hm_cookie('reg8log_failed_logins');
$cookie->secure=$https;

$_username=$reg8log_db->quote_smart($manual_identify['username']);

if(!isset($site_key)) {
	require $index_dir.'include/code/code_fetch_site_vars.php';
	
}

$lock_name=$reg8log_db->quote_smart('reg8log--failed_login-'.$manual_identify['username']."--$site_key");
$reg8log_db->query("select get_lock($lock_name, -1)");

$query="select * from `failed_logins` where `username`=$_username limit 1";

$reg8log_db->query($query);

if(!$reg8log_db->result_num()) {
$attempts=$reg8log_db->quote_smart(pack('l10', $req_time, 0, 0, 0, 0, 0, 0, 0, 0, 0));
$pos=2;
$field_values="$_username, $username_exists, $attempts, $pos, $req_time";
$query="insert into `failed_logins` (`username`, `username_exists`, `attempts`, `pos`, `last_attempt`) values($field_values)";
$reg8log_db->query($query);

$cookie_contents=$cookie->get();
$tmp12=strtolower($manual_identify['username']);
if($cookie_contents===false) $cookie_contents=$tmp12."\n".$req_time;
else $cookie_contents=$cookie_contents."\n".$tmp12."\n".$req_time;
$cookie_contents=implode("\n", array_slice(explode("\n", $cookie_contents), -2*20));
$cookie->set(null, $cookie_contents);

if($lockdown_threshold==1) {
$lockdown=$manual_identify['username'];
$lockdown_duration=$req_time+$lockdown_period-time();
}
else if($captcha_threshold==1) $captcha_needed=true;

$failed_attempts=1;

return;
}

$rec=$reg8log_db->fetch_row();

$attempts = unpack("l10", $rec['attempts']);

$count=1; //1 for current failed attempt
$oldest=$req_time;
foreach($attempts as $value) if(($req_time-$value)<$lockdown_period) {
$count++;
if($value<$oldest) $oldest=$value;
}

$failed_attempts=$count;

if($lockdown_threshold!=-1 and $count>=$lockdown_threshold) {
$lockdown=$manual_identify['username'];
$lockdown_duration=$oldest+$lockdown_period-$req_time;
}
else if($captcha_threshold!=-1 and $count>=$captcha_threshold) $captcha_needed=true;

$pos=$rec['pos'];

$attempts[$pos]=$req_time;

$attempts=$reg8log_db->quote_smart(pack('l10', $attempts[1], $attempts[2], $attempts[3], $attempts[4], $attempts[5], $attempts[6], $attempts[7], $attempts[8], $attempts[9], $attempts[10]));

$pos++;
if($pos>10) $pos=1;

$query="update `failed_logins` set `attempts`=$attempts, `pos`=$pos, `last_attempt`=$req_time where `username`=$_username limit 1";

$reg8log_db->query($query);

$cookie_contents=$cookie->get();
$tmp12=strtolower($manual_identify['username']);
if($cookie_contents===false) $cookie_contents=$tmp12."\n".$req_time;
else $cookie_contents=$cookie_contents."\n".$tmp12."\n".$req_time;
$cookie->set(null, $cookie_contents);

require_once $index_dir.'include/info/info_cleanup.php';

if(mt_rand(1, floor(1/$cleanup_probability))==1) {
$table_name='failed_logins';
require $index_dir.'include/code/code_failed_logins_expired_cleanup.php';
}

if(mt_rand(1, floor(1/$cleanup_probability))==1) {
$table_name='failed_logins';
require $index_dir.'include/code/code_failed_logins_size_cleanup.php';
}

?>