<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="public/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="public/css/font-awesome-4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="public/css/ionicons-2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="public/css/main.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="public/plugins/iCheck/square/blue.css">
    <link href="public/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="hold-transition login-page">
    <div class="login-box login">
      <div class="login-logo"> <a href="<?php echo base_url(); ?>"><b>MI-DAS</b></a>    </div><!-- /.login-logo -->
     
        <p class="login-box-msg">MANAGEMENT INFORMATION DASHBOARD</p>
        <p class="login-box-msg">Please login into your account.</p>
        <div class="has-error" id="loginError"></div>
        <form action="" id="loginfrm" method="post">
          <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email" name="user_name" required>
            
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="userPass" required>
            
          </div>
          <div class="row">
           
            <div class="col-xs-12 margin-bottom">
              <button type="submit" class="btn btn-success btn-block btn-flat">Login</button>
            </div>
          </div>
        </form>

        <p class="login-box-msg text-sm" id="forgot"><a href="#">Forgot Password?</a></p>
        <p class="login-box-msg text-sm">&copy; <?php echo date('Y',time());?>  MI-DAS by Kieran Kelly Consultancy Services Ltd.</p>
        

      
    </div><!-- /.login-box -->
    
    <div class="login-box forget-password" style="display:none">
    	<div class="login-logo">  <a href="<?php echo base_url(); ?>"><b>MI-DAS</b></a> </div><!-- /.login-logo -->
      	<p class="login-box-msg">Enter your e-mail address below to reset your password</p>
      	<div class="has-error" id="forgotError"></div>
      	<form action="" id="forgotfrm" method="post">
          <div class="form-group has-feedback">
            <input type="text" name="email" class="form-control" placeholder="Email">
            
          </div>
          
          <div class="row">
           
            <div class="col-xs-12 margin-bottom">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div>
          </div>
        </form>

    <a class="btn btn-default btn-block margin-bottom" href="#" id="login-back">Back</a>
    <p class="login-box-msg text-sm">&copy; <?php echo date('Y',time());?>  MI-DAS by Kieran Kelly Consultancy Services Ltd.</p>
    </div><!-- /.login-box -->
    
    

    <!-- jQuery 2.1.4 -->
    <script src="public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="public/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="public/plugins/iCheck/icheck.min.js"></script>
    
    <script src="public/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="public/plugins/pace-master/pace.min.js" type="text/javascript"></script>
	<script src="public/js/custom.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
    <script>
    $(document).ready(function(){
		$('#forgot').click(function(){
			$('.forget-password').fadeIn();
			$('.login').fadeOut();
		});
		$('#login-back').click(function(){
			$('.login').fadeIn();
			$('.forget-password').fadeOut();
		});
	});
    </script>
  </body>
</html>

