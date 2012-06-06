<?php
include_once( __path::app() . '/Profile/class.profile.php');
include_once( __path::app() . '/Cookies/class.cookie.php');
include_once( __path::app() . '/Session/class.session.php');

class app_extends extends API_app {	
	
	var $images;
	private $request;
	public $urd;
	public $uri;
	
	private static $template;
	private static $default_template = 'home';
	private static $page_meta        = array();
	private static $viewing_profile  = false;
	private static $header_called    = false;
	private static $footer_called    = false;
	
	## ##
	##
	## Initial default method that fires immediately onload
	##
	## ##	
		public function __construct() {
			
			
			config::$cookie->lifespan = 3600*24*365;
			
			if ( __req::component() == 'renew-session') :
				session::rebuild_session_variables();
			endif;
			
			
			if ( session::is_logged_in() && __req::component() == 'sign-in' ) {
				header('Location: ' . __url::dashboard());
			
			} else if ( !session::is_logged_in() && @__page::session_required() && !session::is_logging_in() ) {
				header('Location:' . __url::login() . "/referer," . $_SERVER['REQUEST_URI'] );
				exit();
			}
			
			
			# ####
			#
			# Legacy Login Method for sub-pages from Creative State
			#
			#### #
			if ( form::has_post() ) :
				form::validate();
				if ( isset(form::$posts->incl_email) && !session::is_registering() ) :
					session::try_login();
				
				elseif ( @session::is_registering() ) :
					session::create_account();
					
				endif;
			endif;
			
			#
			# because custom enterprise URLs would return a 404 (technically a view file does not exist for them)
			# we need to catch that problem before it presents itself
			
				# are we viewing an enterprise page or sub-page?
				# if we are, set the component to enterprise
				# the content() method will handle the rest
			
			
			self::$template = ( @self::is_home() ) ? self::$default_template : strtolower(__req::component());
			return true;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## determines if the user is at the Home Page
	##
	## ##	
		public function is_home() {
			
			$component    = __req::component();
			$home_template     = self::$default_template;
			
			$has_component = ( !empty($component) ) ? true : false;
			
			$is_home = (bool) (!$has_component || ($component == $home_template) );
			
			return $is_home;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## determines if the user is viewing an image
	##
	## ##	
		public function is_viewing_image() {
			$component = __req::component();
			$comp = (bool) ( $component == 'i' );
			return $comp;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## If the user viewing the home page, add "Home" link to the navigation
	##
	## ##
		public function home_link() {
			if ( !$this->is_home() ) :
				echo '<li><a href="' . __url::root() . '">home</a></li>';
			endif;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Includes the header when needed
	##
	## ##
	public function header($vars=array()) {
		
		
		if ( !self::$header_called ) {
			self::$header_called = true;
			self::$page_meta = array();
				
			if ( is_array($vars) ) {
				foreach ( $vars as $key=>$val ) {
					self::$page_meta[$key] = $val;
				}
			}
			
			##
			# Get the Meta Description
			if ( isset(self::$page_meta['meta_description']) && !empty(self::$page_meta['meta_description']) ) {
				config::$app->meta_description .= ' ' . self::$page_meta['meta_description'];
			}
			# Meta Description
			##
			
			##
			# Get the Meta Description
			if ( isset(self::$page_meta['meta_keywords']) && !empty(self::$page_meta['meta_keywords']) ) {
				config::$app->meta_keywords .= ', ' . self::$page_meta['meta_keywords'];
			}
			# Meta Description
			##
			
				
			##
			# Handle the page title
			if ( empty(self::$page_meta['page_title']) ) {
				
				if ( self::$template == 'error' ) {
					$page_title = "Page Not Found";
				
				} else {
					
					$content   = __req::content();
					$component = __req::component();
					
					$page_title  = $component . ' ' . $content;
					$page_title  = str_replace('-',' ', $page_title);
					$page_title  = ucwords($page_title);
				}
	
				self::$page_meta['page_title'] = $page_title;
	
			}
			
			config::$app->title = str_replace('-',' ', self::$page_meta['page_title']);
			#
			## Page Title
			
			cache::start();
			include( __path::modules() . '/header.php' );
		}
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Includes the header when needed
	##
	## ##
	public function footer() {
		if ( !self::$footer_called ) {
			self::$footer_called = true;
			include( __path::modules() . '/footer.php' );
			cache::close();
		}
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	public function section_content() {
		
		$section_file = __path::views() . '/section-headers/' . $this->template . '.php';
		$common_file  = __path::views() . '/section-headers/common.php';
		
		if ( is_file($section_file) ) :
			include_once( $section_file );
		else :
			if (  __req::component() != 'administrator' ) :
				include_once( $common_file );
			endif;
		endif;
		
	}
	
	
	
	public function css() {
		
		$css_file = __path::assets() . '/css/components/' . self::$template . '.css';
		
		if ( is_file($css_file) ) :
			echo '<link rel="stylesheet" href="' . __url::assets() . '/css/components/' . self::$template . '.css" type="text/css" />';
		endif;
		
	}
	
	
	
	public function js() {
		
		$js_file = __path::assets() . '/js/components/functions.' . __req::component() . '.js';
		
		if ( is_file($js_file) ) :
			echo '<script type="text/javascript" src="' . __url::assets() . '/js/components/functions.' . __req::component() . '.js?v=1.1" type="text/javascript"></script>';
		endif;
		
	}
		
	
	
	
	## ##
	##
	## Based the request, the $template has been created
	## If a file exists, in the view folder, then include it
	## If not, this is an invalid page request, so the error template
	##
	## ##
		public function the_content() {
			
			## the full path the view folder and template
			$component = __req::component();
			
			if ( empty($component)) {
				$component = 'content';
			}
			$dir  = __path::views() . '/' . $component;
			$cont = __req::content();
			
			$is_dir = is_dir($dir); 
			
			if ( !$is_dir ) :
				$dir = __path::views() . '/content';
			endif;
			
			$file = $dir . '/' . self::$template;
			
			## the full path the Functions file for this template
			$functions_file = __path::app() . '/Components/' . self::$template . '/functions.' . self::$template . '.php';
			
			## does the Functions file exist? 
			## if so, include it
			if ( is_file($functions_file) ) :
				include_once($functions_file);
			endif;
			
			## if the template exists, include it
			if (is_file( $file . '.php' )) :
				require ( $file . '.php' );
				
			## if not, show the error template
			elseif ( !$is_dir && is_file( __path::views() . '/error.php' ) ) :
				$this->template = 'error';
				require (__path::views() . '/error.php');
			endif;
		}
	## ##
	##
	## end
	##
	## ##
		
	
	
	
	## ##
	##
	## Based the request, the $template has been created
	## If a file exists, in the view folder, then include it
	## If not, this is an invalid page request, so the error template
	##
	## ##
		public function include_component_class() {
			
			
			## the full path the Functions file for this template
			$functions_file = __path::component() . '/functions.' . self::$template . '.php';
			
			## does the Functions file exist? 
			## if so, include it
			if ( is_file($functions_file) ) :
				include_once($functions_file);
			endif;
		}
	## ##
	##
	## end
	##
	## ##
}

#
# $app extensions
	include_once( __path::app() . '/Application/class.app.extension.php');
	include_once( __path::app() . '/Uploads/class.uploads.php');
	include_once( __path::app() . '/Email/class.email.php');
	include_once( __path::app() . '/Pagination/class.pagination.php');


?>