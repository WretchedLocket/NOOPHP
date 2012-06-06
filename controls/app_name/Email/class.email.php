<?php

class email {
	
	private static $template_dir;
	
	public function __construct() {		
		self::$template_dir = __path::root() . '/templates/email';		
		return true;
	}
	
}

$app->email = new email();

?>