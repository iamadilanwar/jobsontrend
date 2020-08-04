function mo_firebase_auth_firebaseAuthentication( pid, a_key, email, pass, test_check_field ) {
	if( email.length === 0 || pass.length === 0 ) {
		console.log("Email or Password is empty.");
		return;	
	}

	var re = new RegExp(/^.*\//);
	var url = re.exec(window.location.href);
	var createform = document.createElement('form'); 
	createform.setAttribute("action", url+'wp-login.php');
	createform.setAttribute("method", "post");
	createform.setAttribute("name", "jwtform");
	createform.setAttribute("id", "jwtform");

	var inputelement = document.createElement('input'); // Create Input Field for Name
	inputelement.setAttribute("type", "hidden");
	inputelement.setAttribute("name", "fb_jwt");
	inputelement.setAttribute("id", "fb_jwt");
	createform.appendChild(inputelement);
	var inputelement = document.createElement('input'); // Create Input Field for Name
	inputelement.setAttribute("type", "hidden");
	inputelement.setAttribute("name", "fb_is_test");
	inputelement.setAttribute("id", "fb_is_test");
	createform.appendChild(inputelement);
	var inputelement = document.createElement('input'); // Create Input Field for Name
	inputelement.setAttribute("type", "hidden");
	inputelement.setAttribute("name", "fb_error_msg");
	inputelement.setAttribute("id", "fb_error_msg");
	createform.appendChild(inputelement);

	document.body.appendChild(createform);

	var firebaseConfig    = {
	    apiKey: a_key,
	    authDomain: pid+'.firebaseapp.com',
	    databaseURL: 'https://'+pid+'.firebaseio.com',
	    projectId: pid,
	    storageBucket: ''
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.auth().signInWithEmailAndPassword( email, pass )
		.then(function(firebaseUser) {
	   		if ( test_check_field=='test_check_true' ) {
	   			document.getElementById('fb_is_test').value='test_check_true';
	   		}
	   		document.getElementById('fb_jwt').value=firebaseUser['user']['_lat'];
			document.forms['jwtform'].submit();
		})
		.catch(function(error) {
	       // Error Handling
		  	if ( test_check_field=='test_check_true' ) {
		   		document.getElementById('fb_is_test').value='test_check_true';
		  	}
		  	document.getElementById('fb_jwt').value='empty_string';
		  	document.getElementById('fb_error_msg').value = error.message;
			document.forms['jwtform'].submit();
			var errorCode    = error.code;
			var errorMessage = error.message;
		});
}