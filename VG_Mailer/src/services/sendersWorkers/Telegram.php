<?php

namespace VG_Mailer\src\services\sendersWorkers;

use CURLFile;
use VG_Mailer\src\interfaces\SenderWorkerInterface;
use VG_Mailer\src\models\TelegramModel;
use VG_Mailer\src\Response;
use VG_Mailer\src\Template;

class Telegram implements SenderWorkerInterface
{
    public function send(array $data): Response
    {
        $sendMessage = $this->sendMessage($data);
        $sendAttachment = $this->sendAttachment($data);

        $response = [
            'message' => $sendMessage->getData(),
            'attachment' => $sendAttachment->getData(),
        ];

        $messageStatus = $sendMessage->isSuccess() && ($response['message']['ok'] ?? null) === true;
        $attachmentStatus = $sendMessage->isSuccess() && ($response['message']['ok'] ?? null) === true;

        return $messageStatus && $attachmentStatus ? Response::success($response) : Response::error($response);
    }

    private function sendMessage(array $data): Response
    {
        $telegramModel = TelegramModel::make();
        $message = $this->buildMessage($data);

        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL => 'https://api.telegram.org/bot'.$telegramModel->getToken().'/sendMessage',
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => [
                    'chat_id' => $telegramModel->getChatId(),
                    'text' => $message,
                    'parse_mode' => $telegramModel->getParseMode()
                ],
            ]
        );
        $response = curl_exec($ch);
        curl_close($ch);

        return Response::success(json_decode($response, true));
    }

    public function buildMessage(array $data): string
    {
        $template = Template::make();
        $items = $template->getItems('telegram', $data['data']);

        return $template->getTemplate('telegram', 'wrapper', [
            'name' => $data['name'],
            'items' => $items,
            'from' => $data['from'],
        ]);
    }

    private function sendAttachment(array $data): Response
    {
        $telegramModel = TelegramModel::make();

        $files = $data['files'];
        $media = [
            'chat_id' => $telegramModel->getChatId(),
            'media' => []
        ];

        foreach ($files as $file) {
            for ($i = 0; $i < count($file['name']); $i++) {
                if ($file['name'][$i] && $file['tmp_name'][$i]) {
                    $media['media'][] = [
                        'type' => 'document',
                        'media' => 'attach://'.$file['name'][$i]
                    ];
                    $media[$file['name'][$i]] = new CURLFile(
                        $file['tmp_name'][$i],
                        $file['type'][$i],
                        $file['name'][$i]
                    );
                }
            }
        }

        if (!empty($media['media'])) {
            $media['media'] = json_encode($media['media']);

            $ch = curl_init('https://api.telegram.org/bot'.$telegramModel->getToken().'/sendMediaGroup');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $media);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $media_response = curl_exec($ch);
            curl_close($ch);

            return Response::success(json_decode($media_response, true));
        }

        return Response::success();
    }

    public function canSend(): bool
    {
        return TelegramModel::make()->isUse();
    }

    public function getLabel(): string
    {
        return 'Telegram';
    }
}