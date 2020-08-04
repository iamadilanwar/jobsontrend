<?php


/**
 *
 * @link              https://miniorange.com
 * @since             1.0.0
 * @package           Firebase_Authentication
 *
 * @wordpress-plugin
 * Plugin Name:       Firebase Authentication
 * Plugin URI:        firebase-authentication
 * Description:       This plugin allows login into Wordpress using Firebase as Identity provider.
 * Version:           1.1.2
 * Author:            miniOrange
 * Author URI:        https://miniorange.com
 * License:           GPL2
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MO_FIREBASE_AUTHENTICATION_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-firebase-authentication-activator.php
 */
function mo_firebase_activate_firebase_authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-firebase-authentication-activator.php';
	MO_Firebase_Authentication_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-firebase-authentication-deactivator.php
 */
function mo_firebase_deactivate_firebase_authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-firebase-authentication-deactivator.php';
	MO_Firebase_Authentication_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'mo_firebase_activate_firebase_authentication' );
register_deactivation_hook( __FILE__, 'mo_firebase_deactivate_firebase_authentication' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-firebase-authentication.php';
require_once 'class-mo-firebase-config.php';
require('views/feedback_form.php');
require('class-contact-us.php');


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function mo_firebase_run_firebase_authentication() {

	$plugin = new MO_Firebase_Authentication();
	$plugin->run();

}
mo_firebase_run_firebase_authentication();

class mo_firebase_authentication_login {
	function __construct() {
    	add_action( 'init', array( $this, 'postResgiter' ) );
    	add_action( 'admin_init',  array( $this, 'mo_firebase_auth_deactivate' ) );
		if ( get_option( 'mo_enable_firebase_auth' ) == 1 ) {
			remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
			remove_filter( 'authenticate', 'wp_authenticate_email_password', 20, 3 );
			add_filter( 'authenticate', array( $this, 'mo_firebase_auth' ), 0, 3 );
		}
		remove_action( 'admin_notices', array( $this, 'mo_firebase_auth_success_message') );
		remove_action( 'admin_notices', array( $this, 'mo_firebase_auth_error_message') );
		add_action( 'admin_footer', array( $this, 'mo_firebase_auth_feedback_request' ) );
		update_option( 'host_name', 'https://login.xecurify.com' );
    }

	function postResgiter() {
		if ( isset( $_POST['verify_user'] ) && isset( $_REQUEST['page'] ) && sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) == 'mo_firebase_configuration' && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_firebase_auth_config_field'] ) ), 'mo_firebase_auth_config_form' ) ) {

			if( current_user_can( 'administrator' ) ) {
				update_option( 'mo_firebase_auth_disable_wordpress_login', isset( $_POST['disable_wordpress_login'] ) ? (int)filter_var( $_POST['disable_wordpress_login'], FILTER_SANITIZE_NUMBER_INT ) : 0 );

				update_option('mo_firebase_auth_enable_admin_wp_login', isset($_POST['mo_firebase_auth_enable_admin_wp_login']) ? $_POST['mo_firebase_auth_enable_admin_wp_login'] : 0);

				$project_id = isset( $_POST['projectid'] ) ? sanitize_text_field( $_POST['projectid'] ) : '';
				update_option( 'mo_firebase_auth_project_id', $project_id );
				
				$api_key = isset( $_POST['apikey'] ) ? sanitize_text_field( $_POST['apikey'] ) : '';
				update_option( 'mo_firebase_auth_api_key', $api_key );
				
				$response = wp_remote_get( 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com' );
				if ( is_array( $response ) ) {
				  	$header = $response['headers']; // array of http header lines
				  	$body   = $response['body']; // use the content
					
				  	$split_result = explode( ":", $body );

				  	$kid1   = substr( $split_result[0], 5, 40 );
				  	$s      = explode( ",", $split_result[1] );
				  	$c1     = substr( $s[0], 2, 1158 );
				  	$kid2   = substr( $s[1], 4, 40 );
					$c2     = explode( "}", $split_result[2] );
					$c2[0]  = substr( $c2[0], 2, 1158 );			  	
					$c1     = str_replace( '\n', '', $c1 );
					update_option( 'mo_firebase_auth_kid1', $kid1 );
					update_option( 'mo_firebase_auth_cert1', $c1 );
					$c2[0] = str_replace( '\n', '', $c2[0] );
					update_option( 'mo_firebase_auth_kid2', $kid2 );
					update_option( 'mo_firebase_auth_cert2', $c2[0] );
				}
			}
		}
	}


	function mo_firebase_auth( $user, $username, $password ) {

		if( "POST" !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) {
			return $user;
		}
		
		if ( empty( $username ) || empty ( $password ) ) {

				$error = new WP_Error();
				
				if( isset( $_POST['fb_error_msg'] ) ) {
					$error->add( 'firebase_error_msg', __( '<strong>ERROR</strong>: '.esc_html( wp_unslash( $_POST['fb_error_msg'] ) ) ) );
				}

		        //create new error object and add errors to it.
		        else if ( empty( $username ) ) { //No email
		            $error->add( 'empty_username', __( '<strong>ERROR</strong>: Email field is empty.' ) );
		        }

		        else if ( empty( $password ) ) { //No password
		            $error->add( 'empty_password', __( '<strong>ERROR</strong>: Password field is empty.' ) );
		        }
		        return $error;
		}
		if ( get_option( 'mo_firebase_auth_disable_wordpress_login' )  == false ) {
			$user = get_user_by( "login", $username );
			if ( !$user ) {
				$user = get_user_by( "email", $username );
			}
			if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
				return $user;
			}
		}
		else if ( get_option( 'mo_firebase_auth_enable_admin_wp_login' ) ) {
            $user = get_user_by( "login", $username );
            if ( !$user ) {
				$user = get_user_by( "email", $username );
			}
            if ( $user && $this->is_administrator_user( $user ) ) {
                if ( wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
                    return $user;
				}
            }
        }
	}

	function mo_firebase_auth_success_message() {
		$class = "error";
		$message = get_option('message');
		echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
	}

	function mo_firebase_auth_error_message() {
		$class = "updated";
		$message = get_option('message');
		echo "<div class='" . $class . "'><p>" . $message . "</p></div>";
	}

	function is_administrator_user( $user ) {
        $userRole = ( $user->roles );
        if ( ! is_null( $userRole ) && in_array( 'administrator' , $userRole ) ) {
            return true;
        }
        else {
            return false;
        }
    }

    private function mo_firebase_auth_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_firebase_auth_success_message') );
		add_action( 'admin_notices', array( $this, 'mo_firebase_auth_error_message') );
	}

	private function mo_firebase_auth_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_firebase_auth_error_message') );
		add_action( 'admin_notices', array( $this, 'mo_firebase_auth_success_message') );
	}

    function mo_firebase_auth_feedback_request() {
		mo_firebase_auth_display_feedback_form();
	}

	private function mo_firebase_auth_check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

	function mo_firebase_auth_deactivate(){
		
		if ( isset( $_POST['option'] ) ) {
			
			if ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == 'mo_enable_firebase_auth' && wp_verify_nonce( $_REQUEST['mo_firebase_auth_enable_field'], 'mo_firebase_auth_enable_form' ) ){
				update_option( 'mo_enable_firebase_auth', isset( $_POST['mo_enable_firebase_auth'] ) ? (int)filter_var( $_POST['mo_enable_firebase_auth'], FILTER_SANITIZE_NUMBER_INT ) : 0 );

			} else if ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == 'mo_firebase_auth_contact_us' && isset($_REQUEST['mo_firebase_auth_contact_us_field']) && wp_verify_nonce( $_REQUEST['mo_firebase_auth_contact_us_field'], 'mo_firebase_auth_contact_us_form' ) ) {
				$email = isset( $_POST['mo_firebase_auth_contact_us_email'] ) ? sanitize_email( $_POST['mo_firebase_auth_contact_us_email'] ) : "";
				$phone = "+ ".preg_replace( '/[^0-9]/', '', $_POST['mo_firebase_auth_contact_us_phone'] );
				//$phone = sanitize_textarea_field($_POST['mo_firebase_auth_contact_us_phone']);
				$query = isset( $_POST['mo_firebase_auth_contact_us_query'] ) ? sanitize_textarea_field( $_POST['mo_firebase_auth_contact_us_query'] ) : "";
				if ( $this->mo_firebase_auth_check_empty_or_null( $email ) || $this->mo_firebase_auth_check_empty_or_null( $query ) ) {
					echo '<br><b style=color:red>Please fill up Email and Query fields to submit your query.</b>';
				} else {
					$contact_us = new MO_Firebase_contact_us();
					$submited   = $contact_us->mo_firebase_auth_contact_us( $email, $phone, $query );
					if ( $submited == false ) {
						echo '<br><b style=color:red>Your query could not be submitted. Please try again.</b>';
					} else {
						echo '<br><b style=color:green>Thanks for getting in touch! We shall get back to you shortly.</b>';
					}
				}

			} else if ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == 'mo_firebase_auth_skip_feedback' ) {
				deactivate_plugins( __FILE__ );
				update_option( 'message', 'Plugin deactivated successfully' );
				$this->mo_firebase_auth_show_success_message();

			} else if ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == 'mo_firebase_auth_feedback' && isset($_REQUEST['mo_firebase_auth_feedback_field']) && wp_verify_nonce( $_REQUEST['mo_firebase_auth_feedback_field'], 'mo_firebase_auth_feedback_form' ) ) {
				$user    = wp_get_current_user();
				$message = 'Plugin Deactivated:';
				$deactivate_reason         = array_key_exists( 'deactivate_reason_radio', $_POST ) ? $_POST['deactivate_reason_radio'] : false;
				$deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? $_POST['query_feedback'] : false;
				if ( $deactivate_reason ) {
					$message .= $deactivate_reason;
					if ( isset( $deactivate_reason_message ) ) {
						$message .= ':' . $deactivate_reason_message;
					}
					
					$email      = $user->user_email;
					$contact_us = new MO_Firebase_contact_us();
					$submited   = json_decode( $contact_us->mo_firebase_auth_send_email_alert( $email, $message, "Feedback: WordPress Firebase Authentication" ), true );
					deactivate_plugins( __FILE__ );
					update_option( 'message', 'Thank you for the feedback.' );
					$this->mo_firebase_auth_show_success_message();

				} else {
					update_option( 'message', 'Please Select one of the reasons ,if your reason is not mentioned please select Other Reasons' );
					$this->mo_firebase_auth_show_error_message();
				}
			}
		}
	}

}

$mo_firebase_authentication_obj = new mo_firebase_authentication_login();