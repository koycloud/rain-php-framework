<?php
class Action
{
	public $valArr = null;
	public $method = null;
	public $act = null;
	public $tpl = null;
	public $open_token = null;

	protected function set($name, $val)
	{
		Application::$controller_obj->valArr[$name] = $val;
		unset($val);
	}

	protected function checktoken()
	{
		if (count($_POST) && Application::$controller_obj->open_token && count($_REQUEST) && isset($_SESSION[HIDDEN_TOKEN_NAME]) && isset($_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]]))
		{
			if (!isset($_REQUEST[HIDDEN_TOKEN_NAME]))
				return false;
			$val2 = trim($_REQUEST[HIDDEN_TOKEN_NAME]);
			if ($val2 != $_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]])
			{
				unset($_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]]);
				unset($_SESSION[HIDDEN_TOKEN_NAME]);
				return false;
			}
			unset($_SESSION[$_SESSION[HIDDEN_TOKEN_NAME]]);
			unset($_SESSION[HIDDEN_TOKEN_NAME]);
		}
		return true;
	}

	protected function display($tpl = null)
	{
		if (is_null($tpl))
			$tpl = $this->method;
		$this->tpl = $tpl;
		if (Application::$act == $this->act && Application::$mod == $this->method)
			Application::$tpl = $tpl;
		else
		{
			$GLOBALS['_FileCount']++;
			return;
		}

		$content = file_get_contents(APP_TPL.Application::$controller_obj->act.'/'.Application::$tpl.'.html');
		//parse include
		$ret = preg_match_all('/<\{\s*include\s*=\s*"(.*?)"\}>/i', $content, $match);
		if ($ret)
		{
			foreach ($match[1] as $k => $v)
			{
				$tArr = explode('/', $v);
				$act_name = ucfirst($tArr[0].'Action');
				$act_name = new $act_name();
				$act_name->$tArr[1]();
				unset($tArr);
			}
		}

		if (is_array(Application::$controller_obj->valArr) && !empty(Application::$controller_obj->valArr))
		{
			foreach (Application::$controller_obj->valArr as $k => $v)
				$$k = $v;
			unset(Application::$controller_obj->valArr);
		}
		$GLOBALS['_FileCount']++;
		require_once(load_tpl(APP_TPL.Application::$controller_obj->act.'/'.Application::$tpl.'.html', Application::$controller_obj->open_token));
	}
}
