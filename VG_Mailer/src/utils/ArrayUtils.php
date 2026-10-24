<?php

namespace VG_Mailer\src\utils;

class ArrayUtils
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public static function stripslashes($data)
    {
        if (is_array($data)) {
            return array_map('stripslashes_array', $data);
        }
        return is_string($data) ? stripslashes($data) : $data;
    }
}