<?php

require_once 'partials/firebase-authentication-admin-display.php';
class  MO_Firebase_Authentication_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {
		if( $hook != 'toplevel_page_mo_firebase_configuration' ) {
                return;
        }
		wp_enqueue_style( 'mo_firebase_auth_admin_bootstrap_style', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'mo_firebase_auth_firebase_phone_style', plugins_url( 'css/phone.css', __FILE__ ) );
		wp_enqueue_style( 'mo_firebase_auth_settings_style', plugins_url( 'css/style.css', __FILE__ ) );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		if( $hook != 'toplevel_page_mo_firebase_configuration' ) {
                return;
        }
		wp_enqueue_script( 'mo_firebase_auth_bootstrap_script', plugins_url( 'js/bootstrap.min.js', __FILE__) );
		wp_enqueue_script( 'mo_firebase_auth_custom_settings_script', plugins_url( 'js/custom.js', __FILE__) );
		wp_enqueue_script( 'mo_firebase_auth_firebase_phone_script', plugins_url( 'js/phone.js', __FILE__ ), array(), $this->version , false );
	}

	public function enqueue_firebase_scripts() {
		if( isset( $_GET['mo_action'] ) && 'firebaselogin' === sanitize_text_field( wp_unslash($_GET['mo_action'] ) ) ) {
			wp_enqueue_script( 'mo_firebase_app_script', plugins_url( 'js/firebase-app.js', __FILE__) );
			wp_enqueue_script( 'mo_firebase_auth_script', plugins_url( 'js/firebase-auth.js', __FILE__) );
			wp_enqueue_script( 'mo_firebase_firestore_script', plugins_url( 'js/firebase-firestore.js', __FILE__), ['jquery'] );
		}
	}

	public function enqueue_firebase_wp_login_scripts() {
		wp_enqueue_script( 'mo_firebase_app_script', plugins_url( 'js/firebase-app.js', __FILE__) );
		wp_enqueue_script( 'mo_firebase_auth_script', plugins_url( 'js/firebase-auth.js', __FILE__) );
		wp_enqueue_script( 'mo_firebase_firestore_script', plugins_url( 'js/firebase-firestore.js', __FILE__), ['jquery'] );
		wp_register_script( 'mo_firebase_app_main_script', plugins_url( 'js/firebase-auth-main-script.js', __FILE__), [ 'jquery' ] );
		wp_enqueue_script( 'mo_firebase_app_main_script' );
		wp_register_script( 'mo_firebase_app_login_script', plugins_url( 'js/firebase-wp-login.js', __FILE__), [ 'jquery' ] );
		$data = [];
		$data['api_key'] = get_option( 'mo_firebase_auth_api_key' );
		$data['project_id'] = get_option( 'mo_firebase_auth_project_id' );
		$data['enable_firebase_login'] = get_option( 'mo_enable_firebase_auth' );
		$data["disable_wp_login"] =  get_option( "mo_firebase_auth_disable_wordpress_login" );
		$data["enable_admin_wp_login"] = get_option( "mo_firebase_auth_enable_admin_wp_login" );
		if( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'POST' ) {
			$data['log'] = isset( $_POST['log'] ) ? sanitize_text_field( $_POST['log'] ) : '';
			$data['pwd'] = isset( $_POST['pwd'] ) ? sanitize_text_field( $_POST['pwd'] ) : '';
		}
		wp_localize_script( 'mo_firebase_app_login_script', 'firebase_data', $data );
		wp_enqueue_script( 'mo_firebase_app_login_script' );
	}
	
	
	public function mo_firebase_auth_page() {
		update_option( 'host_name', 'https://login.xecurify.com' );
		mo_firebase_auth_page_ui();
	}
	
}
