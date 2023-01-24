<?php

//служебные роутеры для работы api
$defaultRouters = [
    '_images' => ['root'],
    '_logins' => ['all']
];

//список баз данных
$customRouters = [
    'food' => ['root'],
    'section' => ['root'],
	'tag' => ['root'],
    'order' => ['all']
];

$allowedRouters = array_merge($defaultRouters, $customRouters);
