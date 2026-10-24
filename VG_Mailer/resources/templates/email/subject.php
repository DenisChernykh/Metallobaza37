<?php
/** @var string $name - Название формы */
/** @var string $subject - Тема из config.php */
?>
<?= !$name
	? sprintf('%s', $subject)
	: sprintf('Форма: %s', $name) ?>
