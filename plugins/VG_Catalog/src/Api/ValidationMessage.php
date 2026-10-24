<?php

namespace VG_Catalog\Api;
class ValidationMessage
{
    public const REQUIRED = "Обязательный параметр";
    public const EMAIL = "Неверный формат электронной почты";
    public const TYPES = [
        "int" => "Поле должно быть числом",
        "string" => "Поле должно быть строкой"
    ];
    public const LENGTH = [
        "greater" => "Число должно быть больше чем "
    ];
}