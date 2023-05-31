<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/css/font-awesome-4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/css/ionicons-2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/css/main.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/plugins/iCheck/square/blue.css">
    <link href="<?php echo $this->config->item('base_folder'); ?>public/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
     <script>var base_url='<?php echo $this->config->item('base_folder'); ?>'; </script>
  </head>
  <body class="hold-transition login-page">
    <div class="login-box login">
      <div class="login-logo"> <a href="<?php echo base_url(); ?>"><b>MI-DAS</b></a>    </div><!-- /.login-logo -->
     
        <p class="login-box-msg">Please enter and confirm your passwork.</p>
        <div class="has-error" id="resetError"><?php echo $error; ?></div>
        <form action="" id="resetfrm" method="post">
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="New Password" name="newPass" id="newPass" required>
            
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Re New Password" name="reNewPassword" id="reNewPassword" required>
            <input type="hidden" name="enc" value="<?php echo $encrypt; ?>">
          </div>
          <div class="row">
           
            <div class="col-xs-12 margin-bottom">
              <button type="submit" class="btn btn-success btn-block btn-flat" <?php echo ($error!='')?'disabled="disabled"':''; ?>>Reset</button>
            </div>
          </div>
        </form>

        <p class="login-box-msg text-sm">2015 © MI-DAS by Kieran Kelly Consultancy Services Ltd.</p>
        

      
    </div><!-- /.login-box -->
   
    <!-- jQuery 2.1.4 -->
    <script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- iCheck -->
    <script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/iCheck/icheck.min.js"></script>
    
    <script src="<?php echo $this->config->item('base_folder'); ?>public/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/pace-master/pace.min.js" type="text/javascript"></script>
	<script src="<?php echo $this->config->item('base_folder'); ?>public/js/custom.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>

