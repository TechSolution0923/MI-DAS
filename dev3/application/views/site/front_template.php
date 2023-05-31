<?php
	$this->load->view('site/front_header');
	$this->load->view('site/front_menubar');
	$this->load->view('site/front_sidebar');
?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
<?php
		$this->load->view($main_content, $template);
?>
	</div><!-- /.content-wrapper -->
<?php

$this->load->view('site/front_footer');
	?>
