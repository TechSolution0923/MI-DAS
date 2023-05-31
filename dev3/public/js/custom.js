$(function(){
	$("#loginfrm").validate({
		focusInvalid: false,
		//by default validation will run on input keyup and focusout
		//set this to false to validate on submit only
		onkeyup: false,
		onfocusout: false,
		//by default the error elements is a <label>
		errorElement: "div",
		//place all errors in a <div id="errors"> element
		errorPlacement: function(error, element) {
			error.appendTo("div#loginError");
		},
		rules: {
				user_name: {
						required: true
				},
				userPass: {
					required: true,
					minlength: 2
				}
			},
		messages: {
				user_name: "Please enter a valid User Name",
				userPass: "Please enter valid password"
		},
		submitHandler: function() {
			$.post('site/loginForm', 
			$('#loginfrm').serialize() , 
			function(response){
					$('#loginError').html('');
					if(response.status=='Success')
					{
						window.location.href = response.url;
					} else if(response.status=='failed'){
						$('#loginfrm').trigger("reset");
						$('#loginError').html(response.msg);
					}
					
			}, "json");
		},
	});
	
	$("#forgotfrm").validate({
		rules: {
			email: {
				required: true,
				email: true
			}
		},
		submitHandler: function() {
			$.post('site/forgotPassword', 
			$('#forgotfrm').serialize() , 
			function(response){
				$('#forgotError').html('');
				$('#forgotfrm').trigger("reset");
				$('#forgotError').html(response.msg).delay(1500).promise().done(function(){
					$('.login').fadeIn(function(){
						$('.forget-password').fadeOut();
						$("[name='user_name']").focus();
					});
					
				});;
					
			}, "json");
		},
	});
	
	$("#resetfrm").validate({
		focusInvalid: false,
		//by default validation will run on input keyup and focusout
		//set this to false to validate on submit only
		onkeyup: false,
		onfocusout: false,
		//by default the error elements is a <label>
		errorElement: "div",
		//place all errors in a <div id="errors"> element
		errorPlacement: function(error, element) {
			error.appendTo("div#resetError");
		},
		rules: {
				newPass: "required",
				reNewPassword: {
				equalTo: "#newPass"
				} 
			},
		submitHandler: function() {
			$.post(base_url+'site/resetMyPass', 
			$('#resetfrm').serialize() , 
			function(response){
					$('#resetError').html('');
					if(response.status=='Success')
					{
						//alert(response.url);
						window.location.href = response.url;
					} else if(response.status=='failed'){
						$('#resetfrm').trigger("reset");
						$('#resetError').html(response.msg);
					}
					
			}, "json");
		},
	});
	
});
