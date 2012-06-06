<?php
define( '_DEBUG_MODE', false );
/*
	*	Script Name: Forms
	*	Author: JJ Jiles
	*	Website: http://jjis.me
	*	Version: 1
	*	Copyright (C) March 31 2010
	*
	*	Error Catching and Debugging
	*
*/

class Error_Debug {
	#
	# a couple of basic settings
	# 
	# Display Errors?: 
	#	True|False
	#	This is overridden should there be a $config->show_errors var
	#
	# Email To:
	#	email for the person who needs to receive the error
	#
	# Style Sheet:
	#	Set the link to the style sheet. 
	#	Exclude from the server path ( http://domain.com )
	#	
	# Suppressed Error Message
	#	This is the error message that will be displayed if error reporting is turned off
	#
	var $errors = array(
			'display'     => false,
			'email_to'    => 'tnybit@jjis.me',
			'email_from'  => 'tnybit@jjis.me',
			'style_sheet' => 'assets/css/error-reports.css',
			'suppressed_error_message' => '<div align="center"><b style="font-size: 150%;">an error has occurred and email has been sent the administrator</b></div>'
		);
	
	var $error_chunk = "";
	var $error_js    = "";
	var $header_echo = false;
	var $footer_echo = false;
			

	
	function Error_Debug() {
		
		$this->error_js = "\n
			<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\" type=\"text/javascript\"></script>\n
			<script type='text/javascript'>
				window.onload = function() {
					if (typeof(jQuery) == 'undefined' || typeof($) == 'undefined') {
					var jQ = document.createElement('script');
					jQ.type = 'text/javascript';
					jQ.onload=runthis;
					jQ.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
					document.body.appendChild(jQ);
				} else {
					runthis();
				}
				
				function runthis() { 
						body_height = $('body').height();
						body_offset = $(window).scrollTop();
						
						$('body').append('<div id=\"error-dim\"></div>');
						$('#error-dim').css({
							top    : '0',
							left   : '0',
							height : body_height,
							width  : '100%'
						});					
						
						//$('#alert-message-overlay-wrapper').css({
						//	top : body_offset,
						//	marginLeft : '-141px'
						//});
						
						/* ***
						* REQUIRED
						* set you classes accordingly below. */
						var accordion = {
							parent      : '.error-error-item',    // parent element containing the clickable header and collapsible
							header      : '.error-error-view',    // clickable header
							collapsible : '.error-error-details'  // collapsible element
						};
						
						/* ***
						* hide all collapsible items */
						$(accordion.collapsible).css('display','none');
						
						/* ****
						* bind the accordion header click event */
						$(accordion.header).unbind('click');
						$(accordion.header).bind('click', function() {
							var header_obj      = $(this);          // The current header
							var next_parent_obj = $(this).parent(); // The parent of the header
						
							var content_obj = $(this).siblings(accordion.collapsible); // find the collapsible element
						
							if(content_obj.is(\":hidden\")) {
								// collapse all collapsible elements
								next_parent_obj.siblings(accordion.parent).each(function() {
									$(this).children(accordion.collapsible).slideUp('fast');
								});
								// show the content of the clicked header
								content_obj.slideDown('fast');
							} else {
								content_obj.slideUp('fast');
							}
						});
					$('#error-close> a').click(function() { $('#error-container').slideUp('fast',function() { $('#error-dim').remove(); }); }); }
				}
			</script>\n
		";
			
		return true; 
	}
	
	
	function debug_header() {
		if ( !$this->header_echo ) :	
			echo $this->echo_css();
			echo $this->error_js;
			echo "<div id=\"error-container\"><h1>An error has been caught</h1><div id=\"error-wrapper\">";
			//echo "<ul id=\"error-list\">";
			$this->header_echo = true;
		endif;
	}
			
	
	
	function debug() {
			
		if (!empty($this->error_chunk)) :
			echo $this->error_chunk;
		endif;
	}
	
	function debug_footer() {
		if ( !$this->footer_echo ) :
			echo "</ul>";
			echo "<p id=\"error-close\"><a href=\"#\">close</a></p></div></div>";
			$this->footer_echo = true;
		endif;
	}





	/* ***********************************************************************
	* START :::
	*
	*	Initial error catching method
	*	This will determine if it's a parse error, or other
	*	types of errors.
	*
	*	PARSE:
	*		stops everything dead in its tracks. If display
	*		errors is allowed, then show the error message
	*		otherwise suppress the error and email it
	*
	*
	*	ALL OTHERS:
	*		Run the backtrace through the previous called
	*		functions and display the information for debugging
	*		If display errors is false, suppress the error
	*		and email the error
	*********************************************************************** */
	function catch_error($n='', $s='', $f='', $l='') {
		global $security, $config;	
		
		#
		# check to see if the $config object exists. if so, use it's setting
		$this->errors['display'] = (isset($config->errors['display']) ) ? $config->errors['display'] : $this->errors['display'];
		
		#
		# if this is a parse error, we need to handle it differently
		# because it completely stops all PHP parsing, we catch it immediately
		# and display a simple output
		/*if( empty($n) && false === is_null($aError = error_get_last()) ) :
		
			#
			# suppress the error and show something entertaining
			if ( !$this->errors['display'] && !defined( '_DEBUG_MODE' ) || (defined( '_DEBUG_MODE' ) && _DEBUG_MODE === false)  ) :
				echo $this->errors['suppressed_error_message'];
				$this->error_thrown($n, $s, $f, $l); 
			
			#
			# for debugging, display the information about the error
			elseif ( @$this->errors['display'] || (defined( '_DEBUG_MODE' ) && _DEBUG_MODE === true) ) :				
				$this->error_backtrace($n, $s, $f, $l);
				$this->debug();
			endif;
		
		#
		# exception errors get sent to either backtracing or suppression
		else :
		*/
			#
			# backtrace the error and display the information for debugging
			if ( @$this->errors['display'] ) : 
				$this->error_backtrace($n, $s, $f, $l);
				
				//$this->error_thrown($n, $s, $f, $l); 
				
				///if ( defined( '_DEBUG_MODE' ) && _DEBUG_MODE === true) :
					//$this->debug();
				//else :
					$this->debug_header();	
					$this->debug();
				//endif;
			
			#
			# suppress the error and email it
			else : 
			
				if ( !headers_sent() ) :
					header("HTTP/1.1 400 Bad Request");
				endif;
				
				$this->error_thrown($n, $s, $f, $l); 
			
			endif;
			
			return true;
			
		//endif;
		//exit();
	} 
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	Backtraces through the errors and displays a nice 
	*	It's pretty simple. Not a lot that needs explaining
	*
	*********************************************************************** */
	function error_backtrace($errno, $errstr, $error_file, $error_line) {
		global $css, $app;
		
		
		$errorsThrown = debug_backtrace();
		
		#
		# we skip the first two errors
		# they are the initial calls from error catching
		# we can ignore them
		#
		if ( isset($errorsThrown[0]) ) :
			$errorThrown  = $errorsThrown[0]['args'];
			
			if ( !empty($errorThrown[1]) ) :
				
				#
				# this is the primary error that started it all
				$this->error_chunk .=  "<div class=\"error-error-item\">"
								. "<b class=\"error-error-view\">+</b>"
								. "<b class=\"error-error-header\">".$errorThrown[1]."</b>"
								. "<div class=\"error-error-details\">"
								. "<table id=\"primary-error\" cellspacing=2>\n"
								. "<tr><td class=\"left\"><strong>Error: </strong></td><td>" . $errorThrown[1] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>Line: </strong></td><td>" . $errorThrown[3] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>File: </strong></td><td>" . $errorThrown[2] . "</td></tr>\n</table>\n";
				
			endif; 
			
			#
			# for additional debugging, loop through all previous function calls
			# this assists in debugging if you call a method multiple times
			# now we can figure out which instance caused the error
			#
			$this->error_chunk_header .= "<p>All previously executed functions: </p>\n";
			
			$this->error_chunk_header .= "<table id=\"previous-errors\" cellpadding=5 cellspacing=0 border=0 style=\"border: 1px solid #333;\">"
							. "<tr style=\"background: #d3d3d3;\">"
							. "<th>order</th>"
							. "<th>function name</th>"
							. "<th>file name</th>"
							. "<th>line</th>"
							. "</tr>";
				
			#
			# loop thu the backtrace and output
			
			if (!empty($this->error_chunk)) :
			
				$count = 0;
				array_reverse($errorsThrown);
				
				foreach ($errorsThrown as $error) :
					if ($count > 1 && !empty($error['args'][1]) ) :
						if ($count == 1) :
							$this->error_chunk .= $this->error_chunk_header;
						endif;
						$this->error_chunk .= '<tr>'
							. '<td>' . ($count-1) . '</td>'
							. '<td>' . $error['args'][1] . '()</td>'
							. '<td>' . $error['args'][2] . '</td>'
							. '<td>' . $error['args'][3] . '</td>'
							. '</tr>';
					endif;
					$count++;
	
				endforeach;
				
				#
				# close it all up
				$this->error_chunk .= '</table></div></div>';
				
			endif;
				
		
		elseif ( true == ($err = error_get_last()) ) :
				$this->error_chunk = "<li class=\"error-error-item\">"
								. "<b class=\"error-error-view\">+</b>"
								. "<b class=\"error-error-header\">".$err['message']."</b>"
								. "<div class=\"error-error-details\">"
								. "<table id=\"primary-error\" cellspacing=2>\n"
								. "<tr><td class=\"left\"><strong>Error Type: </strong></td><td>" . $err['type'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>Message: </strong></td><td>" . $err['message'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>File: </strong></td><td>" . $err['file'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>On line: </strong></td><td>".$err['line']."</td></tr>\n</table></div></li>\n";
		endif;
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	Backtraces through the errors and displays a nice 
	*	It's pretty simple. Not a lot that needs explaining
	*
	*********************************************************************** */
	function error_thrown($errno, $errstr, $error_file, $error_line) {
		global $security,$app;
		
		$errorThrown = debug_backtrace();
		$errorFunction = '';
		
		#
		# we skip the first two errors
		# they are the initial calls from error catching
		# we can ignore them
		#		
		if (isset($errorThrown[2])) :
			$errorThrown = $errorThrown[2];
			$errorFunction = "\n".'Calling Function: '.$errorThrown['function'];
		
			$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
			
			$server_r = '';
			$server_out = $_SERVER;
			foreach ($server_out as $key=>$val) {
				$server_r .= $key.' ::: '.$val."\n\r";
			}
			
			$message = '';	
			$message .= "Error Thrown: [{$errno}] {$errstr}{$errorFunction}\n"
					. "File Name: {$error_file}\n"
					. "On line: {$error_line}\n"
					. "Server: " . $_SERVER['SERVER_NAME'] . "\n"
					. "URL: " . $_SERVER['REQUEST_URI'] . "\n"
					. "Referer: {$referer}";
					
					
			
			if ( isset($errorsThrown[2]) ) :
				$errorThrown  = $errorsThrown[2];
				
				$count = 0;
				array_reverse($errorsThrown);
				
				#
				# loop thu the backtrace and output
				foreach ($errorsThrown as $error) :
					if ($count > 1) :
						$message .= "\n\n".'--------------------------------------------------------------------------'
								. 'Function: '  . $error['function'] . "\n"
								. 'File Name: ' . $error['file']     . "\n"
								. 'On Line: '   . $error['line']     . "\n";
					endif;
					$count++;
	
				endforeach;
			endif;
			
			$message .= "\n\n\n"
					. "Server Output:\n"
					. $server_r
					. "\n\n\n";
					
			if ( !error_log(
				$message, 1, 
				$this->errors['email_to'],
				"From: " . $this->errors['email_from']
				)
			) :
				
				echo $this->errors['suppressed_error_message'];
				
			endif;
		endif;
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	echo the css style sheet so the error reporting is pretty
	*
	*********************************************************************** */
	function echo_css() {
		global $config;
		
		$root_url  = $_SERVER['HTTP_HOST'];		
		$s = empty($_SERVER["HTTPS"]) ? '' : 's';
		$root_url  = "http{$s}://".$root_url;
		
		$sub_dir = dirname(dirname(dirname(__FILE__)));
		
		$sub_dir = str_replace('\\','/',$sub_dir);
		$sub_dir = explode('/',$sub_dir);
		
		$root_url .= '/' . array_pop($sub_dir);
		
		
		$css_url = $root_url . '/' . $this->errors['style_sheet'];
		
		if ( isset($config->url->root) ) :
			$css_url = $config->url->root . '/' . $this->errors['style_sheet'];
		endif;

		
		$css_link = '<link rel="stylesheet" id="mainstyle" type="text/css" href="' . $css_url . '" />';
		return $css_link;
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */

}
		//if ( $config->errors['display'] == false ) :
			
		# Upon All Errors, call the error handling function
		//	set_error_handler(array(new Error_Debug(),'catch_error'),E_ALL);
		
		# Register function to execute at the end of the script
			//register_shutdown_function(array(new Error_Debug(),'catch_error'));
		
		# Hide error messages
		//	error_reporting(E_ALL);
		//	ini_set('display_errors', 0);
		
		//endif;



?>