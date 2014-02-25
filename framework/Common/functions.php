<?php
//some useful functions by Rain

//get client IP if $num is true return int number else return ip address by string
//if invalid ip address return unknown
//note: this function maybe get Agent IP
function getIp($num = false)
{
	if (!isset($_SERVER['REMOTE_ADDR']))
		return 'unknown';
	else
	{
		$ip = trim($_SERVER['REMOTE_ADDR']);
		if (!ip2long($ip))
			return 'unknown';
		else
		{
			if ($num)
				return printf( '%u', ip2long($ip));
			else
				return $ip;
		}
	}
}

//this function use for setting cookie to client
//if set cookie success return true else return false
//default expire time one day
function setc($name, $value, $expire = null, $path = '/', $domain = null, $secure = false, $httponly = true)
{
	if (is_null($expire))
		$expire = 86400;
	if (is_null($domain) && isset($_SERVER['HTTP_HOST']))
		$domain = trim(str_ireplace('www.', '', $_SERVER['HTTP_HOST']));
	return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}


//this function use for getting cookie from client
//if get cookie success return the value of the cookie else return false
//note: this function will use  htmlspecialchars function
function getc($name)
{
	if (!isset($_COOKIE[$name]))
		return false;
	return htmlspecialchars($_COOKIE[$name]);
}

//this function use for deleting cookie from client
//if delete cookie success return true else return false
function delc($name)
{
	return setcookie ($name, '', time() - 3600);
}

//this function use for making directories
//if success return true else return false
//note: this function parameter need  the absolute address
function mkdirs($dir, $mode = 0700)
{
	$dir = str_replace("\\", '/', $dir);
	if (is_dir($dir))
		return true;
	$dirArr = explode('/', $dir);
	$dirArr = array_filter($dirArr);
	if (!is_array($dirArr) || empty($dirArr))
		return true;
	$tmp = '';
	foreach ($dirArr as $k => $dir)
	{
		if (0 != ($k % 2))
			$tmp .= '/'.$dir.'/';
		else
			$tmp .= $dir;
		if (!is_dir($tmp))
		{
			$ret = @mkdir($tmp, $mode);
			if (!$ret)
			{
				unset($dirArr);
				return $ret;
			}
		}
	}
	unset($dirArr);
	return true;
}

//this function use for remove directories or files
//note: this function parameter need  the absolute address
function rm($dir, $deleteRootToo = false)
{
	$dir = str_replace("\\", '/', $dir);
	if (is_file($dir) && file_exists($dir))
		return @unlink($dir);
	if (is_dir($dir))
		return unlinkRecursive($dir, $deleteRootToo);
}

/**
  * Recursively delete a directory
  *
  * @param string $dir Directory name
  * @param boolean $deleteRootToo Delete specified top-level directory as well default value false
*/
function unlinkRecursive($dir, $deleteRootToo = false)
{
     if (!$dh = @opendir($dir))
         return false;
     while (false !== ($obj = readdir($dh)))
     {
        if($obj == '.' || $obj == '..') 
            continue;
        if (!@unlink($dir . '/' . $obj))
             unlinkRecursive($dir.'/'.$obj, $deleteRootToo);
     }
     closedir($dh);
     if ($deleteRootToo)
         return @rmdir($dir);
     return true;
}

function send_http_status($code)
{
    static $_status = array(
        // Success 2xx
        200 => 'OK',
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );
	if (isset($_status[$code]))
	{
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        header('Status:'.$code.' '.$_status[$code]);
    }
}

//get configuration value
//success return value else return false
function C($key)
{
	static $arr = array();

	if (!file_exists(CONFIG_FILE))
		return false;
	if (empty($arr))
		$arr = require_once(CONFIG_FILE);
	if (!is_array($arr) || empty($arr))
		return false;
	$tmpArr = explode('-', $key);
	$value = false;
	foreach ($tmpArr as $t)
	{
		if ((!isset($arr[$t]) && (false === $value)) || ((false !== $value) && !isset($value[$t])))
			return false;
		if (false === $value)
			$value = $arr[$t];
		else
			$value = $value[$t];
	}
	unset($arr, $tmpArr);
	return $value;
}

//safe model filter variable from $_REQUEST / $_POST / $_GET / $_COOKIE / $_SERVER
//default open safe model
function safe()
{
	if (!OPEN_SAFE_MODEL)
		return;
	if (is_array($_REQUEST) && !empty($_REQUEST))
	{
		foreach ($_REQUEST as $k => $v)
		{
			$is_get = isset($_GET[$k]) ? true : false;
			$is_post = isset($_POST[$k]) ? true : false;
			$v = trim($v);
			unset($_REQUEST[$k], $_GET[$k], $_POST[$k]);
			$k = trim($k);
			$k = urldecode($k);
			$v = urldecode($v);

			if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
				die('you are too young too simple, you ip:'.getIp());
			//integer value
			if (stripos($k, 'i_') === 0)
				$v = intval($v);
			//float value
			elseif (stripos($k, 'f_') === 0)
				$v = floatval($v);
			//double value
			elseif (stripos($k, 'd_') === 0)
				$v = doubleval($v);
			//text value
			elseif (stripos($k, 't_') === 0)
				$v = trim(strip_tags($v));
			//html value
			elseif (stripos($k, 'h_') === 0)
				$v = '<pre>'.trim(htmlspecialchars($v)).'</pre>';
			if ($is_get)
				$_GET[$k] = $v;
			if ($is_post)
				$_POST[$k] = $v;
			$_REQUEST[$k] = $v;
		}
	}

	if (is_array($_SERVER) && !empty($_SERVER))
	{
		foreach ($_SERVER as $k => $v)
		{
			if (is_array($v))
				continue;
			$v = trim($v);
			$k = trim($k);

			if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
				die('you are too young too simple, you ip:'.getIp());
		}
	}

	if (is_array($_COOKIE) && !empty($_COOKIE))
	{
		foreach ($_COOKIE as $k => $v)
		{
			$v = trim($v);
			unset($_COOKIE[$k]);
			$k = trim($k);
			$k = urldecode($k);
			$v = urldecode($v);

			if ($k != addslashes($k) || $k != strip_tags($k) || htmlspecialchars($k) != $k || (strpos($k, '%') !== false))
				die('you are too young too simple, you ip:'.getIp());
			//integer value
			if (stripos($k, 'i_') === 0)
				$v = intval($v);
			//float value
			elseif (stripos($k, 'f_') === 0)
				$v = floatval($v);
			//double value
			elseif (stripos($k, 'd_') === 0)
				$v = doubleval($v);
			//text value
			elseif (stripos($k, 't_') === 0)
				$v = trim(strip_tags($v));
			//html value
			elseif (stripos($k, 'h_') === 0)
				$v = trim(htmlspecialchars($v));
			$_COOKIE[$k] = $v;
		}
	}
}

//if build success return true else return false
function build()
{
	if (file_exists(RUNTIME_PATH.'build.lock'))
		return true;
	if (!defined('APP_NAME') || !defined('APP_PATH'))
		return false;
	$path = str_replace("\\", '/', realpath(str_replace("\\", '/', APP_PATH)));
	if (!$path)
		return false;
	$ret = true;
	if (!is_dir($path.'/'.APP_NAME.'/Common'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Common');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Static/js'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Static/js');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Static/css'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Static/css');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Lib/Action'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Lib/Action');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Lib/Model'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Lib/Model');
	if (!$ret)
		return false;

	if (!is_dir($path.'/'.APP_NAME.'/Lib/Class'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Lib/Class');
	if (!$ret)
		return false;

	if (!is_dir($path.'/'.APP_NAME.'/Runtime/Cache'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Runtime/Cache');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Runtime/Data'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Runtime/Data');
	if (!$ret)
		return false;
	if (!is_dir($path.'/'.APP_NAME.'/Tpl'))
		$ret = mkdirs($path.'/'.APP_NAME.'/Tpl');
	if (!$ret)
		return false;
	file_put_contents(RUNTIME_PATH.'build.lock', '');
	return true;
}


function echo_memory_usage($mem_usage)
{
	if ($mem_usage < 1024)
		 return $mem_usage." b";
	elseif ($mem_usage < 1048576)
		 return round($mem_usage/1024,2)." kb";
	else
	 return round($mem_usage/1048576,2)." mb";
}

//if success return true else return false
function import($file)
{
	if (file_exists($file))
	{
		$GLOBALS['_FileCount']++;
		require_once($file);
		return true;
	}
	return false;
}

function debuginfo()
{
	if (!APP_DEBUG)
		return;
	echo '<div>use time: '.($GLOBALS['_endTime'] - $GLOBALS['_beginTime']).' seconds<br/>memory use: '.echo_memory_usage($GLOBALS['_endUseMems'] - $GLOBALS['_startUseMems']).'<br/>SQL Counts: '.$GLOBALS['_SQLCount'].'<br/>require file counts: '.$GLOBALS['_FileCount'].'</div>';
}

//load system functions and user definition functions
function load_functions()
{
	if (is_dir(SYS_COMMON_PATH))
	{
		if (!($dir = @opendir(SYS_COMMON_PATH)))
			die('open system common function directory failed');
		while (false !== ($file = readdir($dir)))
		{
			if ($file != "." && $file != ".." && is_file(SYS_COMMON_PATH.$file) && substr($file, strpos($file, '.')) == '.php')
				import(SYS_COMMON_PATH.$file);
		}
		closedir($dir);
	}

	if (is_dir(APP_COMMON))
	{
		if (!($dir = @opendir(APP_COMMON)))
			die('open User common function directory failed');
		while (false !== ($file = readdir($dir)))
		{
			if ($file != "." && $file != ".." && is_file(APP_COMMON.$file) && substr($file, strpos($file, '.')) == '.php')
				import(APP_COMMON.$file);
		}
		closedir($dir);
	}
}

function my_autoload($classname)
{
	$sys_class = FRAMEWORK_PATH.'Class/'.$classname.'.php';
	$user_class1 = APP_CLASS.$classname.'.php';
	$user_class2 = APP_ACTION.$classname.'.php';
	$user_class3 = APP_MODEL.$classname.'.php';
	if (!import($sys_class) && !import($user_class1) && !import($user_class2) && !import($user_class3))
		die('load class: '.$classname.' failed');
}

//if success return url else return false
function U($act, $param = null)
{
	$act = trim($act);
	if (strlen($act) < 1)
		return false;
	$ret = SITE_URL;
	$ret .= strtoupper(substr($act, 0, 1)).substr($act, 1).'/';
	if (is_array($param) && !empty($param))
	{
		foreach ($param as $k => $v)
			$ret .= urlencode($k).'/'.urlencode($v).'/';
	}
	$ret = substr($ret, 0, -1);
	return $ret.'.html';
}

function location($url = SITE_URL, $time = 0, $msg = '')
{
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent())
	{
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
		}
		else
		{
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
	}
	else
	{
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

function load_tpl($tpl, $open_token = true)
{
	$tpl = trim($tpl);
	if (!file_exists($tpl))
	{
		if (APP_DEBUG)
			die('template file: '.$tpl.' not exists. ');
		else
			die('template file not exists. ');
	}
	$cache_file = RUNTIME_CACHE.md5($tpl).'.php';
	if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($tpl) || $open_token)
	{
		$content = file_get_contents($tpl);
		$content = str_replace("\r", '', $content);
		$content = str_replace("\n", '', $content);
		$token_key = substr(SITE_URL, 0, -1).$_SERVER['REQUEST_URI'];
		foreach ($_REQUEST as $k => $v)
		{
			if ($k == HIDDEN_TOKEN_NAME)
				continue;
			$token_key .= $k;
		}
		$token_key = md5($token_key);
		if ($open_token && count($_POST))
		{
			if (!isset($_SESSION[$token_key]) || !isset($_SESSION[HIDDEN_TOKEN_NAME]) || !isset($_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]]))
			{
				$val = md5(microtime());
				if (!isset($_SESSION[HIDDEN_TOKEN_NAME]) || !isset($_REQUEST[HIDDEN_TOKEN_NAME]))
				{
					$_SESSION[HIDDEN_TOKEN_NAME] = $token_key;
				}
				$_SESSION[$token_key] = $val;
			}
			$content = preg_replace('/<form(.*?)>(.*?)<\/form>/i', '<form$1><input type="hidden" value="'.$_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]].'" name="'.HIDDEN_TOKEN_NAME.'"/>$2</form>', $content);
		}

		//parse include
		$ret = preg_match_all('/<\{\s*include\s*=\s*"(.*?)"\}>/i', $content, $match);
		if ($ret)
		{
			foreach ($match[1] as $k => $v)
			{
				$tArr = explode('/', $v);
				$tCount = count($tArr);
				if ($tCount == 3)
					$content = str_ireplace($match[0][$k], '<?php require_once(load_tpl(APP_TPL."'.$tArr[0].'".\'/\'."'.$tArr[2].'".\'.html\')); ?>', $content);
				elseif ($tCount == 2)
					$content = str_ireplace($match[0][$k], '<?php require_once(load_tpl(APP_TPL."'.$tArr[0].'".\'/\'."'.$tArr[1].'".\'.html\')); ?>', $content);
				unset($tArr);
			}
		}
		$content = preg_replace('/<\{\$(\w*?)\}>/i', '<?php echo \$${1}; ?>', $content);
		$content = preg_replace('/\{\s*u(.*?)\}/i', '<?php echo U${1}; ?>', $content);
		$content = preg_replace('/<\{\s*if\s*(.*?)\s*\}>/i', '<?php if(${1}) { ?>', $content);
		$content = preg_replace('/<\{\s*else\s*if\s*(.*?)\s*\}>/i', '<?php } elseif(${1}) { ?>', $content);
		$content = preg_replace('/<\{\s*else\s*\}>/i', '<?php } else { ?>', $content);
		$content = preg_replace('/<\{\s*\/if\s*\}>/i', '<?php } ?>', $content);
		$content = preg_replace('/<\{\s*loop(.*?)\s*\}>/i', '<?php foreach${1} { ?>', $content);
		$content = preg_replace('/<\{\s*\/loop\s*\}>/i', '<?php } ?>', $content);
		$content = preg_replace('/<\{\s*foreach(.*?)\s*\}>/i', '<?php foreach${1} { ?>', $content);
		$content = preg_replace('/<\{\s*\/foreach\s*\}>/i', '<?php } ?>', $content);
		$content = compress_html($content);
		file_put_contents($cache_file, $content);
	}
	return $cache_file;
}


function compress_html($string) {
    $string = str_replace("\r\n", '', $string);
    $string = str_replace("\n", '', $string);
    $string = str_replace("\t", '', $string);
	$pattern = array (
                    "/> *([^ ]*) *</",
                    "/[\s]+/",
                    "/<!--[\\w\\W\r\\n]*?-->/",
                    "'/\*[^*]*\*/'"
                    );
    $replace = array (
                    ">\\1<",
                    " ",
                    "",
                    ""
                    );
    return preg_replace($pattern, $replace, $string);
}

function check_code($name)
{
	if (!isset($_SESSION['code']))
		return false;
	$s_code = $_SESSION['code'];
	unset($_SESSION['code']);
	return (strtolower(trim($_REQUEST[$name])) == $s_code);
}

function shutdown_function($req)
{
	$e = error_get_last();
	//remove some error like E_WARNING E_NOTICE and so on
	if (!is_null($e))
	{
		if (APP_DEBUG)
		{
			die('info: '.$e['message'].' , in file:'.$e['file'].' , line:'.$e['line']);
		}
		else if (in_array($e['type'], array(1,4, 16, 32, 64, 128, 256, 4096)))
		{
			header("Content-type: text/html; charset=utf-8");
			die('服务器异常，请稍后访问，或者通知服务器管理员，邮箱：'.ADMIN_EMAIL.' 谢谢合作！');
		}
	}
	$GLOBALS['_endTime'] = microtime(TRUE);
	if (MEMORY_LIMIT_ON) $GLOBALS['_endUseMems'] = memory_get_usage();
	if (APP_DEBUG)
		debuginfo();
}
