<?php

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
