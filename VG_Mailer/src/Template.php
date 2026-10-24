<?php

namespace VG_Mailer\src;

use Exception;

class Template
{
    private static Template $model;

    public static function make(): Template
    {
        if (!isset(self::$model)) {
            self::$model = new Template();
        }
        return self::$model;
    }

    public function getItems(string $dir, array $data): string
    {
        $result = '';
        foreach ($data as $datum) {
            $result .= "\n".$this->getTemplate($dir, 'item', $datum);
        }

        return $result;
    }

    public function getTemplate(string $dir, string $name, array $data): string
    {
        $path = dirname(__FILE__, 2).'/resources/templates/'.$dir.'/'.$name.'.php';
        if (!file_exists($path)) {
            throw new Exception('Шаблон ('.$path.') не найден');
        }

        extract($data);
        ob_start();
        require $path;
        return ob_get_clean();
    }
}