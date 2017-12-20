<?php

if (! function_exists('rand_name')) {
	function rand_name() {
		$prefix = chr(mt_rand(65, 90)).chr(mt_rand(65, 90)).chr(mt_rand(65, 90));
		$code = mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9);
		return $prefix.$code;
	}
}

if (! function_exists('rand_password')) {
	function rand_password() {
		$password = mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9);
		return $password;
	}
}

if (! function_exists('rand_code')) {
	function rand_code() {
		$code = mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9);
		return $code;
	}
}