<?php

namespace VG_Mailer\src\logs;

use VG_Mailer\src\Core;

class JsLog
{
    private static array $logs = [];

    public static function addLog(string $status, string $message, bool $hard = false)
    {
        $core = Core::make();
        if (!$core->isDebug() && !$hard) {
            return;
        }

        self::$logs[] = [
            'status' => $status,
            'message' => $message,
        ];
    }

    public static function saveToSession()
    {
        $_SESSION['js_log'] = json_encode(self::$logs);
    }

    public static function getSessionData()
    {
        return json_decode($_SESSION['js_log'] ?? '[]', true);
    }
}
