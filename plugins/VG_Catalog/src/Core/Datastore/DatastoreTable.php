<?php

namespace VG_Catalog\Core\Datastore;

use Carbon_Fields\Datastore\Datastore;
use Carbon_Fields\Field\Field;

class DatastoreTable extends Datastore {
    private array $fieldMapping = [
        'wp_vg_products' => [
            '_description' => 'description',
            '_price' => 'price',
            '_price_sale' => 'sale_price',
            '_sku' => 'sku',
            '_unit' => 'unit'
        ],
        'wp_vg_group_options' => [
            '_required' => 'required',
            '_multiple' => 'multiple',
            'checkbox' => [
                'required',
                'multiple'
            ]
        ],
        'wp_vg_options' => [
            '_group_id' => 'group_id',
            '_vg_option_additional' => 'additional',
            'json' => [
                '_vg_option_additional' => 'additional'
            ],
        ],
        'wp_vg_product_groups' => [
            '_group_id' => 'group_id',
            'foreignKey' => 'product_id',
            'multiple' => 'group_id',
            'forceDelete' => true,
            'subType' => 'vg_group_options'
        ]
    ];
    
    private string $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getPostId(): int
    {
        $postId = $this->get_object_id();
        if (!$postId && isset($_GET['post'])) {
            $postId = intval($_GET['post']);
        }
        return $postId;
    }

    public function init() {}

    public function save(Field $field): void
    {
        $postId = $this->getPostId();
        if (!$postId) return;

        $fieldName = $field->get_name();
        $foreignKey = $this->fieldMapping[$this->tableName]['foreignKey'] ?? 'post_id';
        if (!isset($this->fieldMapping[$this->tableName][$fieldName])) {
            return;
        }

        if (isset($this->fieldMapping[$this->tableName]['json'][$fieldName])) {
            $values = $field->get_formatted_value();
            $json = [];
            foreach ($values as $key => $value) {
                $json[$key]['title'] = $value[substr($fieldName, 1).'_title'];
                $json[$key]['value'] = $value[substr($fieldName, 1).'_value'];
            }
            $json = json_encode($json, true);

            $this->saveFieldValue($postId, $this->fieldMapping[$this->tableName][$fieldName], $json, $foreignKey);
            return;
        }
        $value = $field->get_value();

        $columnName = $this->fieldMapping[$this->tableName][$fieldName];

        if (isset($this->fieldMapping[$this->tableName]['forceDelete'])) {
            global $wpdb;
            $wpdb->delete($this->tableName, [$foreignKey => $postId], [ '%d' ]);
        }
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->saveFieldValue($postId, $columnName, $val['id'], $foreignKey);
            }
        }
        else {
            $this->saveFieldValue($postId, $columnName, $value, $foreignKey);
        }
    }

    public function load(Field $field)
    {
        $postId = $this->getPostId();
        if (!$postId) return;

        $fieldName = $field->get_name();

        if (!isset($this->fieldMapping[$this->tableName][$fieldName])) {
            return;
        }

        $columnName = $this->fieldMapping[$this->tableName][$fieldName];
        if (isset($this->fieldMapping[$this->tableName]['json'][$fieldName])) {
            $json = $this->loadFieldValue($postId, $columnName);
            $result = [];
            foreach (json_decode($json, true) as $key => $value) {
                $result[$key]['_type'] = "_";
                $result[$key][substr($fieldName, 1).'_title'] = $value['title'];
                $result[$key][substr($fieldName, 1).'_value'] = $value['value'];
            }

            return $result;
        }
        return $this->loadFieldValue($postId, $columnName);
    }

    public function delete(Field $field) {
        $postId = $this->getPostId();
        if (!$postId) return;

        $fieldName = $field->get_name();
        $foreignKey = $this->fieldMapping[$this->tableName]['foreignKey'] ?? 'post_id';
        if (!isset($this->fieldMapping[$this->tableName][$fieldName])) {
            return;
        }
        if (isset($this->fieldMapping[$this->tableName]['forceDelete'])) {
            global $wpdb;
            $wpdb->delete($this->tableName, [$foreignKey => $postId], [ '%d' ]);
        }
        else {
            $columnName = $this->fieldMapping[$this->tableName][$fieldName];
            $this->deleteFieldValue($postId, $columnName, $foreignKey, $fieldName);
        }
    }

    protected function saveFieldValue($postId, $column, $value, $foreignKey = 'post_id') {
        global $wpdb;

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->tableName} WHERE $foreignKey = %d",
            $postId
        ));

        $data = [
            $foreignKey => $postId,
            $column => $value,
            'updated_at' => current_time('mysql')
        ];

        if ($exists && !isset($this->fieldMapping[$this->tableName]['forceDelete'])) {
            $wpdb->update($this->tableName, $data, [$foreignKey => $postId]);
        } else {
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($this->tableName, $data);
        }
    }

    protected function loadFieldValue($postId, $column) {
        global $wpdb;
        $foreignKey = isset($this->fieldMapping[$this->tableName]['foreignKey']) ? 'product_id' : 'post_id';

        if (isset($this->fieldMapping[$this->tableName]['multiple'])) {
            $values = $wpdb->get_results($wpdb->prepare(
                "SELECT {$this->fieldMapping[$this->tableName]['multiple']} FROM {$this->tableName} WHERE {$foreignKey} = %d",
                $postId
            ));

            foreach ($values as $val) {
                $value[] = [
                    'id' => $val->$column,
                    'type' => 'post',
                    'title' => get_the_title($val->$column),
                    'label' => get_the_title($val->$column),
                    'subtype' => $this->fieldMapping[$this->tableName]['subType'] ?? ''
                ];
            }
        }
        else {
            $value = $wpdb->get_var($wpdb->prepare(
                "SELECT {$column} FROM {$this->tableName} WHERE {$foreignKey} = %d",
                $postId
            ));

            if (str_contains($column, '_id')) {
                $value = [
                    'id' => $value,
                ];
            }
            if (isset($this->fieldMapping[$this->tableName]['checkbox']) && in_array($column, $this->fieldMapping[$this->tableName]['checkbox'])) {
                $value = $value ?? 'no';
            }
        }

        return $value;
    }

    protected function deleteFieldValue($postId, $column, $foreignKey = 'post_id', $fieldName) {
        global $wpdb;
        $isJson = isset($this->fieldMapping[$this->tableName]['json'][$fieldName]);
        if ($foreignKey !== 'post_id') {
            $wpdb->update(
                $this->tableName,
                [$column => $isJson ? '[]' : ''],
                [$foreignKey => $postId]
            );
        }
    }
}