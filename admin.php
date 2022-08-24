<?php
/* $Id:admin.php 2010/4/25 22:49 xiaogang $ */

require_once './include/common.inc.php';

$module = gp('m', 'user');
$action = gp('a');

$module_file = './admin/modules/'.$module.'.inc.php';
if(!file_exists($module_file)) {
	exit('Module not found');
} else {
	require_once $module_file;
}
