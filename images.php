<?php

//добавление
function addImage($files, $itemId) {
		if ($itemId==='noid') {dropError('id not specified');}

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
		if (isset($newData)) {echo json_encode($newData);}
		else {echo '[]';}
}

//удаление
function removeImage($formData) {
	foreach ($formData as $key => $file) {
		$localFile = $file;
		while ((strlen($localFile)>0) && (strpos($localFile, 'base/_images/')!==0)) {
			$localFile = substr($localFile, 1);
		}
		if (strlen($localFile)>0) {@unlink($localFile);}
	}
}

//обработка изображений
function workWithImg($method, $files, $itemId, $formData) {
	if ($method === 'POST') {
		addImage($files, $itemId);
	}
	elseif ($method === 'DELETE') {
		removeImage($formData);
	}
}
