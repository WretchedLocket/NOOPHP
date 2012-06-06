<?php
	
	# ####
	#
	# Error Constants
	#
		define('_APP_CACHE_'            , false);
		define('_APP_REQUIRE_SESSION_'  , false);
		define('_APP_NAME_'             , 'app_name');
		define('_APP_TITLE_'            , 'This is my site');
		define('_APP_META_TITLE_'       , 'This is my site');
		define('_APP_META_DESCRIPTION_' , 'This is my site');
		define('_APP_META_KEYWORDS_'    , 'PHP, framework, OOP');
		
		define('_APP_COOKIE_LIFESPAN_'  , 20);
		define('_APP_SALTS_PASSWORD_'   , '');
		define('_APP_AES_PASSWORD_'     , '');
		define('_APP_CDN_URL_'          , '');
		define('_APP_FB_OPENGRAPH_META_', '');
		
		define('_ADMIN_URL_'  , '/admin');
		define('_ADMIN_PATH_' , '/admin');		
		
	#
	#
	# ####
	
	
	
	
	
	
	
		
	# ####
	#
	# Database Constants
	#
		define('_DB_HOST_'     , 'localhost');
		define('_DB_NAME_'     , 'noophp');
		define('_DB_USER_'     , '');
		define('_DB_PASSWORD_' , '');
		define('_DB_CONFIG_LOOKUP_TABLE_', 'app_config');
	#
	#
	# ####
	
	
	
	
	
	
	
		
	# ####
	#
	# Security Constants
	#
		// true/false
		define('_SECURITY_POSTS_ALLOW_HTML_',false); 
		
		// comma separated list
		define('_SECURITY_BAD_REQUESTS_','globals,mosconfig_absolute_path,_session,&amp;_request,&_request,&amp;_post,&_post,cftoken,cfid,src=",/**/,/*!,/union/, union ,%20union%20,drop table,alter table,drop/**/table,alter/**/table,load_file,infile');
		 
		// true/false
		define('_SECURITY_ALERT_SEND_EMAIL_','');
		 
		// email address to send errors to
		define('_SECURITY_ALERT_SEND_TO_','');   
		     
		// true/false to show errors
		define('_SECURITY_ERROR_PAGE_','');  
		   
		// url to error page
		define('_SECURITY_ERROR_PAGE_URL_',''); 
	
	#
	#
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# Error Constants
	#          
		// true/false
		define('_ERRORS_DISPLAY_',true);  
		
		// url to redirect user to when there is an error
		define('_ERRORS_REDIRECT_TO_URL_','/error');    
		      
		// email address errors are emailed to
		define('_ERRORS_EMAIL_TO_','errors@domain.com');     
		  
		// From email address of sent messages
		define('_ERRORS_EMAIL_FROM_','errors@domain.com');       
		 
		// style sheet of error page for custom layout
		define('_ERRORS_STYLE_SHEET_','assets/css/error-reports.css');      
		    
		// actual message displayed when an error is caught
		define('_ERRORS_ERROR_MESSAGE_', '<div align="center"><b style="font-size: 150%;">lnnl<b style="font-size: 200%;"> (-_-) </b>lnnl</b><br /><br />aw snap! something broke!</div>');  
	
	#
	# END
	# ####
	
	
	
	
	
	# ####
	#
	# Text Strings, stored as definitions for miscellaneous errors and mesages
	#


		# ######
		#
		# Account Login Error/Success Strings
		#
		# ######
		
		define( '_STRING_LOGIN_ERROR__NOT_VERIFIED_'     , '');
		define( '_STRING_LOGIN_ERROR__ACCOUNT_DISABLED_' , '');
		define( '_STRING_LOGIN_ERROR__LOCKED_OUT_'       , '');
		define( '_STRING_LOGIN_ERROR__INVALID_'          , '');
		define( '_STRING_LOGIN_SUCCESS_'                 , '');
		
		# ###### #

		
		
		# ######
		#
		# Account Registration Error/Success Strings
		#
		# ######
		
		define( '_STRING_REGISTRATION_ERROR__GENERIC_'          , '<p>Registration failed<br /><span>There was a problem registering. Please click Try Again and resubmit your information.</span></p>');
		define( '_STRING_REGISTRATION_ERROR__ACCOUNT_EXISTS_'   , '<p>Registration failed<br /><span>An account for that email address already exists.</span></p>');
		define( '_STRING_REGISTRATION_ERROR__NOTHING_SUBMITTED_', '<p>Registration failed<br /><span>You didn\'t submit anything.</span></p>');
		define( '_STRING_REGISTRATION_ERROR__DATABASE_'         , '<p>Registration failed<br /><span>There was a problem registering. Please click Try Again and resubmit your information.</span></p>');
		define( '_STRING_REGISTRATION_SUCCESS_'                 , '<p>yes, you did it!<br /><span>A verification link has been sent to your email. Click on the link to verify your account.</span><br /><span>Once you\'ve verified your account, click the "Continue" button below.</span></p>');
		
		# ###### #
		
	#
	# END
	# ####
?>