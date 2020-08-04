<?php
class MO_Firebase_contact_us{
	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

	function mo_firebase_auth_contact_us( $email, $phone, $query ) {
		global $current_user;
		wp_get_current_user();
		$query = '[WP Firebase Authentication Plugin] ' . $query;
		$fields = array(
			'firstName'			=> $current_user->user_firstname,
			'lastName'	 		=> $current_user->user_lastname,
			'company' 			=> site_url(),
			'email' 			=> $email,
			'ccEmail' 		    => 'oauthsupport@xecurify.com',
			'phone'				=> $phone,
			'query'				=> $query
		);
		$field_string = json_encode( $fields );
		
		$url = 'https://login.xecurify.com/moas/rest/customer/contact-us';

		$headers = array( 
			'Content-Type'   => 'application/json', 
			'charset'        => 'UTF - 8', 
			'Authorization'  => 'Basic' 
		);
		$args = array(
			'method'          =>'POST',
			'body'            => $field_string,
			'timeout'         => '5',
			'redirection'     => '5',
			'httpversion'     => '1.0',
			'blocking'        => true,
			'headers'         => $headers,
		);
		
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
			exit();
		}
		
		return true;
	}

	function mo_firebase_auth_send_email_alert( $email, $message, $subject ) {

		if( ! $this->check_internet_connection() )
			return;

		$url                 = get_option( 'host_name' ) . '/moas/api/notify/send';
		$customerKey         = $this->defaultCustomerKey;
		$apiKey              = $this->defaultApiKey;
		$currentTimeInMillis = self::get_timestamp();
		$stringToHash 		 = $customerKey .  $currentTimeInMillis . $apiKey;
		$hashValue 			 = hash("sha512", $stringToHash);
		$customerKeyHeader 	 = "Customer-Key: " . $customerKey;
		$timestampHeader 	 = "Timestamp: " .  $currentTimeInMillis;
		$authorizationHeader = "Authorization: " . $hashValue;
		$fromEmail 			 = $email;
		$subject             = "Feedback: WP Firebase Authentication Plugin";
		$site_url            = site_url();

		global $user;
		$user         = wp_get_current_user();
		$query        = '[WP Firebase Authentication] : ' . $message;

		$content = '<div >Hello, <br><br>First Name :'.$user->user_firstname.'<br><br>Last  Name :'.$user->user_lastname.'   <br><br>Company :<a href="'.$site_url.'" target="_blank" >'.$site_url.'</a><br><br>Email :<a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>Query :'.$query.'</div>';

		$fields = array(
			'customerKey'	=> $customerKey,
			'sendEmail' 	=> true,
			'email' 		=> array(
				'customerKey' 	=> $customerKey,
				'fromEmail' 	=> $fromEmail,
				'bccEmail' 		=> 'oauthsupport@xecurify.com',
				'fromName' 		=> 'miniOrange',
				'toEmail' 		=> 'oauthsupport@xecurify.com',
				'toName' 		=> 'oauthsupport@xecurify.com',
				'subject' 		=> $subject,
				'content' 		=> $content
			),
		);
		$field_string             = json_encode($fields);
		$headers                  = array( 'Content-Type' => 'application/json');
		$headers['Customer-Key']  = $customerKey;
		$headers['Timestamp']     = $currentTimeInMillis;
		$headers['Authorization'] = $hashValue;
		
		$args = array(
			'method'      =>'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = wp_remote_post( $url, $args );
		
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
			exit();
		}
	}
	function check_internet_connection() {
		return (bool) @fsockopen('login.xecurify.com', 443, $iErrno, $sErrStr, 5);
	}

	public function get_timestamp() {
		    $url     = get_option ( 'host_name' ) . '/moas/rest/mobile/get-timestamp';
		    $headers = array( 'Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic' );
			$args    = array(
				'method'      =>'POST',
				'body'        => array(),
				'timeout'     => '5',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);
			
			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: $error_message";
				exit();
			}
			
			return wp_remote_retrieve_body($response);
		}
}
?>