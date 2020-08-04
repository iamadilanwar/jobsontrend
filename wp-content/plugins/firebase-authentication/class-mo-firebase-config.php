<?php

class Mo_Firebase_Config {
	function __construct() {
    	add_action('init', array( $this, 'testconfig' ));
	}
	function testconfig() {
		if ( isset($_POST['fb_jwt']) ) {
			$user = $this->sample();
		}

 		if( isset( $_REQUEST['mo_action'] ) && 'firebaselogin' === sanitize_text_field( wp_unslash( $_REQUEST['mo_action'] ) ) && isset( $_REQUEST['test'] ) && 'true' === wp_unslash( $_REQUEST['test'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_firebase_auth_test_config_field'] ) ), 'mo_firebase_auth_test_config_form' ) ) {

		    $project_id = get_option('mo_firebase_auth_project_id');
		    $api_key = get_option('mo_firebase_auth_api_key');
	    	wp_register_script( 'mo_firebase_app_main_script', plugins_url( 'admin/js/firebase-auth-main-script.js', __FILE__), [ 'jquery' ], false, true );
	    	wp_enqueue_script( 'mo_firebase_app_main_script' );
	    	wp_register_script( 'mo_firebase_testconfig_script', plugins_url( 'js/firebase-test-config.js', __FILE__), [ 'jquery' ] );
			$data = [];
			$data['api_key'] = get_option( 'mo_firebase_auth_api_key' );
			$data['project_id'] = get_option( 'mo_firebase_auth_project_id' );
			$data['test_username'] = isset( $_POST['test_username'] ) ? sanitize_text_field( $_POST['test_username'] ) : '';
			$data['test_password'] = isset( $_POST['test_password'] ) ? sanitize_text_field( $_POST['test_password'] ) : '';
			$data['test_check_field'] = isset( $_POST['test_check_field'] ) ? sanitize_text_field( $_POST['test_check_field'] ) : '';
			wp_localize_script( 'mo_firebase_testconfig_script', 'firebase_data_testconfig', $data );
		    wp_enqueue_script( 'mo_firebase_testconfig_script', plugins_url( 'js/firebase-test-config.js', __FILE__), ['jquery'], false, true );
		}
    }
	
	function sample() {
    	if ( isset( $_POST['fb_jwt'] ) && sanitize_text_field( wp_unslash( $_POST['fb_jwt'] ) ) == 'empty_string' ) {
			if ( isset( $_POST['fb_is_test'] ) && sanitize_text_field( wp_unslash( $_POST['fb_is_test'] ) ) == 'test_check_true' ) {
				echo '<div style="font-family:Calibri;padding: 0 30%;">';
				echo '<h1 style="color:#d9534f;text-align:center;">test failed</h1>';
				if ( isset( $_POST['fb_error_msg'] ) ) {
					echo '<h4 style="text-align:center;"><b>ERROR :</b>'.sanitize_text_field( wp_unslash( $_POST['fb_error_msg'] ) ).'</h4>';
				}
				echo '</div>';
				echo '<div style="padding: 10px;"></div><div style="position:absolute;padding:0 46%;"><input style="padding:1%;width:100px;height:30px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Close" onClick="self.close();"></div>';
				exit();
			}
			$error = new WP_Error();
	        $error->add( "error_fetching_user", __( "<strong>ERROR</strong>: user doesn't exist !!." ) );
	        return $error;
		}

	    if ( isset( $_POST['fb_jwt'] ) && sanitize_text_field( wp_unslash( $_POST['fb_jwt'] ) ) != 'empty_string' ) {
	    	$payload = $this->decode_jwt( sanitize_text_field( wp_unslash( $_POST['fb_jwt'] ) ) );

	    	if ( isset( $_POST['fb_is_test'] ) && sanitize_text_field( wp_unslash( $_POST['fb_is_test'] ) ) == 'test_check_true' ) {
				echo '<div style="font-family:Calibri;margin: auto;padding:5%;">';
						echo '<h1 style="color:#00C851;text-align:center;">Test Successful !</h1>';
						echo '<style>table{border-collapse:collapse;}th {background-color: #eee; text-align: center; padding: 8px; border-width:1px; border-style:solid; border-color:#212121;}tr:nth-child(odd) {background-color: #f2f2f2;} td{padding:8px;border-width:1px; border-style:solid; border-color:#212121;}</style>';
						echo '<h3 style="text-align:center;">Test Configuration</h3><table style="margin: auto;"><tr><th>Attribute Name</th><th>Attribute Value</th></tr>';
						$this->testattrmappingconfig( "", $payload );
						echo '</table></div>';
						echo '<div style="margin: auto;padding: 10px;></div><div style="margin: auto;position:absolute;padding:0 46%;"><input style="padding:8px;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
						exit();
			}
	    	
	    	$user = $this->getUser( $payload );
	    	if ( $user ) {
		    	$user_id = $user->ID;
		    	wp_set_auth_cookie( $user_id, true );
		    	wp_redirect( home_url() );
				exit;
			}
	    	
	    	//$user = get_user_by('email',$email);
	    	
	    }
    }
	
	function testattrmappingconfig( $nestedprefix, $payload ) {
		foreach ( $payload as $key => $value ){
			if ( is_array( $value ) || is_object( $value ) ) {
				if ( ! empty( $nestedprefix ) ) {
					$nestedprefix .= ".";
				}
				$this->testattrmappingconfig( $nestedprefix.$key, $value );
			} else {
				echo '<tr><td>';
				if ( ! empty( $nestedprefix ) )
					echo $nestedprefix.".";
				echo $key.'</td><td>'.$value.'</td></tr>';
			}
		}
	}

	function decode_jwt( $jwt_token ) {
		$flag          = 0;
		$pieces        = explode( ".", $jwt_token );
		$jwt_data      = $pieces[0].".".$pieces[1];
		$jwt_signature = str_replace( ['-','_'], ['+','/'], $pieces[2] );
		$jwt_signature = base64_decode( $jwt_signature );
		$jwt_header    = json_decode( base64_decode( str_replace( ['-','_'], ['+','/'], $pieces[0] ) ), true );

		$alg = $jwt_header['alg'];
		$kid = $jwt_header['kid'];

		if ( strpos( $alg, "RS" ) !== false ) {
			$algorithm = "RSA";
			$sha       = explode( "RS", $alg )[1];
		}

		$kid_stored = get_option( 'mo_firebase_auth_kid1' );
		if ( $kid_stored != $kid ) {
			$flag       = 1;
			$kid_stored = get_option( 'mo_firebase_auth_kid2' );
		}
		if ( $kid_stored == $kid ) {
			if ( $flag == 0 ) {
				$jwt_raw_certificate  = get_option( 'mo_firebase_auth_cert1' );
			} else {
				$jwt_raw_certificate  = get_option( 'mo_firebase_auth_cert2' );
			}
		} else {
			echo "Please update your information by clicking on <b>Configuration</b> button in Firebase Authentication plugin and then try.";
			exit;
		}

		$public_key           = "";
		$parts                = explode( "-----", $jwt_raw_certificate );

		if ( preg_match( '/\r\n|\r|\n/', $parts[2] ) ) {
			$public_key = $jwt_raw_certificate;
		} else {
			$encoding = "-----".$parts[1]."-----\n";
			$offset   = 0;
			while ( $segment = substr( $parts[2], $offset, 64 ) ) {
				$encoding  .= $segment."\n";
				$offset    += 64;
			}
			$encoding   .= "-----".$parts[3]."-----\n";
			$public_key = $encoding;
		}

		switch ( $sha ) {
			case '256':
				$verified = openssl_verify( $jwt_data, $jwt_signature, $public_key, OPENSSL_ALGO_SHA256 );
				break;
			case '384':
				$verified = openssl_verify( $jwt_data, $jwt_signature, $public_key, OPENSSL_ALGO_SHA384 );
				break;
			case '512':
				$verified = openssl_verify( $jwt_data, $jwt_signature, $public_key, OPENSSL_ALGO_SHA512 );
				break;
			default:
				$verified = false;
				break;
		}

		if ( ! $verified ) {
			echo "Invalid Token";
			exit();
		}

		$jwt_payload = json_decode( base64_decode( $pieces[1] ), true );
		return $jwt_payload;

	}
	
	function getUser( $jwt_payload )
	{
		if ( isset( $jwt_payload['email'] ) ){
			$email = $jwt_payload['email'];
			$user  = get_user_by( "email", $email );
			if ( ! $user ) {
				$user = get_user_by( 'login', $user_email );
				if ( $user ) {
					return $user;
				} else {
					$user_password = wp_generate_password( 10, false );

				$userdata = array(
					'user_login' => $email,
					'user_pass'  => $user_password,
					'user_email' => $email,
				);

				$user_id = wp_insert_user( $userdata );

				if ( ! is_wp_error( $user_id ) ) 
				{
	                //Store disting
					// shedName in User Meta
	                update_user_meta( $user_id, 'mo_firebase_user_dn', false );
	            }

	            $user = get_user_by( 'email', $email );
	            return $user;
				}
			} else if( $user ) {
				 return $user;
			}
		}
	}
}

$mo_firebase_config_obj = new Mo_Firebase_Config();