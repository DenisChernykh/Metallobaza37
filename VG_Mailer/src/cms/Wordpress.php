<?php

namespace VG_Mailer\src\cms;

use VG_Mailer\src\Core;
use VG_Mailer\src\models\DBModel;

class Wordpress
{
    private static Wordpress $model;

    public function setDbConfig(): void
    {
        $db = DBModel::make();
        if (!$db->isUse()) {
            return;
        }

        $host = $db->getHost();
        $port = $db->getPort();
        $dbName = $db->getDBname();
        $username = $db->getUsername();
        $password = $db->getPassword();

        if ($host === '' && defined('DB_HOST')) {
            $host = DB_HOST;
        }
        if ($port === 0) {
            $port = 3306;
        }
        if ($dbName === '' && defined('DB_NAME')) {
            $dbName = DB_NAME;
        }
        if ($username === '' && defined('DB_USER')) {
            $username = DB_USER;
        }
        if ($password === '' && defined('DB_PASSWORD')) {
            $password = DB_PASSWORD;
        }

        $db
            ->setHost($host)
            ->setPort($port)
            ->setDbName($dbName)
            ->setUsername($username)
            ->setPassword($password);
    }

    public static function make(): Wordpress
    {
        if (!isset(self::$model)) {
            self::$model = new Wordpress();
        }
        return self::$model;
    }

    /**
     * Автоматический подключаем скрипты, если это WP
     *
     * @return void
     */
    public function initScripts(): void
    {
        add_action(
            'wp_enqueue_scripts',
            function () {
                $core = Core::make();
                $scriptsPaths = $core->getFrontScripts();
                $integrationScripts = $core->getIntegrationScripts();
                $prefix = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

                foreach ($scriptsPaths as $path) {
                    if (substr($path, 0, strlen($prefix)) === $prefix) {
                        $path = substr($path, strlen($prefix));
                    }

                    wp_enqueue_script(
                        'VG_Mailer_'.md5($path),
                        $path,
                        [],
                        '',
                        true,
                    );
                }
                foreach ($integrationScripts as $scriptPath) {
                    wp_enqueue_script(
                        'VG_Mailer_'.md5($scriptPath),
                        $scriptPath,
                        [],
                        '',
                        true,
                    );
                }
            },
        );
    }

    public function isAdmin(): bool
    {
        if (!$this->isWp()) {
            return true;
        }

        $isAdmin = function_exists('current_user_can') && current_user_can('manage_options');
        return !!$isAdmin;
    }

    public function isWp(): bool
    {
        return defined('ABSPATH');
    }
}