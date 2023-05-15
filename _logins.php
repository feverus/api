<?php

//логин
function login($formData) {
	$token = '';
	$login = '';
	$password = '';
	$token = '';
	$role = 'client';
	$baseLogins = file('base/_logins.txt');
	$logins = [];
	
	foreach ($baseLogins as $key => $value) {
		if (($value[0]!=='#') && (strlen($value)>5)) {
			$logins[] = explode(" ", $value);
		}
	}

	if (!empty($formData)) {
		foreach ($formData as $key => $value) {
			switch ($key) {
				case 'token':
					$token = trim($value);
					break;
				case 'login':
					$login = trim($value);
					break;
				case 'password':
					$password = trim($value);
					break;
				default:
					break;
			}
		}
	}
	
	if ($token!=='') {
		foreach ($logins as $key => $value) {
			if (trim($value[3])===$token) {$role = $value[0];}
		}
	}
	
	if (($login!=='') && ($password!=='')) {
		foreach ($logins as $key => $value) {
			if (($value[1]===$login) && ($value[2]===$password)) {
				$role = $value[0];
				$token = trim($value[3]);
			}
		}
	}

	return ['token' => $token, 'role' => $role];
}
