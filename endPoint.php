<?php

function deleteDir($path) {
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            deleteDir(realpath($path) . '/' . $file);
        }
        return rmdir($path);
    } else if (is_file($path) === true) {
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
}

//обработка изображений
function workWithImg($method, $files, $itemId, $formData) {	
	if ($method === 'POST') {
		//создаем директорию
		$path = 'base/_images/' . $itemId . '/';
		@mkdir($path, 0755, true);
		foreach ($files as $key => $file) {
			if ($file && $file["error"]== UPLOAD_ERR_OK) {
				$name = $path.$file["name"];
				move_uploaded_file($file["tmp_name"], $name);
				$newData[] = [$name];
			}	
		}
		if (isset($newData)) echo json_encode($newData);
		else echo '[]';
		return;	
	}
	if ($method === 'DELETE') {
		foreach ($formData as $key => $file) {
			$localFile = $file;
			while ((strlen($localFile)>0) and (strpos($localFile, 'base/_images/')!==0)) {
				$localFile = substr($localFile, 1);
			}
			if (strlen($localFile)>0) @unlink($localFile);
		}
		return;
	}
}

//логин
function login($method, $endPoint, $formData) {	
	$token = '';
	$login = '';
	$password = '';
	$token = '';
	$role = 'client';
	$_logins = file('base/_logins.txt');
	$logins = [];
	
	foreach ($_logins as $key => $value) {
		if (($value[0]!=='#') and (strlen($value)>5)) {
			$logins[] = explode(" ", $value);
		}
	}
	
	foreach ($formData as $key => $value) {
		if ($key==='token') $token = trim($value);
		if ($key==='login') $login = trim($value);
		if ($key==='password') $password = trim($value);
	}
	
	if ($token!=='') {
		foreach ($logins as $key => $value) {
			if (trim($value[3])===$token) $role = $value[0];
		}
	}
	
	if (($login!=='') and ($password!=='')) {
		foreach ($logins as $key => $value) {
			if (($value[1]===$login) and ($value[2]===$password)) $role = $value[0];
		}
	}
	
	return $role;
}

// Роутер
function route($method, $urlData, $formData, $endPoint, $files) {	

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
	
	if ($endPoint=='_logins') {
		$role = login($method, $endPoint, $formData);
		echo $role;
		return;
	}
	
    // Добавление нового элемента
    // POST /food
    if ($method === 'POST' && empty($urlData)) {
        updateVersion($endPoint);
        // Добавляем элемент в базу...
		$formData['id'] = time()-1665684000;
        $baseData[] = $formData;
        file_put_contents($baseFileName, json_encode($baseData));
        // Выводим ответ клиенту
        echo json_encode($formData);
        return;
    }
	
    $itemId = $urlData[0];
	
	if ($endPoint=='_images') {
		workWithImg($method, $files, $itemId, $formData);
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
		//удаляем изображения
		$path = 'base/_images/' . $itemId . '/';
		@deleteDir($path);
        // Выводим ответ клиенту
        echo json_encode(array(
            'method' => 'DELETE',
            'id' => $itemId
        ));
        return;
    }

    dropError('Bad Request');
}
