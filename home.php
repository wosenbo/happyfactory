<?php

require_once 'include/bootstrap.php';

/*

@ini_set('display_errors', 0);
@error_reporting(0);







$action = trim($_GET['action']);

require_once './include/common.inc.php';

// inviter_reward();
// login_reward();

$logs = array();

$query = $db->query("SELECT id,title,content,dateline,hits FROM {$tablepre}changelog ORDER BY id DESC");
while($log = $db->fetch_array($query)) {
	$logs[] = $log;
}


$factoryList = array();
$query = $db->query("SELECT * FROM game_factory");
while($row = $db->fetch_array($query)) {
	$factoryList[] = $row;
}

$factoryQuery = $db->query("SELECT * FROM game_factorys");
while($factory = $db->fetch_array($factoryQuery))
{
	$userFactory = $db->fetch_first("SELECT factid,uid,level FROM game_userfactory WHERE uid=$uid AND factid=". $factory['factid']);
	$status = $userFactory ? 1 : 0;
	$factoryList[] = array(
						   'factid' => $factory['factid'],
						   'factoryname' => $factory['factoryname'],
						   'pic' => $factory['pic'],
						   'pic_act' => $factory['pic2'],
						   'status' => $status
						   );
}
$smarty->assign('factoryList', $factoryList);

$userStudy = $db->fetch_first("SELECT * FROM game_userstudy WHERE uid=$uid");
if(!$userStudy)
{
	$smarty->assign('userStudy', 0);
}
else
{
	$userStudy = $db->fetch_first("SELECT * FROM game_userstudy WHERE uid=$uid");
	$query = $db->query("SELECT * FROM game_studys ORDER BY level");
	while($study = $db->fetch_array($query))
	{
		$studyLevels[] = array('efficiency'=>$study['efficiency'], 'cost'=>$study['cost'], 'level'=>$study['level']);
	}
	$smarty->assign('elevel', $userStudy['elevel']);
	$smarty->assign('clevel', $userStudy['clevel']);
	$smarty->assign('studyLevels', $studyLevels);
	$smarty->assign('userStudy', 1);
}

$adviceQuery = $db->query("SELECT * FROM game_advices WHERE uid=$uid");
while($advice = $db->fetch_array($adviceQuery))
{
	$adviceList[] = array('content'=>$advice['content'], 'reply'=>$advice['reply']);
}
$smarty->assign('adviceList', $adviceList);

$smarty->display('index.html');
?>
