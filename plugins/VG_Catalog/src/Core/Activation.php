<?php

namespace VG_Catalog\Core;

use Carbon_Fields\Carbon_Fields;

use function is_plugin_active;

class Activation
{

    public static function createCartTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_catalog_cart';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            quantity INT(11) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (product_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createCartOptionsTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_catalog_cart_options';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            cart_id BIGINT(20) UNSIGNED NOT NULL,
            option_id BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (cart_id) REFERENCES {$wpdb->prefix}vg_catalog_cart (id) ON DELETE CASCADE,
            FOREIGN KEY (option_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createProductsTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_products';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            description TEXT,
            price FLOAT(2) NOT NULL,
            sale_price FLOAT(2),
            sku VARCHAR(255) NOT NULL,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createGroupOptionsTables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'vg_group_options';

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            required VARCHAR(3) NOT NULL DEFAULT 'yes',
            multiple VARCHAR(3) NOT NULL DEFAULT 'yes',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createOptionsTables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'vg_options';

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            group_id BIGINT(20) UNSIGNED NOT NULL,
            additional JSON,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (group_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE,
            FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createProductsGroupsTable()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'vg_product_groups';

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            group_id BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY unique_product_group (product_id, group_id),
            FOREIGN KEY (group_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES {$wpdb->prefix}posts (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createOrderTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_orders';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NOT NULL,
            status ENUM('PROGRESS', 'COMPLETED', 'CANCELED', 'WAITING') NOT NULL DEFAULT 'PROGRESS',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users (id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createOrderItemsTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_order_items';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT(20) UNSIGNED NOT NULL,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            quantity INT(11) DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (order_id) REFERENCES {$wpdb->prefix}vg_orders (id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES {$wpdb->prefix}vg_products (post_id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function createOrderItemOptionsTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'vg_order_item_options';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_item_id BIGINT(20) UNSIGNED NOT NULL,
            option_id BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (order_item_id) REFERENCES {$wpdb->prefix}vg_order_items (id) ON DELETE CASCADE,
            FOREIGN KEY (option_id) REFERENCES {$wpdb->prefix}vg_options (post_id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function checkDependencies()
    {
        add_action('admin_init', function () {
            if (!function_exists('is_plugin_active')) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }

            // Инициализация Carbon Fields только если плагин активен
            if (class_exists('Carbon_Fields\Carbon_Fields')) {
                Carbon_Fields::boot();
            } else {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-error is-dismissible">
                        <p>Для работы плагина "VG Catalog" требуется Carbon Fields. Плагин не активирован.</p>
                    </div>';
                });

                deactivate_plugins(plugin_basename(__FILE__));
            }
        });
    }

    public static function createDefaultTemplates()
    {
        $theme_dir = get_stylesheet_directory();

        $templates = [
            'single-vg_product.php' => <<<PHP
            <div style="padding: 40px; text-align:center; font-size:18px; color:#333;">
            Редактируйте шаблон: {$theme_dir}/single-vg_product.php
            </div>
            PHP
                        ,
                        'taxonomy-vg_product_category.php' => <<<PHP
            <div style="padding: 40px; text-align:center; font-size:18px; color:#333;">
            Редактируйте шаблон: {$theme_dir}/taxonomy-vg_product_category.php
            </div>
            PHP
        ];

        foreach ($templates as $file => $content) {
            $path = $theme_dir . '/' . $file;
            if (!file_exists($path)) {
                @file_put_contents($path, $content);
            }
        }
    }
}