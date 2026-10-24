<?php

/**********************************************************/

/*	@copyright	VictoryGroup					          */
/*	@support	https://victoryagency.ru/				  */

/**********************************************************/

use VG_Mailer\src\models\AlertModel;
use VG_Mailer\src\models\CaptchaModel;
use VG_Mailer\src\models\CloudflareCaptchaModel;
use VG_Mailer\src\models\ConfigModel;
use VG_Mailer\src\models\DBModel;
use VG_Mailer\src\models\EmailModel;
use VG_Mailer\src\models\EmailSMTPModel;
use VG_Mailer\src\models\TelegramModel;
use VG_Mailer\src\models\YandexSmartCaptchaModel;

ConfigModel::make()
    ->setVersion(2.4)
    ->setMaxSize(5)
    ->setMaxFilesCount(8);

// Данные на фронт идут через запрос и кешируются там.
// Если тут что-то изменили, то в консоли браузера введите VGMailerCache.clear()
AlertModel::make()
    ->setSuccess([
        'text' => 'Форма успешно отправлена',
        'classes' => ['success'],
        'closeButton' => true
    ])
    ->setError([
        'text' => 'Ошибка при отправке формы',
        'classes' => ['error'],
        'closeButton' => true
    ]);

EmailModel::make()
    ->setUse(false)
    ->setToMail('')
    ->setSubject('test subject')
    ->setName('test name')
    ->setFromMail('')
    ->setHtml(true)
    ->setSMTPModel(
        EmailSMTPModel::make()
            ->setServer('')
            ->setLogin('')
            ->setPassword('')
            ->setPort(465)
            ->setSecure('ssl'),
    );

// ❗❗ Все верно, но не отправляет? ❗❗
// - нужно написать боту.
TelegramModel::make()
    ->setUse(true)
    ->setParseMode('html')
    ->setToken('')
    ->setChatID(0);

// Данные на фронт идут через запрос и кешируются там.
// Если тут что-то изменили, то в консоли браузера введите VGMailerCache.clear()
CaptchaModel::make()
    ->setUse(false)
    ->setModels([
        // ❗❗ При включенном дебаге будет включаться дебаг капчи. ❗❗
        // https://yandex.cloud/ru/services/smartcaptcha
        YandexSmartCaptchaModel::make()
            ->setUse(false)
            ->setClientKey('')
            ->setServerKey(''),
        CloudflareCaptchaModel::make()
            ->setUse(false)
            ->setClientKey('1x00000000000000000000AA')
            ->setServerKey('1x0000000000000000000000000000000AA'),
    ]);

// Если WP заполняется автоматически. 
// Этот set идет самым приоритетным, что тут заполнить, такое подключение и будет.
DBModel::make()
    ->setUse(false)
    ->setHost('')
    ->setPort(0)
    ->setDbName('')
    ->setUserName('')
    ->setPassword('');