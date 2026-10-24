<?php

declare(strict_types=1);

namespace VG_Mailer\src;

class Messages
{
    public const MAX_FILE_SIZE_ERROR = 'Максимальный размер файла %d MB';
    public const MAX_FILE_COUNT_ERROR = 'Слишком много файлов! Максимальное количество %d';
    public const FORM_REQUEST_STRUCTURE_ERROR = 'Запрос не соответствует структуре';
    public const SENDER_WORKER_METHOD_DISABLED = 'Метод отключен';
}