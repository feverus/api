<?php

function deleteDir($path) {
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            deleteDir(realpath($path) . '/' . $file);
        }
        return rmdir($path);
    } elseif (is_file($path) === true) {
        return unlink($path);
    }
    return false;
}

//поиск запрошенного элемента в базе
function findById($baseData, $itemId) {
    foreach($baseData as $key => $value) {
        if ($value->id == $itemId) {
            return ["key" => $key, "value" => $value];
        }
    }
    return ["key" => "error", "value" => "error"];
}

function addItem($baseFileName, $baseData, $formData) {
    // Добавляем элемент в базу...
    $formData['id'] = time()-1665684000;
    $baseData[] = $formData;
    file_put_contents($baseFileName, json_encode($baseData));
}

function putItem($baseFileName, $baseData, $formData, $baseItem, $baseItemKey) {
    // Обновляем все поля элемента в базе...
    foreach ($baseItem["value"] as $key => $value) {
        if ($key!=='id') {
            $baseItem["value"]->$key = $formData[$key];
        }
    }
    $baseData[$baseItemKey] = $baseItem["value"];
    file_put_contents($baseFileName, json_encode($baseData));
    // Выводим ответ клиенту
    echo json_encode($baseData[$baseItemKey]);
}

function deleteItem($baseFileName, $baseData, $baseItemKey, $itemId) {
    // Удаляем элемент из базы...
    array_splice($baseData, $baseItemKey, 1);
    file_put_contents($baseFileName, json_encode($baseData));
    //удаляем изображения
    $path = 'base/_images/' . $itemId . '/';
    @deleteDir($path);
}