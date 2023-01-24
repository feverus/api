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

    $baseItem = json_decode(json_encode($baseItem["value"]), true);
    $baseItem["time"] = Date("U");

    addItem($fileName, $baseData, $baseItem);

    echo json_encode(array(
        'id' => $itemId
    ));
}

function showStat($endPoint, $start, $end) {
    echo '[';
    for ($year = $start[0]; $year <= $end[0]; $year++) {
        for ($month = $start[1]; $month <= $end[1]; $month++) {
            for ($day = $start[2]; $day <= $end[2]; $day++) {
                $fileName = 'base/_archive/' . $endPoint . '/' . $year . '/' . $month . '/' . $day . '.txt';
                if (file_exists($fileName)) {
                    $base = file_get_contents($fileName);
                    echo substr($base, 1, -1);
                }
            }
        }
    }
    echo ']';
}

function archive($endPoint, $urlData) {
    if (count($urlData)===1) {
        //передан id, перемещаем его в архив
        move($endPoint, $urlData[0]);
    } elseif (count($urlData)===2) {
        //передан диапазон дат, собираем статистику
        showStat($endPoint, explode('_', $urlData[0]), explode('_', $urlData[1]));
    } else {
        dropError('Bad Request');
    }
}