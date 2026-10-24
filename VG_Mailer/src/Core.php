<?php

namespace VG_Mailer\src;

use Exception;
use VG_Mailer\src\cms\Wordpress;
use VG_Mailer\src\logs\enums\JsLogEnum;
use VG_Mailer\src\logs\JsLog;
use VG_Mailer\src\models\CaptchaModel;
use VG_Mailer\src\models\ConfigModel;
use VG_Mailer\src\models\DBModel;
use VG_Mailer\src\models\EmailModel;
use VG_Mailer\src\models\TelegramModel;
use VG_Mailer\src\services\db\DBWorker;
use VG_Mailer\src\services\sendersWorkers\Email;

class Core
{

    private static Core $model;

    private array $integration_scripts = [];

    public function init()
    {
        // Включаем debug
        if (isset($_GET['mailer-debug'])) {
            if ($_GET['mailer-debug'] === '0') {
                setcookie("vg-mailer-debug", "1", time() - 3600, "/");
            } else {
                setcookie("vg-mailer-debug", "1", time() + 3600 * 24, "/");
            }
        }
        $this->initSession();

        $wordpress = Wordpress::make();
        if ($wordpress->isWp()) {
            $wordpress->setDbConfig();
        }
        $this->logVersion();

        try {
            $this->checkEmailSettings();
            $this->checkTelegramSettings();
            $this->checkCaptchaSettings();
            $this->checkDbSettings();
            DBWorker::initConnection();
        } catch (Exception $e) {
            JsLog::addLog(
                JsLogEnum::ERROR,
                'Критическая ошибка, включите debug мод',
                true,
            );
        }

        require_once dirname(__FILE__, 2).'/router.php';

        if ($wordpress->isWp()) {
            $wordpress->initScripts();
        }
        JsLog::saveToSession();
    }

    public function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function make(): Core
    {
        if (!isset(self::$model)) {
            self::$model = new Core();
            self::$model->init();
        }

        return self::$model;
    }

    private function logVersion()
    {
        $configModel = ConfigModel::make();
        JsLog::addLog(
            JsLogEnum::INFO,
            'init v'.$configModel->getVersion(),
            true,
        );
    }

    /**
     * Проверяем все ли правильно заполнили для отправки писем на почту
     *
     * @return void
     * @throws Exception
     */
    private function checkEmailSettings(): void
    {
        $emailModel = EmailModel::make();

        if (!$emailModel->isUse()) {
            JsLog::addLog(JsLogEnum::INFO, 'Отправка форм на почту отключена');
        } else {
            JsLog::addLog(
                JsLogEnum::SUCCESS,
                'Отправка форм на почту включена',
            );

            $message = 'Установите PHPMailer для работы модуля (composer require phpmailer/phpmailer)';
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                if ($this->isDebug()) {
                    JsLog::addLog(JsLogEnum::ERROR, $message);
                    $emailModel->setUse(false);
                } else {
                    throw new Exception($message);
                }
            }

            $message = 'Установите email настройки';
            if (!$emailModel->getToMail() || !$emailModel->getSubject()
                || !$emailModel->getFromMail()
            ) {
                if ($this->isDebug()) {
                    JsLog::addLog(JsLogEnum::ERROR, $message);
                } else {
                    throw new Exception($message);
                }
            }

            if (!Email::isValidSmtp()) {
                $message = 'SMTP настройки не установлены. Письма будут отправляться через php-mail';

                if ($this->isDebug()) {
                    JsLog::addLog(JsLogEnum::INFO, $message);
                } else {
                    throw new Exception($message);
                }
            }
        }
    }

    /**
     * Проверка на debug mode
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return isset($_COOKIE["vg-mailer-debug"]);
    }

    /**
     * Проверяем все ли правильно заполнили для отправки сообщений в телегу
     *
     * @return void
     * @throws Exception
     */
    private function checkTelegramSettings()
    {
        $telegramModel = TelegramModel::make();

        if (!$telegramModel->isUse()) {
            JsLog::addLog(
                JsLogEnum::INFO,
                'Отправка форм в телеграмм отключена',
            );
        } else {
            JsLog::addLog(
                JsLogEnum::SUCCESS,
                'Отправка форм в телеграмм включена',
            );

            if (!$telegramModel->getToken() || !$telegramModel->getChatId()) {
                $message = 'Установите Telegram настройки';
                if ($this->isDebug()) {
                    $telegramModel->setUse(false);
                    JsLog::addLog(JsLogEnum::ERROR, $message);
                } else {
                    throw new Exception($message);
                }
            }
        }
    }

    /**
     * Проверяем все ли правильно заполнили для работы капчи и возвращаем урлы
     * на скрипты
     *
     * @return void
     */
    private function checkCaptchaSettings()
    {
        $captchaModel = CaptchaModel::make();

        if (!$captchaModel->isUse()) {
            JsLog::addLog(JsLogEnum::INFO, 'Модуль «Капча» отключен');
        } else {
            JsLog::addLog(JsLogEnum::SUCCESS, 'Модуль «Капча» включен');

            $captchaModels = $captchaModel->getModels();
            if (empty($captchaModels)) {
                if ($this->isDebug()) {
                    $captchaModel->setUse(false);
                    JsLog::addLog(
                        JsLogEnum::ERROR,
                        'Нет ни одной модели капчи',
                    );
                }
            }

            foreach ($captchaModels as $model) {
                if (!$model->isUse()) {
                    continue;
                }

                $service = $model->getService();
                $src = $service->getScriptSrc($model);
                if ($src) {
                    JsLog::addLog(
                        JsLogEnum::SUCCESS,
                        sprintf('Капча %s включена', $model->getModelName()),
                    );
                    $this->integration_scripts[] = $src;
                }
            }
        }
    }

    /**
     * Проверяем все ли правильно заполнили для подключения к БД
     *
     * @return void
     * @throws Exception
     */
    private function checkDbSettings(): void
    {
        $dbModel = DBModel::make();
        if (!$dbModel->isUse()) {
            return;
        }

        $message = 'Установите настройки БД';
        if (!$dbModel->getPort() || !$dbModel->getHost()
            || !$dbModel->getDbName()
            || !$dbModel->getUsername()
            || !$dbModel->getPassword()
        ) {
            if ($this->isDebug()) {
                JsLog::addLog(JsLogEnum::ERROR, $message);
            } else {
                throw new Exception($message);
            }
        }
    }

    public function getIntegrationScripts(): array
    {
        return $this->integration_scripts;
    }

    /**
     * Получаем пути скриптов от модуля.
     *
     * @return array
     */
    public function getFrontScripts(): array
    {
        $modulePath = $this->getModulePath();
        $assetsPath = '/resources/assets/js/front/';
        $scanDirPath = dirname(__DIR__).$assetsPath;

        return array_map(function ($path) use ($assetsPath, $modulePath) {
            return $modulePath.$assetsPath.$path;
        }, array_diff(scandir($scanDirPath), ['.', '..']));
    }

    public function getModulePath($full = false): string
    {
        if ($full) {
            return dirname(__DIR__);
        }

        $dirname = str_replace('\\', '/', dirname(__DIR__));
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $dirname);
    }
}
