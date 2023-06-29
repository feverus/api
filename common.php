<?php

function closeFileAndUnlock($fileName) {
    global $fileEndPoint, $fileVersion, $VERSIONS_FILE_NAME;

    if ($fileName === $VERSIONS_FILE_NAME) {
        $file = $fileVersion;
    } else {
        $file = $fileEndPoint;
    }
    @flock($file, LOCK_UN);
    @fclose($file);
}

function filePutContents($fileName, $data) {
    global $fileEndPoint, $fileVersion, $VERSIONS_FILE_NAME;
    if ($fileName === $VERSIONS_FILE_NAME) {
        $file = $fileVersion;
    } else {
        $file = $fileEndPoint;
    }

    ftruncate($file, 0);
    rewind($file);
    fwrite($file, $data);
}

function fileGetContents($fileName) {
    global $fileEndPoint, $fileVersion, $VERSIONS_FILE_NAME;
    $tryCount = 0;

    if ($fileName === $VERSIONS_FILE_NAME) {
        $file = $fileVersion;
    } else {
        $file = $fileEndPoint;
    }

    $result = false;
    while (($result === false) && ($tryCount < 100)) {
        $result = fread($file, filesize($fileName) + 1);
        $tryCount++;
        usleep(100);
    }
    return $result;
}

function generateString($max = 10) {
    $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $inputLength = strlen($input);
    $output = '';
    for($i = 0; $i <= $max; $i++) {
        $output .= $input[mt_rand(0, $inputLength - 1)];
    }
    return $output;
}

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
    $baseData[] = $formData;
    filePutContents($baseFileName, json_encode($baseData));
}

//оборачивает не массив в массив или избавляется от stdClass object
function wrapVarToArray($var) {
    if (gettype($var)!=='array') {
        $var = [$var];
    } else {
        $var = json_decode(json_encode($var), true);
    }
    return $var;
}

function putItem($baseFileName, $baseData, $formData, $baseItem, $baseItemKey, $mode = 'full') {
    global $ZERO_TIME;

    // Проверяем версию данных для этого id
    if (!isset($formData["version"])) {
        dropError('version not defined');
    } else {
        if (!is_numeric($formData["version"])) {
            dropError('version must be number');
        }
        if ($formData["version"] <= $baseItem["value"]->version) {
            dropError('the version is outdated, possible resending data');
        }
        $baseItem["value"]->version = $formData["version"];
    }

    // Обновляем все поля элемента в базе...
    if ($mode==='full') {
        foreach ($baseItem["value"] as $key => $value) {
            if ($key!=='id') {
                $baseItem["value"]->$key = $formData[$key];
            }
        }
    } elseif ($mode==='inc') {
        // Добавляем отметку времени
        $formData['time'] = (int)(strval(time() - $ZERO_TIME));
        foreach ($formData as $key => $value) {
            if (($key!=='id') && ($key!=='version')) {
                $formData[$key] = wrapVarToArray($formData[$key]);
                if (isset($baseItem["value"]->$key)) {
                    $baseItem["value"]->$key = wrapVarToArray($baseItem["value"]->$key);
                    //объединяем старые и новые данные
                    $baseItem["value"]->$key = array_merge($baseItem["value"]->$key, $formData[$key]);
                } else {
                    $baseItem["value"]->$key = $formData[$key];
                }
            }
        }
    } else {
        closeFileAndUnlock('');
        closeFileAndUnlock('base/_versions.txt');
        exit;
    }


    // Сохраняем в файл
    $baseData[$baseItemKey] = $baseItem["value"];
    filePutContents($baseFileName, json_encode($baseData));

    // Выводим ответ клиенту
    echo json_encode($baseData[$baseItemKey]);
}

function deleteItem($baseFileName, $baseData, $baseItemKey, $itemId) {
    // Удаляем элемент из базы...
    array_splice($baseData, $baseItemKey, 1);
    filePutContents($baseFileName, json_encode($baseData));
    //удаляем изображения
    $path = 'base/_images/' . $itemId . '/';
    @deleteDir($path);
}