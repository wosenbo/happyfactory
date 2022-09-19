<?php
require_once './include/common.inc.php';

$action = gp('action');
$fuid = gp('fuid', 0);

switch ($action) {
	case 'refresh':
		$db->query("DELETE FROM game_friends WHERE uid = $uid");
		$fuids = $my->api_client->friend_getAppUsers();
		foreach ($fuids as $fuid) {
			$db->query("INSERT INTO game_friends (uid, fuid) VALUES ('{$uid}', '{$fuid}')");
		}
		$query = $db->query("SELECT f.fuid, u.username FROM game_friends f LEFT JOIN game_users u ON u.fuid = u.uid WHERE f.uid = '{$uid}'");
		while ($friend = $db->fetch_array($query)) {
			echo '<li><a href="friend.php?fuid='.$friend['fuid'].'">'.$friend['username'].'</a></li>';
		}
		break;
	
	default:
		$userInfo = $db->fetch_first("SELECT u.*,u_p.*,u_pl.empiric AS uEmpiric FROM game_users u ".
									 "LEFT JOIN game_userproperty u_p ON u.uid = u_p.uid ".
									 "LEFT JOIN game_userlevel u_pl ON u_p.level = u_pl.level ".
									 "WHERE u.uid = $fuid");
		$smarty->assign('userInfo', $userInfo);
		
		$makeQuery = $db->query("SELECT m.*,p.productname,p.pic FROM game_makeprocess m ".
								"LEFT JOIN game_products p ON m.pid = p.pid ".
								"WHERE m.uid = $fuid");
		if($makeQuery)
		{
			while($makeItem = $db->fetch_array($makeQuery)) {
				$makeList[] = array(
									'id' => $makeItem['id'],
									'uid' => $makeItem['uid'],
									'productname' => $makeItem['productname'],
									'pic' => $makeItem['pic'],
									);
			}
			$smarty->assign('makeList', $makeList);
		}

		$friendInfo = $db->fetch_first("SELECT COUNT(*) AS fCount FROM game_friends WHERE uid = $uid");
		if($friendInfo['fCount'] == 0)
		{
			$friendList = $my->api_client->friend_getAppUsers();
			foreach($friendList as $friendUid) {
				$db->query("INSERT INTO game_friends(uid,fuid) VALUES ($uid,$friendUid)");
			}
			$smarty->assign('updated', '1');
		}

		$friendQuery = $db->query("SELECT f.uid,f.fuid,u.username FROM game_friends f ".
								  "LEFT JOIN game_users u ON f.fuid = u.uid ".
								  "WHERE f.uid = $uid");
		while($friend = $db->fetch_array($friendQuery)) {
			$friend_list[] = array('userid' => $friend['fuid'], 'username' => $friend['username']);
		}
		$smarty->assign('friend_list', $friend_list);
		
		$smarty->display('friend.html');
		/*
		$friend = $db->fetch_first("SELECT COUNT(*) AS fcount FROM game_friends WHERE uid = $fuid");
		if($friend['fcount'] == 0)
		{
			$friends = $my->api_client->friend_getAppUsers();
			foreach($friends as $fuid)
			{
				$db->query("INSERT INTO game_friends(uid,fuid) VALUES ($uid,$fuid)");
			}
			$smarty->assign('updated', '1');
		}
		
		$result = $db->query("SELECT f.uid,f.fuid,u.username FROM game_friends f ".
							 "LEFT JOIN game_users u ON f.fuid=u.uid ".
							 "WHERE f.uid=$uid");
		while($row = $db->fetch_array($result))
		{
			$friend_list[] = array('userid'=> $row['fuid'], 'username'=>$row['username']);
		}
		
		$smarty->assign('userId', $fuid);
		$smarty->assign('friend_list', $friend_list);
		$smarty->display('friend.html');
		*/
}
