<?php
include_once( path::app() . '/Profile/class.profile.php');
include_once( path::app() . '/Cookies/class.cookie.php');
include_once( path::app() . '/Session/class.session.php');

class app_extends extends API_app {	
	
	public $images;
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
			
			if ( request::component() == 'renew-session') :
				session::rebuild_session_variables();
			endif;
			
			
			if ( session::is_logged_in() && request::component() == 'sign-in' ) {
				header('Location: ' . url::dashboard());
			
			} else if ( !session::is_logged_in() && @page::session_required() && !session::is_logging_in() ) {
				header('Location:' . url::login() . "/referer," . trim(request::$urd, '/') );
				exit();
			}
			
			
			# ####
			#
			# Legacy Login Method for sub-pages from Creative State
			#
			#### #
			if ( form::has_post() ) :
				form::validate();
				if ( isset(form::$posts->incl_email) && !session::is_registering() && session::is_logging_in() ) :
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
			
			
			self::$template = ( @self::is_home() ) ? self::$default_template : strtolower(request::component());
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
		static public function is_home() {
			
			$component     = request::component();
			$home_template = self::$default_template;
			
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
			$component = request::component();
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
				echo '<li><a href="' . url::root() . '">home</a></li>';
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
	static public function header($vars=array()) {
		
		
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
					
					$content   = request::content();
					$component = request::component();
					
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
			include( path::modules() . '/header.php' );
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
	static public function footer() {
		if ( !self::$footer_called ) {
			self::$footer_called = true;
			include( path::modules() . '/footer.php' );
			cache::close();
		}
		return false;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	static public function section_content() {
		
		$section_file = path::views() . '/section-headers/' . $this->template . '.php';
		$common_file  = path::views() . '/section-headers/common.php';
		
		if ( is_file($section_file) ) :
			include_once( $section_file );
		else :
			if (  request::component() != 'administrator' ) :
				include_once( $common_file );
			endif;
		endif;
		
	}
	
	
	
	static public function css() {
		
		$css_file = path::assets() . '/css/components/' . self::$template . '.css';
		
		if ( is_file($css_file) ) :
			echo '<link rel="stylesheet" href="' . url::assets() . '/css/components/' . self::$template . '.css" type="text/css" />';
		endif;
		
	}
	
	
	
	static public function js() {
		
		$js_file = path::assets() . '/js/components/functions.' . request::component() . '.js';
		
		if ( is_file($js_file) ) :
			echo '<script type="text/javascript" src="' . url::assets() . '/js/components/functions.' . request::component() . '.js?v=1.1" type="text/javascript"></script>';
		endif;
		
	}
		
	
	
	
	## ##
	##
	## Based the request, the $template has been created
	## If a file exists, in the view folder, then include it
	## If not, this is an invalid page request, so the error template
	##
	## ##
		static public function the_content() {
			
			## the full path the view folder and template
			$component = request::component();
			
			if ( empty($component)) {
				$component = 'content';
			}
			$dir  = path::views() . '/' . $component;
			$cont = request::content();
			
			$is_dir = is_dir($dir); 
			
			if ( !$is_dir ) :
				$dir = path::views() . '/content';
			endif;
			
			$file = $dir . '/' . self::$template;
			
			## the full path the Functions file for this template
			$functions_file = path::app() . '/Components/' . self::$template . '/functions.' . self::$template . '.php';
			
			## does the Functions file exist? 
			## if so, include it
			if ( is_file($functions_file) ) :
				include_once($functions_file);
			endif;
			
			## if the template exists, include it
			if (is_file( $file . '.php' )) :
				require ( $file . '.php' );
				
			## if not, show the error template
			elseif ( !$is_dir && is_file( path::views() . '/error.php' ) ) :
				self::$template = 'error';
				require (path::views() . '/error.php');
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
			$functions_file = path::component() . '/functions.' . self::$template . '.php';
			
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
	include_once( path::app() . '/Application/class.app.extension.php');
	include_once( path::app() . '/Uploads/class.uploads.php');
	include_once( path::app() . '/Email/class.email.php');
	include_once( path::app() . '/Pagination/class.pagination.php');


?>