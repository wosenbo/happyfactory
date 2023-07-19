<?php
function get_factories() {
	global $db;
	static $factories = null;
	if (is_null($factories)) {
		$factories = array();
		$query = $db->query("SELECT * FROM game_factorys");
		while ($factory = $db->fetch_array($query)) {
			$factories[] = $factory;
		}
	}
	return $factories;
}

function get_user_factories($uid) {
	global $db;
	$user_facts = array();
	$factories = get_factories();
	foreach ($factories as $factory) {
		$user_fact = $db->fetch_first("SELECT factid, status FROM game_userfactory WHERE uid = {$uid} AND factid = {$factory['factid']}");
		$status = $user_fact ? $user_fact['status'] : 0;
		$user_facts[] = array(
			'factid' => $factory['factid'],
			'factoryname' => $factory['factoryname'],
			'pic' => $factory['pic'],
			'pic_act' => $factory['pic2'],
			'status' => $status,
		);
	}
	return $user_facts;
}

function get_studies() {
	global $db;
	$studies = [];
	$query = $db->query("SELECT * FROM game_studys ORDER BY level");
	while ($study = $db->fetch_array($query)) {
		$studies[] = $study;
	}
	return $studies;
}

function get_advises($uid) {
	global $db;
	$advises = array();
	$query = $db->query("SELECT * FROM game_advices WHERE uid = '{$uid}'");
	while ($advise = $db->fetch_array($query)) {
		$advises[] = $advise;
	}
	return $advises;
}

require_once './include/api.php';
require_once './include/user.func.php';

$action = gp('action');

// inviter_reward();
// login_reward();

$logs = array();

switch($action){
	case 'factory':
		json_result(['factories' => get_user_factories($uid)]);
		break;

	case 'study':
		json_result([
			'studies' => get_studies(),
			'user_study' => get_user_study($uid)]);
		break;
}

$smarty->assign('factoryList', get_user_factories($uid));
$userStudy = get_user_study($uid);
if (!$userStudy) {
	$smarty->assign('userStudy', 0);
} else {
	$levels = array();
	$studies = get_studies();
	foreach ($studies as $study) {
		$levels[] = array(
			'efficiency' => $study['efficiency'],
			'cost' => $study['cost'],
			'level' => $study['level'],
		);
	}
	$smarty->assign('elevel', $userStudy['elevel']);
	$smarty->assign('clevel', $userStudy['clevel']);
	$smarty->assign('studyLevels', $levels);
	$smarty->assign('userStudy', 1);
}
$smarty->assign('adviceList', get_advises($uid));
$smarty->display('index.html');
