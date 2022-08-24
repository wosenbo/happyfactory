<?php
function check_friend($uid, $fuid) {
	global $db;
	$row = $db->fetch_first("SELECT COUNT(*) as totalnum FROM game_friends WHERE uid = '{$uid}' AND fuid = '{$fuid}'");
	return intval($row['totalnum']) > 0;
}

require_once './include/api.php';
require_once './include/warehouse.php';

$module = gp('module');
$action = gp('action');
$ajax = gp('ajax', 0);

switch($action){
	case 'product':
		json_result(['products' => get_warehouses($uid)]);
		break;
	case 'sell':
		$products = [];
		$query = $db->query("SELECT m.*, p.productname FROM game_markets m LEFT JOIN game_products p ON p.pid = m.pid WHERE m.uid = '{$uid}'");
		while($row = $db->fetch_array($query)){
			$products[] = $row;
		}
		json_result(['products' => $products]);
		break;
}

switch($module){
	case 'sell':
		// $products = get_warehouses($uid);
		// $smarty->assign('product_list', $products);
		$smarty->display('market_sell.html');
		break;

	case 'move':
		$query = $db->query("SELECT m.*, p.productname FROM game_markets m
			LEFT JOIN game_products p ON m.pid = p.pid
			WHERE m.uid = '$uid'");
		$prudcts = array();
		while($row = $db->fetch_array($query)) {
			$products[] = $row;
		}
		$smarty->assign('products', $products);
		$smarty->display('market_move.html');
		break;

	case 'detail':
		$productid = isset($_GET['productid']) ? intval($_GET['productid']) : 0;
		if($productid){
			$product = $db->fetch_first("SELECT * FROM hap_product WHERE id = '{$productid}'");
			$material = null;
			if(!empty($product['material'])){
				$arr = json_decode($product['material'], true);
				$result = $db->query("SELECT * FROM hap_product WHERE id IN (".implode(',', array_keys($arr)).")");
				while($row = mysql_fetch_assoc($result)){
					$row['quantity'] = $arr[$row['id']];
					$material[] = $row;
				}
			}
			$product['material'] = $material;
			echo json_encode(array('product' => $product));
		}
	break;
		
	case 'buylog':
		$marketlog = $db->query("SELECT m.*,p.productname,u.username FROM game_marketlog m ".
							 "LEFT JOIN game_products p ON m.pid=p.pid ".
							 "LEFT JOIN game_users u ON m.suid=u.uid ".
							 "WHERE m.buid=$uid ORDER BY dateline DESC");
		while($row = $db->fetch_array($marketlog))
		{
			$log_list[] = array(
								'productname' => $row['productname'],
								'price' => $row['price'],
								'count' => $row['count'],
								'seller' => $row['username'],
								'date' => $row['dateline']
								);
		}
		$smarty->assign('log_list', $log_list);
		$smarty->display('market_buy_log.html');
		break;
		
	case 'selllog':
		$marketlog = $db->query("SELECT m.*,p.productname,u.username FROM game_marketlog m ".
							 "LEFT JOIN game_products p ON m.pid=p.pid ".
							 "LEFT JOIN game_users u ON m.suid=u.uid ".
							 "WHERE m.suid=$uid ORDER BY m.dateline DESC");
		while($row = $db->fetch_array($marketlog))
		{
			$log_list[] = array(
								'productname' => $row['productname'],
								'price' => $row['price'],
								'count' => $row['count'],
								'buyer' => $row['username'],
								'date' => $row['dateline']
								);
		}
		if($ajax){
			foreach($log_list as $key => &$val){
				$val['date'] = date('Y-m-d H:i', $val['date']);
			}
			die(json_encode(array('list'=>$log_list)));
		}
		$smarty->assign('log_list', $log_list);
		$smarty->display('market_sell_log.html');
		break;
	
	default:
		$cate_id = intval(gp('cateid', 0));
		$ajax = intval(gp('ajax', 0));
		if($ajax){
			$sqladd = '';
			if($cate_id>0){
				$sqladd .= " AND p.cateid = '{$cate_id}'";
			}
			$query = $db->query("SELECT m.*, p.productname FROM game_markets m LEFT JOIN game_products p ON p.pid = m.pid WHERE m.uid != '{$uid}'{$sqladd}");
			$products = [];
			while($row = $db->fetch_array($query)){
				$row['is_friend'] = check_friend($uid, $row['uid']) ? 1 : 0;
				$products[] = $row;
			}
			json_result(['products' => $products]);
		}
		// $categoryId = $_REQUEST['cateid'];
		// $categoryId = isset($_GET['cateid']) ? intval($_GET['cateid']) : 0;
		// $sqladd = empty($categoryId) ? "" : "AND cateid = $categoryId";
		
		// $result = $db->query("SELECT id,category FROM game_productcategory");
		// while($category = $db->fetch_array($result)) {
		// 	$categoryList[] = array('id'=>$category['id'], 'category'=>$category['category']);
		// }
		// $smarty->assign('categoryList', $categoryList);
		
		// $data = $db->query("SELECT m.*,p.productname FROM game_markets m ".
		// 				   "LEFT JOIN game_products p ON m.pid = p.pid ".
		// 				   "WHERE m.uid <> $uid $sqladd");
		// while($market = $db->fetch_array($data)) {
		// 	$friend = $db->fetch_first("SELECT uid,fuid FROM game_friends WHERE uid = $uid AND fuid = ". $market['uid']);
		// 	$friendstatus = ($friend == FALSE) ? 0 : 1;
		// 	$market_list[] = array(
		// 						   'marketid' => $market['id'],
		// 						   'pid' => $market['pid'],
		// 						   'productname' => $market['productname'],
		// 						   'uid' => $market['uid'],
		// 						   'seller' => $market['username'],
		// 						   'count' => $market['count'],
		// 						   'price' => $market['price'],
		// 						   'friendstatus' => $friendstatus,
		// 						   );
		// }
		// if($ajax){
		// 	die(json_encode(array('list'=>$market_list)));
		// }
		// $smarty->assign('market_list', $market_list);
		$smarty->display('market.html');
}
?>