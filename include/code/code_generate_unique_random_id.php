<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!isset($parent_page)) exit("<center><h3>Error: Direct access denied!</h3></center>");

if(!isset($index_dir)) $index_dir='';

require_once $index_dir.'include/func/func_random.php';

$query="select * from `$table_name` where `$field_name`";
$i=0;
$random_ids=array();
do {
do {
$rid=random_string(8);
if($i++>1000) {
$failure_msg=($debug_mode)? $reg8log_db->err_msg : "Unable to generate a random unique  id.";
require $index_dir.'include/page/page_failure.php';
exit;
}
} while(in_array($rid, $random_ids));
$random_ids[]=$rid;
$i++;
} while($reg8log_db->result_num("$query='$rid' limit 1"));

?>