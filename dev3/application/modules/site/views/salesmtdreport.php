<section class="content-header">
  <h1>Sales MTD Report</h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Sales MTD Report</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table class="table table-bordered table-striped" id="salesmtd-table">
            <thead>
              <tr>
                <th>Account</th>
                <th>Cust. Name</th>
                <th>Order No.</th>
                <th>Order type</th>
                <th>Product Code</th>
                <th>Product</th>
                <th>Rep</th>
                <th>Quantity</th>
                <th>Sales</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
            <tfoot>
              <tr>
                <th>Account</th>
                <th>Cust. Name</th>
                <th>Order No.</th>
                <th>Order type</th>
                <th>Product Code</th>
                <th>Product</th>
                <th>Rep</th>
                <th>Quantity</th>
                <th>Sales</th>
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
    $("#salesmtd-table").DataTable({ 
      "processing": true,
      "serverSide": true,
      "order": [[ 7, "asc" ]],
      "ajax" : {
        url : base_url+"site/ajaxsalesmtdreport",
        type: "post",
        complete: function(){ 
          $(".dataTables_processing").css("display","none");
          colorCrRed("#salesmtd-table");
        }
      },
      dom: 'Bfrtip',
      buttons: [
      {
        text: '<span title="Export" class="glyphicon glyphicon-export"></span>',
        action: function ( e, dt, node, config ) {
          var ValuesOfSearch = getValuesOfSearch();
          var urltogo = base_url+'site/salesmtd_excel_exportcustom/'+ValuesOfSearch;
          document.location.href = urltogo;
        }
      }
      ]
    });
  });

  var getValuesOfSearch = function() {
    var data = ''; 
    var qlength = $('input[type="text"], input[type="search"]').length;
    var iteration_watch = 1;
    $('input[type="text"], input[type="search"]').each(function(key,val) {
      var ks = $(this).val();
      if(""!=ks) {} else {
        ks="nosearchedvalue";
      }
      if(iteration_watch<qlength) {
        data += ks+'/';
      }else {
        data += ks;
      }  
      iteration_watch++;
    });
    return data;
  }

</script>