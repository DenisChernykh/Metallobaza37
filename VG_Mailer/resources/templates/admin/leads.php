<?php
/** @var string $modulePath */

/** @var string $head - /admin/blocks/head.php */

/** @var string $leftSection - /admin/blocks/leftSection.php */

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
                    <h1 class="block__title">Заявки с форм на сайте</h1>
                    <div class="buttons">
                        <button class="button js-filter-reset button--outline">
                            <svg fill="none" height="16rem" viewBox="0 0 16 16" width="16rem"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.425537 15.5L15.4255 0.5M15.5748 15.5L0.57478 0.5"
                                      stroke="currentColor"/>
                            </svg>
                            <span>Сбросить фильтры</span>
                        </button>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table" id="invoiceTable">
                        <thead>
                        <tr>
                            <th>
                                <div class="table__sort createdate text-nowrap"
                                     data-datepicker-type="range">
                                    <span>Дата заявки</span>
                                    <button aria-label="Выбрать дату" class="button-icon"
                                            type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort dropdown">
                                    <span>Номер</span>
                                    <button aria-label="Показать фильтры" class="button-icon"
                                            data-dropdown-block="phone" type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                    <div class="dropdown__block" data-dropdown-id="phone">
                                        <div class="field">
                                            <input autocomplete autofocus id="phone" name="phone"
                                                   placeholder="Номер телефона" type="text"
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort dropdown">
                                    <span>Источник</span>
                                    <button aria-label="Показать фильтры" class="button-icon"
                                            data-dropdown-block="source" type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                    <div class="dropdown__block" data-dropdown-id="source">
                                        <div class="field">
                                            <input autocomplete autofocus id="source"
                                                   name="source" placeholder="Введите URL"
                                                   type="text" value="">
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort dropdown">
                                    <span>Название формы</span>
                                    <button aria-label="Показать фильтры" class="button-icon"
                                            data-dropdown-block="form_name"
                                            type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                    <div class="dropdown__block" data-dropdown-id="form_name">
                                        <div class="field">
                                            <input autocomplete autofocus id="form_name"
                                                   name="form_name" placeholder="Введите название"
                                                   type="text" value="">
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="table__sort dropdown">
                                    <span>Статус</span>
                                    <button aria-label="Показать фильтры" class="button-icon"
                                            data-dropdown-block="status" type="button">
                                        <span class="icon icon-filter"></span>
                                    </button>
                                    <div class="dropdown__block js-filter-status" data-dropdown-id="status">
                                        <div class="checkbox-group">
                                            <label class="checkbox">
                                                <input name="status[]" type="checkbox"
                                                       value="sent">
                                                <span>Отправлено</span>
                                            </label>
                                            <label class="checkbox">
                                                <input name="status[]" type="checkbox" value="error">
                                                <span>Ошибка</span>
                                            </label>
                                            <label class="checkbox">
                                                <input name="status[]" type="checkbox"
                                                       value="blocked">
                                                <span>Заблокировано</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="sticky-col">Детали заявки</th>
                        </tr>
                        </thead>
                        <tbody id="invoiceTableBody"></tbody>
                    </table>
                </div>
                <div class="block__bottom">
                    <nav aria-label="Навигация по страницам" class="pagination" id="invoiceTablePagination"
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

<div class="modal" data-modal="viewOrder">
    <div class="modal-window">
        <div class="modal-close"></div>
        <div class="modal-body">
            <div class="modal-field">
                <div class="title">Дата заявки:</div>
                <div class="value js-created-at-field"></div>
            </div>
            <div class="modal-field">
                <div class="title">Название формы:</div>
                <div class="value js-form_name-field"></div>
            </div>
            <div class="modal-block">
                <div class="modal-block__heading">Детали заявки:</div>
                <div class="js-detail-fields"></div>
            </div>
            <div class="modal-block">
                <div class="modal-block__heading">Дополнительная информация:</div>
                <div class="modal-field js-senders-workers-statuses">
                    <div class="title">Статус:</div>
                    <div class="value">
                        <div class="status orange">Telegram</div>
                        <div class="status green">Почта</div>
                    </div>
                </div>
            </div>
            <div class="modal-block js-senders-logs">
                <div class="modal-block__heading">Логи отправки</div>
                <div class="js-senders-logs"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="button button--black" data-button-repeat data-order-id="">
                Отправить повторно
            </button>
            <button class="button button--danger" data-button-block data-order-id="">
                Заблокировать
            </button>
        </div>
    </div>
</div>

<script src="<?= $modulePath ?>/resources/assets/js/admin/imask.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/datepicker.min.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/front.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/vgalerts.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/dropdown.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/modals.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/ajax-table.js"></script>
<script src="<?= $modulePath ?>/resources/assets/js/admin/leads-page.js"></script>

</body>
</html>