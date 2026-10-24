<?php

namespace VG_Mailer\src\utils;

class StringUtils
{
    public static function filterDigits(string $string, bool $allowAsterisk = false): string
    {
        $regExp = '/[^0-9' . ($allowAsterisk ? '*' : '') . ']+/';
        return preg_replace($regExp, '', $string);
    }
}