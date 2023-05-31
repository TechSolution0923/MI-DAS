<section class="content-header">
  <h1>Pac Sales Target</h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Pac sales target</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
        
        
        
      
       
            <div class="row">
                <div class="col-xs-12">
                       <table class="table table-bordered table-striped target-list-table target-listing" id="example">
                        <thead>                                          
                            <tr>
                               
                               <th>PAC</th>
                              
                                   <th>Description</th>
                                   <th>Sales MTD</th>
                                   <th>Target</th>
                                   <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                                                              
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
                               <?php if($pac1->paccode!=''){?>
                               <tr>
                                    
                                    <td><?php echo  $pac1->paccode; ?></td>
                                   
                                    <td><a href="<?= base_url(); ?>products/details2/<?php echo $pac1->tabl; ?>/<?=$pac1->paccode; ?> "><?php echo $pac1->description; ?></a></td>
                                    <td><?php echo  $getSalesTotalMonthWise[$pac1->paccode]; ?></td>
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
                               <th>Code</th>
                                
                         
                                   <th>Description</th>
                                   <th>Sales MTD</th>
                                   <th>Target</th>
                                   <th>Progress</th>
                          </tr>
                        </tfoot>
                      </table>
                    
                </div><!-- /.col -->
              </div><!-- /.row -->
           
         
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
      
  </div>
</section>

<script type="text/javascript">
  

$(document).ready(function() {
    $('#example').DataTable( {
        "PAC": [[ 3, "desc" ]]
    } );
} );

</script>