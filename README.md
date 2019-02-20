# Интеграция сервиса мониторинга цен конкурентов Priceva и CMS 1С-Битрикс 

## Описание

Данный программный продукт является сторонним модулем для CMS `1C-Bitrix`, разработанным специалистами компании `Priceva`. Его основной целью является загрузка данных о ценах из вашего аккаунта в сервисе `Priceva` на ваш сайт, функционирующий на CMS `1C-Bitrix`. Цены сохраняются в указанном вами в настройках модуля типе цен. Это может быть как базовый тип цен, так и любой другой, созданный вами или автоматически нашим модулем. Загрузка цен может производиться как по указанному вами расписанию, так и вручную.

## Требования:

* php: 7.1
* php-json
* php-curl
* CMS: 1C-Bitrix (редакция «Бизнес»)

## Органичения

На данный момент модуль умеет работать лишь с одной из ваших кампаний в аккаунте `Priceva`. Для обхода этого ограничения вы можете либо разработать подходящее вам решение самостоятельно, используя данный модуль как базу для него, либо обратиться к нам по адресу [support@priceva.com](mailto:support@priceva.com), для обсуждения индивидуального решения данной задачи.

## Установка модуля

### Установка из архива

1. Скайчате актуальный релиз [со страницы релизов](https://github.com/priceva/priceva-1c-bitrix/releases).

2. Подключитесь к своему хостингу и создайте в папке вашего сайта на одном уровне с папкой `bitrix` папку `local`, а в ней папку `modules`, если их еще не было. В последней папке создайте папку `priceva.connector`. Сохранять именно такое название обязательно.

3. Перенесите в созданную папку файлы и папки из скачанного вами архива. В итоге у вас должна получиться вот такая структура директорий:

    ````
    ...
    ├── bitrix
    ...
    ├── index.php
    ├── local
    │   └── modules
    │       └── priceva.connector
    │           └── admin
    ...
    ```

4. **Внимание!** Так как модуль хранится в данном репозитории в кодировке `UTF-8`, вам будет необходимо конвертировать его в `win-1251` кодировку, если ваша версия `Bitrix` работает с ней. Для этого вы можете воспользоваться скриптом `/utils/converter.sh`. Скопируйте его из папки `utils` в корень папки модуля, после чего запустите в консоли вашего сервера, находясь в папке модуля:

    ````bash
    $ ./converter.sh
    ````
    
    Если у вас нет возможности использовать консоль вашего сервера, то вы можете попробовать скачать последний доступный релиз в кодировке `win-1251` [со страницы релизов](https://github.com/priceva/priceva-1c-bitrix/releases). Либо, если вам требуется самая последняя версия модуля, которая еще не попала в релизы, вы можете попробовать сделать это самостоятельно, самостоятельно найдя другие инструменты для конвертации кодировки файлов модуля.
    
    В любом случае, не забудьте удалить папку `utils`, файл `converter.sh` и все другие инструменты перед началом использования модуля. Вместе с ней возможно удалить и другие лишние файлы и папки: `docs/`, `LICENSE`, `README.md`.

5. Зайдите в админку Битрикс по адресу `http://ваш-сайт.домен/login/`, после чего откройте в меню слева `Администрирование / Marketplace / Установленные решения` и найдите в списке модулей `Priceva: Коннектор`.

    ![readme-1.png](docs/images/readme-1.png)

6. Щелкните на "гамбургере" слева от имени модуля и выберите `Установить`. Следуйте инструкциям инстяллатора.

    ![readme-2.png](docs/images/readme-2.png)
 
7. Получите api-ключ в сервисе Priceva, следуя [данной инструкции﻿](https://priceva.ru/knowledge/kak-poluchit-dostup-k-api/).
    
8. Настройте модуль на странице `Администрирование / Настройки / Настройки продукта / Настройка модулей / Priceva: Коннектор`.

### Установка из Маркетплейса

Перейдите на [страницу модуля](http://marketplace.1c-bitrix.ru/priceva.connector) в Marketplace 1C-Bitrix, после чего следуйте инструкциям на данной странице.

## Обновление модуля

### Обновление из архива

К сожалению, какого-то специального механизма для обновления модуля из архива, то есть в том случае, когда вы устанавливаете модуль самостоятельно, а не через Маркетплейс, не существует. В связи с этим обновление модуля представляет собой следующие последовательные шаги:

* Удаление модуля при помощи стандартных средств Bitrix
* Удаление папок `bitrix/modules/priceva.connector` и `local/modules/priceva.connector`
* Установка новой версии модуля из скачанного архива

### Обновление из Маркетплейса

Обновление модуля в случае, когда сам модуль установлен через Маркетплейс, осуществляется средствами самой системы Bitrix. Вам необходимо только лишь согласиться на очередное обновление модуля, если функционал автоматического обновления не был отключен на вашем сайте.  

## Описание настроек модуля

|                       Название                       |                                                                                                                                                                                                                                                                                                                                                                         Назначение                                                                                                                                                                                                                                                                                                                                                                        |
|:----------------------------------------------------:|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| Api-ключ вашей кампании в аккаунте Priceva           | Данный ключ служит одновременно для двух целей: сохранения приватности ваших данных и указания конкретной кампании в вашем аккаунте, данные из которой вы хотите видеть у вас на сайте. Узнайте, как получить API-ключ, [здесь](https://priceva.ru/knowledge/kak-poluchit-dostup-k-api/).                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| Тип цен, с которым ведется работа                    | Данный тип цен будет использоваться для загрузки в него данных из вашего аккаунта в сервисе Priceva.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| Ключ-поле синхронизации цен                          | Здесь вы можете выбрать, что конкретно будет использоваться в качестве поля, по которому будут синхронизироваться ваши товары между вашим аккаунтом в сервисе Priceva и вашим сайтом.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| Что использовать в качестве аналога поля client_code | Какое из полей системы Bitrix будет использовано в качестве аналога клиентского поля из системы Priceva. На данный момент доступно два варианта: ID товара и его символьный код.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| В синхронизации участвуют только активные товары     | Если эта опция включена, то при переборе товаров как в Priceva, так и в Bitrix, будут игнорироваться товары, которые неактивны. Игнорирование будет происходить даже в том случае, если в одной из систем товар неактивен, а в другой - активен.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| Первоисточник данных                                 | Здесь вы можете выбрать, по какому множеству товаров будет идти их перебор. Например, вы можете выбрать в качестве источника Priceva, и тогда все товары, которые есть в 1C-Bitrix, но нет в вашем аккаунте Priceva, будут проигнорированы как в попытке установить для них цену, так и в отчетах об ошибках.                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| Количество загрузок за раз                           | Данный модуль может загружать информацию о товарах и их ценах из сервиса Priceva разными порциями. Это может быть как 10 товаров за раз, так и 1000 товаров. Данная настройка призвана контролировать данную цифру, что может быть важно, если при загрузке большого количества товаров за раз возникают какие-либо ошибки. Такое может происходить, к примеру, если вы используете недостаточно мощный сервер. **Внимание!** Данный параметр актуален только в случае, если первоисточником данных указан сервис Priceva, иначе данный параметр теряет смысл. Объяснение: алгоритм не может перебирать товары Bitrix и порционно запрашивать данные из сервиса Priceva, в этом случае поиск товара по артикулу или иному полю будет происходить неверно. |
| Пересчитывать цену при ее установке                  | Данный параметр указывает, необходимо ли пересчитать цену при ее установке в соответствии с формулами и настройками, заданными вами на вашем сайте. Подробнее об этом вы можете узнать в документации 1C-Bitrix.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| Валюта                                               | Данная настройка отвечает за то, в какой валюте будут загружены цены из сервиса Priceva на ваш сайт.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| Обрабатывать торговые предложения                    | По умолчанию модуль меняет цены только в самих товарах, не трогая торговые предложения. Данная опция включает установку цен в том числе в торговых предложениях на товары.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| Режим отладки модуля                                 | Включает запись отладочной информации, генерируемой модулем, в файл priceva.log в корне вашего сайта.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |