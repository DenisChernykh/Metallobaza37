<?php

use VG_Mailer\src\Response;

/**
 * $item можно тут полностью изменить
 * Если вернуть Response::error(string), то на фронтенде под полем будет ошибка с этим тестом
 *
 * @param  array{name: string, key: string, value: mixed}  $item
 * @return Response
 */
function vgMailerItemValidate(array &$item): Response
{
    switch ($item['key']) {
        case 'test':
            return Response::error('Тестовое название нельзя');
        case 'test1':
            if ($item['value'] == 'test') {
                return Response::error('Валидация по значению провалена');
            }
            break;
        case 'test2':
            $item['value'] = 'Изменили значение у поля';
            $item['key'] = 'new-item-key';
            $item['name'] = 'Новое название';
            break;
    }

    return Response::success();
}
