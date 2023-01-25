<?php

function checkAccess($endPoint, $action = 'read') {
    global $allowedRouters;

    $token = (isset($_COOKIE["token"])) ? htmlspecialchars($_COOKIE["token"]) : '';

    $role = login(['token' => $token]);
    $accessArray = $allowedRouters[$endPoint][$action];

    if ((!empty($accessArray)) && (($accessArray[0]==='all') || (array_search($role, $accessArray)!==false))) {
            return true;
    }
    
    dropError('Access denied');
    exit;
}
