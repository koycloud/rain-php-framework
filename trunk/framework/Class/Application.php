<?php
class Application
{
	public static $act = null;
	public static $mod = null;
	public static $tpl = null;
	public static $controller_obj = null;

	public static function run()
	{
		self::init();
		self::parseurl();
	}

	private static function init()
	{
		load_functions();
		if (!build())
			die('build directories error');
		safe();
	}

	private static function parseurl()
	{
		if (isset($_REQUEST['act']) && isset($_REQUEST['mod']))
		{
			$act = ucfirst(trim($_REQUEST['act']).'Action');
			$method = trim($_REQUEST['mod']);
		}
		else
		{
			$act = 'IndexAction';
			$method = 'index';
		}

		if (CREATE_DEMO)
		{
			//if need create demo
			$demo = new Demo();
			$demo->run();
		}

		if (!class_exists($act))
		{
			if (APP_DEBUG)
				die('controller class: '.$act.' not find. ');
			else
				location();
		}
		$controller = new $act();
		if (!method_exists($controller, $method))
		{
			if (APP_DEBUG)
				die('controller class: '.$act.', method: '.$method.' not find. ');
			else
				location();
		}
		self::$controller_obj = $controller;
		$controller->method = $method;
		$controller->open_token = OPEN_TOKEN;
		$controller->act = str_ireplace('Action', '', $act);
		self::$act = $controller->act;
		self::$mod = $method;
		$controller->$method();
	}
}
