<?php
if (empty($_REQUEST['action']) && empty($_REQUEST['msd_action'])) {
	die('Access denied');
}

if (!empty($_REQUEST['action'])) {$_REQUEST['msd_action'] = $_REQUEST['action'];}

require dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';