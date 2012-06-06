<?php
class __path {
	
	private static $cfg;
	
	function __construct() {
		self::$cfg = config::$path;
		return true;
	}
	
	
	function absolute() {
		return self::$cfg->absolute;
	}
	
	
	function ajax() {
		return self::$cfg->ajax;
	}
	
	
	function app() {
		return self::$cfg->app;
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
		self::$req = __req::component();
		$path = self::$cfg->app . '/Components/' . self::$req->component;
		return $path;
	}
	
	
	function legacy_includes() {
		return self::$cfg->root . '/includes/common/pages/';
	}
	
}

$__path = new __path();
?>