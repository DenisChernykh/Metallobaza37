<?php

namespace VG_Mailer\src\services\sendersWorkers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use VG_Mailer\src\cms\Wordpress;
use VG_Mailer\src\interfaces\SenderWorkerInterface;
use VG_Mailer\src\models\EmailModel;
use VG_Mailer\src\models\EmailSMTPModel;
use VG_Mailer\src\Response;
use VG_Mailer\src\Template;

class Email implements SenderWorkerInterface
{
    public function canSend(): bool
    {
        return EmailModel::make()->isUse();
    }

    public function getLabel(): string
    {
        return 'Почта';
    }

    public function send(array $data): Response
    {
        $emailModel = EmailModel::make();
        $wordpress = Wordpress::make();

        $subject = $this->buildSubject($data);
        $message = $this->buildMessage($data);
        $files = $data['files'];

        try {
            return Email::isValidSmtp() ? $this->sendFromVgMailer($subject, $message,
                $files) : $this->sendFromPhpMail($subject, $message, $files);
        } catch (Exception $e) {
            return Response::error('Что-то пошло не так при отправке формы '.$e->getMessage());
        }
    }

    public function buildSubject(array $data): string
    {
        $emailModel = EmailModel::make();
        return Template::make()->getTemplate('email', 'subject', [
            'subject' => $emailModel->getSubject(),
            'name' => $data['name'],
        ]);
    }

    public function buildMessage(array $data): string
    {
        $template = Template::make();

        // убираем products_order из items
        $filteredData = array_filter($data['data'], function ($item) {
            return $item['key'] !== 'products_order';
        });

        $items = $template->getItems('email', $filteredData);
        $products = $this->getProducts($data);

        return $template->getTemplate('email', 'wrapper', [
            'name'     => $data['name'],
            'items'    => $items,
            'from'     => $data['from'],
            'products' => $products,
        ]);
    }

    private function getProducts(array $data): array
    {
        foreach ($data['data'] as $item) {

            if ($item['key'] === 'products_order' && !empty($item['value'])) {
                // снимаем лишние экранирования
                $cleaned = stripslashes($item['value']);
                $decoded = json_decode($cleaned, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                if (!empty($decoded['items'])) {
                    return $decoded;
                }
            }
        }

        return [];
    }


    public static function isValidSmtp(): bool
    {
        $SMTPModel = EmailModel::make()->getSMTPModel();

        return $SMTPModel instanceof EmailSMTPModel
            && $SMTPModel->getServer()
            && $SMTPModel->getLogin()
            && $SMTPModel->getPassword()
            && $SMTPModel->getPort()
            && $SMTPModel->getSecure();
    }

    private function sendFromVgMailer($subject, $message, $files): Response
    {
        $mail = new PHPMailer(true);

        $emailModel = EmailModel::make();
        $SMTPModel = $emailModel->getSMTPModel();

        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();

        $mail->Host = $SMTPModel->getServer();
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';
        $mail->Username = $SMTPModel->getLogin();
        $mail->Password = $SMTPModel->getPassword();
        $mail->SMTPSecure = $SMTPModel->getSecure();
        $mail->Port = $SMTPModel->getPort();

        $mail->setFrom($emailModel->getFromMail(), $emailModel->getName() ?? '');
        $mail->addAddress($emailModel->getToMail());

        $mail->isHTML($emailModel->isHtml());
        $mail->Subject = $subject;
        $mail->Body = $message;

        foreach ($files as $file) {
            for ($i = 0; $i < count($file['name']); $i++) {
                if ($file['name'][$i] && $file['tmp_name'][$i]) {
                    $mail->addAttachment($file['tmp_name'][$i], $file['name'][$i]);
                }
            }
        }

        return Response::success($mail->send());
    }

    private function sendFromPhpMail($subject, $message, $files): Response
    {
        $emailModel = EmailModel::make();

        // Получатель и email отправителя
        $to = $emailModel->getToMail();
        $from = $emailModel->getFromMail();

        // Заголовки письма
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Тело письма (включая вложение)
        $body = $message.PHP_EOL;

        if (count($files) > 0) {
            $body .= "\r\n";
            $body .= "Прикреплено файлов - ";
            $body .= count($files)."\r\n";
            $body .= "Отправка файлов не поддерживается";
        }

        // Отправка письма
        $success = mail($to, $subject, $body, $headers);

        if ($success) {
            return Response::success($success);
        } else {
            return Response::error($success);
        }
    }
}