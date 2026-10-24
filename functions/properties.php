<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Регистрируем кастомный тип характеристики
add_action( 'init', 'register_product_feature_post_type' );
function register_product_feature_post_type() {
    $labels = array(
        'name' => 'Характеристики',
        'singular_name' => 'Характеристика',
        'add_new_item' => 'Добавить новую характеристику',
        'edit_item' => 'Редактировать характеристику',
        'all_items' => 'Все характеристики',
    );

    register_post_type( 'product_feature', array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-admin-generic',
        'supports' => array('title'), // Только заголовок
    ));
}

// Поля для привязки характеристик к товарам
add_action( 'carbon_fields_register_fields', 'register_product_feature_values' );
function register_product_feature_values() {
    Container::make( 'post_meta', 'Характеристики товара' )
        ->where( 'post_type', '=', 'vg_product' )
        ->add_fields( array(
            Field::make( 'complex', 'product_features', 'Характеристики товара' )
                ->set_layout('tabbed-vertical')
                ->add_fields( array(
                    Field::make( 'association', 'feature_id', 'Выберите характеристику' )
                        ->set_types( array(
                            array(
                                'type' => 'post',
                                'post_type' => 'product_feature',
                            )
                        ))
                        ->set_max(1),
                    Field::make( 'text', 'feature_value', 'Значение характеристики' ),
                ))
                ->set_header_template( '
                    <% if (feature_id.length) { %>
                        <%- feature_id[0].title %> — <%- feature_value %>
                    <% } else { %>
                        Характеристика не выбрана
                    <% } %>
                ')
        ));
}

add_action( 'carbon_fields_post_meta_container_saved', 'save_product_features_to_table', 10, 2 );
function save_product_features_to_table( $post_id, $container ) {
    if ( get_post_type($post_id) !== 'vg_product' ) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'vg_product_chars';

    // Получаем данные из Carbon Fields
    $features = carbon_get_post_meta( $post_id, 'product_features' );

    // Удаляем старые значения
    $wpdb->delete( $table, ['product_id' => $post_id] );

    if ( $features ) {
        foreach ( $features as $feature ) {
            $feature_obj_array = $feature['feature_id'] ?? [];
            $feature_value = $feature['feature_value'] ?? '';

            if ( !empty($feature_obj_array) ) {
                $feature_id = $feature_obj_array[0]['id'] ?? 0; // берём реальный ID поста
                if ( $feature_id ) {
                    $wpdb->insert(
                        $table,
                        [
                            'product_id' => $post_id,
                            'feature_id' => $feature_id,
                            'feature_value' => $feature_value,
                        ]
                    );
                }
            }
        }
    }
}


/**
 * Получает характеристики товара через Carbon Fields
 *
 * @param int $product_id ID товара
 * @return array Массив характеристик в формате ['Название характеристики' => 'Значение']
 */
function get_product_properties($product_id) {
    $features_data = carbon_get_post_meta($product_id, 'product_features');
    $features = [];

    if ($features_data) {
        foreach ($features_data as $feature) {
            $feature_obj_array = $feature['feature_id'] ?? [];
            $feature_value = $feature['feature_value'] ?? '';

            if (!empty($feature_obj_array)) {
                $feature_id = $feature_obj_array[0]['id'] ?? 0;
                $feature_name = get_the_title($feature_id);

                if ($feature_name && $feature_value) {
                    $features[$feature_name] = $feature_value;
                }
            }
        }
    }

    return $features;
}