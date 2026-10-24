<?php

namespace VG_Mailer\src\services;

use VG_Mailer\src\cms\Wordpress;
use VG_Mailer\src\Core;
use VG_Mailer\src\services\sendersWorkers\SenderWorkerLoader;
use VG_Mailer\src\Template;

class AdminService
{
    public function getTemplateVariables(string $pagePrefix): array
    {
        $core = Core::make();
        $modulePath = $core->getModulePath();

        $head = Template::make()->getTemplate('admin', 'blocks/head', [
            'modulePath' => $modulePath,
        ]);
        $leftSection = $this->getLeftSection($pagePrefix);

        return [
            'modulePath' => $modulePath,
            'head' => $head,
            'leftSection' => $leftSection,
        ];
    }

    public function getLeftSection(string $pagePrefix): string
    {
        $core = Core::make();
        $modulePath = $core->getModulePath();

        $pages = [
            'phones' => [
                'name' => 'Блокировка номеров',
                'href' => '?vgmailer',
            ],
            'leads' => [
                'name' => 'Заявки',
                'href' => '?vgmailer-leads',
            ],
        ];

        $menu = '';
        foreach ($pages as $key => $page) {
            $menu .= Template::make()->getTemplate('admin', 'blocks/menu-item', [
                'name' => $page['name'],
                'href' => $page['href'],
                'active' => $key == $pagePrefix ? 'active' : '',
            ]);
        }

        return Template::make()->getTemplate('admin', 'blocks/left-section', array_merge([
            'modulePath' => $modulePath,
            'menu' => $menu,
        ], $this->getUsername()));
    }

    public function getUsername(): array
    {
        $name = 'Admin';
        $direction = 'username';

        $wordpress = Wordpress::make();
        if ($wordpress->isWp()) {
            $user = wp_get_current_user();

            $name = $user->nickname ?? $name;
            $direction = $user->display_name ?? $direction;
        }

        return [
            'name' => $name,
            'direction' => $direction,
        ];
    }

    public function getPhoneServiceItems(): string
    {
        $result = '';
        $senderWorkerLoader = SenderWorkerLoader::make();
        $activeSendersWorkers = $senderWorkerLoader->loadWorkers(true);

        foreach ($activeSendersWorkers as $senderWorker) {
            $class = new $senderWorker();
            $label = $class->getLabel();

            $result .= Template::make()->getTemplate('admin', 'blocks/phone-service-item', [
                'label' => $label,
                'class' => $senderWorker,
            ]);
        }

        return $result;
    }

}