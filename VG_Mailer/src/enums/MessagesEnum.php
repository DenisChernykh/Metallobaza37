<?php

declare(strict_types=1);

namespace VG_Mailer\src\enums;

class MessagesEnum
{
    public const MAX_FILE_SIZE_ERROR = 'Максимальный размер файла %d MB';
    public const MAX_FILE_COUNT_ERROR = 'Слишком много файлов! Максимальное количество %d';
    public const FORM_REQUEST_STRUCTURE_ERROR = 'Запрос не соответствует структуре';
    public const SENDER_WORKER_METHOD_DISABLED = 'Метод отключен';
    public const PHONE_IS_INVALID = 'Телефон не валидный';
    public const PHONE_ALREADY_BLOCKED = 'Телефон уже заблокирован';
    public const FAKE_SEND = 'Фейковая отправка';
    public const FAKE_SEND_HOOK = 'Фейковая отправка от хука';
    public const ROW_NOT_FOUND = 'Запись не найдена';
}