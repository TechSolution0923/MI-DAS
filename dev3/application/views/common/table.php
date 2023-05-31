<?php 
  /* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
  $canSeeMargins = canSeeMargins();
  $canEditNotes = canEditNotes();
  $canEditTerms = canEditTerms();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1> Customers  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Customers</li>
  </ol>
</section>
<?php echo form_open('customer/index'); ?>
<!-- Main content -->
<section class="content">
  <div class="row">
      <div class="col-xs-12">
        <div style="float:left;">
          <h2>Old data</h2>
          <pre>
            <?php print_r($resultold);?>
          </pre>
        </div>
        <div style="float:left;">
          <h2>New data</h2>
          <pre>
            <?php print_r($result);?>
          </pre>
        </div>
      </div>
    </div>
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
<?php echo form_close(); ?>