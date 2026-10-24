<?php

namespace VG_Mailer\src\models;

class AlertModel
{
    private static AlertModel $model;

    private array $success = [];
    private array $error = [];

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new AlertModel();
        }
        return self::$model;
    }

    public function setSuccess(array $success): AlertModel
    {
        $this->success = $success;
        return $this;
    }

    public function setError(array $error): AlertModel
    {
        $this->error = $error;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error,
        ];
    }
}