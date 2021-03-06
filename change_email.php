<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
$parent_page=true;

require 'include/common.php';

require 'include/code/code_encoding8anticache_headers.php';

require 'include/code/code_identify.php';

if(is_null($identified_username)) exit('<center><h3>You are not authenticated! <br>First log in.</h3><a href="index.php">Login page</a></center>');

require 'include/info/info_register_fields.php';

$email_format=$fields['email'];

if(!isset($site_salt)) if(isset($_COOKIE['reg8log_site_salt'])) $site_salt=$_COOKIE['reg8log_site_salt'];
else {
	require 'include/code/code_fetch_site_vars.php';
	setcookie('reg8log_site_salt', $site_salt, 0, '/', null, $https, true);
}

$try_type='email';
require 'include/code/code_check_captcha_needed4user.php';

if(!isset($captcha_needed)) {
	$try_type='password';
	require 'include/code/code_check_captcha_needed4user.php';
}

if(isset($captcha_needed)) {
	require 'include/code/code_sess_start.php';
	$captcha_verified=isset($_SESSION['captcha_verified']);
}

if(isset($_POST['password'], $_POST['newemail'], $_POST['reemail'])) {

	require 'include/code/code_prevent_repost.php';

	require 'include/code/code_prevent_xsrf.php';

	require_once 'include/func/func_utf8.php';
	
	if(isset($captcha_needed) and !$captcha_verified) require 'include/code/code_verify_captcha.php';
	
	if($_POST['password']==='') $err_msgs[]='Password field is empty!';
	else if(!isset($captcha_err)) {
		if(strpos($_POST['password'], "hashed-$site_salt")!==0) $_POST['password']='hashed-'.$site_salt.'-'.hash('sha256', $site_salt.$_POST['password']);
		require 'include/code/code_verify_password.php';
		if(isset($err_msgs)) {
			$try_type='password';
			require 'include/code/code_update_user_last_ch_try.php';
		}
		else if(isset($_COOKIE['reg8log_ch_pswd_try'])) {
			$query='update `accounts` set `ch_pswd_tries`=`ch_pswd_tries`-'.$reg8log_db->quote_smart($_COOKIE['reg8log_ch_pswd_try']).' where `username`='.$reg8log_db->quote_smart($identified_username).' limit 1';
			$reg8log_db->query($query);
			setcookie('reg8log_ch_pswd_try', false, mktime(12,0,0,1, 1, 1990), '/', null, $https, true);
		}
	}
	
	if(utf8_strlen($_POST['newemail'])<$email_format['minlength']) $err_msgs[]="new email is shorter than {$email_format['minlength']} characters!";
	else if(utf8_strlen($_POST['newemail'])>$email_format['maxlength'])	$err_msgs[]="new email is longer than {$email_format['maxlength']} characters!";
	else if($email_format['php_re'] and $_POST['newemail']!=='' and !preg_match($email_format['php_re'], $_POST['newemail'])) $err_msgs[]="New email is invalid!";
	else if($_POST['newemail']!==$_POST['reemail']) $err_msgs[]="email fields aren't match!";
	else if(!isset($err_msgs)) {
		if(isset($_SESSION['captcha_verified'])) unset($_SESSION['captcha_verified']);
		$captcha_verified=false;
		$captcha_needed=true;
		$try_type='email';
		require 'include/code/code_update_user_last_ch_try.php';
		$field_name='email';
		$except_user=$identified_username;
		$field_value=$_POST['newemail'];
		require 'include/code/code_check_field_uniqueness.php';
	}
	
	if(!isset($err_msgs)) {
		require 'include/code/code_change_email.php';
		$success_msg='<h3>Your email changed successfully.</h3>';
		$no_specialchars=true;
		require 'include/page/page_success.php';
		require 'include/code/code_set_submitted_forms_cookie.php';
		exit;
	}
}

require 'include/page/page_change_email_form.php';

?>