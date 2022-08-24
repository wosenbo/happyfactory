<?php

/**
 * 初始化模板引擎
 */
function template_init()
{
	global $smarty;
	$smarty->template_dir = './templates/';
	$smarty->compile_dir = './templates_c/';
	$smarty->cache_dir = './cache/';
	$smarty->config_dir = './configs/';
	$smarty->left_delimiter = '<{';
	$smarty->right_delimiter = '}>';
}

/**
 * 玩家登录奖励
 */
function login_reward()
{
	global $db, $my, $uid, $smarty;
	$userArr = $db->fetch_first("SELECT uid,updated FROM game_users WHERE uid = '$uid'");
	$interval = ceil((time() - $userArr['updated']) / 3600);
	if($interval > 2) {
		$db->query("UPDATE game_userproperty SET money=money+". LOGIN_REWARD ." WHERE uid = '$uid'");
		$db->query("UPDATE game_users SET updated='". time() ."' WHERE uid = '$uid'");
		$smarty->assign('loginReward', 1);
		
		// 发送feed
		$feed_message = '登录 <a href="index.php">开心梦工厂</a> 获得 '. LOGIN_REWARD . ' 金币。';
		$feed_body = '';
		
		$title_template = '{actor} ' . $feed_message . '';
		$title_data = '';
		$body_template = '';
		$body_data = '';
		$body_general = $message;
		$image_1 = '';
		$image_1_link = 'index.php';
		$image_2 = '';
		$image_2_link = '';
		$image_3 = '';
		$image_3_link = '';
		$image_4 = '';
		$image_4_link = '';
		$target_ids = $uid;
		$my->api_client->feed_publishTemplatizedAction(
					   $title_template, $title_data, $body_template, $body_data,
					   $body_general, $image_1, $image_1_link, $image_2, $image_2_link,
					   $image_3, $image_3_link, $image_4, $image_4_link, $target_ids
					   );
		
	} else {
		$smarty->assign('loginReward', 0);
	}
}

/**
 * 检查玩家是否来自邀请
 */
function inviter_check()
{
	global $db, $uid;
	if(!empty($_REQUEST['inviter'])) {
		$invUid = $_REQUEST['inviter'];
		$invArr = $db->fetch_first("SELECT uid,inviter,status FROM game_invites WHERE uid='$uid' AND inviter='$invUid' AND status=0");
		if($invArr) {
			$db->query("UPDATE game_invites SET dateinto='". time() ."' WHERE uid='$uid' AND inviter='$invUid'");
		}
	}
}

/**
 * 发送奖励给邀请者
 */
function inviter_reward()
{
	global $db, $uid;
	$invQuery = $db->query("SELECT uid,inviter,dateinto,status FROM game_invites WHERE uid='$uid' AND dateinto<>0 AND status=0");
	while($invItem = $db->fetch_array($invQuery)) {
		$invUid = $invItem['inviter'];
		$db->query("UPDATE game_userproperty SET money=money+". INVITE_REWARD ." WHERE uid='$invUid'");  // fixed by xiaogang
		$db->query("UPDATE game_invites SET status=1 WHERE uid='$uid' AND inviter='$invUid'");
	}
}

?>