<?php
include_once 'allowedRouters.php';
include_once '_versions.php';
include_once '_logins.php';
include_once 'images.php';
include_once 'common.php';
include_once 'archive.php';
include_once 'checkAccess.php';

// Роутер
function route($method, $urlData, $formData, $endPoint, $files) {
    global $allowedRouters;

    if ($endPoint=='_logins') {
		$role = login($method, $endPoint, $formData);
		echo $role;
		return;
	}

    if ($endPoint=='_images') {
		workWithImg($method, $files, empty($urlData) ? 'noid' : $urlData[0], $formData);
		return;
	}

    if ($method=='ARCHIVE') {
		archive($endPoint, $urlData);
		return;
	}

    $baseFileName = 'base/'.$endPoint.'.txt';
    $base = file_get_contents($baseFileName);
    $baseData = json_decode($base);

    if (empty($urlData)) {
        // Получение всей базы
        // GET /food/
        if ($method === 'GET') {
            checkAccess($endPoint, 'read');
            // Выводим ответ клиенту
            echo $base;
            return;
        }

        // Добавление нового элемента
        // POST /food
        if ($method === 'POST') {
            updateVersion($endPoint);
            addItem($baseFileName, $baseData, $formData);
            // Выводим ответ клиенту
            echo json_encode($formData);
        }
        return;
    }
	
    if (count($urlData) === 1) {
        
        $itemId = $urlData[0];
        
        //поиск в базе
        $baseItem = findById($baseData, $itemId);
        $baseItemKey = $baseItem["key"];
        //и сразу выдаем ошибку, если не найден
        if ($baseItemKey === 'error') {dropError('id not found');}
    
        // Получение информации о элементе
        // GET /food/{itemId}
        if ($method === 'GET') {
            // Выводим ответ клиенту
            echo json_encode($baseItem["value"]);
            return;
        }
    
        // Обновление всех данных элемента
        // PUT /food/{itemId}
        if ($method === 'PUT') {
            updateVersion($endPoint);
            putItem($baseFileName, $baseData, $formData, $baseItem, $baseItemKey);
            return;
        }
    
        // Удаление элемента
        // DELETE /food/{itemId}
        if ($method === 'DELETE') {
            updateVersion($endPoint);
            deleteItem($baseFileName, $baseData, $baseItemKey, $itemId);
            // Выводим ответ клиенту
            echo json_encode(array(
                'method' => 'DELETE',
                'id' => $itemId
            ));
            return;
        }
    }

    dropError('Bad Request');
}
