<?php
function get_categories() {
	global $db;
	static $categories = null;
	if (is_null($categories)) {
		$categories = array();
		$query = $db->query("SELECT * FROM game_productcategory");
		while ($row = $db->fetch_array($query)) {
			$categories[] = $row;
		}
	}
	return $categories;
}

function get_warehouse_price($uid) {
	global $db;
	$row = $db->fetch_first("SELECT SUM(w.count * p.price) AS price FROM game_warehouses w INNER JOIN game_products p ON p.pid = w.pid WHERE w.uid = '{$uid}'");
	return $row ? $row['price'] : 0;
}

require_once './include/api.php';
require_once './include/warehouse.php';

switch(gp('action')) {
	case 'get_product_count':
		$pid = isset($_POST['pid']) ? (int) $_POST['pid'] : 0;
		$count = $db->result_first("SELECT COUNT(*) FROM #__warehouse WHERE uid = '$uid'
			AND pid = '$pid'");
		return $count;
		break;


	case 'list':
		$cid = isset($_POST['cid']) ? (int) $_POST['cid'] : 0;
		if($cid)
			$sqladd = "AND p.cateid = '$cid' ";
		$query = $db->query("SELECT w.*, p.productname, p.pic, p.price FROM #__warehouse w
			LEFT JOIN #__products p ON w.pid = p.pid WHERE w.uid = '$uid' {$sqladd}");
		while($row = $db->fetch_array($query)) {
			$warehouse_list[] = $row;
		}
		$smarty->assign('warehouse_list', $warehouse_list);
		$smarty->display('warehouse_productlist.html');
		break;
	
	case 'input':
		$pid = $_POST['pid'];
		$sql = "SELECT w.*,p.pid,p.productname,p.pic,p.price FROM game_warehouses w ".
				"LEFT JOIN game_products p ON w.pid=p.pid ".
				"WHERE w.uid=$uid AND w.pid=$pid";
		
		
		
		$warehouse = $db->fetch_first($sql);
		if($warehouse == FALSE)
		{
			echo '您刚刚已经把这个商品卖给商店了！';
		}
		else
		{
			$smarty->assign('productname', $warehouse['productname']);
			$smarty->assign('count', $warehouse['count']);
			$smarty->assign('pic', $warehouse['pic']);
			$smarty->assign('price', $warehouse['price']);
			$smarty->display('warehouse_form.html');
		}
		break;
		
		

		
	case 'sell':
		$pid = gp('pid', 0);
		$amount = intval(gp('amount', 0));
		if($amount < 1){
			json_error('出售数量不能小于1');
		}
		$warehouse = get_warehouse($uid, $pid);
		if ($amount > $warehouse['count']) {
			json_error('库存数量不足');
		}
		if($amount >= $warehouse['count']){
			$db->query("DELETE FROM game_warehouses WHERE uid = '{$uid}' AND pid = '{$pid}'");
		}else{
			$db->query("UPDATE game_warehouses SET count = count - {$amount} WHERE uid = '{$uid}' AND pid = '{$pid}'");
		}
		$db->query("UPDATE game_userproperty SET money = money + {$warehouse['total_price']} WHERE uid = '{$uid}'");
		json_success();
		break;

	case 'stat':
		json_result(['total_price' => get_warehouse_price($uid)]);
		break;

	default:
		$cate_id = gp('cateid', 0);
		$ajax = gp('ajax', 0);
		if($ajax){
			json_result(['products' => get_warehouses($uid, $cate_id)]);
		}
		$smarty->display('warehouse.html');
		break;
}
