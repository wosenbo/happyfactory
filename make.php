<?php
function get_make_products($cate_id) {
	global $db;
	$query = $db->query("SELECT m.*, p.productname FROM game_makes m LEFT JOIN game_products p ON p.pid = m.pid WHERE m.makecateid = '{$cate_id}'");
	$list = array();
	while ($row = $db->fetch_array($query)) {
		$list[] = $row;
	}
	return $list;
}

function get_make_product($mid) {
	global $db;
	return $db->fetch_first("SELECT m.*, p.productname, p.level FROM game_makes m LEFT JOIN game_products p ON p.pid = m.pid WHERE m.makeid = '{$mid}'");
}

function count_make_process($uid, $cate_id) {
	global $db;
	$row = $db->fetch_first("SELECT COUNT(*) totalnum FROM game_makeprocess WHERE uid = '{$uid}' AND makecateid = '{$cate_id}'");
	return $row ? $row['totalnum'] : 0;
}

function get_make_process($proc_id) {
	global $db;
	return $db->fetch_first("SELECT mp.*, p.productname, p.level FROM game_makeprocess mp LEFT JOIN game_products p ON p.pid = mp.pid WHERE id = '{$proc_id}'");
}

function add_make_process($uid, $count, $product) {
	global $db;
	$start_time = time();
	$end_time = time() + ($product['maketime'] * $count);
	$db->query(sprintf("INSERT INTO game_makeprocess (makecateid, uid, pid, count, starttime, endtime) VALUES (%d, %d, %d, %d, %d, %d)",
						$product['makecateid'], $uid, $product['pid'], $count, $start_time, $end_time));
}

function add_make_log($uid, $proc) {
	global $db;
	$now = time();
	$db->query(sprintf("INSERT INTO game_makelog (makecateid, uid, pid, productname, count, starttime, endtime, dateline) VALUES (%d, %d, %d, '%s', %d, %d, %d, %d)",
						$proc['makecateid'], $uid, $proc['pid'], $proc['productname'], $proc['count'], $proc['starttime'], $proc['endtime'], $now));
}

function remove_process($proc_id) {
	global $db;
	$db->query("DELETE FROM game_makeprocess WHERE id = '{$proc_id}'");
}

function extract_material($data) {
	$materials = [];
	$items = explode(',', $data);
	foreach($items as $key => $item){
		list($pid, $count) = explode(':', $item);
		$materials[] = ['pid' => $pid, 'count' => $count];
	}
	return $materials;
}

function check_material($materials){
	global $uid;
	$check_pass = true;
	foreach($materials as $material){
		$wh_count = get_warehouse_count($uid, $material['pid']);
		if($wh_count < $material['count']){
			$check_pass = false;
			break;
		}
	}
	return $check_pass;
}

function update_user_level($uid, $exp) {
	global $db;
	$user_prop = $db->fetch_first("SELECT * FROM game_userproperty WHERE uid = '{$uid}'");
	if (!$user_prop) {
		return;
	}
	$user_level = $db->fetch_first("SELECT * FROM game_userlevel WHERE level = '{$user_prop['level']}'");
	$new_exp = $user_prop['empiricnow'] + $exp;
	if ($new_exp < $user_level['empiric']) {
		$db->query("UPDATE game_userproperty SET empiricnow = empiricnow + {$exp} WHERE uid = '{$uid}'");
	} else {
		$remain_exp = $new_exp - $user_level['empiric'];
		$db->query("UPDATE game_userproperty SET level = level + 1, empiricnow = {$remain_exp} WHERE uid = '{$uid}'");
	}
}

function upgrade_factory_level($uid, $fact_id, $new_level) {
	global $db;
	$status = 0;
	if($new_level == MAKE_MAX_LEVEL){
		$status = 1;
	}
	$db->query("UPDATE game_userfactory SET level='{$new_level}', status = '{$status}' WHERE factid = '{$fact_id}' AND uid = '{$uid}'");
}

define('FACTORY_LEVEL_MAX', 5);
// 生产线最高等级
define('F_MAX_LEVEL', 5);
define('MAX_LEVEL', 5);
// 生产数量限制
define('MAKE_LIMIT', 20);
define('MAKE_MAX_LEVEL', 5);

require_once './include/factory.php';
require_once './include/warehouse.php';
require_once './include/api.php';

$op = gp('op');
$fact_id = gp('fact_id', 0);

switch($op)
{
	case 'refresh_make_info':
		$factoryId = $_POST['factid'];
		$arr = $db->fetch_first("SELECT uf.*,f.pic FROM game_userfactory uf ".
								"LEFT JOIN game_factorys f ON uf.factid = f.factid ".
								"WHERE uf.factid = $factoryId AND uf.uid = $uid");
		$result = $arr['level'];
		echo $result;
		break;


	case 'get_levelup_info':
		$factid = $_POST['factid'];
		//$userfactory = $db->fetch_first("SELECT factid,uid,level FROM game_userfactory WHERE factid=$factid");
		$sql = "SELECT uf.*,f.factoryname,f.pic2 FROM game_userfactory uf ".
				"LEFT JOIN game_factorys f ON uf.factid=f.factid ".
				"WHERE uf.factid=$factid AND uf.uid=$uid";
		$userfactory = $db->fetch_first($sql);
		$nextLevel = $userfactory['level'] + 1;
		$levelInfo = $db->fetch_first("SELECT factid,level,price FROM game_factorylevel ".
									  "WHERE factid=$factid AND level=$nextLevel");
		$smarty->assign('factoryname', $userfactory['factoryname']);
		$smarty->assign('pic', $userfactory['pic2']);
		$smarty->assign('nextLevel', $nextLevel);
		$smarty->assign('price', $levelInfo['price']);
		$smarty->display('levelup.html');
		break;
		
	case 'get_make_title':
		$factid = $_POST['factid'];
		$factoryInfo = $db->fetch_first("SELECT uf.*,f.factoryname FROM game_userfactory uf ".
										"LEFT JOIN game_factorys f ON uf.factid=f.factid ".
										"WHERE uf.factid=$factid AND uf.uid=$uid");
		$smarty->assign('factid', $factid);
		$smarty->assign('factoryname', $factoryInfo['factoryname']);
		$smarty->assign('level', $factoryInfo['level']);
		$smarty->assign('status', ($factoryInfo['level'] > $userData['level']) ? 1 : 0);
		$smarty->display('make_title.html');
		break;

	case 'upgrade':
		$fact_id = gp('factid', 0);
		$user_fact = get_user_factory_by_id($uid, $fact_id);
		$new_level = $user_fact['level'] + 1;
		if ($new_level > $userData['level']) {
			json_error('目前只能升到'.$user_fact['level'].'级');
		}
		if ($new_level > MAKE_MAX_LEVEL) {
			json_error('已达到最高等级');
		}
		$fact_level = get_factory_level($fact_id, $new_level);
		if ($fact_level['price'] > $userData['money']) {
			json_error('金币不足');
		}
		$status = $new_level == MAKE_MAX_LEVEL ? 1 : 0;
		update_money($uid, -$fact_level['price']);
		upgrade_factory_level($uid, $fact_id, $new_level);
		json_result(['new_level' => $new_level, 'status' => $status]);
		break;

	case 'levelup':
		// $factid = $_POST['factid'];
		// $sql = "SELECT uf.*,f.factoryname FROM game_userfactory uf ".
		// 		"LEFT JOIN game_factorys f ON uf.factid=f.factid ".
		// 		"WHERE uf.factid=$factid AND uf.uid=$uid";
		// $userfactory = $db->fetch_first($sql);
		// $nextLevel = $userfactory['level'] + 1;
		
		// if($nextLevel > $userData['level'])
		// {
		// 	echo '您的生产线目前只能升到 '. $userData['level'] .' 级！';
		// 	exit();
		// }
		// if($nextLevel > F_MAX_LEVEL)
		// {
		// 	echo '此生产线已经达到最高等级，不需要再升级！';
		// 	exit();
		// }
		
		// $levelInfo = $db->fetch_first("SELECT factid,level,price FROM game_factorylevel ".
		// 							  "WHERE factid=$factid AND level=$nextLevel");
		// if($userData['money'] < $levelInfo['price'])
		// {
		// 	echo '您的金钱不足，无法完成升级！';
		// }
		// else
		// {
		// 	$db->query("UPDATE game_userproperty SET money=money-". $levelInfo['price'] ." WHERE uid=$uid");
		// 	$db->query("UPDATE game_userfactory SET level=$nextLevel WHERE factid=$factid AND uid=$uid");
		// 	echo "恭喜，你的生产线已成功升级至 $nextLevel 级，确定后刷新本页面查看！";
		// }
		break;
	
	case 'process':
		require_once './include/date.func.php';
		$fact_id = gp('factid', 0);
		$now = time();
		$user_fact = get_user_factory_by_id($uid, $fact_id);
		$tasks = [];
		$query = $db->query("SELECT mp.*, p.productname, p.pic FROM game_makeprocess mp LEFT JOIN game_products p ON p.pid = mp.pid WHERE mp.makecateid = '{$user_fact['makecateid']}' AND uid = '{$uid}'");
		while($row = $db->fetch_array($query)){
			if($now >= $row['endtime']){
				$row['status'] = 1;
			}else{
				$row['remain'] = DateDiff($row['endtime'], $now, 's');
			}
			$tasks[] = $row;
		}
		json_result(['tasks' => $tasks]);
		break;

	case 'makeprocess':
		require_once './include/date.func.php';
		$factid = $_POST['factid'];
		$userFactory = $db->fetch_first("SELECT uf.*,f.factoryname,f.makecateid FROM game_userfactory uf ".
										"LEFT JOIN game_factorys f ON uf.factid=f.factid ".
										"WHERE uf.factid=$factid AND uf.uid=$uid");
		$makeProcessQuery = $db->query("SELECT mp.*,p.productname,p.pic FROM game_makeprocess mp ".
									   "LEFT JOIN game_products p ON mp.pid=p.pid ".
									   "WHERE makecateid=". $userFactory['makecateid'] ." AND uid=$uid");
		for($i=1; $i<=$userFactory['level']; $i++)
		{
			$makeProcess = $db->fetch_array($makeProcessQuery);
			if($makeProcess == FALSE)
			{
				echo '<li class="product-item">'.
					 '  <img src="'. APP_URL .'/images/factory_wait.gif" border="0"><br>'.
					 '  空闲</li>';
			}
			else
			{
				/*				
				echo '<li class="product-item">'.
					 ' <img src="'. APP_URL .'/images/product/'. $makeProcess['pic'] .'" border="0"><br>'.
					 $makeProcess['productname'] .'('. $makeProcess['count'] .')</li>';
				*/
				if(time() >= $makeProcess['endtime'])
				{
					echo '<li class="product-item">'.
						 '<a href="javascript:;" onclick="move_to_warehouse('. $makeProcess['id'] .','. $factid .')"><img src="'. APP_URL .'/images/product/'. $makeProcess['pic'] .'" border="0"></a><br>'.
						 $makeProcess['productname'] .'('. $makeProcess['count'] .')<br>'.
						 '<a href="javascript:;" onclick="move_to_warehouse('. $makeProcess['id'] .','. $factid .')">放入仓库</a></li>';
				}
				else
				{
					$leftTime = DateDiff($makeProcess['endtime'], time(), 's');
					echo '<li class="product-item">'.
						 '<img src="'. APP_URL .'/images/product/'. $makeProcess['pic'] .'" border="0"><br>'.
						 $makeProcess['productname'] .'('. $makeProcess['count'] .')<br>'.
						 '<span id="makeprocess_'. $makeProcess['id'] .'">'. $leftTime .'</span></li>'.
						 '<script type="text/javascript">new timer('. $leftTime .','. $makeProcess['id'] .','. $factid .');</script>';
				}
			}
		}
		break;
		
	case 'save':
		$proc_id = gp('proc_id', 0);
		$fact_id = gp('factid', 0);
		$proc = get_make_process($proc_id);
		if (!$proc) {
			json_error('任务不存在');
		}
		add_warehouse($uid, $proc['pid'], $proc['count']);
		add_make_log($uid, $proc);
		remove_process($proc_id);
		$exp = $proc['count'] * $proc['level'];
		update_user_level($uid, $exp);
		json_success();
		break;

	case 'movetowarehouse':
		$makeProcessId = $_POST['makeprocessid'];
		//$makeProcess = $db->fetch_first("SELECT * FROM game_makeprocess WHERE id=$makeProcessId");
		$makeProcess = $db->fetch_first("SELECT mp.*,p.productname,p.level FROM game_makeprocess mp ".
										"LEFT JOIN game_products p ON mp.pid=p.pid ".
										"WHERE id=$makeProcessId");
		if(!$makeProcess)
		{
			echo '此物品已经存入您的仓库中！';
		}
		else
		{
			$warehouseQuery = $db->query("SELECT uid,pid,count AS pCount FROM game_warehouses WHERE uid=$uid AND pid=". $makeProcess['pid']);
			if(!$db->num_rows($warehouseQuery))
			{
				$db->query("INSERT INTO game_warehouses(uid,pid,count) VALUES ($uid,". $makeProcess['pid'] .",". $makeProcess['count'] .")");
			}
			else
			{
				$db->query("UPDATE game_warehouses SET count=count+". $makeProcess['count'] ." WHERE uid=$uid AND pid=". $makeProcess['pid']);
			}
			$logSql = "INSERT INTO game_makelog(makecateid,uid,pid,productname,count,starttime,endtime,dateline) VALUES (".
						$makeProcess['makecateid'] .",$uid,". $makeProcess['pid'] .",'". $makeProcess['productname'] ."',".
						$makeProcess['count'] .",". $makeProcess['starttime'] .",". $makeProcess['endtime'] .",". time() .")";
			$db->query($logSql);
			$db->query("DELETE FROM game_makeprocess WHERE id=$makeProcessId");
			
			// 更新玩家经验值
			$totalEmpiric = $makeProcess['count'] * $makeProcess['level'];
			if($userData['empiricnow'] + $totalEmpiric < $userData['pEmpiric'])
			{
				$db->query("UPDATE game_userproperty SET empiricnow=empiricnow + ". $totalEmpiric ." WHERE uid=$uid");
			}
			else
			{
				if($userData['empiricnow'] + $totalEmpiric > $userData['pEmpiric'])
				{
					$leftEmpiric = ($userData['empiricnow'] + $totalEmpiric) - $userData['pEmpiric'];
					$db->query("UPDATE game_userproperty SET level=level+1,empiricnow=$leftEmpiric WHERE uid=$uid");
				}
				else
				{
					$db->query("UPDATE game_userproperty SET level=level+1,empiricnow=0 WHERE uid=$uid");
				}
			}
			
			echo '成功将 '. $makeProcess['count'] .' 个 '. $makeProcess['productname'] .' 放入仓库！';
		}
		break;

	case 'makeinfo':
		$makeid = $_POST['makeid'];
		$makeInfo = $db->fetch_first("SELECT m.*,p.productname,p.pic,p.level FROM game_makes m ".
									 "LEFT JOIN game_products p ON m.pid=p.pid ".
									 "WHERE m.makeid=$makeid");
		
		$makeItems = explode(',', $makeInfo['items']);
		for($i=0; $i<count($makeItems); $i++)
		{
			$item = explode(':', $makeItems[$i]);
			$product = $db->fetch_first("SELECT pid,productname,pic FROM game_products WHERE pid=". $item[0]);
			$productList[] = array(
								   'productname' => $product['productname'],
								   'pic' => $product['pic'],
								   'count' => $item[1]
								   );
		}
		
		$smarty->assign('productList', $productList);
		$smarty->assign('productname', $makeInfo['productname']);
		$smarty->assign('level', $makeInfo['level']);
		$smarty->assign('pic', $makeInfo['pic']);
		$smarty->assign('maketime', $makeInfo['maketime']);
		$smarty->display('makeinfo.html');
		break;


	case 'makenew':
		$makeid = gp('makeid', 0);
		$count = gp('count', 0);
		if($count > MAKE_LIMIT){
			json_error('生产数量超限');
		}
		$make_product = get_make_product($makeid);
		$user_fact = get_user_factory($uid, $make_product['makecateid']);
		if($make_product['level'] > $user_fact['level']){
			json_error('生产线等级不够');
		}
		// $proc_count = count_make_process($uid, $make_product['makecateid']);
		// if($proc_count > $user_fact['level']){
		// 	json_error('生产任务已满');
		// }
		$materials = extract_material($make_product['items']);
		if (!check_material($materials)) {
			json_error('原料不足');
		}
		add_make_process($uid, $count, $make_product);
		foreach ($materials as $key => $material) {
			$total_count = $count * $material['count'];
			update_warehouse_count($uid, $material['pid'], -$total_count);
		}
		json_success('正在制造'.$count.'个物品');
		break;

	case 'factory':
		$factories = get_user_factories($uid);
		foreach($factories as $key => $factory){
			$factory['products'] = get_make_products($factory['factid']);
			$factories[$key] = $factory;
		}
		json_result(['factories' => $factories]);
		break;
	
	default:
		// $query = $db->query("SELECT f.*, u.uid FROM game_userfactory u LEFT JOIN game_factorys f ON u.factid = f.factid WHERE u.uid = '{$uid}'");
		// $tmp = 0;
		// $make_list = array();
		// while($row = $db->fetch_array($query))
		// {
		// 	$factory_list[] = array(
		// 							'factid' => $row['factid'],
		// 							'factoryname' => $row['factoryname'],
		// 							'level' => $row['level'],
		// 							'pic' => $row['pic'],
		// 							'pic2' => $row['pic2'],
		// 							'status' => ($userData['level'] > $row['level'] && $row['level'] < F_MAX_LEVEL) ? 1 : 0,
		// 							'makecateid' => $row['makecateid']
		// 							);
			// $make_list[$tmp++] = get_make_list($row['makecateid']);

			// $sql = "SELECT m.*,p.productname FROM game_makes m ".
			// 		"LEFT JOIN game_products p ON m.pid=p.pid ".
			// 		"WHERE m.makecateid=". $row['makecateid'];
			// $makeItemsQuery = $db->query($sql);
			// while($makeItems = $db->fetch_array($makeItemsQuery))
			// {
			// 	$make_list[$tmp][] = array(
			// 										 'makeid' => $makeItems['makeid'],
			// 										 'productname' => $makeItems['productname']
			// 										 );
			// }
			// $tmp++;
		// }
		// $smarty->assign('make_list', $make_list);
		// $smarty->assign('make_list', $make_list);
		// $smarty->assign('factory_list', $factory_list);
		$smarty->display('make.html');
		break;
}
