<?php
/*
	only use in utf8, why do not use gbk please see http://blog.csdn.net/felio/article/details/1226569
*/
class Mysql
{
	private $conf = null;
	private $pdo = null;
	private $statement = null;
	private $lastInsID = null;
	private static $_instance = null;

	private function __clone()
	{
		die('Clone is not allow!');
	}

	private function __construct($conf = null)
	{
		if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql'))
			die('open PDO and pdo_mysql first');

		if (version_compare(PHP_VERSION, '5.3.9', '<'))
			die('to be safe, PDO need PHP_VERSION > 5.3.6 and PHP_VERSION 5.3.8 has hash bug, so need PHP_VERSION >= 5.3.9, you php version:'.PHP_VERSION);

		$this->conf = array(
			'dsn' => C('db-dsn'),
			'un' => C('db-un'),
			'pw' => C('db-pw'),
		);

		if (is_array($conf) && !empty($conf))
		{
			foreach ($conf as $k => $v)
			{
				if (!is_scalar($v) || !isset($this->conf[$k]))
				{
					unset($conf[$k]);
					continue;
				}
				$this->conf[$k] = $v;
			}
		}
	}

	public static function getInstance($conf = null)
	{
		if (!(self::$_instance instanceof self))
			self::$_instance = new self($conf);
		return self::$_instance;
	}

	public function connect()
	{
		if (!is_null($this->pdo))
			return $this->pdo;
		try {
			$this->pdo = new PDO($this->conf['dsn'], $this->conf['un'], $this->conf['pw'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_EMULATE_PREPARES => false));
		} catch (PDOException $e) {
			if (APP_DEBUG)
				throw new Exception($e->getMessage()); 
			die('new PDO  class error');
		}
	}


	public function free()
	{
		if (!is_null($this->statement))
		{
			$this->statement->closeCursor();
			$this->statement = null;
		}
	}

	public function query($sql, $data = array(), $one = false)
	{
		if (!is_array($data))
			return false;

		if (is_null($this->pdo))
			$this->connect();
		$this->free();
		
		$this->statement = $this->pdo->prepare($sql);
		if (false === $this->statement)
		{
			if (APP_DEBUG)
				throw new Exception('sql:'.$sql);
			die('execute sql error');
        }
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				$this->statement->bindValue($k, $v);
			}
		}
		$this->statement->execute();

		$GLOBALS['_SQLCount']++;

		if (preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql))
			$this->lastInsID = $this->getLastInsertId();
		else
			$this->lastInsID = null;

		if (!is_null($this->lastInsID))
			return $this->lastInsID;
		if ($one)
			return $this->statement->fetch(PDO::FETCH_ASSOC);
		else
			return $this->statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getLastInsertId()
	{
		if (is_null($this->pdo))
			$this->connect();
		return $this->pdo->lastInsertId();
	}
}
