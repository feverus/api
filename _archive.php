<?php

function move($endPoint, $itemId) {
    //удаление из базы элемента
    $fileName = 'base/'.$endPoint.'.txt';
    $base = file_get_contents($fileName);
    $baseData = json_decode($base);
    $baseItem = findById($baseData, $itemId);
    $baseItemKey = $baseItem["key"];
    if ($baseItemKey === 'error') {dropError('id not found');}

    deleteItem($fileName, $baseData, $baseItemKey, $itemId);

    //добавление в архив $baseItem
    $path = 'base/_archive/' . $endPoint . '/' . date("Y") . '/' . date("n") . '/' ;
    @mkdir($path, 0755, true);
    
    $fileName = $path . date("j") . '.txt';
    testBase('_archive/' . $endPoint . '/' . date("Y") . '/' . date("n") . '/' . date("j"));

    $base = file_get_contents($fileName);
    $baseData = json_decode($base);

    addItem($fileName, $baseData, json_decode(json_encode($baseItem["value"]), true));

    echo json_encode(array(
        'id' => $itemId
    ));
}

function archive($endPoint, $urlData) {
    if (count($urlData)===1) {
        //передан id, перемещаем его в архив
        move($endPoint, $urlData[0]);
    } elseif (count($urlData)===2) {
        //передан диапазон дат, собираем статистику
    } else {
        dropError('Bad Request');
    }
}