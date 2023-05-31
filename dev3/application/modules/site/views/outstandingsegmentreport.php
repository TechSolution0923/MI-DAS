<section class="content-header">
  <h1>Outstanding Orders ( <?php echo $headTitle; ?> )</h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active"><?php echo $headTitle; ?></li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table class="table table-bordered table-striped" id="segment-table">
            <thead>
              <tr>
                <th>Account</th>
                <th>Cust. Name</th>
                <th>Order No.</th>
                <th>Type</th>
                <th>Product Code</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Sales</th>
                <th>Rep</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($reportData as $segmentData) {
                ?>
                <tr class="<?php ("CR"!=$segmentData[3])?"nofill":"redrow" ?>" >
                  <td><?php echo $segmentData[0]; ?></td>
                  <td><?php echo $segmentData[1]; ?></td>
                  <td><?php echo $segmentData[2]; ?></td>
                  <td><?php echo $segmentData[3]; ?></td>
                  <td><?php echo $segmentData[4]; ?></td>
                  <td><?php echo $segmentData[5]; ?></td>
                  <td><?php echo $segmentData[6]; ?></td>
                  <td><?php echo $segmentData[7]; ?></td>
                  <td><?php echo $segmentData[8]; ?></td>
                  <td><?php echo $segmentData[9]; ?></td>
                  <td><?php echo $segmentData[10]; ?></td>
                </tr>
                <?php
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Account</th>
                <th>Cust. Name</th>
                <th>Order No.</th>
                <th>Type</th>
                <th>Product Code</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Sales</th>
                <th>Rep</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </tfoot>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  var base_url='<?php echo base_url(); ?>';
  $(document).ready(function() {
    $("#segment-table").DataTable({ 
      "order": [[2,"desc"]],
      dom: 'Bfrtip',
      buttons: [
      {
         extend: 'csv',text: ' <span title="Export" class="glyphicon glyphicon-export"></span>',title:"Outstanding Order",
      }
      ]
    });
  });

</script>