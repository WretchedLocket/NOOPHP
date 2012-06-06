<?php
class __url {
	
	private static $cfg = array();
	
	function __construct() {
		self::$cfg = config::$url;
		return true;
	}
	
	
	function absolute() {
		return self::$cfg->absolute;
	}
	
	
	function login() {
		return self::$cfg->login;
	}
	
	
	function assets() {
		return self::$cfg->assets;
	}
	
	
	function controls() {
		return self::$cfg->controls;
	}
	
	
	function modules() {
		return self::$cfg->modules;
	}
	
	
	function root() {
		return self::$cfg->root;
	}
	
	
	function views() {
		return self::$cfg->views;
	}
	
	
	function component() {
		$url = self::$cfg->root . '/' . __req::component();
		return $url;
	}
	
	
	function dashboard() {
		
		if ( isset($_SESSION['profile']) ) :
			$dash = '/account';
			$url = self::$cfg->root . '/' . $dash;
		else :
			$url = self::$cfg->root . '/sign-in';
		endif;
		
		return $url;
	}
	
	function ajax() {
		
		$url = self::$cfg->root . '/ajax';
		return $url;
	}
}

$__url = new __url;
?>