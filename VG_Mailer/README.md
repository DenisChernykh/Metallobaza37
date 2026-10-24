# VG_Mailer

Библиотека для отправки форм на почту и телеграмм.

## Старт работы

1. ### скачиваем архив или делаем `git clone`
    - <u>если скачали готовый архив, то второй шаг пропускаем</u>
    - если это WP загружаем в тему `/wp-content/themes/theme-name/VG_Mailer`
    - если другая CMS или самопис, то можно в корень сайта загрузить
2. ### выполнить `composer install` что бы подгрузить библиотеки для работы модуля
3. ### подключаем `VG_Mailer/index.php`
    - если WP, то делаем это в `functions.php`
    - если другая CMS или какой-нибудь самопис, то там сами смотрите как, но скорее всего можно просто подключить
      в главном `index.php`
4. ### Подключение фронтенд скриптов
    - если WP и все правильно сделали, то этот шаг можно пропустить
    - если что-то другое, нужно сделать что-то подобное
    ```php
      $core = Core::make();
      $scriptPaths = $core->getFrontScripts();
      $integrationScripts = $core->getIntegrationScripts();
      
      foreach ($scriptPaths as $scriptPath) {
         // подключение скриптов для работы модуля
         echo '<script src="'.$scriptPath.'"></script>';
      }
      foreach ($integrationScripts as $scriptPath) {
         // подключение скриптов для работы интеграций, например капч.
         echo '<script src="'.$scriptPath.'"></script>';
      }
    ```  

## Полезные функции

1. ### как включить debug мод?

   Что бы включить debug мод, нужно единоразово добавить гет параметр в урл на любой странице сайта `?mailer-debug=1`
   и что бы выключить `?mailer-debug=0`. debug мод работает на куки, так что будет только у вас.

   _Пример_: http://localhost:8080/?mailer-debug=1
2. ### Какие формы были инициализированы скриптом?

   `vgMailer.showInitForms() // покажет в консоли все инициализованные формы`

## Моя первая форма

```html

<form data-vgform data-vgform-name="Форма обратной связи">

    <div>
        <input type="tel" name="phone" data-vgform-label="Телефон">
    </div>
    <div>
        <input type="text" name="name" data-vgform-label="ФИО">
    </div>

    <button type="submit">Отправить</button>

</form>
```

## Хуки

### Frontend

<u>Все хуки работают через `addEventListener`</u>

```javascript
const $example = document.querySelector('form#example');

// глобальный хук, который сработывает для всех форм при успешной отправки формы
document.addEventListener('vg-mailer:success', ({$form}) => {
  const id = $form.id;
});

// локальный хук для формы с id example при успешной отправки формы
document.addEventListener('vg-mailer:success:example', ({$form}) => {
  const id = $form.id;
});
$example.addEventListener('vg-mailer:success', ({$form}) => {
  const id = $form.id;
});

// глобальный хук, который сработывает для всех форм при ошибочной отправки формы
document.addEventListener('vg-mailer:error', ({$form}) => {
  const id = $form.id;
});

// локальный хук для формы с id example при ошибочной отправки формы
document.addEventListener('vg-mailer:error:example', ({$form}) => {
  const id = $form.id;
});
$example.addEventListener('vg-mailer:error', ({$form}) => {
  const id = $form.id;
});

// глобальный хук, который сработывает для всех форм перед отправкой формы
// во всех хуках before можно работать с полями и мутировать их
document.addEventListener('vg-mailer:before', ({$form, fields}) => {
  for (const field of fields) {
    // input[name]
    const key = field.movel.key;

    // если будет ошибка валидации, то форма не отправится
    switch (key) {
      case 'field-1':
        field.validate(/regExp/, 'Ошибка валидации');
        break;
      case 'field-2':
        field.setInputError(true, 'Всегда будет ошибка валидации');
        break;
      case 'field-3':
        // заменили data-vgform-label

        break;
      case 'phone-text':
        // было <input type="text" name="phone-text" data-vgform-label="Это type text" />
        // стало <input type="tel" name="phone" data-vgform-label="Теперь это type tel />
        // но только для бека, т.е. визуально и в HTML останется как было, но на бек улетят уже новые значения.
        field.model.name = 'Теперь это type tel';
        field.model.type = 'tel';
        field.model.key = 'phone'
        break;
    }
  }
});

// локальный хук для формы с id example при ошибочной отправки формы
document.addEventListener('vg-mailer:before:example', ({$form, fields}) => {
  const id = $form.id;
});
$example.addEventListener('vg-mailer:before', ({$form, fields}) => {
  const id = $form.id;
});

// глобальный хук, который сработывает для всех полей для своих валидаций
// key это input[name]
document.addEventListener('vg-mailer:validate', ({field, key}) => {
  // теперь любой input со значением test никогда не пройдет валидацию
  field.validate(/test/, 'Ага, ага, попался на регулярке');
});

// хук для всех полей с [name="field-1"] для своих валидаций
document.addEventListener('vg-mailer:validate:field-1', ({field}) => {
  field.validate(/test/, 'Ага, ага, попался на регулярке');
});
// хук для всех полей внутри формы
$example.addEventListener('vg-mailer:validate', ({field, key}) => {});
```

### Backend

1. `hooks/data-after-prepare.php` - в этот хук приходят все данные после валидации на бекенде.
   Можно использовать для интеграций
2. `hooks/fake-success.php` - хук для фейковой отправки формы. Возвращаешь true, форма никуда не отправляется,
   но на фронте «Успешная отправка формы»
3. `hooks/item-exception.php` - хук для удаления полей. Т.е. если сделать там возвращать `true` на `[name="field-1"]`,
   это поле не отправится ни в телегу, ни на почту
4. `hooks/item-validate.php` - хук для валидации полей на сторону бекенде и изменения значений.

## Документация по тегам

### Системные атрибуты

| # | Атрибут                                      | Описание                                                                                                                                                                        |
|---|----------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1 | `[data-vgform]`                              | Атрибут `<form>` для инициализации скрипта.                                                                                                                                     |
| 2 | `[data-vgform-name]`                         | Атрибут названия формы. Берет либо значение атрибута, либо `innerText`                                                                                                          |
| 3 | `[data-vgform-agree]`                        | Атрибут согласия на обработку ПДН. Если не задать `data-vgform-label` и/или `name`, то значения по умолчанию `[data-vgform-label="Согласие на обработку ПДН"]`,`[name="agree"]` |
| 3 | `[data-vgform-form-error]`                   | Атрибут добавляется к `<form>` если там есть ошибки валидации                                                                                                                   |
| 4 | `[data-vgform-captcha]`                      | Атрибут элемента DOM внутри формы при использовании капчи, т.е. капча будет загружена в этот блок, если не будет найден, то капча добавится в конец формы                       |
| 5 | `button[type="submit"], input[type="submit]` | Селектор кнопок отправки формы                                                                                                                                                  |

Примеры работы:

1. `<form data-vgform >` - теперь модуль знает о существовании формы
2. `<form data-vgform-name="Заказать звонок">` - мы задаем название формы, которое будет использоваться в рассылке
3. `<input type="checkbox" data-vgform-agree>` — форма не отправится, пока чекбокс не будет отмечен.

### Поля формы

| # | Атрибут                             | Описание                                                                        |
|---|-------------------------------------|---------------------------------------------------------------------------------|
| 1 | `[data-vgform-label]`               | Атрибут задающий название поля                                                  |
| 2 | `[data-vgform-radio]`               | Атрибут для родительского блока n вложенности `<input type="radio>`             |
| 3 | `[data-vgform-checkbox]`            | Атрибут для родительского блока n вложенности `<input type="checkbox>`          |
| 4 | `[data-vgform-exception]`           | Атрибут для исключения поля, т.е. оно никуда не отправится и валидации не будет | 
| 5 | `[data-vgform-empty]`               | Атрибут дающий право пропускать валидацию на наличие значения                   |
| 6 | `[data-vgform-disable-error-block]` | Атрибут исключающий добавление блока с текстом ошибки валидации                 |

Дополнительная информация:

1. Если `<input>` не проходит валидацию, то после него добавляется
   `<div class="error-text">Текст ошибки валидации</div>`,
   если сделать `<input data-vgform-disable-error-block>`, то блок добавляться не будет

Примеры работы:

1.
    1. `<input name="phone" data-vgform-label="Телефон" />` - теперь в телегу/почту вместо `phone` будет `Телефон`.
    2. `<input data-vgform-label="s:.phone-label">` или `<input data-vgform-label="selector:.phone-label">`
       будет искать внутри формы `.phone-label` и брать `innerText`
2. Что бы не пописывать к каждому `radio` `data-vgform-label` можно это сделать один раз
    ```html
   <div data-vgform-radio data-vgform-label="Услуги">
        <!-- уровень вложенности не важен -->
        <div>
            <input type="radio" name="services" value="Услуга 1" />
        </div>
        <input type="radio" name="services" value="Услуга 2" />
        <input type="radio" name="services" value="Услуга 3" />
    </div> 
   ```
3. Точно так же как и для `radio`, просто заменить `radio` на `checkbox`

### Отправка формы

| # | Класс                       | Описание                                                                                                                              |
|---|-----------------------------|---------------------------------------------------------------------------------------------------------------------------------------|
| 1 | `[data-vgform-ok]`          | Атрибут внутри формы, которому добавится класс `.active` при успешной отправке формы                                                  |
| 2 | `[data-vgform-error]`       | Атрибут внутри формы, которому добавится класс `.active` при провальной отправке формы                                                |
| 3 | `[data-vgform-alert]`       | Атрибут `<form>` для изменения сообщения об успешной отправки формы                                                                   |
| 4 | `[data-vgform-alert-error]` | Атрибут `<form>` для изменения сообщения об провальной отправки формы                                                                 |
| 5 | `.preloader`                | Класс прелоадера. Во время отправки формы внутри формы будет добавляться `<div class".preloader">` и удаляться после получения ответа |
| 6 | `.loading`                  | Класс для формы во время отправки (`<form class="loading">`)                                                                          |                                                                        

Дополнительный функционал:

1. Если сделать `<div data-vgform-ok="replace">` то после успешной отправки формы `<form>` заменится на этот `<div>`
   и добавится класс `.active`
2. `VGAlert` сработает только в том случае:
    1. если форма успешно отправлена и внутри формы нет атрибута `[data-vgform-ok]`
    2. если форма отправилась с ошибками и внутри формы нет атрибута `[data-vgform-error]`
3. `<form [data-vgform-alert] />` сработает, если внутри формы нет `[data-vgform-ok]`
4. `<form [data-vgform-alert-error] />` сработает, если внутри формы нет `[data-vgform-error]`

### Классы

| # | Класс         | Описание                                                                                                                                      |
|---|---------------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| 1 | `.active`     | Этот класс добавляется:  <br/>1. `[data-vgform-error]` при провальной отправке формы <br /> 2. `[data-vgform-ok]` при успешной отправке формы |
| 2 | `.error`      | Этот класс добавляется к `<input>` который не прошел валидацию                                                                                |
| 3 | `.error-text` | Этот класс будет добавться к `<div>` при проваленной валидации поля                                                                           | 

### Текста

| # | Текст                        | Описание                                                        |
|---|------------------------------|-----------------------------------------------------------------|
| 1 | `Обязательное поле`          | Текст который будет в ошибке валидации, если поле будет пустое  |
| 2 | `Неправильно заполнен номер` | Текст который будет в ошибке валидации для `<input type="tel">` |

### Админ панель

Теперь под WP будут автоматический сохраняться формы в базе (не для WP тоже можно,
главное заполнить ``DB::model`` в ``config.php``)
Что бы попасть туда, нужно добавить гет параметр в урл ``?vgmailer``