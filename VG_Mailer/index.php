<?php

/**********************************************************/

/*	@copyright	VictoryGroup					          */
/*	@support	https://victoryagency.ru/				  */

/**********************************************************/

use VG_Mailer\src\Core;

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    // Возможно следует сделать - composer install
    throw new RuntimeException('VG_Mailer requires the composer.phar install');
}
require_once $autoload;

require_once 'hooks/fake-success.php';
require_once 'hooks/data-after-prepare.php';
require_once 'hooks/item-validate.php';
require_once 'hooks/item-exception.php';

// Загрузка config.php
$configPath = __DIR__ . '/config.php';
$configExamplePath = __DIR__ . '/config.example.php';
if (!file_exists($configPath) && file_exists($configExamplePath)) {
    file_put_contents($configPath, file_get_contents($configExamplePath));
}
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    throw new Exception('VG_Mailer: config.php not found');
}
$core = Core::make();
return $core;