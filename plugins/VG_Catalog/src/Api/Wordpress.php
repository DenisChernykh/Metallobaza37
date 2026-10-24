<?php

namespace VG_Catalog\Api;

class Wordpress
{
    private static Wordpress $model;

    public static function make(): Wordpress
    {
        if (!isset(self::$model)) {
            self::$model = new Wordpress();
        }
        return self::$model;
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