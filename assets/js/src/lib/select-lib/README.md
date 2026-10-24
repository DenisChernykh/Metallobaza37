# VGSelect — кастомный select

## Описание

Легкая обертка над нативным `<select>`: скрывает исходный элемент и отрисовывает управляемый UI-слой.
Поддерживает одиночный и множественный выбор, плейсхолдер и поиск.
Скрипт сам инициализируется при загрузке, находя `select.vgselect` в DOM; также следит за динамическими вставками через `MutationObserver`.

## Разметка

Обычный одиночный select:

```html
<label>
  Город
  <select class="vgselect" name="city" placeholder="Выберите город">
    <option value="">Не выбрано</option>
    <option value="msk">Москва</option>
    <option value="spb">Санкт‑Петербург</option>
  </select>
</label>
```

С плейсхолдером в отдельном атрибуте и поиском:

```html
<select class="vgselect" name="city" data-placeholder="Город" data-search>
  <option value="msk">Москва</option>
  <option value="spb">Санкт‑Петербург</option>
  <option value="ekb" disabled>Екатеринбург</option>
</select>
```

Множественный выбор:

```html
<select class="vgselect" name="tags" multiple data-placeholder="Теги">
  <option value="one" selected>Один</option>
  <option value="two">Два</option>
  <option value="three">Три</option>
</select>
```

Принудительный рендер меню в `body` (удобно для перекрытия overflow контейнеров):

```html
<select class="vgselect" name="city" data-body="true"></select>
```

Поддерживаемые атрибуты:

- `placeholder` — текст плейсхолдера (для одиночного select)
- `data-placeholder` — альтернатива placeholder (показывается отдельным элементом)
- `data-search` — вкл. поле поиска в меню
- `data-body` — рендер меню в `body` и позиционирование относительно контрола
- `multiple`, `required`, `disabled` — нативные атрибуты `<select>` и `<option>`

## Инициализация

Скрипт — самовызываемый модуль. Достаточно подключить файл в бандл/на страницу.

- Автоинициализация: все `select.vgselect` будут обработаны на `DOMContentLoaded`.
- Динамика: за новыми селектами следит `MutationObserver`.

При необходимости ручной активации на уже существующей странице (после ленивой подгрузки скрипта) — добавьте класс `vgselect` к нужным `<select>`; модуль обработает их через наблюдатель.

## Стили

Готового SCSS в проекте нет — добавьте базовые стили у себя. Используемые классы:

- `.vgselect` — корневой контейнер (также копирует классы с нативного select)
- `.vgselect__control` — видимая кнопка/контрол
- `.vgselect__placeholder`, `.vgselect__value`, `.vgselect__values`, `.vgselect__nowrap`
- `.vgselect__menu` — выпадающее меню (позиционируется абсолютом)
- `.vgselect__search` — блок поиска, `.vgselect__input` — поле ввода
- `.vgselect__options` — контейнер опций, `.vgselect__option` — пункт
- Состояния: `.open` (на корне), `.error` (на корне), `.align-right` (у меню)

Минимальный пример стилей:

```scss
.vgselect {
  position: relative;
  display: inline-block;
  min-width: 200px;
}
.vgselect__control {
  min-height: 40px;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border: 1px solid #dcdfe6;
  border-radius: 6px;
  background: #fff;
  cursor: pointer;
}
.vgselect.error .vgselect__control {
  border-color: #e53935;
}
.vgselect__placeholder {
  color: #9aa1a9;
}
.vgselect__menu {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  min-width: 100%;
  max-height: 240px;
  overflow: auto;
  margin-top: 4px;
  padding: 8px;
  border: 1px solid #dcdfe6;
  border-radius: 6px;
  background: #fff;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}
.vgselect__menu.align-right {
  left: auto;
  right: 0;
}
.vgselect__search {
  padding-bottom: 6px;
}
.vgselect__input {
  width: 100%;
  padding: 6px 8px;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
}
.vgselect__options {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.vgselect__option {
  padding: 8px 10px;
  border-radius: 4px;
  cursor: pointer;
}
.vgselect__option[aria-disabled='true'],
.vgselect__option.disabled {
  color: #a0a4aa;
  cursor: not-allowed;
}
.vgselect__option:hover {
  background: #f5f7fa;
}
```

## Поведение

- Корневой `.vgselect` получает/теряет класс `.open` при открытии/закрытии меню.
- При `required`/ошибке валидации у нативного select — корню добавляется `.error`.
- Для множественного выбора значения в контроле объединяются через запятую.

## Примечания

- Нативный `<select>` остается в DOM (со значениями), так что формы отправляются корректно.
- Управление с клавиатуры: стрелки и Enter/Space для выбора (для выделения фокуса стилизуйте `.vgselect__option:focus`).
- Если меню должно выходить за пределы контейнера с `overflow`, используйте `data-body="true"` и, при необходимости, z-index.
