# Небольшой бэкенд на php. 
Написан для отладки, не использует баз данных, только файловое хранение.
### Поддерживает
- основные crud операции
- авторизация учетными данными в файле _logins.txt - *устанавливается кука token*
- создание своих эндпоинтов - *файл allowedRouters.php*
- разграничение прав доступа, используйте 'all' для отладки
- хранение изображений (описание в разработке)
- "архивация" данных - *перенос элемента из базы в директорию /год/месяц/день.txt и возможность запроса данных в диапазоне дат*

*Пока сервис находится в разработке и содержит мои отладочные эндпоинты, в дальнейшем мусор будет убран.*
____

# Перед использованием api
Нужно отредактировать в файле *allowedRouters.php* массив *$customRouters* - список баз данных, с которыми вы будете работать. Описание синтаксиса в самом файле.

# Типы запросов:

## Авторизация

http://localhost/api/_logins

content-type: application/json {}

{"login":"имя пользователя", "password":"пароль"}

### Ответ:
{"token":"токен для дальнейшей работы","role":"роль пользователя (админ, модератор etc))", "time":"метка времени для синхронизации"}

В дальнейшем все запросы от пользователей (админ, модератор etc) должны включать заголовок (header)
Authorization: token

## Получение данных 

### Вся база
**метод GET**

http://localhost/api/{database_name}

### Один элемент
**метод GET**

http://localhost/api/{database_name}/{id}

## Отправка данных 

### Добавить элемент
**метод POST**

http://localhost/api/{database_name}

content-type: application/json {}

### Редактирование элемента
**метод PUT**

http://localhost/api/{database_name}/{id}

content-type: application/json {"version": "100"}

Необходимо передавать версию в виде числа, большую, чем сохраненная в базе (для идемпотентности метода PUT).

Работает в двух режимах в зависимости от прав доступа в allowedRouters.php:
- **'write'=>['all']** добавляет данные к уже имеющимся, при необходимости преобразуя в массив из старых и новых. Сделано для защиты от удаления данных клиентом с помощью атаки произвольным запросом. Также добавляется параметр time с меткой времени.
- перезаписывает старые данные переданными в запросе

## Удаление данных
**метод DELETE**

http://localhost/api/{database_name}/{id}

## Работа с изображениями
Используем роутер *_images*

### Добавить элементы
**метод POST**

Отправляем массив изображений, получаем в ответ список урлов для дальнейшего использования.

### Удалить элементы
**метод DELETE**

Удаляет список изобрадений в */base/_images/*

## Архивация
### Переместить элемент в архив
**метод GET**

Перенос элемента из /base/{database_name} в /base/_archive/{database_name}/{year}/{month}/{day}.txt

http://localhost/api/{database_name}/archive/{id}

### Просмотр архива
**метод GET**

Вывод списка всех элементов в временном промежутке. Даты указывать в формате 2022_01_01/2022_12_31

http://localhost/api/{database_name}/archive/{startYear_month_day}/{endYear_month_day}