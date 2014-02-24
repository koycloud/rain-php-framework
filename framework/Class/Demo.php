<?php
class Demo
{
	public function run()
	{
		$this->create_action();
		$this->create_model();
		$this->create_tpl();
	}

	public function create_action()
	{
		if (file_exists(APP_ACTION.'IndexAction.php'))
			return;
		$str = <<<EOT
<?php
/**
	this is system auto create controller, you can modify it anyway
*/
class IndexAction extends Action
{
	public function index()
	{
		\$this->set('val', 'hello world');
		\$this->set('tarr', array('框架追求安全', '框架追求可扩展', '框架最求简单', '框架追求高效'));
		\$this->display();
	}
}
EOT;
		file_put_contents(APP_ACTION.'IndexAction.php', $str);
	}

	public function create_model()
	{
		if (file_exists(APP_MODEL.'IndexModel.php'))
			return;
		$str = <<<EOT
<?php
/**
	this is system auto create model, you can modify it anyway
*/
class IndexModel extends Model
{
}
EOT;
		file_put_contents(APP_MODEL.'IndexModel.php', $str);
	}

	public function create_tpl()
	{
		mkdirs(APP_TPL.'Index');
		if (file_exists(APP_TPL.'Index/index.html'))
			return;
		$str = <<<EOT
<html>
<head>
	<title>框架测试页面内容</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>
	<div style="margin 100px auto"><h2><{\$val}></h2></div>
	<div style="margin 100px auto"><h2><{if (1 == 1)}>IF 语句的测试1<{/if}></h2></div>
	<div style="margin 100px auto"><h2><{if (1 == 2)}>IF 语句的测试2<{/if}></h2></div>
	<div style="margin 100px auto">
		<h2>
			<h1>第一种循环例子</h1>
			<{ foreach (\$tarr as \$t)}>
			<{\$t}><br/>
			<{/foreach}>
			<h1>第二种循环例子</h1>
			<{ loop (\$tarr as \$t) }>
			<{\$t}><br/>
			<{/loop}>
		</h2>
	</div>
</body>
</html>
EOT;
		file_put_contents(APP_TPL.'Index/index.html', $str);
	}
}
