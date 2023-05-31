<?php
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();
?>
<!--footer start-->
<!--<footer class="main-footer">
		<?php echo date('Y'); ?> &copy; MI-DAS by <a href="http://www.kk-cs.co.uk" target="_blank">Kieran Kelly Consultancy Services Ltd.</a>
    </footer>-->

<!-- Control Sidebar -->
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<script>
    <?php if($canEditTerms) {?>
    var canEditTerms = true;
    <?php } else {?>
    var canEditTerms = false;
    <?php } ?>
</script>

<script>
    <?php if($canEditNotes) {?>
    var canEditNotes = true;
    <?php } else {?>
    var canEditNotes = false;
    <?php } ?>
</script>
<link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/colorbox.css" />
<script src="<?php echo $this->config->item('base_folder'); ?>public/jquery.colorbox.js"></script>
<script>
    $(document).ready(function(){
        //Examples of how to assign the Colorbox event to elements
        $(".iframe").colorbox({iframe:true, width:"100%", height:"100%"});

        //Example of preserving a JavaScript event for inline calls.
    });
</script>
<script>
    function hide_pop(url)
    {


        $.colorbox({width:"100%", height:"100%", iframe:true, href:url});
    }
</script>

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
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/jquery.alerts.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/quotation.js" type="text/javascript"></script>
<!-- FLOT CHARTS -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.time.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.symbol.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/jquery-ui.js"></script>



<div class="overlay"></div>
</body>
</html>

