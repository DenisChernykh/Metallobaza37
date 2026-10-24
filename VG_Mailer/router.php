<?php

use VG_Mailer\src\Api;
use VG_Mailer\src\api\AlertRequest;
use VG_Mailer\src\api\CaptchaRequest;
use VG_Mailer\src\api\FormRequest;
use VG_Mailer\src\api\LogsRequest;
use VG_Mailer\src\api\PhoneRequest;
use VG_Mailer\src\middlewares\AuthMiddleware;
use VG_Mailer\src\middlewares\DatabaseMiddleware;
use VG_Mailer\src\Router;

// ФРОНТ
// Отправленные формы
Api::post('vg-mailer-form-submit', [FormRequest::class, 'request']);
// Получаем активную капчу и ее настройки
Api::post('vg-mailer-get-captcha', [CaptchaRequest::class, 'getData']);
// Получаем конфиги для VGAlert
Api::post('vg-mailer-get-alert', [AlertRequest::class, 'getVGAlertData']);
// Получаем путь до VGAlert
Api::post('vg-mailer-get-alert-script', [AlertRequest::class, 'getVGAlertScript']);
// Получаем логи
Api::post('vg-mailer-get-logs', [LogsRequest::class, 'getData']);

// АДМИН ПАНЕЛЬ - ЗАЯВКИ
// Получаем все заявки
Api::post('vg-mailer-get-forms', [FormRequest::class, 'getForms'])->middleware(
    AuthMiddleware::class,
    DatabaseMiddleware::class
);
// Переотправить форму
Api::post('vg-mailer-form-resend', [FormRequest::class, 'resend'])->middleware(
    AuthMiddleware::class,
    DatabaseMiddleware::class
);
// Заблокировать номера из формы 
Api::post('vg-mailer-block-phones', [PhoneRequest::class, 'blockPhonesByFormId'])->middleware(
    AuthMiddleware::class,
    DatabaseMiddleware::class
);

// АДМИН ПАНЕЛЬ - Блокировка номеров
// Получаем все телефоны
Api::post('vg-mailer-get-phones', [PhoneRequest::class, 'getPhones'])
    ->middleware(AuthMiddleware::class, DatabaseMiddleware::class);
// Заносим номер в ЧС
Api::post('vg-mailer-block-phone', [PhoneRequest::class, 'blockPhone'])
    ->middleware(AuthMiddleware::class, DatabaseMiddleware::class);
// Получаем заблокированные номера
Api::post(
    'vg-mailer-get-blocked-phones',
    [PhoneRequest::class, 'getBlockedPhones']
)->middleware(AuthMiddleware::class, DatabaseMiddleware::class);
// Обновляем приемщики у заблокированного номера
Api::post('vg-mailer-update-services', [PhoneRequest::class, 'updateServicesByRowId'])->middleware(
    AuthMiddleware::class,
    DatabaseMiddleware::class
);
Api::post('vg-mailer-delete-blocked-phone', [PhoneRequest::class, 'deletePhone'])->middleware(
    AuthMiddleware::class,
    DatabaseMiddleware::class
);

// Админка - блокировка номеров
Api::get(
    'vgmailer',
    [PhoneRequest::class, 'page']
)->middleware(AuthMiddleware::class);
// Админка - заявки
Api::get(
    'vgmailer-leads',
    [FormRequest::class, 'page']
)->middleware(AuthMiddleware::class);

Router::dispatch();