<?php

namespace VG_Mailer\src\api;

use Exception;
use VG_Mailer\src\enums\MessagesEnum;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\AdminService;
use VG_Mailer\src\services\db\DBWorker;
use VG_Mailer\src\services\db\PhoneBlackListWorker;
use VG_Mailer\src\services\db\PhonesToFormsWorker;
use VG_Mailer\src\services\db\PhoneWorker;
use VG_Mailer\src\Template;
use VG_Mailer\src\utils\ArrayUtils;

class PhoneRequest
{

    private PhoneWorker $phoneWorker;

    private PhoneBlackListWorker $phoneBlackList;

    private AdminService $adminService;
    private PhonesToFormsWorker $phoneToFormsWorker;

    public function __construct()
    {
        $this->phoneWorker = DBWorker::make(PhoneWorker::class);
        $this->phoneBlackList = DBWorker::make(PhoneBlackListWorker::class);
        $this->phoneToFormsWorker = DBWorker::make(PhonesToFormsWorker::class);

        $this->adminService = new AdminService();
    }

    public function page(): void
    {
        echo Template::make()->getTemplate('admin', 'phones', array_merge([
            'services' => $this->adminService->getPhoneServiceItems(),
        ], $this->adminService->getTemplateVariables('phones')));
        exit();
    }

    public function updateServicesByRowId($data): void
    {
        $id = (int)$data['id'] ?? null;
        $services = ArrayUtils::stripslashes(json_encode($data['services'] ?? []));

        if (!$id) {
            throw new Exception(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
        }

        $this->phoneBlackList->updateField($id, 'services', $services);
        Response::success()->json();
    }

    public function getPhones($data): void
    {
        $data = $this->phoneWorker->findAll($data);
        $data->json();
    }

    public function blockPhone($data): void
    {
        $this->phoneBlackList->create($data)->json();
    }

    public function blockPhonesByFormId($data): void
    {
        $formId = $data['formId'] ?? null;
        if (!$formId) {
            throw new Exception(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
        }
        $phones = $this->phoneToFormsWorker->findPhonesByFormId($formId);
        if (!$phones) {
            throw new Exception(MessagesEnum::ROW_NOT_FOUND);
        }

        foreach ($phones as $phone) {
            $this->phoneBlackList->create([
                'phone' => $phone['phone'],
            ], true);
        }

        Response::success()->json();
    }

    public function getBlockedPhones($data): void
    {
        $data = $this->phoneBlackList->findAll($data);
        $data->json();
    }

    public function deletePhone($data): void
    {
        $this->phoneBlackList->deleteById($data['id'] ?? -1);
        Response::success()->json();
    }
}