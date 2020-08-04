jQuery(document).ready(function() {
	jQuery("#mo_enable_firebase_auth").click(function() {
            jQuery("#mo_enable_firebase_auth_form").submit();
  });

  jQuery('[data-toggle="tooltip"]').tooltip();

  //jQuery("#mo_firebase_auth_contact_us_phone").intlTelInput();
});

/*function mo_firebase_auth_contact_us_valid_query(f) {
    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
        /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
}*/

function showAlert() {
  if ( jQuery('#project_id').val() != "" && jQuery('#api_key').val() != "" ) {
    jQuery("#mo_firebase_auth_test_config_button").prop('disabled', false);
    if ( jQuery("#mo_firebase_auth_success_container").find("div#mo_firebase_auth_success_alert").length == 0 ) {
      jQuery("#mo_firebase_auth_success_container").append("<div class='alert alert-success alert-dismissable' id='mo_firebase_auth_success_alert' data-fade='3000'> <button type='button' class='close' data-dismiss='alert'  aria-hidden='true'>&times;</button> Configurations saved successfully.</div>");
    }
    jQuery("#mo_firebase_auth_success_container").css("display", "");
  }
  else {
    jQuery("#mo_firebase_auth_test_config_button").prop('disabled', false);
    if ( jQuery("#mo_firebase_auth_error_container").find("div#mo_firebase_auth_error_alert").length == 0 ) {
      jQuery("#mo_firebase_auth_error_container").append("<div class='alert alert-danger alert-dismissable' id='mo_firebase_auth_error_alert' data-fade='3000'> <button type='button' class='close' data-dismiss='alert'  aria-hidden='true'>&times;</button> Please enter required fields.</div>");
    }
    jQuery("#mo_firebase_auth_error_container").css("display", "");
  }
  
}