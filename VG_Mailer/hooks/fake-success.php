<?php

/**
 * Если вернуть true, то визуально форма отправится, но на самом деле никуда не отправится.
 *
 * @param  array{name: string, key: string, value: mixed}  $items
 * @return bool
 */
function vgMailerFakeSuccess(array $items): bool
{
    foreach ($items as $item) {
        if ($item['key'] === 'test-fake-success' || $item['value'] === 'test-fake-success') {
            return true;
        }
    }
    return false;
}