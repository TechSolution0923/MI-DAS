<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>MI-DAS | Management Information Dashboard</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <script>
        var base_url = '<?php echo $this->config->item('base_folder'); ?>';
    </script>
	<!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="public/css/new_dashboard.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/css/twoyearsaleschart.css">
  
	<link rel="stylesheet" href="public/bootstrap/css/bootstrap.min.css">
   
    <!-- Font Awesome -->
    <link rel="stylesheet" href="public/css/font-awesome-4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="public/css/ionicons-2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="public/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="public/plugins/line-icons/simple-line-icons.css">
 
    <link rel="stylesheet" href="public/css/mycss.css">
    <link rel="stylesheet" href="public/css/bootstrap-switch.css">
    <link rel="stylesheet" href="public/css/skins/_all-skins.min.css">
	<link href="public/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>

    <link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/plugins/datatables/datatables.min.css">
       
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.1.4 --> 
    <script src="public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <style>
        .skin-blue .main-header .navbar{background-color: #000;}
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper no-display">
