<?php
class session {
	
	var $is_remembered = false;
	var $has_id        = false;
	var $request;
	var $profile;
	var $login_required       = false;
	var $verification_resent  = false;
	var $account_created      = false;
	
	private static $cookie;
	private static $page_request;
	private static $login_message;
	private static $registration_message = '';
	
	function _construct() {
		
		
		# ##
		#
		# only renew session and stuff if we're on the home page
		# if not, it destroys the Twitter session
		#
		# ##
		self::$cookie = config::$cookie;
		self::$cookie->lifespan = 3600*24*365;
		
		if ( isset($_SESSION['profile']->password) ) :
			unset($_SESSION['profile']->password);
		endif;
		
		page::get_details();
		
		if ( request::component() == 'sign-out' ) :
			self::logout();
		
		else :		
		
			if ( @page::session_required() ) :
			
				if ( !self::is_logging_in() && !self::is_logged_in() && !self::is_logging_out() && @self::$login_required ) :
					header("Location: " . self::login_page() . "/referer," . trim(request::$urd, '/') );
					exit();
				endif;
				
				if ( @$profile->is_logged_in() && @$profile->not_completed() && @$profile->not_viewing_profile() && $_SESSION['profile']->account_type != 'A' ) :
					header("Location: " . $profile->profile_page() );
					exit();
				endif;
				
				$sql = "SELECT id FROM accounts WHERE random_salt IS NULL";
				$sql = $db->query($sql);
				while ($row = $db->fetch_object($sql) ) :
					$random_salt = hash( "sha256", rand() . mktime() . rand() );
					$a = "UPDATE accounts SET random_salt = '$random_salt' WHERE id = $row->id";
					$a = $db->query($a);
					$db->error();
				endwhile;
			endif;
		
		endif;
		
		return true;
	}
	## ##
	##
	## end intialization
	##
	## ##
	
	
	
	static public function id() {
		$id = isset($_SESSION['profile']->id) ? $_SESSION['profile']->id : false;
		return $id;
	}
	
	
	
	
	## ##
	##
	## Login Methods
	##
	## ##	
	
		## is the user logged in?
		static public function is_logged_in() {
			
			$logged_in = (bool) ( isset($_COOKIE[config::$cookie->session]) || isset($_COOKIE[config::$cookie->remember]) );
			$logged_in = (bool) ( isset($_SESSION['profile']->id) && !empty($_SESSION['profile']->id) );
			return $logged_in;
		}
		
		## is the user logging in?
		static public function is_logging_in() {
			
			$component = request::component();
			$component = !empty($component) ? strtolower($component) : 'home';			
			$comp      = (bool) ( $component == 'login' || $component == 'sign-in' );			
			return $comp;
			
		}
		
		static public function login_page() { 
			return url::login();
		}
		
		#
		# Are they trying to register a new account		
		static public function is_registering() {
			return (bool) (request::component() == 'register');
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Logout Methods
	##
	## ##	
			
			## Is the user logging out? If so, log them out and redirect to home page
			static public function is_logging_out() {
			
				$component = request::component();
				$component = !empty($component) ? strtolower($component) : 'home';	
				$comp      = (bool) ( $component == 'logout' );
				return $comp;
			}
			
			
			## logout the user
			static public function logout() {
			
				header("Content-Type: text/html; charset=WINDOWS-1252");
				header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
				header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
				header( "Cache-Control: no-store, no-cache, must-revalidate" );
				header( "Cache-Control: post-check=0, pre-check=0", false );
				header( "Pragma: no-cache" );
				
				#
				# remove every cookie stored on the device for this site
				foreach ($_COOKIE as $key=>$val) :
					setcookie($key, '', time()-72000, "/", $cookie->domain()); 
				endforeach;
				#
				# unset every session variable stored
				foreach ($_SESSION as $key=>$val) :
					unset($_SESSION[$key]);
				endforeach;
					session_destroy();
				
				# return the user to the home screen, after logging out
				header('Location: ' . url::root() . '/sign-in');
				exit();
			}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## with each page refresh, renew the session to extend it's timeout
	## ##	
		static public function renew_session() {
			
			#
			# if the user is already logged in, renew their session to keep them logged in
			if ( @app::is_home() && isset($_COOKIE[self::$cookie->remember]) ) :
			
				# break down the cookie's value to get the user id and salt
				$raw_val = @self::$is_remembered ? $_COOKIE[self::$cookie->remember] : $_COOKIE[self::$cookie->session];
				$raw_val = explode('|',$raw_val);
				
				$_SESSION['user_id'] = $raw_val[0];
				
				# to prevent a person from simply changing their cookie value to a new account id
				# each cookie has a salt unique to that account record
				if ( isset($raw_val[1]) ) :
					$session->rs = $raw_val[1];
				
					self::rebuild_session_variables();
					cookie::set_session();
				endif;
				
			endif;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## with each page refresh, renew the session to extend it's timeout
	## ##
		static public function rebuild_session_variables() {
				
				if ( self::is_logged_in() ) :
					$sql = "SELECT
								a.id, a.email, a.password, a.first, a.last, a.company, a.website, a.address, a.city, a.state, a.zip,
								a.country, a.phone, a.referer, a.account_type, a.account_verified, a.account_status, a.credits, a.enterprise_url,
								a.enterprise_logo, a.is_enterprise, a.random_salt, a.is_locked, a.locked_time, a.activation_key, 
								c.coupon_id
							FROM
								accounts as a
								LEFT JOIN accounts_coupons_history as c ON c.account_id = a.id
							WHERE
								a.id = ".self::id()
								." ORDER BY c.redemption_date DESC LIMIT 0,1";
					$sql = db::query($sql);
					db::error();
					
					#
					# user was found, log them in
					if (db::num_rows() > 0) :
					
						$row = db::fetch_object($sql);
						
						#
						# the salt passed from the cookie is valid and matches record found
						if ( $_SESSION['profile']->rs == $row->random_salt ) :
							self::set_session_variables($row);
						
						#
						# invalid salt, log the user out
						else :
							header('Location: ' . config::root() . '/sign-out');
							exit();
							
						endif;
					
					#
					# should a user account be removed from the DB, but the user was never
					# logged out, we'll need to log them out now
					elseif ( (db::num_rows() <= 0 && @self::is_logged_in()) && !self::is_logging_out() ) :
						//$this->logout();
						//exit();
						
					endif;
				endif;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	static private function encode_password($pass) {
		$pass = md5( cPRE_SALT.$pass.cPOST_SALT );
		return $pass;
	}
	
	
	
	
	
	
	
	
	## ##
	##
	## If the user is attempting to login, validate them
	## if they are valid, log them in
	##
	## ##	
		static public function try_login() {
			
			$referer = '';
			
			$_SESSION['profile'] = (object) array();
			
			if ( form::has_post() && !form::has_error() ) :
				
				if ( isset(form::$posts->incl_email) ) :
					
					$email    = filter_var(form::$posts->incl_email, FILTER_SANITIZE_EMAIL);
					$password = form::$posts->incl_password;
				
				else :
					$email    = filter_var(form::$posts->_email, FILTER_SANITIZE_EMAIL);				
					$password = form::$posts->_pass;
					$referer  = form::$posts->_ref;
				
				endif;
				
				$password = md5( config::$salts->password . $password . config::$salts->password );
				
				$sql = "SELECT * FROM accounts WHERE email = '$email' AND password = '$password' LIMIT 0,1";
				
				$row  = db::to_array($sql);
				$cnt  = db::num_rows();
				
				$is_user       = false;
				$time_locked   = 0;
				$lock_interval = config::$app->account_lock_out_interval;
				
				if ($cnt > 0 ) :
					$is_user = true;
					
					$row->locked_time = 0;
					$row->is_locked   = 0;
					
					$is_verified = ($row->account_verified == 0) ? false : true;
					$status_good = ($row->account_status == 0)   ? false : true;
					
					if ( $row->locked_time > 0 ) :
						$time_locked = (time()-$row->locked_time)/60;
					endif;
					
					$is_locked = ($row->is_locked > 0 && $time_locked <= $lock_interval) ? true : false;
				
				if ( (@$is_user && !$is_verified) && !self::is_verifying() ) :
					if ( request::component() != 'ajax') :
						self::verify_account($row);
					endif;
					
					form::$error .= '<li>'._STRING_LOGIN_ERROR__NOT_VERIFIED_.'</li>';
					self::$login_message = 'error:::' . _STRING_LOGIN_ERROR__NOT_VERIFIED_;
					
				elseif ( (@$is_user && !$status_good) && !self::is_verifying() ) :
					self::$login_message = "error:::" . _STRING_LOGIN_ERROR__ACCOUNT_DISABLED_;
					
				elseif (  @$is_user && @$is_locked && $time_locked <= $lock_interval ) :				
					self::$login_message = "error:::" . _STRING_LOGIN_ERROR__LOCKED_OUT_;
				
				else :
					if ( !self::set_session_variables($row) ) :
						return false;
					endif;
					
					self::$login_message = 'location:::' . _STRING_LOGIN_SUCCESS_ . ':::' . url::root() . '/' . self::$sess->account_type_long . '/dashboard';
					
					if ( $referer == 'enterprise' ) :
						self::$login_message = 'success:::' . _STRING_LOGIN_SUCCESS_;
					endif;
					
					# ######
					#
					# is the user logging in using the traditional login form ###### #
					if ( isset(form::$posts->incl_sign_in) ) :
						#
						# User is:
						switch (form::$posts->incl_sign_in) :
							# verifying their account
							case 'verify-account' :
								self::verify_account($row);
								break;
							
							# just logging in
							default :
								header('Location: ' . url::root() . '/' . self::$sess->account_type_long . '/dashboard');
								exit();
								break;
						endswitch;
					endif;
					# ###### #
					return true;
					
				endif;
					
				else :
					form::$error .= '<li>'._STRING_LOGIN_ERROR__INVALID_.'</li>';
					self::$login_message = "error:::" . _STRING_LOGIN_ERROR__INVALID_;
					
				endif;
			endif;
			
			return false;
			
		}
	## ##
	##
	## end
	##
	## ##
	
	
	static public function echo_login_message() {
		if ( isset(self::$login_message) && !empty(self::$login_message) ) :
			echo str_replace('error:::','', self::$login_message);
		endif;
	}
	
	
	static public function is_verifying() {		
		return (bool) ( isset(form::$posts->incl_sign_in) && form::$posts->incl_sign_in == 'verify-account');			
	}
	
	static public function verify_account( $data ) {
		
		$content      = request::content();
		$url_has_code = (bool) !empty($content);
		$db_has_code  = (bool) !empty($data->activation_key);
		$has_content  = (bool) !empty($content);
		$codes_match  = (bool) (@$url_has_code && $content == $data->activation_key);
		
		if ( (@$db_has_code && $codes_match) || ($codes_match && $data->account_verified == 0) ) :
			self::set_session_variables($data);
			$sql = "UPDATE accounts SET activation_key = '', account_verified = '1', account_status = '1' WHERE id = $data->id";
			$sql = db::query($sql);
			db::error();
			header('Location: ' . url::root() . '/' . self::$sess->account_type_long . '.php/profile');
			exit();
			
		elseif ( !$url_has_code && $data->account_verified == 0 ) :
		
		elseif ( $data->account_verified == 1) :
			self::set_session_variables($data);
			header('Location: ' . url::root() . '/' . self::$sess->account_type_long . '.php/dashboard');
			exit();
		
		endif;
		
	}
	
	static public function resend_verification_email() {
		
		$email    = filter_var(form::$posts->incl_email, FILTER_SANITIZE_EMAIL);
		
		$sql = "SELECT first, last, email, activation_key, account_status, account_verified FROM accounts WHERE email = '$email'";
		$sql = db::query($sql);
		
		if ( db::num_rows() > 0 ) :
			
			$row = db::fetch_object($sql);
		
			$is_user = true;
			
			$row->locked_time = 0;
			$row->is_locked   = 0;
			
			$is_verified = ($row->account_verified == 0) ? false : true;
			$status_good = ($row->account_status == 0)   ? false : true;
			
			if ( @$is_user && !$status_good ) :
				form::$error =  _STRING_LOGIN_ERROR__ACCOUNT_DISABLED_;
			
			else :
			
				if ( empty($row->activation_key) ) :
					$row->activation_key = hash( "sha256", rand() . mktime() . rand() . rand() );
					$sql = "UPDATE accounts SET activation_key = '$row->activation_key' WHERE email = '$email'";
					$sql = db::query($sql);
				endif;
			
				app::$email->verify_account($row);
				self::$verification_resent = true;
				
			endif;
			
		else :
			form::$error .= '<li>Email address not found</li>';
		endif;
				
	}
	
	
	
	
	## ##
	##
	## If the user is attempting to login, validate them
	## if they are valid, log them in
	##
	## This is not used if the user is registering via Facebook
	## Facebook registration is handled in /OAuth/functions.facebook.php
	##
	## ##	
	static public function create_account() {
		
		if ( !empty(form::$posts->incl_registration_email) ) :
				
				$email        = filter_var(form::$posts->incl_registration_email, FILTER_SANITIZE_EMAIL);				
				$password     = form::$posts->incl_password;
				$conf_pass    = form::$posts->confirm_password;
				
				if ( $password != $conf_pass ) :
					form::$error .= '<li>Password not confirmed successfully</li>';
				endif;
			
			if ( !form::$validation->is_valid_email($email) ) {
				form::$error .= '<li>You did not enter a valid email</li>';
			}
			
			if ( !form::has_error() ) :
			
				$privacy      = form::$posts->incl_privacy;
				$referer      = form::$posts->incl_referer;
				$account_type = form::$posts->incl_account_type;
				
				if ( is_array($account_type) ) :
					if ( isset($account_type[2]) ) :
						$account_type = 'B';
					else :
						$account_type = $account_type[1];
					endif;
				endif;
					
				$password = md5( cPRE_SALT.$password.cPOST_SALT );
				
				$sql = "SELECT id FROM accounts WHERE email = '$email'";
				$sql = db::query($sql);
				
				if ($db->num_rows() == 0) :
					$random_salt    = hash( "sha256", rand() . mktime() . rand() );
					$activation_key = hash( "sha256", rand() . mktime() . rand() . rand() );
					
					$sql = "INSERT INTO accounts (`email`, `password`, `referer`, `account_type`, `random_salt`, `activation_key`)"
						. " VALUES"
						. " ('$email', '$password', '$referer', '$account_type', '$random_salt', '$activation_key' )";
						
					if (!db::query($sql)) :
						db::error();
						form::$error .= _STRING_REGISTRATION_ERROR__DATABASE_ . ' ' . db::$error;
						
					else :
				
						$sql = db::query("SELECT * FROM accounts WHERE id = LAST_INSERT_ID()");
						$row = db::fetch_object($sql);
						
						if ( !$row ) :
							form::$error .= '<li>' . _STRING_REGISTRATION_ERROR__GENERIC_ . '</li>';
							return false;
							
						else :
							app::$email->verify_account($row);
							self::$account_created = true;
							//header('Location: ' . url::root() );
							
						endif;
						
						return true;
					endif;
				
				else :
				
					form::$error .= '<li>' . _STRING_REGISTRATION_ERROR__ACCOUNT_EXISTS_ . '</li>';
				
				endif;
			
			else :
				form::$error .= '<li>' . _STRING_REGISTRATION_ERROR__ACCOUNT_EXISTS_ . '</li>';
				
			endif;
		
		endif;
		
		return false;
		
		
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	static private function set_session_variables($row=false) {
		
		if ( !$row ) : 
			self::$login_error = 'you did not pass the user info';
		
		else :
			self::$sess = (object) array();
			self::$sess->id                = $row->id;
			self::$sess->name              = $row->first.' '.$row->last;
			self::$sess->first             = $row->first;
			self::$sess->last              = $row->last;
			self::$sess->company           = $row->company;
			self::$sess->website           = $row->website;
			self::$sess->address           = $row->address;
			self::$sess->city              = $row->city;
			self::$sess->state             = $row->state;
			self::$sess->zip               = $row->zip;
			self::$sess->country           = $row->country;
			self::$sess->phone             = $row->phone;
			self::$sess->email             = $row->email;
			self::$sess->type              = $row->account_type;
			self::$sess->mode              = ( $row->account_type == 'B' )?'D':$row->account_type;
			self::$sess->account_type      = self::$sess->mode;
			self::$sess->credits           = $row->credits;
			self::$sess->rs                = $row->random_salt;
			self::$sess->account_verified  = $row->account_verified;
			self::$sess->account_status    = $row->account_status;
			self::$sess->activation_key    = $row->activation_key;
			
			self::$sess->is_enterprise     = $row->is_enterprise;
			self::$sess->enterprise_url    = $row->enterprise_url;
			self::$sess->enterprise_logo   = $row->enterprise_logo;
			
			self::$sess->account_type_long = ( self::$sess->account_type == 'C' || self::$sess->account_type == 'B' ) ? 'client' : 'developer';
			self::$sess->account_type_long = ( self::$sess->account_type == 'A' ) ? 'administrator' : self::$sess->account_type_long;
			
			$_SESSION['profile'] = self::$sess;
		
			unset($_SESSION['profile']->password);
			
			$_SESSION['user_email']     = $_SESSION['profile']->email;
			$_SESSION['user_id']        = $_SESSION['profile']->id;
			$_SESSION['user_name']      = $_SESSION['profile']->name;
			$_SESSION['logged_in_time'] = time();
			
			if ( isset($row->coupon_id) && !empty($row->coupon_id) ) :
			
				$sql = "SELECT unlimited_credits, unlimited_date_start, unlimited_date_end FROM coupon_codes as c"
					. " WHERE"
					. " id = $row->coupon_id"
					. " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%c-%d'), DATE_FORMAT(c.unlimited_date_start, '%Y-%c-%d')) >= 0"
					. " AND DATEDIFF(DATE_FORMAT(c.unlimited_date_end,'%Y-%c-%d'), DATE_FORMAT(NOW(), '%Y-%c-%d')) >= 0";
				$sql = $db->query($sql);
				
				$db->error();
				
				if ( $db->num_rows() > 0 ) :
					$_SESSION['profile']->credits = 'Unlimited';
				endif;
				
			endif;
			
			return true;
			
		endif;
		
		return false;
		
	}
	
}
$session = new session();
?>