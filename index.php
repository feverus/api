<?php
ini_set('memory_limit', '1000M');

$ZERO_TIME = 1665684000;
$VERSIONS_FILE_NAME = 'base/_versions.txt';

include_once 'dropError.php';
include_once 'endPoint.php';

//вывод отладочной информации
file_put_contents('log.txt',
    "_GET\r\n".implode($_GET).
    "\r\n_POST\r\n".implode($_POST).
	"\r\ninput\r\n".file_get_contents('php://input'));

// Получение данных из тела запроса
function getFormData() {
    preg_match('/\{.+\}/s', file_get_contents('php://input'), $json);
    
    if (!empty($json)) {
        return json_decode($json[0], true);
    }
    return '{}';
}

//проверяем, существует ли файл с базой
function testBase($fileName) {
    $fileName='base/'.$fileName.'.txt';
    if (!file_exists($fileName)) {
        $f = fopen($fileName, "w+");
    } else {
        $f = fopen($fileName, "r+");
    }
    //блокируем файл на время работы скрипта
    //flock($f, LOCK_EX | LOCK_NB);
    flock($f, LOCK_EX);
    return $f;
}


// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

//для Preflight request
if ($method=='OPTIONS') {
    header('HTTP/1.1 204 No Content');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
	header('Access-Control-Allow-Headers: x-requested-with, Content-Type, origin, authorization, accept, x-access-token');

    exit;
}

// Получаем данные из тела запроса
$formData = getFormData();

// Разбираем url
$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

// Определяем базу и url data
$endPoint = $urls[0];

if (!isset($allowedRouters[$endPoint])) {
    dropError('Bad Request');
}

$urlData = array_slice($urls, 1);

//архивация - перенос элемента из /base/{database_name} в /base/_archive/{database_name}/{year}/{month}/{day}.txt
if (!empty($urlData) && ($urlData[0]==='archive')) {
    $method = 'ARCHIVE';
    $urlData = array_slice($urlData, 1);
}

//инициализация файлов баз
if ($endPoint !== '_logins') {
    $fileEndPoint = testBase($endPoint);
    if ($endPoint !== '_versions') {
        $fileVersion = testBase('_versions');
    } else {
        $fileVersion = $fileEndPoint;
    }
}

// Подключаем файл-роутер и запускаем главную функцию
route($method, $urlData, $formData, $endPoint, $_FILES);

closeFileAndUnlock('');
closeFileAndUnlock('base/_versions.txt');
