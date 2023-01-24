<?php

//служебные роутеры для работы api
$defaultRouters = [
    '_versions',
    '_images',
    '_logins'
];

//список баз данных
$customRouters = [
    'food',
    'section',
	'tag',
    'order'
];

$allowedRouters = array_merge($defaultRouters, $customRouters);
