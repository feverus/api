<?php
include_once 'dropError.php';
include_once 'endPoint.php';

$allowedRouters = [
    '_versions',
    'food',
    'section'
];

// Получение данных из тела запроса
function getFormData() {
    return json_decode(file_get_contents('php://input'), true);
}
//проверяем, существует ли файл с базой
function testBase($fileName) {
    $fileName='base/'.$fileName.'.txt';
    if (!file_exists($fileName)) {
        file_put_contents($fileName, '');
    }
}


// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Получаем данные из тела запроса
$formData = getFormData();

// Разбираем url
$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

// Определяем базу и url data
$endPoint = $urls[0];

if (array_search($endPoint, $allowedRouters)===false) {
    dropError('Bad Request');
}
$urlData = array_slice($urls, 1);

//инициализация файлов баз
testBase($endPoint);
testBase('_versions');
// Подключаем файл-роутер и запускаем главную функцию
route($method, $urlData, $formData, $endPoint);
