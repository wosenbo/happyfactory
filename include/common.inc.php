<?php
/**
 * $Id: common.inc.php 2010/3/10 00:42 xiaogang $
 */

function gp($name, $default = '') {
	$value = $default;
	if (array_key_exists($name, $_POST)) {
		$value = trim($_POST[$name]);
	} elseif (array_key_exists($name, $_GET)) {
		$value = trim($_GET[$name]);
	}
	return $value;
}

@ini_set('display_errors', 1);
@error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

define('APP_URL', '/happyfactory/');

// header('Content-type:text/html; Charset=utf-8');

// set default timezone
if(function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Asia/Shanghai');
}

define('ROOT_PATH', dirname(dirname(__FILE__)));

require_once ROOT_PATH.'/./config.inc.php';
require_once ROOT_PATH.'/./libs/Smarty.class.php';
require_once ROOT_PATH.'/./include/Manyou_v0.5.php';
require_once ROOT_PATH.'/./include/db_mysql.class.php';
require_once ROOT_PATH.'/./include/global.func.php';

foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) {
		$_key{0} != '_' && $$_key = $_value;
	}
}

$smarty = new Smarty();
template_init();
$db = new dbstuff();
$db->connect($dbhost, $dbuser, $dbpwd, $dbname, 0, TRUE, 'utf8');

// $my = new Manyou(API_KEY, API_SECRET);
// $uid = intval($my->api_client->user_getLoggedInUser());
$uid = 397329017;

//inviter_check();
//$my->require_add();    // 要求安装应用

$sql = sprintf("SELECT u.*,u_p.*,u_pl.level AS pLevel,u_pl.empiric AS pEmpiric FROM game_users u
	LEFT JOIN game_userproperty u_p ON u.uid = u_p.uid
	LEFT JOIN game_userlevel u_pl ON u_p.level = u_pl.level
	WHERE u.uid = '%d'", $uid);

$userData = $db->fetch_first($sql);

if(!$userData) {
	// $currentUserData = $my->api_client->user_getinfo($uid, array('uch_id,name,admin_level,site'));
	// $currentUserData = $currentUserData[0];
	// $db->query("INSERT INTO game_users(uid,uchid,username,admin_level,siteid,updated) VALUES (".
	// 				$uid .",". $currentUserData['uch_id'] .",'". $currentUserData['name'] ."','".
	// 				$currentUserData['admin_level'] ."',". $currentUserData['site'] .",$now)");
	// $db->query("INSERT INTO game_userproperty(uid) VALUES ($uid)");
	// $userData = $db->fetch_first($sql);	
}

if($userData['status'] == 1) {
	die('报歉：您的帐号处于锁定状态，无法访问本应用！');
}

$adminid = empty($userData['adminid']) ? '0' : $userData['adminid'];

$smarty->assign('app_url', APP_URL);
$smarty->assign('uid', $userData['uid']);
$smarty->assign('adminid', $adminid);
$smarty->assign('username', $userData['username']);
$smarty->assign('money', $userData['money']);
$smarty->assign('level', $userData['level']);
$smarty->assign('empiricnow', $userData['empiricnow']);
$smarty->assign('empiric', $userData['pEmpiric']);
