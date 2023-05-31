<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="keywords" content="reset, password, set, email, credentials, profiles">
		<meta name="author" content="Mi-Das">
		<meta name="description" content="Secure page to allow password to be set for specified email address">
    <title> Set Password</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?=site_url("public/bootstrap/css/bootstrap.min.css");?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=site_url("public/css/font-awesome-4.5.0/css/font-awesome.min.css");?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?=site_url("public/css/ionicons-2.0.1/css/ionicons.min.css");?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=site_url("public/css/main.min.css");?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=site_url("public/plugins/iCheck/square/blue.css");?>">
    <link href="<?=site_url("public/plugins/pace-master/themes/blue/pace-theme-flash.css");?>" rel="stylesheet"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="hold-transition login-page">
    <div class="login-box forget-password">
    	<div class="login-logo">  
				<a href="<?php echo base_url(); ?>"><b>MI-DAS</b></a> 
			</div>
			
			<!-- /.login-logo -->
      <p class="login-box-msg">Enter your e-mail address below to set your password</p>
      <div class="has-error" id="forgotError"></div>
			<?php echo $this->session->flashdata('password_message');?>
			<?php echo form_open('site/setPassword', array("id"=>"serpasswordfrm")); ?>
				<div class="form-group has-feedback">
					<?php echo form_input(array("name"=>"email", "class"=>"form-control", "placeholder"=>"Email", "type"=>"email", "required"=>"true"));?>
				</div>
				
				<div class="row">
					<div class="col-xs-12 margin-bottom">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
					</div>
				</div>
			<?php echo form_close(); ?>
			<a class="btn btn-default btn-block margin-bottom" href="<?php echo site_url();?>" id="login-back">Login Back</a>
			<p class="login-box-msg text-sm">2015 © MI-DAS by Kieran Kelly Consultancy Services Ltd.</p>
    </div>
		<!-- /.login-box -->
		
    <!-- jQuery 2.1.4 -->
    <script src="<?=site_url("public/plugins/jQuery/jQuery-2.1.4.min.js");?>"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?=site_url("public/bootstrap/js/bootstrap.min.js");?>"></script>
    <script src="<?=site_url("public/js/jquery.validate.min.js");?>" type="text/javascript"></script>
    <script src="<?=site_url("public/plugins/pace-master/pace.min.js");?>" type="text/javascript"></script>
		<script src="<?=site_url("public/js/custom.js");?>" type="text/javascript"></script>
  </body>
</html>

