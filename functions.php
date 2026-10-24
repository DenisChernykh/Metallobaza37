<?php
if (!defined('ABSPATH')) exit;
require __DIR__ . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/VG_Mailer/index.php';


foreach (glob(__DIR__ . '/functions/*.php') as $file) {
	require_once $file;
}

