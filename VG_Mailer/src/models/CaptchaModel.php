<?php

namespace VG_Mailer\src\models;

use Exception;
use VG_Mailer\src\interfaces\CaptchaItemInterface;

class CaptchaModel
{
    private static CaptchaModel $model;

    private bool $use = true;
    private array $models = [];

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new CaptchaModel();
        }
        return self::$model;
    }

    public function isUse(): bool
    {
        return $this->use;
    }

    public function setUse(bool $use): CaptchaModel
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return CaptchaItemInterface[]
     */
    public function getModels(): array
    {
        return $this->models;
    }

    public function setModels(array $models): CaptchaModel
    {
        foreach ($models as $model) {
            if (!$model instanceof CaptchaItemInterface) {
                throw new Exception('Неверная модель');
            }
        }
        $this->models = $models;
        return $this;
    }
}
