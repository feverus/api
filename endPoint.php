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
    global $allowedRouters, $ZERO_TIME;

    if ($endPoint=='_logins') {
		$loginData = login($formData);
        if ($loginData['role']==='client') {
            header('HTTP/1.0 401');
        }
            
        echo json_encode($loginData);
		return;
	}

    if ($endPoint=='_images') {
        checkAccess($endPoint, 'write');
		workWithImg($method, $files, empty($urlData) ? 'noid' : $urlData[0], $formData);
		return;
	}

    if ($method=='ARCHIVE') {
		archive($endPoint, $urlData);
		return;
	}

    $baseFileName = 'base/'.$endPoint.'.txt';
    $base = fileGetContents($baseFileName);
    $baseData = json_decode($base);

    if (empty($urlData)) {
        // Получение всей базы
        // GET /food
        if ($method === 'GET') {
            checkAccess($endPoint, 'read');
            // Выводим ответ клиенту
            echo $base;
            return;
        }

        // Добавление нового элемента
        // POST /food
        if ($method === 'POST') {
            checkAccess($endPoint, 'write');
            updateVersion($endPoint);

            $formData['version'] = 0;

            $formData['id'] = strval(time() - $ZERO_TIME) . generateString();

            $formData = array_reverse($formData);

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
            checkAccess($endPoint, 'readOneItem');
            // Выводим ответ клиенту
            echo json_encode($baseItem["value"]);
            return;
        }
    
        // Обновление данных элемента
        // PUT /food/{itemId}
        if ($method === 'PUT') {
            checkAccess($endPoint, 'write');
            updateVersion($endPoint);

            $mode = ($allowedRouters[$endPoint]['write']===['all']) ?
            'inc' : 'full';

            putItem($baseFileName, $baseData, $formData, $baseItem, $baseItemKey, $mode);
            return;
        }
    
        // Удаление элемента
        // DELETE /food/{itemId}
        if ($method === 'DELETE') {
            checkAccess($endPoint, 'write');
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
