<?php

/** @var string $name - Название формы */
/** @var string $items - Поля */
/** @var string $from - Откуда была отправлена форма */
?>

<?= !$name ?: sprintf("Форма: %s\n", $name) ?>
<?= !$items ?: $items ?>

<?= !$from ?: sprintf("\nФорма отправлена со страницы: %s", $from) ?>