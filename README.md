# Config

**Config** is an easy and simple PHP class that allows to configure your settings by allowing to read environment from operating system and public variables that you define.

# Install
Easy installation
```bash
composer install caiola/config
```
## How to use

Use composer autoload and use it

## Obtaining and overriding environment variables

```php
<?php

// Autoload files using Composer autoload
$loader = require_once __DIR__ . '/vendor/autoload.php';

use Caiola\Config\Config as Config;

Config::getInstance()->setImmutable(false);
Config::getInstance()->setUseEnvironment(true);

echo "# windir: " . Config::getInstance()->windir . PHP_EOL;

Config::getInstance()->windir = 'c:\\win\\';

echo "# windir: " . Config::getInstance()->windir . PHP_EOL;
```

Results
```
# windir: C:\WINDOWS
# windir: c:\win\
```

## Obtaining and set environment variables as immutable

```php
<?php

// Autoload files using Composer autoload
$loader = require_once __DIR__ . '/vendor/autoload.php';

use Caiola\Config\Config as Config;

// Allow to read environment variables
Config::getInstance()->setUseEnvironment(true);

// Allow to override environment variables
Config::getInstance()->setImmutable(true);

// Configuration example
$databases = array(
    'error_log' => '/var/log/error_log',
    'master'    => array(
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'pass',
        'db'   => 'dbapp'
    )
);

Config::getInstance()->set($databases);

// Obtain configuration values through several methods
$cfg = new Config();
echo "# db master: " . $cfg->master['db'] . PHP_EOL;
echo "# db master: " . Config::getInstance()->master['db'] . PHP_EOL;
echo "# db master: " . Config::getByKey("master.db") . PHP_EOL;

echo "# error_log: " . $cfg->error_log . PHP_EOL;
echo "# error_log: " . Config::getInstance()->error_log . PHP_EOL;
echo "# error_log: " . Config::getByKey("error_log") . PHP_EOL;

echo "# windir: " . $cfg->windir . PHP_EOL;
echo "# windir: " . Config::getInstance()->windir . PHP_EOL;
echo "# windir: " . Config::getByKey("windir") . PHP_EOL;

```

Results
```
# db master: dbapp
# db master: dbapp
# db master: dbapp
# error_log: /var/log/error_log
# error_log: /var/log/error_log
# error_log: /var/log/error_log
# windir: C:\WINDOWS
# windir: C:\WINDOWS
# windir: C:\WINDOWS
```


