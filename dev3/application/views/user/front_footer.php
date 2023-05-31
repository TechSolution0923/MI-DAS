<!--footer start-->
	<footer class="main-footer"> 
		<?php echo date('Y'); ?> &copy; MI-DAS by <a href="http://www.kk-cs.co.uk" target="_blank">Kieran Kelly Consultancy Services Ltd.</a>
    </footer>
  
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark"> 
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content"> 
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li> <a href="javascript::;">
            <h4 class="control-sidebar-subheading"> Custom Template Design <span class="label label-danger pull-right">70%</span> </h4>
            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
            </div>
            </a> </li>
          <li> <a href="javascript::;">
            <h4 class="control-sidebar-subheading"> Update Resume <span class="label label-success pull-right">95%</span> </h4>
            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-success" style="width: 95%"></div>
            </div>
            </a> </li>
          <li> <a href="javascript::;">
            <h4 class="control-sidebar-subheading"> Laravel Integration <span class="label label-warning pull-right">50%</span> </h4>
            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
            </div>
            </a> </li>
          <li> <a href="javascript::;">
            <h4 class="control-sidebar-subheading"> Back End Framework <span class="label label-primary pull-right">68%</span> </h4>
            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
            </div>
            </a> </li>
        </ul>
        <!-- /.control-sidebar-menu --> 
        
      </div>
      <!-- /.tab-pane --> 
      
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>
          <div class="form-group">
            <label class="control-sidebar-subheading"> Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>
            <p> Some information about this general settings option </p>
          </div>
          <!-- /.form-group -->
          
          <div class="form-group">
            <label class="control-sidebar-subheading"> Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>
            <p> Other sets of options are available </p>
          </div>
          <!-- /.form-group -->
          
          <div class="form-group">
            <label class="control-sidebar-subheading"> Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>
            <p> Allow the user to show his name in blog posts </p>
          </div>
          <!-- /.form-group -->
          
          <h3 class="control-sidebar-heading">Chat Settings</h3>
          <div class="form-group">
            <label class="control-sidebar-subheading"> Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->
          
          <div class="form-group">
            <label class="control-sidebar-subheading"> Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->
          
          <div class="form-group">
            <label class="control-sidebar-subheading"> Delete chat history <a href="javascript::;" class="text-red pull-right"><i class="fa fa-trash-o"></i></a> </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane --> 
    </div>
  </aside>
  <!-- /.control-sidebar --> 
  <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper --> 

<!-- FastClick --> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.min.js"></script> 
<!-- AdminLTE App --> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/app.min.js"></script> 
<!-- DataTables -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/datatables/datatables.min.js"></script>
<!-- SlimScroll 1.3.0 --> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/slimScroll/jquery.slimscroll.min.js"></script> 
<!-- AdminLTE for demo purposes --> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/demo.js"></script> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/bootstrap-switch.js"></script> 
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/pace-master/pace.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>application/modules/users/js/jquery.alerts.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/customer.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>application/modules/users/js/users.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>application/modules/users/js/jquery.uploadfile.min.js" type="text/javascript"></script>
<!-- FLOT CHARTS -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.time.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.symbol.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.tooltip.min.js"></script>
<div class="overlay"></div>
</body>
</html>

