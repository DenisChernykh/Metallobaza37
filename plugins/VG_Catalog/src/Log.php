<?php

namespace VG_Catalog;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log
{
    private static Logger $logger;

    public static function make(): Logger
    {
        if (!isset(self::$logger)) {
            $dir = dirname(__FILE__, 2) . '/logs/';
            self::$logger = new Logger('vg-amocrm-module');

            // формат без времени, имени логгера и уровня
            $formatter = new LineFormatter("%message% %context%\n", null, true, true);

            $infoHandler = new RotatingFileHandler($dir . 'info.log', 7, Logger::DEBUG);
            $infoHandler->setFormatter($formatter);
            $infoHandler = new FilterHandler($infoHandler, Logger::DEBUG, Logger::NOTICE);

            $errorHandler = new RotatingFileHandler($dir . 'error.log', 7, Logger::ERROR);
            $errorHandler->setFormatter($formatter);
            $errorHandler = new FilterHandler($errorHandler, Logger::ERROR, Logger::EMERGENCY);

            self::$logger->pushHandler($infoHandler);
            self::$logger->pushHandler($errorHandler);
        }

        return self::$logger;
    }
}
