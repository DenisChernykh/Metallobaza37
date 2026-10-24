<?php

namespace VG_Mailer\src\models;

class DBModel
{

    private static DBModel $model;

    private string $host;

    private int $port;

    private string $username;

    private string $password;

    private string $dbName;

    private bool $use = false;

    public static function make(): DBModel
    {
        if (!isset(self::$model)) {
            self::$model = new DBModel();
        }
        return self::$model;
    }

    /**
     * @return DBModel
     */
    public static function getModel(): DBModel
    {
        return self::$model;
    }

    /**
     * @param  DBModel  $model
     */
    public static function setModel(DBModel $model): void
    {
        self::$model = $model;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param  string  $host
     *
     * @return DBModel
     */
    public function setHost(string $host): DBModel
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param  int  $port
     *
     * @return DBModel
     */
    public function setPort(int $port): DBModel
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param  string  $username
     *
     * @return DBModel
     */
    public function setUsername(string $username): DBModel
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param  string  $password
     *
     * @return DBModel
     */
    public function setPassword(string $password): DBModel
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @param  string  $dbName
     *
     * @return DBModel
     */
    public function setDbName(string $dbName): DBModel
    {
        $this->dbName = $dbName;
        return $this;
    }

    public function isUse(): bool
    {
        return $this->use;
    }

    public function setUse(bool $use): DBModel
    {
        $this->use = $use;
        return $this;
    }
}