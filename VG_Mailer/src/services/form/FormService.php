<?php

namespace VG_Mailer\src\services\form;

use VG_Mailer\src\Core;
use VG_Mailer\src\enums\FormStatusesEnum;
use VG_Mailer\src\enums\MessagesEnum;
use VG_Mailer\src\interfaces\SenderWorkerInterface;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\db\DBWorker;
use VG_Mailer\src\services\db\FormsWorker;
use VG_Mailer\src\services\db\PhoneBlackListWorker;
use VG_Mailer\src\services\db\PhonesToFormsWorker;
use VG_Mailer\src\services\db\PhoneWorker;
use VG_Mailer\src\services\sendersWorkers\Email;
use VG_Mailer\src\services\sendersWorkers\Telegram;

class FormService
{
    private FormsWorker $formsWorker;
    private PhoneBlackListWorker $phoneBlackListWorker;
    private PhonesToFormsWorker $phoneToFormsWorker;
    private PhoneWorker $phoneWorker;

    public function __construct()
    {
        $this->formsWorker = DBWorker::make(FormsWorker::class);
        $this->phoneBlackListWorker = DBWorker::make(PhoneBlackListWorker::class);
        $this->phoneToFormsWorker = DBWorker::make(PhonesToFormsWorker::class);
        $this->phoneWorker = DBWorker::make(PhoneWorker::class);
    }

    public function getFormValueByKey(string $key, array $formData): Response
    {
        $isValidStructure = $this->isValidDataStructure($formData);
        if ($isValidStructure->isError()) {
            return $isValidStructure;
        }

        foreach ($formData['data'] as $formDataItem) {
            $requiredKeys = ['key', 'value'];
            foreach ($requiredKeys as $requiredKey) {
                if (!isset($formDataItem[$requiredKey])) {
                    return Response::error(
                        MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR
                    );
                }
            }

            if ($formDataItem['key'] === $key) {
                return Response::success($formDataItem['value']);
            }
        }
        return Response::error('Ключ не найден');
    }

    public function isValidDataStructure(array $formData): Response
    {
        if (!isset($formData['data']) || !is_array($formData['data'])) {
            return Response::error(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
        }
        return Response::success();
    }

    public function deleteFormValueByKey(string $key, array &$formData): Response
    {
        $isValidStructure = $this->isValidDataStructure($formData);
        if ($isValidStructure->isError()) {
            return $isValidStructure;
        }

        foreach ($formData['data'] as $index => $formDataItem) {
            if (!isset($formDataItem['key'])) {
                return Response::error(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
            }
            if ($formDataItem['key'] === $key) {
                unset($formData['data'][$index]);
                break;
            }
        }

        return Response::success();
    }

    public function send(array $data): Response
    {
        $formData = $data['data'];
        $from = $data['from'];
        $formName = $data['name'];

        $isFakeByHook = vgMailerFakeSuccess($formData);
        vgMailerDataAfterPrepare($formData, $from, $formName);

        $exceptionSendersWorkers = [];
        $exceptionsSendersWorkersLogs = [];

        if (DBWorker::isCanUse()) {
            $phones = $this->getPhonesFromData($formData);
            $phoneExceptionRows = $this->phoneBlackListWorker->getByPhone($phones);

            foreach ($phoneExceptionRows as $phoneExceptionRow) {
                foreach ($phoneExceptionRow['services'] as $phoneService) {
                    if (!in_array($phoneService, $exceptionSendersWorkers)) {
                        $exceptionSendersWorkers[] = $phoneService;
                        $exceptionsSendersWorkersLogs[] = $phoneService;
                    }
                }
            }
        }

        $senderWorkers = [
            Email::class,
            Telegram::class,
        ];

        $isSuccess = false;
        $responses = [];
        foreach ($senderWorkers as $senderWorker) {
            /** @var SenderWorkerInterface $class */
            $class = new $senderWorker();
            $canSend = $class->canSend();
            $fakeSuccess = in_array($senderWorker, $exceptionSendersWorkers);

            if ($isFakeByHook) {
                $response = Response::error(MessagesEnum::FAKE_SEND_HOOK);
                $isSuccess = true;
                $status = FormStatusesEnum::BLOCKED;
            } elseif ($fakeSuccess) {
                $response = Response::error(MessagesEnum::FAKE_SEND);
                $isSuccess = true;
                $status = FormStatusesEnum::BLOCKED;
            } elseif ($canSend) {
                $response = $class->send($data);
                if ($response->isSuccess()) {
                    $isSuccess = true;
                    $status = FormStatusesEnum::SENT;
                } else {
                    $status = FormStatusesEnum::ERROR;
                }
            } else {
                $response = Response::error(MessagesEnum::SENDER_WORKER_METHOD_DISABLED);
                $status = FormStatusesEnum::ERROR;
            }

            $responses[$senderWorker] = [
                'status' => $status,
                'data' => $response->getData(),
            ];
        }

        $isCreated = $this->create($data, $responses);
        if (Core::make()->isDebug()) {
            $responses = [
                'debug' => [
                    'senderWorkers' => $responses,
                    'exception_phones' => $exceptionsSendersWorkersLogs,
                    'created' => $isCreated,

                    'data' => $data,
                ],
            ];
        } else {
            $responses = null;
        }

        return $isSuccess ? Response::success($responses) : Response::error(
            $responses
        );
    }

    protected function getPhonesFromData(array $data): array
    {
        $response = [];

        foreach ($data as $item) {
            if ($item['key'] === 'phone' || $item['type'] === 'tel') {
                $values = explode(',', $item['value']);
                $response = array_merge($response, $values);
            }
        }
        return array_unique($response);
    }

    private function create($data, $response): bool
    {
        if (!DBWorker::isCanUse()) {
            return false;
        }

        $existBlockedStatus = false;
        $existErrorStatus = false;
        $countDisabledStatuses = 0;

        foreach ($response as $responseItem) {
            switch ($responseItem['status']) {
                case FormStatusesEnum::BLOCKED:
                    $existBlockedStatus = true;
                    break;
                case FormStatusesEnum::ERROR:
                    if ($responseItem['data'] === MessagesEnum::SENDER_WORKER_METHOD_DISABLED) {
                        $countDisabledStatuses++;
                        break;
                    }
                    $existErrorStatus = true;
                    break;
            }
        }

        $formStatus = FormStatusesEnum::SENT;
        if ($existErrorStatus || $countDisabledStatuses === count($response)) {
            $formStatus = FormStatusesEnum::ERROR;
        } elseif ($existBlockedStatus) {
            $formStatus = FormStatusesEnum::BLOCKED;
        }

        $formId = $this->formsWorker->create([
            'source' => $data['from'],
            'form_name' => $data['name'],
            'form_fields' => $data['data'],
            'status' => $formStatus,
            'senders_responses' => $response,
        ])->getData()['id'];
        $phoneIds = $this->phoneWorker->createMany($data);
        foreach ($phoneIds as $phoneId) {
            $this->phoneToFormsWorker->create([
                'form_id' => $formId,
                'phone_id' => $phoneId,
            ]);
        }

        return true;
    }

    public function prepareData($data, $files): Response
    {
        $prepareData = new FormServicePrepareData($data, $files);
        return $prepareData->run();
    }
}