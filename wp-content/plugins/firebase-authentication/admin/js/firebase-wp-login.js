
$fb_data = firebase_data;

jQuery(document).ready(function() {
	var enable_firebase_login = $fb_data["enable_firebase_login"];
	var disable_wp_login      = $fb_data["disable_wp_login"];
	var enable_admin_wp_login = $fb_data["enable_admin_wp_login"];
	
	if( ( jQuery("#login_error").text().length === 0 || enable_firebase_login == 0 ) && ((!disable_wp_login) ||(disable_wp_login && enable_admin_wp_login) )) {
		return;
	}
	if( disable_wp_login && !enable_admin_wp_login ) {
		jQuery("#wp-submit").on("click", function(event) {
			event.preventDefault();
			mo_firebase_auth_do_fb_login();
		});
	} else if( ( jQuery("#login_error").text().length > 0 && enable_firebase_login ) ) {
		mo_firebase_auth_do_fb_login();
	}
});

function mo_firebase_auth_do_fb_login() {
	var a_key            = $fb_data["api_key"];
	var pid              = $fb_data["project_id"];
	var email            = document.getElementById("user_login").value;
	var pass             = document.getElementById("user_pass").value;
	if( email.length === 0 ) {
		email = $fb_data['log'];
	}
	if( pass.length === 0 ) {
		pass = $fb_data['pwd'];
	}
	if( email.length > 0 && pass.length > 0 ) {
		jQuery("#login_error").remove();
		document.body.innerHTML = "Please wait...";
	}
	mo_firebase_auth_firebaseAuthentication( pid, a_key, email, pass, "" );
}