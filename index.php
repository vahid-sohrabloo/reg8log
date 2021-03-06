<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
$parent_page=true;

// All codes created with Notepad++
//Thanks for such a lightweight, fast and powerful tool with excellent features.

require 'include/common.php';

require 'include/code/code_encoding8anticache_headers.php';

if(!isset($site_salt)) if(isset($_COOKIE['reg8log_site_salt'])) $site_salt=$_COOKIE['reg8log_site_salt'];
else {
	require 'include/code/code_fetch_site_vars.php';
	setcookie('reg8log_site_salt', $site_salt, 0, '/', null, $https, true);
}

if(isset($_POST['username'], $_POST['password']) and $_POST['username']!=='' and $_POST['password']!=='') { //login attempt

	require 'include/code/code_prevent_repost.php';

	require 'include/code/code_prevent_xsrf.php';

	if(strpos($_POST['password'], "hashed-$site_salt")!==0) $_POST['password']='hashed-'.$site_salt.'-'.hash('sha256', $site_salt.$_POST['password']);

	$_POST['username']=str_replace(array('ي', 'ك'), array('ی', 'ک'), $_POST['username']);

	$manual_identify=array('username'=>$_POST['username'], 'password'=>$_POST['password']);

	require 'include/info/info_lockdown.php';

	$_username=$_POST['username'];
	require 'include/code/code_check_account_lockdown.php';

	if(isset($lockdown)) {
		require 'include/page/page_lockdown.php';
		exit;
	}
	
	require 'include/code/code_check_ip_lockdown.php';
	
	if(isset($ip_lockdown)) {
		require 'include/page/page_ip_lockdown.php';
		exit;
	}

	if(isset($captcha_needed)) {
		require 'include/code/code_verify_captcha.php';
		if(isset($captcha_err)) if(!isset($captcha_msg)) $err_msg=$err_msgs[0];
	}
	
} //login attempt

if(isset($_POST['remember'])) $remember=true;
else $remember=false;

if(!isset($captcha_err)) {
	require 'include/code/code_identify.php';
	if($identify_error) {
		$failure_msg=($debug_mode)? $user->err_msg : 'Identification error';
		require 'include/page/page_failure.php';
		exit;
	}
}
else {
	require 'include/page/page_login_form.php';
	exit;
}

if(isset($manual_identify)) require 'include/code/code_record_login_attempt.php';

if(!is_null($identified_username)) {//Identified

if(isset($manual_identify)) {

$_identified_username=$identified_username;
require 'include/code/code_dec_failed_logins.php';

if($remember) $user->save_identity('permanent');
else $user->save_identity('session');

$msg='<h1>You logged in successfully <span style="white-space: pre; color: #155;">'.htmlspecialchars($identified_username, ENT_QUOTES, 'UTF-8').'</span>.</h1>';

}
else $msg='<h1>Hello <span style="white-space: pre; color: #155;">'.htmlspecialchars($identified_username, ENT_QUOTES, 'UTF-8').'</span>.<br />You are logged in.</h1>';

require 'include/page/page_members_area.php';

}//Identified
else if(isset($pending_user)) {
	$_identified_username=$pending_user;
	require 'include/code/code_dec_failed_logins.php';
	require 'include/code/code_detect8fix_failed_activation.php';
	require 'include/page/page_pending_user.php';
}
else if(isset($banned_user)) require 'include/page/page_banned_user.php';
else {//Not identified
	if(isset($manual_identify)) {
		require 'include/code/code_set_submitted_forms_cookie.php';
		$err_msg='You are not authenticated!<br />Check your login information.';
		require 'include/code/code_failed_login.php';
	}
	if(isset($lockdown)) require 'include/page/page_lockdown.php';
	else require 'include/page/page_login_form.php';
}//Not identified
