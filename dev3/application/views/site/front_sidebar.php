 <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar"> 
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar"> 
    <div class="user-panel">
        <div class="pull-left image"> 
			<img src="<?php echo generate_profile_image_url(); ?>" class="img-circle" alt="User Image" width="45" height="45">
			<!--
			<img src="<?php // echo $this->config->item('base_folder'); ?>public/img/user2-160x160.jpg" class="img-circle" alt="User Image">--> </div>
        <div class="pull-left info">
          <p><?php echo $this->session->userdata('username'); ?></p>
         </div>
      </div>
      <ul class="sidebar-menu">
        <li <?php echo ($this->router->fetch_class()=='site')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>dashboard"> <i class="menu-icon glyphicon glyphicon-home"></i> <span>Dashboard</span></a></li>
		
        <li <?php echo ($this->router->fetch_class()=='customer')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>customer"> <i class="menu-icon glyphicon glyphicon-list"></i> <span>Customers</span></a></li>
		
        <li <?php echo ($this->router->fetch_class()=='products')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>products"> <i class="menu-icon glyphicon  glyphicon-shopping-cart"></i> <span>Products</span></a></li>

        <li <?php echo ($this->router->fetch_class()=='quotation')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>quotation"> <i class="menu-icon glyphicon  glyphicon-gbp"></i> <span>Quotations</span></a></li>

        <li <?php echo ($this->router->fetch_class()=='tasks')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>tasks"> <i class="menu-icon glyphicon glyphicon-tasks"></i> <span>Tasks</span></a></li>
		
		<!-- Functionality and the style for the side sub-menu bar of Settings -->
		
		<?php 
		/* Functionality for the side sub-menu bar of Settings */
		$module = $this->router->fetch_module(); 
		$display = "style='display:none'";
		if("users"==$module || "logs"==$module || "branches"==$module) {
			$display = "style='display:block'";
		}
		?>
		<style>
		/* Style for the slide opening settings sub-menu */
		ul.sidebar-menu .sub li a {
			padding: 20px 0 20px 20px;
		}
		</style>

        <li <?php echo ($this->router->fetch_class()=='settings')?'class="active"':''; ?> onclick="togglesubmenu();"> <a href="javascript:void(0);"> <i class="menu-icon glyphicon glyphicon-cog"></i> <span>Settings</span></a>
			<ul class="sidebar-menu sub" <?php echo $display;?>>
				<li <?php echo ($this->router->fetch_class()=='users')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>users"> <i class="menu-icon glyphicon glyphicon-user"></i><span>Users</span></a></li>
				<?php if(isAdmin()) { ?>
				<li <?php echo ($this->router->fetch_class()=='branches')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>branches"> <i class="menu-icon glyphicon glyphicon-user"></i><span>Branches</span></a></li>
					<li <?php echo ($this->router->fetch_class()=='company')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>company/details"> <i class="menu-icon glyphicon glyphicon-user"></i><span>Company</span></a></li>
				<li <?php echo ($this->router->fetch_class()=='logs')?'class="active"':''; ?>> <a href="<?php echo base_url(); ?>logs"> <i class="menu-icon glyphicon glyphicon-calendar"></i> <span>System log</span></a></li>
			
				<?php } ?>
			</ul>
		</li>
		<!-- END Functionality and the style for the side sub-menu bar of Settings -->
		
      </ul>
    </section>
    <!-- /.sidebar --> 
  </aside>
  
