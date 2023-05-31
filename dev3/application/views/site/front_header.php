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
        body
        {
          margin:0;
          padding:0;
          background:#262626;
        }
        .ring
        {
          position:absolute;
          top:50%;
          left:50%;
          transform:translate(-50%,-50%);
          width:150px;
          height:150px;
          background:transparent;
          border:3px solid #3c3c3c;
          border-radius:50%;
          text-align:center;
          line-height:150px;
          font-family:sans-serif;
          font-size:20px;
          color:#fff000;
          letter-spacing:4px;
          text-transform:uppercase;
          text-shadow:0 0 10px #fff000;
          box-shadow:0 0 20px rgba(0,0,0,.5);
        }
        .ring:before
        {
          content:'';
          position:absolute;
          top:-3px;
          left:-3px;
          width:100%;
          height:100%;
          border:3px solid transparent;
          border-top:3px solid #fff000;
          border-right:3px solid #fff000;
          border-radius:50%;
          animation:animateC 2s linear infinite;
        }
        span.ring_span
        {
          display:block;
          position:absolute;
          top:calc(50% - 2px);
          left:50%;
          width:50%;
          height:4px;
          background:transparent;
          transform-origin:left;
          animation:animate 2s linear infinite;
        }
        span.ring_span:before
        {
          content:'';
          position:absolute;
          width:16px;
          height:16px;
          border-radius:50%;
          background:#fff000;
          top:-6px;
          right:-8px;
          box-shadow:0 0 20px #fff000;
        }
        @keyframes animateC
        {
          0%
          {
            transform:rotate(0deg);
          }
          100%
          {
            transform:rotate(360deg);
          }
        }
        @keyframes animate
        {
          0%
          {
            transform:rotate(45deg);
          }
          100%
          {
            transform:rotate(405deg);
          }
        }
    </style>
    <script>
        window.addEventListener('load', function() {
			$("div.wrapper").css("display", "none");
		});
    </script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="ring">Loading
      <span class="ring_span"></span>
    </div>
<div class="wrapper">
