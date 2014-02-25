<?php
//rain framework create time 2014-02-13 by Rain
error_reporting(0);
//记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
$GLOBALS['_SQLCount'] = 0;
$GLOBALS['_FileCount'] = 1;
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if (MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();

//////////////////////////////////////////////user application const definition start, do not modify const here//////////////////////
if (!defined('SITE_URL'))
{
	$host = trim($_SERVER['HTTP_HOST']);
	if (count(explode('.', $host)) > 2)
		define('SITE_URL', 'http://'.$host.'/');
	else
		define('SITE_URL', 'http://www.'.$host.'/');
}

defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_NAME') or define('APP_NAME', 'App');
defined('APP_COMMON') or define('APP_COMMON', APP_PATH.APP_NAME.'/Common/');
defined('APP_LIB') or define('APP_LIB', APP_PATH.APP_NAME.'/Lib/');
defined('APP_ACTION') or define('APP_ACTION', APP_PATH.APP_NAME.'/Lib/Action/');
defined('APP_CLASS') or define('APP_CLASS', APP_PATH.APP_NAME.'/Lib/Class/');
defined('APP_MODEL') or define('APP_MODEL', APP_PATH.APP_NAME.'/Lib/Model/');
defined('APP_TPL') or define('APP_TPL', APP_PATH.APP_NAME.'/Tpl/');
defined('APP_UPLOAD') or define('APP_UPLOAD', APP_PATH.APP_NAME.'/uploads/');
defined('APP_STATIC') or define('APP_STATIC', APP_PATH.APP_NAME.'/Static/');
defined('APP_CSS') or define('APP_CSS', APP_PATH.APP_NAME.'/Static/css/');
defined('APP_IMAGE') or define('APP_IMAGE', APP_PATH.APP_NAME.'/Static/images/');
defined('APP_JS') or define('APP_JS', APP_PATH.APP_NAME.'/Static/js/');
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH.APP_NAME.'/Runtime/');
defined('RUNTIME_CACHE') or define('RUNTIME_CACHE', APP_PATH.APP_NAME.'/Runtime/Cache/');
defined('RUNTIME_DATA') or define('RUNTIME_DATA', APP_PATH.APP_NAME.'/Runtime/Data/');
defined('CONFIG_FILE') or define('CONFIG_FILE', FRAMEWORK_PATH.'config.php');
defined('FONTS_PATH') or define('FONTS_PATH', FRAMEWORK_PATH.'fonts/');
defined('ADMIN_EMAIL') or define('ADMIN_EMAIL', '563268276@qq.com');
defined('CREATE_DEMO') or define('CREATE_DEMO', true);
defined('OPEN_TOKEN') or define('OPEN_TOKEN', true);
defined('HIDDEN_TOKEN_NAME') or define('HIDDEN_TOKEN_NAME', 'token_name');
//////////////////////////////////////////////user application const definition end//////////////////////

/////////////////////////////////////////////framework const definition start, do not modify const here///////////////////////////
defined('FRAMEWORK_PATH') or define('FRAMEWORK_PATH', dirname(str_replace("\\", '/', __FILE__)).'/');
defined('SYS_COMMON_PATH') or define('SYS_COMMON_PATH', FRAMEWORK_PATH.'Common/');
defined('SYS_CLASS_PATH') or define('SYS_CLASS_PATH', FRAMEWORK_PATH.'Class/');
/////////////////////////////////////////////framework const definition end///////////////////////////

//////////////////////////////////////////////some useful configuration const definition start, do not modify const here//////////////////////////
//是否开启安全模式，默认开启
defined('OPEN_SAFE_MODEL') or define('OPEN_SAFE_MODEL', true);
// 是否调试模式
defined('APP_DEBUG') or define('APP_DEBUG', false);
//////////////////////////////////////////////some useful configuration const definition end//////////////////////////

require_once(SYS_COMMON_PATH.'functions.php');
if (!spl_autoload_register('my_autoload'))
	die('register auto load class function failed');

register_shutdown_function('shutdown_function', $_REQUEST);
///////////////////////////////////execute core class start//////////////////////
Application::run();
///////////////////////////////////execute core class end//////////////////////
