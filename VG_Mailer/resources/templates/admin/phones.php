<?php
/** @var string $modulePath */

/** @var string $head - /admin/blocks/head.php */

/** @var string $leftSection - /admin/blocks/leftSection.php */

/* @var string $services - /admin/blocks/phone-service-item.php */

?>

<!DOCTYPE html>
<html lang="ru-RU">
<?= $head ?>
<body>
<div class="wrapper">
    <?= $leftSection ?>
    <div class="rightside full">
        <div class="rightside__columns">
            <div class="block w-100">
                <div class="block__header">
                    <h1 class="block__title">Блокировка номеров</h1>
                    <div class="buttons">
                        <button class="button button--outline js-filter-reset">
                            <svg fill="none" height="16rem" viewBox="0 0 16 16"
                                 width="16rem"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.425537 15.5L15.4255 0.5M15.5748 15.5L0.57478 0.5"
                                      stroke="currentColor"/>
                            </svg>
                            <span>Сбросить фильтры</span>
                        </button>
                    </div>
                </div>
                <form class="block__actions">
                    <?php
                    if (!$services) : ?>
                        <p>Нет активных сервисов для отправки заявок</p>
                    <?php
                    else : ?>
                        <div class="form-item">
                            <label>Номер телефона</label>
                            <input required name="phone" placeholder="+7 495 *** **-**" id="phoneToBlock"
                                   type="text">
                        </div>
                        <div class="form-item h-100">
                            <label>Блокировка для приемщика</label>
                            <div class="checkbox-row" id="servicesToBlock">
                                <?= $services ?>
                            </div>
                        </div>
                        <button class="button button--black mt-md-auto" id="blockButton">
                            Добавить
                        </button>
                    <?php
                    endif; ?>
                </form>
                <div class="table-wrapper">
                    <table class="table" id="invoiceTable">
                        <thead>
                        <tr>
                            <th>
                                <div class="table__sort createdate text-nowrap"
                                     data-datepicker-type="range">
                                    <span>Дата создания</span>
                                    <button aria-label="Выбрать дату"
                                            class="button-icon" type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort dropdown">
                                    <span>Номер</span>
                                    <button aria-label="Показать фильтры"
                                            class="button-icon"
                                            data-dropdown-block="nomer"
                                            type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                    <div class="dropdown__block"
                                         data-dropdown-id="nomer">
                                        <div class="field">
                                            <input autocomplete autofocus
                                                   id="nomer" name="nomer"
                                                   placeholder="Номер телефона"
                                                   type="text"
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort">
                                    <span>Приемщики</span>
                                </div>
                            </th>
                            <th class="sticky-col">Действия</th>
                        </tr>
                        </thead>
                        <tbody id="invoiceTableBody">

                        </tbody>
                    </table>
                </div>
                <div class="block__bottom">
                    <nav aria-label="Навигация по страницам" class="pagination"
                         id="invoiceTablePagination"
                         role="navigation"></nav>
                    <div class="block__actions">
                        <div class="select-label me-md-auto">
                            <label for="limit">Показывать по:</label>
                            <select class="form-input" id="invoiceTableLimits">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= $modulePath ?>/resources/assets/js/admin/imask.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/datepicker.min.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/front.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/vgalerts.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/dropdown.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/ajax-table.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/phone-page.js"></script>

</body>

</html>