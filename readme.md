# Перед использованием api
Нужно отредактировать в файле *allowedRouters.php* массив *$customRouters* - список баз данных, с которыми вы будете работать.

# Типы запросов:

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

content-type: application/json {}

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