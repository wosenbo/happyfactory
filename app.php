<?php
/* $Id:app.php 2010/4/25 22:43 xiaogang $ */

require_once './config.inc.php';
require_once INC_PATH .'/Manyou_v0.5.php';
require_once INC_PATH .'/db_mysql.class.php';

$my = new Manyou($api_key, $secret);
$uid = (int) $my->get_loggedin_user();

$db = new dbstuff();
$db->connect($options);

$action = isset($action) ? $action : '';

switch($action) {
	case 'install':
		if($my->is_installation())
			$db->query("UPDATE #__users set unstalled = 0 WHERE uid = '$uid'");

		$my->redirect('index.php');
	break;

	case 'uninstall':
		$db->query("UPDATE #__users SET unstalled = 1 WHERE uid = '$uid'");
	break;
}
