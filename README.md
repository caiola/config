# Config

**Config** is an easy and simple PHP class that allows to configure your settings by allowing to read environment from operating system and public variables that you define.

# Install
Easy installation
```
composer install caiola/config
```
## How to use

Use composer autoload and use it
```
<?php
// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Caiola\Config;

// Example of configuration
$databases = array(
	'error_log' => '/var/log/error_log',
	'master' => array(
		'host' => '127.0.0.1',
		'port' => '3306',
		'user' => 'root',
		'pass' => 'pass',
		'db' => 'dbapp'
	)
);

Config::getInstance()->set($databases);

// Access to variables
$cfg = new Config();

echo "# Error Log: " . $cfg->error_log . PHP_EOL;
echo "# Error Log: " . Config::getInstance()->error_log . PHP_EOL;
echo "# Error Log: " . Config::getByKey("error_log") . PHP_EOL;
echo "# DB - Host: " . Config::getByKey("master.host") . PHP_EOL;

```
