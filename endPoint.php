<?php


//поиск запрошенного элемента в базе
function findById($baseData, $itemId) {
    foreach($baseData as $key => $value) {
        if ($value->id == $itemId) {
            return ["key" => $key, "value" => $value];
        }
    }
    return ["key" => "error", "value" => "error"];
}

//обновление версии базы
function updateVersion($name) {
    $versionsFileName = 'base/_versions.txt';
    $versions = json_decode(file_get_contents($versionsFileName));
    if ($versions===NULL) {
        $versions = json_decode(json_encode(array($name => 0)));
    }
    if (!property_exists($versions, $name)) {
        $versions->$name = 0;
    }
    $versions->$name++;
    file_put_contents($versionsFileName, json_encode($versions));
    exit;
}

// Роутер
function route($method, $urlData, $formData, $endPoint) {
    $baseFileName = 'base/'.$endPoint.'.txt';
    $base = file_get_contents($baseFileName);

    // Получение всей базы
    // GET /food/
    if ($method === 'GET' && empty($urlData)) {
        // Выводим ответ клиенту
        echo $base;
        return;
    }

    $baseData = json_decode($base);
    $itemId = $urlData[0];

    
    // Добавление нового элемента
    // POST /food
    if ($method === 'POST' && empty($urlData)) {
        updateVersion($endPoint);
        // Добавляем элемент в базу...
        $newData = array_merge(
            array('id' => time()-1665684000),
            $formData);
        $baseData[] = $newData;
        file_put_contents($baseFileName, json_encode($baseData));
        // Выводим ответ клиенту
        echo json_encode($newData);
        return;
    }

    //поиск в базе
    $baseItem = findById($baseData, $itemId);
    $baseItemKey = $baseItem["key"];
    //и сразу выдаем ошибку, если не найден
    if ($baseItemKey === 'error') {
        dropError('id not found');
    }

    // Получение информации о элемента
    // GET /food/{itemId}
    if ($method === 'GET' && count($urlData) === 1) {
        // Выводим ответ клиенту
        echo json_encode($baseItem["value"]);
        return;
    }

    // Обновление всех данных элемента
    // PUT /food/{itemId}
    if ($method === 'PUT' && count($urlData) === 1) {
        updateVersion($endPoint);
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
        return;
    }


    // Удаление элемента
    // DELETE /food/{itemId}
    if ($method === 'DELETE' && count($urlData) === 1) {
        updateVersion($endPoint);
        // Удаляем элемент из базы...
        array_splice($baseData, $baseItemKey, 1);
        file_put_contents($baseFileName, json_encode($baseData));
        // Выводим ответ клиенту
        echo json_encode(array(
            'method' => 'DELETE',
            'id' => $itemId
        ));
        return;
    }

    dropError('Bad Request');
}
