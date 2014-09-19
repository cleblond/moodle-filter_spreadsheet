<?php
require_once(dirname(__FILE__) . '/../../../../config.php');
//echo ;
$db_host = $CFG->dbhost;
$db_port = (empty($CFG->dboptions['dbport']) ? '3306' : $CFG->dboptions['dbport']);
//$db_port = $CFG->dboptions['dbport'];
$db_user = $CFG->dbuser;
$db_pass = $CFG->dbpass;
$db_name = $CFG->dbname;
$db_prefix = $CFG->prefix . 'atto_spreadsheet_';
$db_type = 'MySQLi';
require_once('db_mysqli.php');
?>
