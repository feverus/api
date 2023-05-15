<?php

function checkAccess($endPoint, $action = 'read') {
    global $allowedRouters;

    //Разрешам чтение одного элемента, если клиент знает id (кроме скрытых баз, например _logins)
    if (($action === 'readOneItem') && ($allowedRouters[$endPoint]!=='')) {return true;}

    //Если пользователь залогинен, у него должна быть кука. Для отладки откройте доступ для всех.
    $allheaders = getallheaders();
    
    $token = (isset($allheaders["Authorization"])) ? $allheaders["Authorization"] : '';

    $role = login(['token' => $token])['role'];

    $accessArray = $allowedRouters[$endPoint][$action];

    if ((!empty($accessArray)) && (($accessArray[0]==='all') || (array_search($role, $accessArray)!==false))) {
            return true;
    }
    
    dropError('Access denied');
    exit;
}
