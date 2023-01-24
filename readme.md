# Перед использованием api
Нужно отредактировать в файле allowedRouters.php массив $customRouters - список баз данных, с которыми вы будете работать.

# Типы запросов:

## Получение данных 

### Вся база
**метод GET**
http://localhost/api/{database name}

### Один элемент
**метод GET**
http://localhost/api/{database name}/{id}

## Отправка данных 

### Добавить элемент
**метод POST**
http://localhost/api/{database name}
content-type: application/json {}

### Редактирование элемента
**метод PUT**
http://localhost/api/{database name}/{id}
content-type: application/json {}

## Удаление данных
**метод DELETE**
http://localhost/api/{database name}/{id}

## Работа с изображениями
Используем роутер *_images*

### Добавить элементы
**метод POST**
Отправляем массив изображений, получаем в ответ список урлов для дальнейшего использования.

### Удалить элементы
**метод DELETE**
Удаляет список изобрадений в */base/_images/*