<?php
function json_result($data, $errmsg = '', $errcode = 0) {
	if (!is_array($data)) {
		$data = [];
	}
	$data['errcode'] = $errcode;
	$data['errmsg'] = $errmsg;
	echo json_encode($data);
	exit;
}

function json_error($errmsg = '', $errcode = 1, $data = null) {
	json_result($data, $errmsg, $errcode);
}

function json_success($errmsg = '', $errcode = 0, $data = null) {
	json_result($data, $errmsg, $errcode);
}

require_once 'user.func.php';
require_once 'common.inc.php';
