<?php

class Email_System {
	
	private $temaplte_dir;
	
	function __construct() {
		global $__path;
		
		$this->temaplate_dir = $__path->root() . '/templates/email';
		
		return true;
	}	
	
}

$app->email = new Email_System();

?>