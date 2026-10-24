<?php
/**
 * Plugin Name: VICTORY Group Catalog
 * Description: Базовый каталог с товарами и категориями.
 * Version: 1.0
 * Author: VICTORY Group
 */

use VG_Catalog\Core\Activation;
use VG_Catalog\Core\Fields;
use VG_Catalog\Core\InitScripts;
use VG_Catalog\Core\PostTypes;
use VG_Catalog\Database;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/validationAdditionalData.php';
require_once __DIR__ . '/src/Api/ValidationMessage.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/includes/Helpers/ProductHelper.php';
require_once __DIR__ . '/includes/Helpers/CategoryHelper.php';
require_once __DIR__ . '/includes/Helpers/CartHelper.php';

register_activation_hook(__FILE__, function () {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    Activation::createProductsTable();
    Activation::createGroupOptionsTables();
    Activation::createOptionsTables();
    Activation::createProductsGroupsTable();
    Activation::createCartTable();
    Activation::createCartOptionsTable();
    Activation::createOrderTable();
    Activation::createOrderItemsTable();
    Activation::createOrderItemOptionsTable();
    Activation::createDefaultTemplates();
});

add_action('plugins_loaded', function () {
    Database::init();
    PostTypes::register();             // Регистрация CPT и таксономий
    Fields::register();                // Поля через Carbon Fields
    InitScripts::init();
    Activation::checkDependencies();   // Проверка Carbon Fields
}); 
