<?php
define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
define('APP_NAME', 'Home');
define('FRAMEWORK_PATH', APP_PATH.'framework/');
define('APP_DEBUG', true);
//open safe model need more running time, but this is very important ,default  value  true
define('OPEN_SAFE_MODEL', true);

require(FRAMEWORK_PATH.'Framework.php');
