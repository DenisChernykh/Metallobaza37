<?php

namespace VG_Mailer\src\models;

class ConfigModel
{
    private static ConfigModel $model;
    private float $version = 1.0;
    private int $maxSize = 5;
    private int $maxFilesCount = 8;

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new ConfigModel();
        }
        return self::$model;
    }

    public static function getModel(): ConfigModel
    {
        return self::$model;
    }

    public static function setModel(ConfigModel $model): void
    {
        self::$model = $model;
    }

    /**
     * @return int
     */
    public function getMaxFilesCount(): int
    {
        return $this->maxFilesCount;
    }

    /**
     * @param  int  $maxFilesCount
     * @return ConfigModel
     */
    public function setMaxFilesCount(int $maxFilesCount): ConfigModel
    {
        $this->maxFilesCount = $maxFilesCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxSize(): int
    {
        return $this->maxSize;
    }

    /**
     * @param  int  $maxSize
     * @return ConfigModel
     */
    public function setMaxSize(int $maxSize): ConfigModel
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @return float
     */
    public function getVersion(): float
    {
        return $this->version;
    }

    /**
     * @param  float  $version
     * @return ConfigModel
     */
    public function setVersion(float $version): ConfigModel
    {
        $this->version = $version;
        return $this;
    }
}