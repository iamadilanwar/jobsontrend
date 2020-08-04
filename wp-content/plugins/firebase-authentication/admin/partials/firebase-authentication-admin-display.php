<?php

function mo_firebase_auth_page_ui() {
	 ?>
		<html>
		<body>
		<div class="mo_firebase_auth_success_container" style="display:none;" id="mo_firebase_auth_success_container">
	        <div class="alert alert-success alert-dismissable" id="mo_firebase_auth_success_alert" data-fade="3000">
	            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	            Configurations saved successfully.
	        </div>
		</div>
		<div class="mo_firebase_auth_error_container" style="display:none;" id="mo_firebase_auth_error_container">
	        <div class="alert alert-danger alert-dismissable" id="mo_firebase_auth_error_alert" data-fade="3000">
	            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	            Please enter required fields.
	        </div>
		</div>
		<div style="margin-left:15px;overflow:hidden">
			<div class="row" style="margin:15px">
				<img width="30px" src="<?php echo dirname(plugin_dir_url( __FILE__ ));?>/images/logo.png"><h5>miniOrange Firebase Authentication</h5>
			</div>
			<div class="row">
			<div class="col-md-8">
				<div>
					<div class="mo_firebase_auth_card" style="width:90%" >
						<form action="" method="post" id="mo_enable_firebase_auth_form">
							<?php wp_nonce_field('mo_firebase_auth_enable_form','mo_firebase_auth_enable_field'); ?>
							<div style="display:inline"><div style="display:inline-block;padding:0px 10px 10px 0px"><strong>Enable Firebase Authentication:</strong></div>
							<div style="display:inline-block"><label class="mo_firebase_auth_switch">
								<input value="1" name="mo_enable_firebase_auth" type="checkbox" id="mo_enable_firebase_auth" <?php  echo get_option('mo_enable_firebase_auth') ? 'checked' : '' ?>>
								<span class="mo_firebase_auth_slider round"></span>
								<input type="hidden" name="option" value="mo_enable_firebase_auth">
								</label>
							</div>
							</div>
						</form>
						<form action="" method="post" id="mo_firebase_auth_form">
							<?php wp_nonce_field('mo_firebase_auth_config_form','mo_firebase_auth_config_field'); ?>
							<div style="padding:5px;"></div>Allow login with
							<input style="margin-left: 5px;"type="radio" name="disable_wordpress_login" id="disable_wordpress_login" <?php if(get_option('mo_enable_firebase_auth') == false){ echo '';}else {echo get_option('mo_firebase_auth_disable_wordpress_login') ? '' : 'checked';} ?> value="0"onclick="mo_firebase_auth_hideDiv();">Both Firebase and WordPress
							<span style="padding-right:10px" ></span>
							<input type="radio" name="disable_wordpress_login" id="disable_wordpress_login" <?php if(get_option('mo_enable_firebase_auth') == false){ echo '';}else {echo get_option('mo_firebase_auth_disable_wordpress_login') ? 'checked' : '';} ?> value="1"onclick="mo_firebase_auth_showDiv();">Only Firebase
							<div style="padding:5px;"></div>
							<div style="<?php if(get_option('mo_firebase_auth_disable_wordpress_login') == 1) echo "display: block";else echo "display: none";?>"id="mo_firebase_auth_enable_admin_wp_login_div">
								<p>Enabling Firebase login will restrict logins with only Firebase credentials and won't allow WP login. <b>Please enable this only after you have successfully tested your configuration</b> as the default WordPress login will stop working.
								</p>
								<input type="checkbox" id="mo_firebase_auth_enable_admin_wp_login" name="mo_firebase_auth_enable_admin_wp_login" value="1" <?php checked((esc_attr(get_option('mo_firebase_auth_enable_admin_wp_login')) === false) || (esc_attr(get_option('mo_firebase_auth_enable_admin_wp_login')) == 1));?> /> Allow Administrators to use WordPress login&emsp;<div class="mo-firebase-auth-tooltip">&#x1F6C8;<div class="mo-firebase-auth-tooltip-text mo-tt-right">Selecting this option will only allow Wordpress Administrators to log in.</div> </div>
								<br>
							</div>
							<br>
							<h6><font color="#FF0000">*</font><font color="#000000">Project Id</font>&emsp;<div class="mo-firebase-auth-tooltip">&#x1F6C8;<div class="mo-firebase-auth-tooltip-text mo-tt-right">collect project Id from your firebase project</div> </div></h6>
							<input style="width:60%"type="text" id="project_id" name="projectid" value= "<?php echo get_option( 'mo_firebase_auth_project_id' ); ?>" placeholder="Enter Project Id.." required="">
							<br><br>
							<h6><font color="#FF0000">*</font><font color="#000000">API Key</font>&emsp;<div class="mo-firebase-auth-tooltip">&#x1F6C8;<div class="mo-firebase-auth-tooltip-text mo-tt-right">collect API key from your firebase project</div> </div></h6>
							<input style="width:60%"type="text" id="api_key" name="apikey" value="<?php echo get_option( 'mo_firebase_auth_api_key' ); ?>" placeholder="Enter your API Key.." required="">
							<br>
							<br>
							<input type="submit" class="btn btn-primary" style="width:170px;height:40px" name="verify_user" value=" Save Configuration" id = "mo_auth_configure_button"onclick="showAlert();">
						</form>
					</div>
				</div>
				<div>
					<div class="mo_firebase_auth_card" style="width:90%" >
						<h4 style="margin-bottom:30px">Test Authentication</h4>
						<form name="test_configuration_form" id="mo_firebasetestconfig"  method="post" target="firebasetestconfig">
							<?php wp_nonce_field('mo_firebase_auth_test_config_form','mo_firebase_auth_test_config_field'); ?>
							<input type="hidden" name="option" value="mo_firebase_auth_test_configuration">
							<font color="#FF0000">* </font><input type="text" id="test_username" name="test_username" value="" placeholder="Username" style="margin-bottom:30px;width:35%;" required=""> <br>
							<font color="#FF0000">* </font><input type="password" id="test_password" name="test_password" value="" placeholder="Password" style="margin-bottom:30px;width:35%;" required=""> <br>
							<input type="hidden" id="test_check_field" name="test_check_field" value="test_check_true">
							<input type="submit" class="btn btn-primary" id="mo_firebase_auth_test_config_button" style="width:170px;height:40px" name="test_configuration" value="Test Authentication" <?php if( ! get_option( 'mo_firebase_auth_project_id' ) && ! get_option( 'mo_firebase_auth_api_key' ) ) echo 'disabled' ?> >
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mo_firebase_auth_card" style="width:90%" >
					<h4 style="margin-bottom:30px">Contact us</h4>
					<p class="mo_firebase_auth_contact_us_p"><b>Need any help?<br>Just send us a query so we can help you.</b></p><br>
					<form action="" method="POST">
						<?php wp_nonce_field('mo_firebase_auth_contact_us_form','mo_firebase_auth_contact_us_field'); ?>
						<input type="hidden" name="option" value="mo_firebase_auth_contact_us">
						<div class="form-group">
							<input type="email" placeholder="Enter email here" class="form-control" name="mo_firebase_auth_contact_us_email" id="mo_firebase_auth_contact_us_email" required>
						</div>	
						<div class="form-group">
							<input type="tel" id="mo_firebase_auth_contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" placeholder="Enter phone here" class="form-control" name="mo_firebase_auth_contact_us_phone">
						</div>
						<div class="form-group">
							<textarea class="form-control" onkeypress="mo_firebase_auth_contact_us_valid_query(this)" onkeyup="mo_firebase_auth_contact_us_valid_query(this)" onblur="mo_firebase_auth_contact_us_valid_query(this)"  name="mo_firebase_auth_contact_us_query" placeholder="Enter query here" rows="5" id="mo_firebase_auth_contact_us_query" required></textarea>
						</div>
						<input type="submit" class="btn btn-primary" style="width:130px;height:40px" value="Submit">								
					</form>
					<br>
					<p class="mo_firebase_auth_contact_us_p"><b>If you want custom features in the plugin, just drop an email at<br><a href="mailto:info@xecurify.com">info@xecurify.com</a></b></p>
				</div>
			</div>
	        </div>
			</div>
			<script>
				jQuery("#mo_firebase_auth_contact_us_phone").intlTelInput();
				function mo_firebase_auth_contact_us_valid_query(f) {
				    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
				        /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
				}

				jQuery("#mo_firebase_auth_test_config_button").on("click", function(event) {
					var test_username = document.forms["test_configuration_form"]["test_username"].value;
					var test_password = document.forms["test_configuration_form"]["test_password"].value;
					if( test_username == "" || test_password == "" ){
						return;
					}
					event.preventDefault();
					let url = "<?php echo site_url(); ?>/?mo_action=firebaselogin&test=true";
					jQuery("#mo_firebasetestconfig").attr("action", url);
					let newwindow = window.open("about:blank", 'firebasetestconfig', 'location=yes,height=700,width=600,scrollbars=yes,status=yes');
					jQuery("#mo_firebasetestconfig").submit();
				});
				function mo_firebase_auth_showDiv(){
						document.getElementById("mo_firebase_auth_enable_admin_wp_login_div").style.display = "block";
				}
				function mo_firebase_auth_hideDiv(){
					document.getElementById("mo_firebase_auth_enable_admin_wp_login_div").style.display = "none";
				}

			</script>
			<?php
	
}

?>