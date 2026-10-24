<?php

namespace VG_Catalog\Core;

if (!defined('ABSPATH')) {
    exit;
}

class InitScripts
{
    public static function init()
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_scripts']);
    }

    public static function enqueue_scripts()
    {
        wp_enqueue_script(
            'vg-axios',
            plugin_dir_url(dirname(__DIR__)) . 'resources/assets/js/shared/axios.min.js',
            [],
            '1.6.7',
            true
        );

        wp_enqueue_script(
            'vg-catalog-cart',
            plugin_dir_url(dirname(__DIR__)) . 'resources/assets/js/cart.js',
            [],
            time(),
            true
        );
        wp_localize_script('vg-catalog-cart', 'VG_Catalog', [
            'ajax_url' => get_site_url(),
        ]);
    }
}