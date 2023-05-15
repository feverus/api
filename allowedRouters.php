<?php
/*
Массив прав доступа может быть:
1) пустым для запрета на прямой доступ (используется для закрытия служебных файлов)
2) содержать список пользователей: role из _logins.txt
3) содержать 'all' для открытого доступа всем:
    'write'=>['all'] меняет режим полного редактирования на инкременирующий
    (например, чтобы заказчик не мог очистить свой заказ.
     В таком режиме он будет добавлять);
    ['read'=>['all'] меняет способ генерации id у добавляемых элементов с _длинного случайного_ на короткий;
*/

//служебные роутеры для работы api
$defaultRouters = [
    '_images' =>    ['read'=>[], 'write'=>['admin']],
    '_versions' =>  ['read'=>['all'], 'write'=>[]],
    '_logins' =>    ['read'=>[], 'write'=>[]]
];

//список баз данных
$customRouters = [
    'food' => ['read'=>['all'], 'write'=>['admin']],
    'section' => ['read'=>['all'], 'write'=>['admin']],
	'tag' => ['read'=>['all'], 'write'=>['admin']],
    'order' => ['read'=>['admin', 'panel', 'manager', 'worker'], 'write'=>['all']]
];

$allowedRouters = array_merge($defaultRouters, $customRouters);
