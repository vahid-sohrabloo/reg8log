<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
$parent_page=true;

$setup_page=true;

$index_dir='../';

$site_encr_key='ff'; //just to prevent func_encryption_with_site8client_keys.php from complaining!

require_once '../include/common.php';

require '../include/code/code_encoding8anticache_headers.php';

require '../include/code/code_prevent_repost.php';

$encrypt_session_files_contents=false;

require '../include/code/code_sess_start.php';

require_once '../include/func/func_random.php';

$file_contents=file_get_contents('setup.txt');

if(empty($_SESSION['setup_key']) or strpos($file_contents, $_SESSION['setup_key'])===false) {
	$setup_key=random_string(22);
	$_SESSION['setup_key']=$setup_key;
	require '../include/page/page_setup_form1.php';
	exit;
}

require '../include/info/info_register_fields.php';

require '../include/code/code_fetch_site_vars.php';

if(!isset($site_salt)) if(isset($_COOKIE['reg8log_site_salt'])) $site_salt=$_COOKIE['reg8log_site_salt'];
else {
	$site_salt=random_string(22);
	setcookie('reg8log_site_salt', $site_salt, 0, '/', null, $https, true);
}

do {
if(!isset($_POST['username'])) break;

require '../include/code/code_prevent_xsrf.php';

require '../include/code/code_validate_admin_register_submit.php';

if(strpos($_POST['password'], "hashed-$site_salt")!==0) $_POST['password']='hashed-'.$site_salt.'-'.hash('sha256', $site_salt.$_POST['password']);

if(isset($err_msgs)) break;

echo '<html><head><meta http-equiv="Content-type" content="text/html;charset=UTF-8" /><META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"><META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"><META HTTP-EQUIV="EXPIRES" CONTENT="0"><title>Setup db - final</title></head><body bgcolor="#D1D1E9" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000"><table align="center" valign="center" height="100%"><tr><td><h4>';
echo '<hr style="width: 250px">';
require '../include/code/code_create_tables.php';

echo '<hr style="width: 250px">';
require '../include/code/code_create_site_vars.php';

echo '<hr style="width: 250px">';
require '../include/code/code_add_admin_account.php';

echo 'Account <span style="color: green">Admin</span> created.<br>';
echo '<hr style="width: 250px">';
$query="insert ignore into `dummy` (`i`) values (1)";
$reg8log_db->query($query);
echo '</h4><center><h3>Setup completed.</h3>';
echo '<a href="../index.php">Login page</a></center>';
require '../include/code/code_set_submitted_forms_cookie.php';

echo '</td></tr></table></body></html>';
unset($_SESSION['setup_key']);
exit;
} while(false);

require '../include/page/page_setup_form2.php';

?>
