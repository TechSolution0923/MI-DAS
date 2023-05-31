<section class="content-header">
  <h1>Customer Sales Target for <?php echo $account;?></h1>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                       <table class="table table-bordered table-striped target-list-table target-listing" id="example">
                        <thead>                                          
                            <tr>
                                <th>Type</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Sales MTD</th>
                                <th>Sales YTD</th>
                                <th>Sales <?php echo intval(date("Y"))-1;?></th>
                                <th>Sales <?php echo intval(date("Y"))-2;?></th>
                                <th>Target</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody id="salestablebody">
                                                              
                               <?php  foreach($pac1salestarget as $pac1){
                               $pac_progress= round($getSalesTotalMonthWise[$pac1->paccode]*100/$pac1->salestarget,2);
                               if($pac1->salestarget=="")
                               {
                                   $pac1->salestarget=0;
                               }
                                if($getSalesTotalMonthWise[$pac1->paccode]=="")
                               {
                                  $getSalesTotalMonthWise[$pac1->paccode]=0;
                               }
                               if($pac_progress=="")
                               {
                                   $pac_progress=0; 
                               }
                               
                               if($pac_progress==""||$pac_progress<=30)
                               {
                                  
                                  $class="danger";
                               }
                               elseif($pac_progress<=30 )
                               {
                                   $class="danger";
                               }
                               elseif($pac_progress>30 &&  $pac_progress<=60)
                               {
                                   $class="warning";
                               }
                               else
                               
                               {
                                   $class="success";
                               }
                               
                               
                               ?>
                               <?php if($pac1->paccode!=''){ ?>
                               <tr class="
                               <?php if($pac1->ytd > $pac1->ytp){ ?>
                                    greenrow
                               <?php } else if($pac1->ytd < $pac1->ytp){ ?>
                               redrow
                               <?php } ?>"
                               >
                                    
                                <td><?php echo  '5'!=$pac1->tabl?$pac1->tabl:'P'; ?></td>
                                <td><?php echo  $pac1->paccode; ?></td>
                                   
                                <td><?php if($pac1->paccode) { ?><a href="<?= base_url(); ?>products/details2/<?php echo $pac1->tabl; ?>/<?=$pac1->paccode; ?> "><?php echo $pac1->description; ?></a><?php } else {
                                echo $pac1->description;
                               }?></td>
                               <td><?php echo  $getSalesTotalMonthWise[$pac1->paccode]; ?></td>
                                <td><?php echo  $pac1->ytd; ?></td>
                                <td><?php echo  $pac1->ytp; ?></td>
                                <td><?php echo  $pac1->ytpp; ?></td>
                                <td><?php  echo $pac1->salestarget; ?></td>
                                <td>
                                        
                                    <div class="progress" style="height:5px;">
                                    <div class="progress-bar progress-bar-<?= $class; ?>" style="width:<?= $pac_progress; ?>% !important;"></div>
                                    </div>
                                    <span class="progress-description">
                                      <?= $pac_progress; ?>% 
                                    </span>
                                        
                                    <!--<div class="progress">-->
                                    <!--<div class="progress-bar progress-bar-<?= $class; ?>" style="width: <?= $pac_progress; ?>%;">-->
                                    <!--<?= $pac_progress; ?>%-->
                                    <!--</div>-->
                                    <!--</div>-->
                                    </td>
                               </tr>
                               <?php } ?>
                               
                               <?php } ?>
                           </tbody>
                        
                        <tfoot>
                          <tr>
                                <th>Type</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Sales MTD</th>
                                <th>Sales YTD</th>
                                <th>Sales <?php echo intval(date("Y"))-1;?></th>
                                <th>Sales <?php echo intval(date("Y"))-2;?></th>
                                <th>Target</th>
                                <th>Progress</th>
                          </tr>
                        </tfoot>
                      </table>
                    
                </div><!-- /.col -->
              </div><!-- /.row -->
           
         
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
      
  </div>
</section>
<?php //print_r($result);?>
<script type="text/javascript">
  

$(document).ready(function() {
    $('#example').DataTable({
      "PAC": [[ 3, "desc" ]],
		  dom: 'Bfrtip',
		  buttons:[{
				text: '<i class="fa fa-download" aria-hidden="true"></i>',
				action: function ( e, dt, node, config ) {
					var ValuesOfSearch = (function () {
            return $("#example_filter > label > input").val();
          })();
          urltogo = base_url+"customer/excel_export_sales_target/"+account+'/'+ValuesOfSearch;
					document.location.href = urltogo;
				}
			}]
    });
});
</script>