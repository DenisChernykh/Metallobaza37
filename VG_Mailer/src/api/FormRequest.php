<?php

namespace VG_Mailer\src\api;

use Exception;
use VG_Mailer\src\enums\MessagesEnum;
use VG_Mailer\src\services\AdminService;
use VG_Mailer\src\services\captcha\CaptchaService;
use VG_Mailer\src\services\db\DBWorker;
use VG_Mailer\src\services\db\FormsWorker;
use VG_Mailer\src\services\form\FormService;
use VG_Mailer\src\Template;

class FormRequest
{


    private CaptchaService $captchaService;

    private FormService $formService;
    private FormsWorker $formsWorker;
    private AdminService $adminService;

    public function __construct()
    {
        $this->captchaService = new CaptchaService();
        $this->formService = new FormService();
        $this->adminService = new AdminService();

        $this->formsWorker = DBWorker::make(FormsWorker::class);
    }

    public function request($data, $files): void
    {
        $captchaResponse = $this->captchaService->checkCaptcha($data);
        if ($captchaResponse->isError()) {
            $captchaResponse->json();
        }

        $preparedData = $this->formService->prepareData($data, $files);
        if ($preparedData->isError()) {
            $preparedData->json();
        }

        $this->formService->send($preparedData->getData())->json();
    }

    public function getForms($data): void
    {
        $this->formsWorker->findAll($data)->json();
    }

    public function page(): void
    {
        echo Template::make()->getTemplate(
            'admin',
            'leads',
            $this->adminService->getTemplateVariables('leads')
        );
        exit();
    }

    public function resend($data): void
    {
        $formId = $data['formId'] ?? null;
        if (!$formId) {
            throw new Exception(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
        }
        $form = $this->formsWorker->findById($formId);
        if (!$form) {
            throw new Exception(MessagesEnum::ROW_NOT_FOUND);
        }
        $this->formService->send([
            'data' => $form['form_fields'],
            'from' => $form['source'],
            'name' => $form['form_name'],
            'files' => []
        ])->json();
    }
}