<?php
/** @var string $modulePath */

/** @var string $menu */
/** @var string $name */
/** @var string $direction */

?>

<div class="leftpanel">
    <div class="userprofile">
        <div class="userprofile__info">
            <div class="name"><?= $name ?></div>
            <div class="direction"><?= $direction ?></div>
        </div>
        <a class="userprofile-logout" href="/">
            Выйти
        </a>
    </div>
    <div class="leftpanel__menu">
        <?= $menu ?>
    </div>
    <div class="leftpanel__bottom">
        <a class="leftpanel__logo" href="https://victoryagency.ru/digital"
           target="_blank">
            <img alt="VICTORY digital"
                 src="<?= $modulePath ?>/resources/assets/images/logo.svg">
        </a>
    </div>
</div>
