<?php

/**
 * Если вернуть true, то поле будет пропущено и удалено.
 *
 * @param  array{name: string, key: string, value: mixed}  $item
 * @return bool
 */
function vgMailerItemException(array $item): bool
{
    switch ($item['key']) {
        case 'test3':
            return true;
    }

    return false;
}
