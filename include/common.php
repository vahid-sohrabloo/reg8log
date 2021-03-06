<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!isset($parent_page)) exit("<center><h3>Error: Direct access denied!</h3></center>");

if(!isset($index_dir)) $index_dir='';

$debug_mode=true;

$db_installed=false;

//====================================================

if($debug_mode) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}
else ini_set('display_errors', '0');

if(!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS']!=='off' || $_SERVER['SERVER_PORT']==443) $https=true;
else $https=false;

ignore_user_abort(true);

if(get_magic_quotes_gpc()) {
	$_GET=array_map('stripslashes', $_GET);
	$_POST=array_map('stripslashes', $_POST);
	$_COOKIE=array_map('stripslashes', $_COOKIE);
}

header("X-Frame-Options: deny");

if(!$db_installed) require $index_dir.'include/code/code_check_db_setup_status.php';

require $index_dir.'include/code/code_gather_request_entropy.php';

if(isset($_COOKIE['reg8log_client_sess_key'])) $client_sess_key=$_COOKIE['reg8log_client_sess_key'];
else {
	require_once $index_dir.'include/func/func_random.php';
	
	$client_sess_key=random_string(22);
	setcookie('reg8log_client_sess_key', $client_sess_key, 0, '/', null, $https, true);
}

if(!isset($_COOKIE['reg8log_antixsrf_token'])) {
	require_once $index_dir.'include/func/func_random.php';
	
	$antixsrf_token=random_string(22);
	setcookie('reg8log_antixsrf_token', $antixsrf_token, 0, '/', null, $https, true);
	$_COOKIE['reg8log_antixsrf_token']=$antixsrf_token;
}

if(!isset($encrypt_session_files_contents)) require $index_dir.'include/info/info_crypto.php';

if(!$db_installed) {
	if(isset($setup_page)) return;
	require $index_dir.'include/page/page_not_setup.php';
	exit;
}

require_once $index_dir.'include/func/func_shutdown_session.php';

register_shutdown_function('shutdown_session');

?>