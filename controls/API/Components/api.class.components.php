<?php
class component {
	
	function component() {
		return true;
	}
	
	function _include() {
		$content   = __req::content();
		$component = __req::component();
		
		$content = ( empty($content) ) ? 'default' : $content;
		include_once( __path::views() . '/' . $component . '/' . $content . '.php');
	}
	
	function _path() {
		return __path::views() . '/'.__req::component();
	}
	
}

$component = new component();
?>