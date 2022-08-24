<?php
/**
 *
 *
 * Manyou_Platform_v0.2
 * (c) 2008 comsenz inc
 * $Id: Manyou.php 18050 2008-12-12 07:46:17Z chenhao $
 */
class Manyou_API_Client {
    var $user;
    var $friends;
    var $added;
    var $api_key;
    var $secret;
    var $errno;
    var $errmsg;

    function profile_setMYML($myml, $uid = 0) {
        return $this->_call_method('profile.setMYML', array('myml'=> $myml, 'uid' => $uid));
    }

    function profile_getMYML($uid = 0) {
        return $this->_call_method('profile.getMYML', array('uid' => $uid));
    }

    function friend_get() {
        if (isset($this->friends)) {
            return $this->friends;
        }
        return $this->_call_method('friend.get', array());
    }

    function friend_areFriends($uid1, $uid2) {
        //if ($uid1 == $this->user_getLoggedInUser();
        return $this->_call_method('friend.areFriends', array('uid1' => $uid1,
                                                              'uid2' => $uid2
                                                              ));
    }

    function friend_getAppUsers() {
        return $this->_call_method('friend.getAppUsers', array());
    }

    function user_getLoggedInUser() {
        if (isset($this->user)) {
            return $this->user;
        }
        return $this->_call_method('user.getLoggedInUser', array());
    }

	function user_getLoggedInUserLevel() {
        return $this->_call_method('user.getLoggedInUserLevel', array());
    }

	function user_getInvitationURL($sid) {
		return $this->_call_method('user.getInvitationURL', array('sid'=> $sid));
	}

    function user_isAppAdded() {
        if (isset($this->added)) {
            return $this->added;
        }
        return $this->_call_method('user.isAppAdded', array());
    }

    function user_getinfo($uids, $fields = null) {
        return $this->_call_method('user.getinfo', array( 'uids'=> $uids,
                                                          'fields'=>$fields));
    }

    function notification_send($uids, $msg) {
        return $this->_call_method('notification.send', array( 'uids'=> $uids,
                                                          'msg'=>$msg));
    }

    function notification_get() {
        return $this->_call_method('notification.get', array());
    }

    function feed_publishTemplatizedAction($title_template,$title_data,$body_template,$body_data,$body_general,$image_1,$image_1_link,$image_2,$image_2_link,$image_3,$image_3_link,$image_4,$image_4_link,$target_ids = null) {

        return $this->_call_method('feed.publishTemplatizedAction', array('title_template' => $title_template,
                                                                           'title_data' => $title_data,
                                                                           'body_template' => $body_template,
                                                                          'body_data' => $body_data,
                                                                          'body_general' => $body_general,
                                                                          'image_1' => $image_1,
                                                                          'image_1_link' => $image_1_link,
                                                                          'image_2' => $image_2,
                                                                          'image_2_link' => $image_2_link,
                                                                          'image_3' => $image_3,
                                                                          'image_3_link' => $image_3_link,
                                                                          'image_4' => $image_4,
                                                                          'image_4_link' => $image_4_link,
                                                                          'target_ids' => $target_ids));
    }

	function site_get($sid) {
        return $this->_call_method('site.get', array('sid'=> $sid));
    }

	function _call_method($method, $args) {
        $this->errno = 0;
        $this->errmsg = '';

        $url = 'http://api.manyou.com/openapi.php';

        $params = array();
        $params['method'] = $method;
        $params['session_key'] = $this->session_key;
        $params['api_key'] = $this->api_key;
        $params['format'] = 'PHP';
        $params['v'] = '0.1';
        //$params['secret'] = $this->secret;

        ksort($params);
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= $k . '=' . $v . '&';
        }

        ksort($args);
        foreach ($args as $k=>$v) {
            if (is_array($v)) {
                $v = join(',' , $v);
            }
            $params['args'][$k] = $v;
            $k = 'args[' . $k . ']';
            $str .= $k .'=' . $v . '&';
        }
        $params['sig'] = md5($str . $this->secret);

		list($errno, $result) = $this->post_request($url, $params);

        if (!$errno) {
			$result = unserialize($result);
            if (isset($result['errCode']) && $result['errCode'] != 0) {
                $this->errno = $result['errCode'];
                $this->errmsg = $result['errMessage'];
                // TODO handle error
                return null;
            }
            return $result['result'];
        } else {
			return false;
        }
    }

    function post_request($url, $params) {

        $str = '';

        foreach ($params as $k=>$v) {
            if (is_array($v)) {
                foreach ($v as $kv => $vv) {
                    $str .= '&' . $k . '[' . $kv  . ']=' . urlencode($vv);
                }
            } else {
                $str .= '&' . $k . '=' . urlencode($v);
            }
        }

        if (function_exists('curl_init')) {
            // Use CURL if installed...
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Manyou API PHP Client 0.1 (curl) ' . phpversion());
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            return array($errno, $result);
        } else {
            // Non-CURL based version...
            $context =
            array('http' =>
                    array('method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                    'User-Agent: Manyou API PHP Client 0.1 (non-curl) '.phpversion()."\r\n".
                                    'Content-length: ' . strlen($str),
                        'content' => $str));
            $contextid = stream_context_create($context);
            $sock = fopen($url, 'r', false, $contextid);
            if ($sock) {
                $result = '';
                while (!feof($sock)) {
                    $result .= fgets($sock, 4096);
                }
                fclose($sock);
            }
        }
        return array(0, $result);
    }
}

class Manyou {

    var $params;
    var $session_key;
    var $api_client;
    var $api_key;
    var $secret;

    function __construct($api_key, $secret) {

        $this->api_key = $api_key;
        $this->secret = $secret;

        $this->get_valid_params();

	    $this->session_key = $this->params['sessionId'];

        $this->api_client = new Manyou_API_Client();

        $this->api_client->api_key = $api_key;
        $this->api_client->secret = $secret;
        $this->api_client->session_key = $this->session_key;

        if (isset($this->params['friends']) && trim($this->params['friends'])) {
            $this->api_client->friends = explode(',' , $this->params['friends']);
        }
        if (isset($this->params['added'])) {
            $this->api_client->added = $this->params['added'] ? true : false;
        }
        if (isset($this->params['uId'])) {
            $this->api_client->user = $this->params['uId'];
        }
    }

    function generate_sig($params, $namespace = 'my_sig_') {

        ksort($params);
        $str = '';
        foreach ($params as $k=>$v) {
            if ($v) {
                $str .= $namespace . $k . '=' . $v . '&';
            }
        }
        return  md5($str. $this->secret);
    }

	// 闄ゅ幓post/get杩囨潵鐨勫弬鏁板墠闈㈢殑my_sig_锛岄噸缁勪负涓€涓暟缁?
    function get_my_params($params, $namespace = 'my_sig_') {
        $my_params = array();
        foreach ($params as $k=>$v) {
            if (substr($k, 0, strlen($namespace)) == $namespace) {
                $my_params[substr($k, strlen($namespace))] = $this->no_magic_quotes($v);
            }
        }
        return $my_params;
    }

    function is_valid_params($params, $namespace = 'my_sig_') {
		if (!isset($params['key'])) {
			return false;
		}

        $sig = $params['key'];
        unset($params['key']);

	    //print $sig ."<br>" . $this->generate_sig($params) . "<br>";

        if ($sig != $this->generate_sig($params, $namespace)) {
            return false;
        }
        return true;
    }

    function get_valid_params() {
        $params = $this->get_my_params($_POST);

		if (!$params) {
            $params = $this->get_my_params($_GET);

            if (!$params) {
                $params = $this->get_my_params($_COOKIE, $this->api_key . '_');
                foreach ($params as $k => $v) {
                    if (!in_array($k, array('uId', 'sessionId', 'sId', 'key'))) {
                        unset($params[$k]);
                    }
                }
                if ($this->is_valid_params($params, $this->api_key . '_')) {
                    $this->params = $params;
                } else {
                    return ;
                }
            } else if ($this->is_valid_params($params)) {
                $this->set_cookies($params, 3600 * 2);
                $this->params = $params;
            }
        } else if ($this->is_valid_params($params)) {
			$this->params = $params;
        }
    }

    function set_cookies($params, $expires = 3600) {
		//var_dump($params);

		header('P3P: CP="NOI DEV PSA PSD IVA PVD OTP OUR OTR IND OTC"');
	    $cookies = array();
        $cookies[$this->api_key . '_' . 'uId'] = $params['uId'];
        $cookies[$this->api_key . '_' . 'sId'] = $params['sId'];
        $cookies[$this->api_key . '_' . 'sessionId'] = $params['sessionId'];

		$expireTime = time() + (int)$expires;

        foreach ($cookies as $name => $val) {
            setcookie($name, $val, $expireTime);
            $_COOKIE[$name] = $val;
        }
        $sig = $this->generate_sig($cookies, '');
        setcookie($this->api_key . '_key', $sig, $expireTime);
        $_COOKIE[$this->api_key . '_key'] = $sig;

		// 淇濆瓨褰撳墠绔欑偣URL銆丄ppId绛変俊鎭埌Cookie涓紝渚汭frame鏂瑰紡鐨凙pp浣跨敤
		setcookie('prifixUrl', $params['prefix'], $expireTime);
		setcookie('appId', $params['appId'], $expireTime);
		setcookie('added', $params['added'], $expireTime);

		if (isset($params['in_iframe'])) {
			setcookie('inFrame', $params['in_iframe'], $expireTime);
		}
    }

	// 璺宠浆
	function redirect($url) {
		if ($this->in_my_canvas()) {
			// MYML妯″紡鐨凙pp
			echo '<my:redirect url="' . $url . '"/>';
		} else if (strrpos($url, $this->get_site_url()) === false) {
			// Iframe妯″紡鐨凙pp锛屼絾$url涓嶅湪褰撳墠UCH绔欑偣
			header('Location: ' . $url);
		} else {
			// Iframe妯″紡鐨凙pp锛屼絾瑕佽浆鍚戠殑$url浠嶇劧鍦ㄥ綋鍓峌CH绔欑偣涓?
			echo "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
		}
		exit;
    }

	// 杩斿洖褰撳墠宸茬粡鐧诲綍鐢ㄦ埛Id
	function get_loggedin_user() {
		if (isset($this->params['uId'])) {
			return $this->params['uId'];
		} else if(!empty($_COOKIE[$this->api_key . '_uId'])) {
			return $_COOKIE[$this->api_key . '_uId'];
		} else {
			return false;
		}
	}

    // 鏄惁鍦∕YML妯″紡鐨凙pp鐨刢anvas椤甸潰
	function in_my_canvas() {
        return isset($this->params['in_canvas']);
    }

    // 鏄惁鍦↖frame妯″紡鐨凙pp
	function in_frame() {
        return isset($this->params['in_iframe']) || isset($_COOKIE['inFrame']);
    }

    // 瑕佹眰鐧诲綍
	function require_login() {
		if (!$this->get_loggedin_user()) {
			$this->redirect($this->get_login_url());
		}
    }

    // 瑕佹眰宸叉坊鍔犺搴旂敤
	function require_add() {
        if (!$this->added()) {
			$this->redirect($this->get_add_url());
		}
    }

	// 瑕佹眰蹇呴』鍦↖frame涓?
	function require_frame() {
		if (!$this->in_frame()) {
			$this->redirect($this->get_login_url());
		}
	}

	// 杩斿洖鐧诲綍鍦板潃
	function get_login_url() {
		$url = $this->get_site_url() . 'do.php?ac=login&refer=userapp.php?id=' . $this->current_app();
		return $url;
	}

	// 杩斿洖娣诲姞搴旂敤鐨勫湴鍧€
	public function get_add_url() {
		// 濡傛灉褰撳墠鐢ㄦ埛鏄€氳繃绔欏閭€璇疯€屾潵
		if (isset($this->params['invitedby_bi'])) {
			$url = $this->get_site_url() . 'cp.php?ac=userapp&appid=' . $this->current_app() .'&my_extra=' . $this->params['invitedby_bi'];
		} else { // 鏅€氱敤鎴?
			$url = $this->get_site_url() . 'cp.php?ac=userapp&appid=' . $this->current_app();
		}

		return $url;
	}

	// 瀵硅璺宠浆鐨勫湴鍧€杩涜灏佽
    function get_url($suffix) {
        return $this->get_site_url() . 'userapp.php?id=' . $this->current_app() . '&my_suffix=' . urlencode(base64_encode($suffix));
    }

	// 杩斿洖褰撳墠绔欑偣URL鍦板潃
	function get_site_url() {
		if (isset($this->params['prefix'])) {
			return $this->params['prefix'];
		} else if(!empty($_COOKIE['prifixUrl'])) {
			return $_COOKIE['prifixUrl'];
		}
	}

	// 杩斿洖褰撳墠绔欑偣Id
    function current_site() {
		if (isset($this->params['sId'])) {
			return $this->params['sId'];
		} else if(!empty($_COOKIE[$this->api_key . '_sId'])) {
			return $_COOKIE[$this->api_key . '_sId'];
		}
    }

	// 杩斿洖褰撳墠App Id
	function current_app() {
		if (isset($this->params['appId'])) {
			return $this->params['appId'];
		} else if(!empty($_COOKIE['appId'])) {
			return $_COOKIE['appId'];
		}
	}

	// 鐢ㄦ埛鏄惁宸茬粡娣诲姞搴旂敤
	function added() {
		if (isset($this->params['added']) && $this->params['added'] == '1') {
			return true;
		} else if(isset($_COOKIE['added']) && $_COOKIE['added'] == '1') {
			return true;
		} else {
			return false;
		}
	}

	// 绔欏閭€璇凤紝鑾峰緱閭€璇疯€呭湪UC Home绔欑偣涓婄殑uid
	function get_outsite_inviter() {
		if (isset($this->params['invitedby_ai']) && intval($this->params['invitedby_ai']) > 0
			&& $this->is_installation()) {
			return $this->params['invitedby_ai'];
		} else {
			return false;
		}
	}

	//TODO 绔欏唴閭€璇凤紝鑾峰緱閭€璇疯€呭湪UC Home绔欑偣涓婄殑uid
	function get_insite_inviter() {
	}

	// 褰撳墠鐢ㄦ埛鏄惁鍒氬垰瀹夎搴旂敤锛堢涓€娆¤闂級
	function is_installation() {
		if (isset($this->params['installed']) && $this->params['installed'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	// 褰撳墠鐢ㄦ埛鏄惁鍒氬垰鍗歌浇浜嗗簲鐢?
	function is_uninstallation() {
		if (isset($this->params['uninstalled']) && $this->params['uninstalled'] == 1) {
			return true;
		} else {
			return false;
		}
	}

    function no_magic_quotes($val) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($val);
        } else {
            return $val;
        }
    }
}
?>