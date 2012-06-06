<?
class Forms_Validate {
	
	
	
	/* **************************************************************
	*
	* Validate the format of an email address
	*
	************************************************************** */
	public function is_valid_email( $email ) {
		/**
			Validate an email address.
			Provide email address (raw input)
			Returns true if the email address has the email 
			address format and the domain exists.
		*/
		$isValid = true;
		if ( !empty( $email ) ) :
		
			$atIndex = strrpos($email, "@");
			if (is_bool($atIndex) && !$atIndex) {
				$isValid = false;
			} else {
				$domain    = substr($email, $atIndex+1);
				$local     = substr($email, 0, $atIndex);
				$localLen  = strlen($local);
				$domainLen = strlen($domain);
				if ($localLen < 1 || $localLen > 64) {
					// local part length exceeded
					$isValid = false;
				} else if ($domainLen < 1 || $domainLen > 255) {
					// domain part length exceeded
					$isValid = false;
				} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
					// local part starts or ends with '.'
					$isValid = false;
				} else if (preg_match('/\\.\\./', $local)) {
					// local part has two consecutive dots
					$isValid = false;
				} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
					// character not valid in domain part
					$isValid = false;
				} else if (preg_match('/\\.\\./', $domain)) {
					// domain part has two consecutive dots
					$isValid = false;
				} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
					// character not valid in local part unless 
					// local part is quoted
					if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
						$isValid = false;
					}
				}
				
				if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
					// domain not found in DNS
					$isValid = false;
				}
			}
			if ( !$isValid ) {
				$form->error .= "<li>You have entered an invalid email address.</li>";
			}
		
		endif;
		
		return $isValid;

	}
	
	
	function validate_email($email='') {
		if ( !self::is_valid_email($email) ) :
			form::$error .= '<li>You need to enter a valid email address</li>';
		endif;
	}
	/* **************************************************************
	* END
	************************************************************** */
	
	
	
	
	
	/*
	*
	* Validate the phone number format
	*
	*/
	function is_valid_phone( $phone_number='' ) {
		
		$pattern = '/^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/';
		if (preg_match($pattern, $phone_number, $matches)) {
			// we have a match, dump sub-patterns to $matches
			$phone_number = $matches[0]; // original number
			$area_code = $matches[1];    // 3-digit area code
			$exchange = $matches[2];     // 3-digit exchange
			$number = $matches[3];       // 4-digit number
			$extension = $matches[4];    // extension
		}
	}
}
?>