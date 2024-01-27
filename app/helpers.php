<?php
if (!function_exists('isJson')) {
	function isJson($string) {
    	return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
	}
}
if (!function_exists('uuid')) {
	function uuid() {
		return App\Models\Uuid::generate();
	}
}
if (!function_exists('isuuid')) {
	function isuuid($uuid) {
		return App\Models\Uuid::isUuid($uuid);
	}
}
if (!function_exists('randcode')) {
	function randcode($min = 1, $max = 255) {
		$length = rand($min, $max);
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ';
		return substr(str_shuffle($chars),0,$length);
	}
}
if (!function_exists('boolentotext')) {
    function boolentotext($integer) {
        if ($integer == 1 && !empty($integer) && is_numeric($integer)) {
            return "True";
        }
        return "False";
    }
}
