<?php

function get_product_categories() {
	global $db;
	static $p_categories = null;
	if (is_null($p_categories)) {
		$p_categories = [];
		$query = $db->query("SELECT * FROM game_productcategory");
		while ($row = $db->fetch_array($query)) {
			$p_categories[] = $row;
		}
	}
	return $p_categories;
}

function get_products($cate_id) {
	global $db;
	$cate_id = intval($cate_id);
	$sql = "SELECT * FROM game_products WHERE mode = 0";
	if ($cate_id > 0) {
		$sql .= " AND cateid = '{$cate_id}'";
	}
	$products = [];
	$query = $db->query($sql);
	while ($row = $db->fetch_array($query)) {
		$products[] = $row;
	}
	return $products;
}

function get_product($pid) {
	global $db;
	return $db->fetch_first("SELECT * FROM game_products WHERE pid = '{$pid}'");
}

function reduce_money($uid, $money) {
	global $db;
	$db->query("UPDATE game_userproperty SET money = money - {$money} WHERE uid = '{$uid}'");
}

require_once './include/warehouse.php';
require_once './include/api.php';

switch(gp('action'))
{
	case 'category':
		json_result(['categories' => get_product_categories()]);
		break;
	case 'input':
		$pid = $_POST['pid'];
		$product = $db->fetch_first("SELECT * FROM game_products WHERE pid=". $pid);
		$warehouse = $db->fetch_first("SELECT id,uid,pid,count FROM game_warehouses WHERE uid=$uid AND pid=$pid");
		
		$smarty->assign('pid', $pid);
		$smarty->assign('productname', $product['productname']);
		$smarty->assign('warehouse_count', $warehouse['count']);
		$smarty->assign('pic', $product['pic']);
		$smarty->assign('price', $product['price']);
		$smarty->display('store_form.html');
		break;
	
	case 'buy':
		$pid = intval(gp('pid', 0));
		$amount = intval(gp('amount', 0));
		if ($amount <= 0) {
			json_error('购买数量不能少于1');
		}
		$product = get_product($pid);
		if (!$product) {
			json_error('商品不存在或已下架');
		}
		$total_price = $product['price'] * $amount;
		if ($total_price > $userData['money']) {
			json_error('余额不足');
		}
		add_warehouse($uid, $pid, $amount);
		reduce_money($uid, $total_price);
		json_success();

		// $pid = $_POST['pid'];
		// $count = intval($_POST['count']);
		// if($count <= 0)
		// {
		// 	echo '请输入正确的购买数量！';
		// }
		// else
		// {
		// 	$product = $db->fetch_first("SELECT pid,productname,price FROM game_products WHERE pid=$pid");
		// 	$totalPrice = $product['price'] * $count;
		// 	if($totalPrice > $userData['money'])
		// 	{
		// 		echo '您没有足够的金钱购买这些物品！';
		// 	}
		// 	else
		// 	{
		// 		$warehouse = $db->fetch_first("SELECT uid,pid,count FROM game_warehouses WHERE uid=$uid AND pid=$pid");
		// 		if(!$warehouse)
		// 		{
		// 			$db->query("INSERT INTO game_warehouses(uid,pid,count) VALUES ($uid,$pid,$count)");
		// 		}
		// 		else
		// 		{
		// 			$db->query("UPDATE game_warehouses SET count=count+$count WHERE uid=$uid AND pid=$pid");
		// 		}
		// 		$db->query("UPDATE game_userproperty SET money=money-$totalPrice WHERE uid=$uid");
		// 		echo "成功购买了 $count 个 ". $product['productname'];
		// 	}
		// }
		break;
		
	case 'list':
		$categoryId = $_REQUEST['cateid'];
		$sql = "SELECT * FROM game_products WHERE mode = 0 ";
		if(isset($categoryId) && $categoryId != 0)
		{
			$sql .= "AND cateid = $categoryId";
		}
		
		$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			$store_list[] = array(
								  'pid' => $row['pid'],
								  'cateid' => $row['cateid'],
								  'productname' => $row['productname'],
								  'pic' => $row['pic'],
								  'level' => $row['level'],
								  'price' => $row['price'],
								  'mode' => $row['mode'],
								  'remark' => $row['remark']
								  );
		}
		
		$smarty->assign('store_list', $store_list);
		$smarty->display('store_product_list.html');
		break;
	
	default:
		$cate_id = gp('cateid');
		$ajax = intval(gp('ajax', 0));
		if ($ajax) {
			$products = get_products($cate_id);
			json_result(['products' => $products]);
		}
		$smarty->display('store.html');
}
