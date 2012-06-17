<?php
	
	class Email { 
		
		var $To = null;
		var $From = null;
		var $Subject = null;
		var $HTML = true;
		var $Extra = array();
		var $SendHeaders = ""; 

		// Check if the email address is valid or not
		// Returns: true or false
		function ValidateEmail($Email) { 
			return filter_var($Email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $Email);
		}
		
		// Handle errors in this section here. For this default class we're just ecco'ing a pre.
		function Error($error) {
			
			switch ($error) {
				case 0:
					$Reason = "Email Address is Unknown Format";
					break;
				case 1:
					$Reason = "Unknown Error"; 
					break;
				case 2:
					$Reason = "Email Sent Successfully"; 
					break;
			}
			
			echo "<PRE><strong>EMAIL ERROR: </strong>" . $Reason ."</PRE>"; 
		}
		
		function BuildHeaders() {
			// Build the default headers
			$DefaultHeaders = array("From: ".$this->From,
						     "Reply-To: ".$this->From, 
						     "Return-Path: ".$this->From, 
						     "X-mailer: PHP Email 1.0"); 	
			// If the message is HTML then set the mime types.
			if ($this->HTML == true) { 
				array_push($DefaultHeaders,'MIME-Version: 1.0');
				array_push($DefaultHeaders,'Content-type: text/html; charset=iso-8859-1');
			}
			// Merge the default headers and the user defined headers
			$Headers = array_merge($DefaultHeaders,$this->Extra); 
			// For each of the headers put on a new line for the email header
			foreach($Headers as $Header) { 
				$this->SendHeaders .= $Header."\r\n";
			}		   
		}
		
		// Call in the DB Vars
		function SendEmail($To,$From,$Subject,$Body,$HTML = true,$Headers) { 
			 $this->To = $To; 
			 $this->From = $From; 
			 $this->Subject = $Subject;
			 $this->Body = $Body; 
			 $this->HTML = $HTML; 
			 $this->Extra = $Headers;
			 
			 $this->Email_Message(); 
		}
		
		// Send the message
		function Email_Message() { 
		    // Build the headers
			$this->BuildHeaders(); 
	
			// Validate the email address is in the correct format.
			if ($this->ValidateEmail($this->To)) { 
				if(mail($this->To,$this->Subject,$this->Body,$this->SendHeaders)) { 
					// This isn't actually an error but rather a notification the email sent.
					$this->Error(2); 
				}
			} else { 
			// If not in the right format send error code 0. 
				$this->Error(0); 
			}
		}
		
		
	}
	
	/* Example of sending an email
	$Email = new Email(); 
	$Email->SendEmail('Dan@djdclarke.com','Dan@trafficcake.com',"Hello  ","Test",true); 
	*/ 


?>