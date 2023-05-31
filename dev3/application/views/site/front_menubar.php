
<header class="main-header top-menu"> 
    
    <!-- Logo --> 
    <a href="<?php echo base_url(); ?>" class="logo"> 
    <!-- mini logo for sidebar mini 50x50 pixels --> 
    <span class="logo-mini">M</span> 
    <!-- logo for regular state and mobile devices --> 
    <span class="logo-lg">MI-DAS</span> </a> 
    
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation"> 
      <!-- Sidebar toggle button--> 
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> </a> 
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu pull-left nav-cus">
        <ul class="nav navbar-nav">

          
		  <!-- Drop down for the branches. Loading through Ajax -->
		  <li class="dropdown onlyforall"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span id="selected_branch">Loading...</span><span class="caret"></span></a>
            <ul class="dropdown-menu" id="branch_menu"></ul>
          </li>
		  
		  <!-- Drop down for the users. Loading through Ajax -->
		  <li class="dropdown onlyforall"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span id="selected_user">Loading...</span><span class="caret"></span></a>
            <ul class="dropdown-menu" id="user_menu" style="height:269px;overflow-y:scroll;"></ul>
          </li>
		  
		  <!-- Removing the Sales rep box from the top as instructed. -->
          <!-- <li><a href="#"><i class="fa fa-sign-out m-r-xs"></i> Sales Rep</a></li> -->
          
        </ul>
      </div>
      <div class="navbar-custom-menu pull-right">
        <ul class="nav navbar-nav">
			<!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				  
				  <img src="<?php echo generate_profile_image_url(); ?>" class="user-image" alt="User Image">
                <!--  <img src="<?php echo $this->config->item('base_folder'); ?>public/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                  <span class="hidden-xs"><?php echo $this->session->userdata('username'); ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- Menu Footer-->
                  <li><a href="<?php echo base_url(); ?>site/logout"><i class="fa fa-sign-out m-r-xs"></i> Sign out</a> </li>
                </ul>
              </li>
        </ul>
      </div>
    </nav>
  </header>
  
