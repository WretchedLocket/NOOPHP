<?php

class __req {
	
	public static $url = array();
	public static $uri = array();
	public static $urd = array();
	private $query_string = array();
	
	function __construct() {
		
		$this->app_structure = array(
			'component',
			'content',
			'subcontent',
			'extendedcontent'
		);
		
		$this->build_request_structure();		
		return true;
	}
	
	function build_request_structure() {
		global $config, $app;
		
		if ( count(self::$url) == 0 ) :
		
			# create each 
			foreach ($this->app_structure as $str) :
				self::$url[$str]=false;
			endforeach;
			
			parse_str($_SERVER['QUERY_STRING']);
			
			if (isset($urd)) :
			
				$this->urd = $urd;
				$vars = $this->urd;
				$vars = explode('/',$vars);
				
				$count=0;
				for ($i=0; $i < count($vars); $i++):
					if ( isset($this->app_structure[$i]) ) :
						$str = $this->app_structure[$i];
						$this->request->$str = $vars[$i];
						self::$url[$str] = $vars[$i];
					endif;
					
					$this->uri[$vars[$i]] = $vars[$i];
					$mixed = explode(',',$vars[$i]);
					
					if ( isset($mixed[1]) ) :
						$this->uri[$mixed[0]] = $mixed[1];
					endif;
											
					$count++;
				endfor;
			endif;
			
		endif;
			
			
			$qs = explode('&',$_SERVER['QUERY_STRING']);
			foreach ($qs as $q) :
				$q = explode('=',$q);
				$var = $q[0];
				$val = isset($q[1])?$q[1]:'';
				
				$this->query_string[$var]=$val;
			endforeach;
		
	}
	
	
	function component() {
		return self::$url['component'];
	}
	
	
	function content() {
		return self::$url['content'];
	}
	
	
	function subcontent() {
		return self::$url['subcontent'];
	}
	
	
	function extendedcontent() {
		return self::$url['extendedcontent'];
	}
	
	
}

$__req = new __req();
?>