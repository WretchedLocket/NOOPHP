<?php
class AES_System extends app {
	function AES_System() { return true; }
	
	function decrypt($var) {
		global $config;
		return "AES_DECRYPT(BINARY(UNHEX({$var})),'{$config->aes_password}')";
	}
	
	function encrypt($var) {
		global $config;
		return "HEX(AES_ENCRYPT('{$var}','{$config->aes_password}'))";
	}
}
$app->aes = new AES_System;
?>