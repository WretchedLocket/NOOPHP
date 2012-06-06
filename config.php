<?php
	
	# ####
	#
	# Error Constants
	#
		define('_APP_CACHE_'           , false);
		define('_APP_REQUIRE_SESSION_' , false);
		define('_APP_NAME_'            , 'app_name');
		define('_APP_TITLE_'           , 'This is my site');
		define('_APP_META_TITLE_'       , 'This is my site');
		define('_APP_META_DESCRIPTION_' , 'This is my site');
		define('_APP_META_KEYWORDS_'    , 'PHP, framework, OOP');
		
		define('_APP_COOKIE_LIFESPAN_', 20);
		define('_APP_SALTS_PASSWORD_','');
		define('_APP_AES_PASSWORD_','');
		define('_APP_CDN_URL_','');
		define('_APP_FB_OPENGRAPH_META_','');
		
		define('_ADMIN_URL_', '/admin');
		define('_ADMIN_PATH_', '/admin');		
		
	#
	#
	# ####
	
	
	
	
	
	# ####
	#
	# Security Constants
	#
		define('_SECURITY_POSTS_ALLOW_HTML_',false); 
		// true/false
		
		define('_SECURITY_BAD_REQUESTS_','globals,mosconfig_absolute_path,_session,&amp;_request,&_request,&amp;_post,&_post,cftoken,cfid,src=",/**/,/*!,/union/, union ,%20union%20,drop table,alter table,drop/**/table,alter/**/table,load_file,infile');
		// comma separated list
		
		define('_SECURITY_ALERT_SEND_EMAIL_',''); 
		// true/false
		
		define('_SECURITY_ALERT_SEND_TO_','');    
		// email address to send errors to
		
		define('_SECURITY_ERROR_PAGE_','');       
		// true/false to show errors
		
		define('_SECURITY_ERROR_PAGE_URL_','');    
		// url to error page
	
	#
	#
	# ####
	
	
	
	
	
	# ####
	#
	# Error Constants
	#
		define('_ERRORS_DISPLAY_',true);            
		// true/false
		
		define('_ERRORS_REDIRECT_TO_URL_','/error');    
		// url to redirect user to when there is an error
		
		define('_ERRORS_EMAIL_TO_','errors@domain.com');           
		// email address errors are emailed to
		
		define('_ERRORS_EMAIL_FROM_','errors@domain.com');         
		// From email address of sent messages
		
		define('_ERRORS_STYLE_SHEET_','assets/css/error-reports.css');       
		// style sheet of error page for custom layout
		
		define('_ERRORS_ERROR_MESSAGE_', '<div align="center"><b style="font-size: 150%;">lnnl<b style="font-size: 200%;"> (-_-) </b>lnnl</b><br /><br />aw snap! something broke!</div>');      
		// actual message displayed when an error is caught
	
	#
	#
	# ####
	
	
	
	
	# ####
	#
	# Database Constants
	#
		define('_DB_HOST_', 'localhost');
		define('_DB_NAME_', 'noophp');
		define('_DB_USER_', '');
		define('_DB_PASSWORD_', '');
		define('_DB_CONFIG_LOOKUP_TABLE_', 'app_config');
	#
	#
	# ####


define( '_STRING_PROJECT__PENDING_APPROVAL_', '<div id="pending_approval">PROJECT PENDING APPTANK APPROVAL</div>' );
define( '_STRING_PROJECT__UPDATES_UNDER_REVIEW_', '<div id="under_review">PROJECT UPDATES UNDER REVIEW BY APPTANK</div>' );


# ######
#
# Account Login Error/Success Strings
#
# ######

define( '_STRING_LOGIN_ERROR__NOT_VERIFIED_'     , '<div class="form-elements"><p>Account has not been verified<br /><span>A verification email was sent to the registered email address, with a verification link. Please visit that link in order to activiate your account.</span><br /><span>To have the verification email resent, <a href="/verify-account/resend">click here</a></span></p></div>');
define( '_STRING_LOGIN_ERROR__ACCOUNT_DISABLED_' , '<div class="form-elements"><p>Account has been disabled<br /><span>Your account has been disabled. To discuss this with us, please <a href="/contact">contact us</a> with any questions you may have.</span></p></div>');
define( '_STRING_LOGIN_ERROR__LOCKED_OUT_'       , '<div class="form-elements"><p>Account has been locked out<br /><span>You\'ll have to wait up to an hour before logging in again. There have been too many failed attempts.</span></p></div>');
define( '_STRING_LOGIN_ERROR__INVALID_'          , '<div class="form-elements"><p>Invalid User Name or Password<br /><span>Please try again.</span></p></div>');
define( '_STRING_LOGIN_SUCCESS_'                , '<p>You\'ve successfully logged in.</p>');

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
define( '_STRING_REGISTRATION_SUCCESS_'                 , '<p>yes, you did it!<br /><span>A verification link has been sent to your email. Click on the link to verify your account.</span><br /><span>Once you\'ve verified your account, click the "Continue" button below.</span><br /><br /></p></div><div class="buttons"><input type="button" value="Continue" onClick="window.location = window.location.href;" />');

# ###### #


?>