<?php
define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
define('APP_NAME', 'home');
define('FRAMEWORK_PATH', APP_PATH.'framework/');
//open safe model need more running time, but this is very important ,default  value  true
define('OPEN_SAFE_MODEL', true);
define('APP_DEBUG', true);

require(FRAMEWORK_PATH.'Framework.php');
