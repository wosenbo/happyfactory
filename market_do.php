<?php
function get_market($mid) {
	global $db;
	return $db->fetch_first("SELECT m.*, p.productname FROM game_markets m LEFT JOIN game_products p ON m.pid = p.pid WHERE m.id = '{$mid}'");
}

function count_market($uid, $pid) {
	global $db;
	$row = $db->fetch_first("SELECT COUNT(*) totalnum FROM game_markets WHERE uid = '{$uid}' AND pid = '{$pid}'");
	return $row ? intval($row['totalnum']) : 0;
}

function buy_market($uid, $amount, $market) {
	global $db;
	$sql = sprintf("INSERT INTO game_marketlog (suid, buid, pid, price, count, dateline) VALUES (%d, %d, %d, %d, %d, %d)",
					$market['uid'], $uid, $market['pid'], $market['price'], $amount, time());
	$db->query($sql);

	if ($amount == $market['count']) {
		$db->query("DELETE FROM game_markets WHERE id = '{$market['id']}'");
	} else {
		$db->query("UPDATE game_markets SET count = count - {$amount} WHERE id = '{$market['id']}'");
	}
}

function check_friend($uid, $fuid) {
	global $db;
	$row = $db->fetch_first("SELECT COUNT(*) as totalnum FROM game_friends WHERE uid = '{$uid}' AND fuid = '{$fuid}'");
	return intval($row['totalnum']) > 0;
}

// 非好友之间的交易费率
define('TRADE_RATE', 0.05);

require_once './include/api.php';
require_once './include/warehouse.php';

switch(gp('action'))
{
	case 'get_product_count':
		$marketId = $_POST['marketid'];
		$arr = $db->fetch_first("SELECT id,uid,count FROM game_markets WHERE id='$marketId'");
		echo ($arr) ? $arr['count'] : '0';
		break;
	
	case 'sell':
		$pid = gp('pid', 0);
		$amount = gp('amount', 0);
		$price = gp('price', 0);
		$warehouse = get_warehouse_by_pid($uid, $pid);
		if(!$warehouse){
			json_error('库存中无此商品');
		}
		if($amount < 1){
			json_error('出售数量不能小于1');
		}
		if($amount > $warehouse['count']){
			json_error('出售数量不能多于库存');
		}
		if(!count_market($uid, $pid)){
			$now = time();
			$db->query(sprintf("INSERT INTO game_markets (uid, username, pid, price, count, dateline) VALUES (%d, '%s', %d, %d, %d, %d)",
								$uid, $userData['username'], $pid, $price, $amount, $now));
			if($amount == $warehouse['count']){
				$db->query("DELETE FROM game_warehouses WHERE uid = '{$uid}' AND pid = '{$pid}'");
			}else{
				$db->query("UPDATE game_warehouses SET count = count - {$amount} WHERE uid = '{$uid}' AND pid = '{$pid}'");
			}
		}else{
			json_error('此商品正在出售');
		}
		json_success();
		break;
	
	case 'buy':
		$mid = intval(gp('mid', 0));
		$buy_amount = intval(gp('count', 0));
		if ($count<1) {
			json_error('购买数量不能小于1');
		}
		$market = get_market($mid);
		if (!$market) {
			json_error('商品不存在');
		}
		if ($buy_amount > $market['count']) {
			json_error('库存不足');
		}
		$total_price = $buy_amount * $market['price'];
		$is_friend = check_friend($uid, $market['uid']);
		if (!$is_friend) {
			$total_price = ceil($total_price + ($total_price * TRADE_RATE));
		}
		if ($total_price > $userData['money']) {
			json_error('余额不足');
		}
		update_money($uid, -$total_price);
		update_money($market['uid'], $total_price);
		add_warehouse($uid, $market['id'], $buy_amount);
		buy_market($uid, $buy_amount, $market);
		json_success();
		break;
		
	case 'move':
		$marketid = $_POST['marketid'];
		$market = $db->fetch_first("SELECT * FROM game_markets WHERE id=$marketid");
		$query = $db->query("SELECT uid,pid FROM game_warehouses WHERE uid=$uid AND pid=". $market['pid']);
		if($query == FALSE)
		{
			$db->query("INSERT INTO game_warehouses(uid,pid,count) VALUES ($uid,". $market['pid'] .",". $market['count']);
		}
		else
		{
			$db->query("UPDATE game_warehouses SET count=count+". $market['count'] ." WHERE uid=$uid AND pid=". $market['pid']);
		}
		$db->query("DELETE FROM game_markets WHERE id=$marketid");
		echo '下架成功！';
		break;
	
	default:
		echo '参数错误，请重试！';
}
