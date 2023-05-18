<?php

//выдаем ошибку
function dropError($message) {
    // Возвращаем ошибку
    header('HTTP/1.0 400');
    echo json_encode(array(
        'error' => $message
    ));
    
    closeFileAndUnlock('');
    closeFileAndUnlock('base/_versions.txt');
    exit;
}
