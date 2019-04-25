# Интеграция сервиса мониторинга цен конкурентов Priceva и CMS 1С-Битрикс 

## Описание

Данный программный продукт является сторонним модулем для CMS `1C-Bitrix`, разработанным специалистами компании `Priceva`. Его основной целью является загрузка данных о ценах из вашего аккаунта в сервисе `Priceva` на ваш сайт, функционирующий на CMS `1C-Bitrix`. Цены сохраняются в указанном вами в настройках модуля типе цен<sup>[про типы цен](#price_types)</sup>. Это может быть как базовый тип цен, так и любой другой, созданный вами или автоматически нашим модулем. Загрузка цен может производиться как по указанному вами расписанию, так и вручную.

*<a name="price_types">Про типы цен</a>: данный функционал поддерживается исключительно редакцией 1C-Bitrix «Бизнес», в связи с чем для пользователей 1C-Bitrix «Малый бизнес» автоматически включается облегченный функционал модуля, напрямую работающий с базовым, единственным типом цен.*

## Требования:

* php: 7.1
* php-json
* php-curl
* CMS: 1C-Bitrix версии не ниже 14.0, редакции:
  * Малый бизнес
  * Бизнес

## Органичения

На данный момент модуль умеет работать лишь с одной из ваших кампаний в аккаунте `Priceva`. Для обхода этого ограничения вы можете либо разработать подходящее вам решение самостоятельно, используя данный модуль как базу для него, либо обратиться к нам по адресу [support@priceva.com](mailto:support@priceva.com), для обсуждения индивидуального решения данной задачи.

## Известные проблемы

1. **Проблема:** При очередном синхронизации цен данные цены не обновляются на страницах товаров.

    **Решение:** Данная проблема вызвана тем, что Битрикс может некорректно работать с кешем страниц. Соответственно, если вы уверены, что была успешная синхронизация цен, но сами цены на страницах товаров не обновились, то вам стоит попробовать либо просто обновить кеш вручную, через админ. панель Битриса, либо попросить ваших разработчиков настроить вашу систему так, чтобы кеш страниц обновлялся после обновления цен.

## Установка модуля

Данный модуль поддерживает две редакции 1C-Bitrix: «Малый бизнес» и «Бизнес». Это обусловлено тем, что сам модуль работает непосредственно с функционалом модуля `sale`, который поддерживается только перечисленными выше двумя типами редакций CMS.

Также обратите внимание, что редакция «Малый бизнес» с версии Bitrix 12.0 не умеет работать с типами цен. Соответственно наш модуль, который поддерживает Bitrix не старее версии 14.0, так же не будет работать с типами цен, программно отключая данную функцию, и работая напрямую с базовой ценой.

### Установка из архива

1. Скайчате актуальный релиз [со страницы релизов](https://github.com/priceva/priceva-1c-bitrix/releases).

2. Подключитесь к своему хостингу и создайте в папке вашего сайта на одном уровне с папкой `bitrix` папку `local`, а в ней папку `modules`, если их еще не было. В последней папке создайте папку `priceva.connector`. Сохранять именно такое название обязательно. Само собой, что никто не запрещает вам воспользоваться для этого встроенным в 1C-Bitrix файловым менеджером.

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
    ````

4. **Внимание!** Так как модуль хранится в данном репозитории в кодировке `UTF-8`, вам будет необходимо конвертировать его в `win-1251` кодировку, если ваша версия `Bitrix` работает с ней. Для этого вы можете воспользоваться скриптом `/utils/converter.sh`. Скопируйте его из папки `utils` в корень папки модуля, после чего запустите в консоли вашего сервера, находясь в папке модуля:

    ````bash
    $ ./converter.sh
    ````
    
    Если у вас нет возможности использовать консоль вашего сервера, то вы можете попробовать скачать последний доступный релиз в кодировке `win-1251` [со страницы релизов](https://github.com/priceva/priceva-1c-bitrix/releases). Либо, если вам требуется самая последняя версия модуля, которая еще не попала в релизы, вы можете попробовать сделать это собственноручно, самостоятельно найдя другие инструменты для конвертации кодировки файлов модуля.
    
    В любом случае, не забудьте удалить папку `utils`, файл `converter.sh` и все другие инструменты перед началом использования модуля. Вместе с ней возможно удалить и другие лишние файлы и папки: `docs/`, `LICENSE`, `README.md`.

5. Зайдите в админ. панель 1C-Bitrix по адресу `http://ваш-сайт.домен/login/`, после чего откройте в меню слева `Администрирование / Marketplace / Установленные решения` и найдите в списке модулей `Priceva: Коннектор`.

    ![readme-1.png](docs/images/readme-1.png)

6. Щелкните на "гамбургере" слева от имени модуля и выберите `Установить`. Следуйте инструкциям инсталлятора.

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
* Установка новой версии модуля из скачанного архива, следуя [инструкции выше](#%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%B8%D0%B7-%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B0)

### Обновление из Маркетплейса

Обновление модуля в случае, когда сам модуль установлен через Маркетплейс, осуществляется средствами самой системы Bitrix. Вам необходимо только лишь согласиться на очередное обновление модуля, если функционал автоматического обновления не был отключен на вашем сайте, или же произвести поиск обновлений и их установку вручную.

Обратите внимание, что если в настройках вашего сайта отключена обпция `Главный модуль / Система обновлений / Загружать только стабильные обновления`, то при очередном ручном или автоматическом обновлении вы можете получить версию модуля в стадии beta, что может отрицательно сказаться на работоспособности вашего сайта.

## Описание настроек модуля

*Обратите внимание, что не все настройки будут доступны на редакции 1C-Bitrix «Малый бизнес».* 

Настройка агента, производящего синхронизацию по расписанию, производится на странице управления агентами вашего Bitrix по адресу `Администрирование / Настройка / Настройки продукта / Агенты`.

Просмотр уведомлений о работе модуля возможен в журнале вашей системы Bitrix по адресу `Администрирование / Настройка / Настройки продукта / Журнал событий`. На данный момент существует два типа событий: `PRICEVA_SYNC` и `PRICEVA_ERROR`.

| Название | Назначение |
|:----------------------------------------------------:|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
|  | Основные параметры |
| Api-ключ вашей кампании в аккаунте Priceva | Данный ключ служит одновременно для двух целей: сохранения приватности ваших данных и указания конкретной кампании в вашем аккаунте, данные из которой вы хотите видеть у вас на сайте. Узнайте, как получить API-ключ, [здесь](https://priceva.ru/knowledge/kak-poluchit-dostup-k-api/). |
| Режим отладки модуля | Включает запись отладочной информации, генерируемой модулем, в файл priceva.log в корне вашего сайта. |
|  | Работа с простыми товарами |
| Обрабатывать простые товары | Будем ли мы обрабатывать товары, являющиеся так называемыми "простыми". |
| Выбранный тип инфоблоков (для простых товаров) | Какой тип инфоблоков будет считаться типом, содержащим "простые" товары. |
| Обрабатывать инфоблоки (для простых товаров) | Вариант обработки выбранных ранее инфоблоков: обрабатывать все или обрабатывать только один из них |
| Выбранный инфоблок (для простых товаров) | Конкретный экземпляр инфоблока, если ранее мы выбрали, что будем обрабатывать только один из них. |
|  | Работа с торговыми предложениями |
| Обрабатывать торговые предложения | Будем ли мы обрабатывать товары, являющиеся так называемыми "торговыми предложениями". |
| Выбранный тип инфоблоков (для торг. предложений) | Какой тип инфоблоков будет считаться типом, содержащим "торговые предложения". |
| Обрабатывать инфоблоки (для торг. предложений) | Вариант обработки выбранных ранее инфоблоков: обрабатывать все или обрабатывать только один из них |
| Выбранный инфоблок (для торг. предложений) | Конкретный экземпляр инфоблока, если ранее мы выбрали, что будем обрабатывать только один из них. |
|  | Синхронизация |
| Ключ-поле синхронизации цен | Здесь вы можете выбрать, что конкретно будет использоваться в качестве поля, по которому будут синхронизироваться ваши товары между вашим аккаунтом в сервисе Priceva и вашим сайтом. |
| Что использовать в качестве аналога поля client_code | Какое из полей системы Bitrix будет использовано в качестве аналога клиентского поля из системы Priceva. На данный момент доступно два варианта: ID товара и его символьный код. |
| В синхронизации участвуют только активные товары | Если эта опция включена, то при переборе товаров как в Priceva, так и в Bitrix, будут игнорироваться товары, которые неактивны. Игнорирование будет происходить даже в том случае, если в одной из систем товар неактивен, а в другой - активен. |
| Первоисточник данных | Здесь вы можете выбрать, по какому множеству товаров будет идти их перебор. Например, вы можете выбрать в качестве источника Priceva, и тогда все товары, которые есть в 1C-Bitrix, но нет в вашем аккаунте Priceva, будут проигнорированы как в попытке установить для них цену, так и в отчетах об ошибках. |
| Количество загрузок за раз | Данный модуль может загружать информацию о товарах и их ценах из сервиса Priceva разными порциями. Это может быть как 10 товаров за раз, так и 1000 товаров. Данная настройка призвана контролировать данную цифру, что может быть важно, если при загрузке большого количества товаров за раз возникают какие-либо ошибки. Такое может происходить, к примеру, если вы используете недостаточно мощный сервер. **Внимание!** Данный параметр актуален только в случае, если первоисточником данных указан сервис Priceva, иначе данный параметр теряет смысл. Объяснение: алгоритм не может перебирать товары Bitrix и порционно запрашивать данные из сервиса Priceva, в этом случае поиск товара по артикулу или иному полю будет происходить неверно. |
|  | Работа с ценами |
| Тип цен, с которым ведется работа | Данный тип цен будет использоваться для загрузки в него данных из вашего аккаунта в сервисе Priceva. |
| Пересчитывать цену при ее установке | Данный параметр указывает, необходимо ли пересчитать цену при ее установке в соответствии с формулами и настройками, заданными вами на вашем сайте. Подробнее об этом вы можете узнать в документации 1C-Bitrix. |
| Валюта | Данная настройка отвечает за то, в какой валюте будут загружены цены из сервиса Priceva на ваш сайт. |