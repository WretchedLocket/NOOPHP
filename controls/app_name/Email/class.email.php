<?php

class email {
	
	private static $temaplte_dir;
	
	public function __construct() {
		global $__path;
		
		self::$temaplate_dir = __path::root() . '/templates/email';
		
		return true;
	}
	
}

$app->email = new email();

?>