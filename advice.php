<?php
require_once './include/common.inc.php';
$action = gp('action');

switch($action){
case 'add':
	$content = gp('content');
	if(!empty($content)){
		$now = time();
		$db->query(sprintf("insert into game_advices(uid,content,dateline) values (%d, '%s', %d)",
							$uid, $content, $now));
		die('我们已收到您的建议，会尽快给您回复，谢谢！');
	}
	break;
default:
	die('请选择操作');
}