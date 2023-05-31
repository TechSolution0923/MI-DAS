<section class="content-header">
  <h1>Sales MTD Report</h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Pac sales target</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
        
        
        
        
        <div class="tab-pane" id="proCustomers">
            <div class="row">
                <div class="col-xs-12">
                      <table id="new-product-customers" class="table table-bordered table-striped">
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
                               
                               $pac_progress= $pac->salestarget*100/$pac->salesmtd;
                               if($pac_progress==''||$pac_progress<=30)
                               {
                                  $pac_progress=0; 
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
                               
                               <tr>
                                    <td><?php echo  $pac1->code; ?></td>
                                    <td><?php echo $pac1->description; ?></td>
                                    <td><?php echo $pac1->salesmtd; ?></td>
                                    <td><?php  echo $pac1->salestarget; ?></td>
                                    <td>
                                    <div class="progress">
                                    <div class="progress-bar progress-bar-<?= $class; ?>" style="width: <?= $pac_progress; ?>%;">
                                    <?= $pac_progress; ?>%
                                    </div>
                                    </div>
                                    </td>
                               </tr>
                               
                               <?php } ?>
                               
                                
                               <?php  foreach($pac2salestarget as $pac2){
                               
                               $pac_progress2= $pac2->salestarget*100/$pac2->salesmtd;
                               if($pac_progress2==''||$pac_progress2<=30)
                               {
                                  $pac_progress2=0; 
                                  $class2="danger";
                               }
                               elseif($pac_progress2<=30 )
                               {
                                   $class2="danger";
                               }
                               elseif($pac_progress2>30 &&  $pac_progress2<=60)
                               {
                                   $class2="warning";
                               }
                               else
                               
                               {
                                   $class2="success";
                               }
                               
                               ?>
                               
                               <tr>
                                    <td><?php echo  $pac2->code; ?></td>
                                    <td><?php echo $pac2->description; ?></td>
                                    <td><?php echo $pac2->salesmtd; ?></td>
                                    <td><?php  echo $pac2->salestarget; ?></td>
                                    <td>
                                    <div class="progress">
                                    <div class="progress-bar progress-bar-<?= $class2; ?>" style="width:  <?= $pac_progress2; ?>%;">
                                    <?= $pac_progress2; ?>%
                                    </div>
                                    </div>
                                    </td>
                               </tr>
                               
                               <?php } ?>
                               
                               
                               
                               
                                <?php  foreach($pac3salestarget as $pac3){
                               
                               $pac_progress3= $pac3->salestarget*100/$pac3->salesmtd;
                               if($pac_progress3==''||$pac_progress3<=30)
                               {
                                  $pac_progress3=0; 
                                  $class3="danger";
                               }
                               elseif($pac_progress3<=30 )
                               {
                                   $class3="danger";
                               }
                               elseif($pac_progress3>30 &&  $pac_progress3<=60)
                               {
                                   $class3="warning";
                               }
                               else
                               
                               {
                                   $class3="success";
                               }
                               
                               ?>
                               
                               <tr>
                                    <td><?php echo  $pac3->code; ?></td>
                                    <td><?php echo $pac3->description; ?></td>
                                    <td><?php echo $pac3->salesmtd; ?></td>
                                    <td><?php  echo $pac3->salestarget; ?></td>
                                    <td>
                                    <div class="progress">
                                    <div class="progress-bar progress-bar-<?= $class2; ?>" style="width:  <?= $pac_progress3; ?>%;">
                                    <?= $pac_progress3; ?>%
                                    </div>
                                    </div>
                                    </td>
                               </tr>
                               
                               <?php } ?>
                               
                               
                               
                               
                               
                               
                               
                               
                               
                               
                               
                               
                                <?php  foreach($pac4salestarget as $pac4){
                               
                               $pac_progress4= $pac4->salestarget*100/$pac4->salesmtd;
                               if($pac_progress4==''||$pac_progress4<=30)
                               {
                                  $pac_progress4=0; 
                                  $class4="danger";
                               }
                               elseif($pac_progress4<=30 )
                               {
                                   $class4="danger";
                               }
                               elseif($pac_progress4>30 &&  $pac_progress4<=60)
                               {
                                   $class4="warning";
                               }
                               else
                               
                               {
                                   $class4="success";
                               }
                               
                               ?>
                               
                               <tr>
                                    <td><?php echo  $pac4->code; ?></td>
                                    <td><?php echo $pac4->description; ?></td>
                                    <td><?php echo $pac4->salesmtd; ?></td>
                                    <td><?php  echo $pac4->salestarget; ?></td>
                                    <td>
                                    <div class="progress">
                                    <div class="progress-bar progress-bar-<?= $class3; ?>" style="width:  <?= $pac_progress3; ?>%;">
                                    <?= $pac_progress3; ?>%
                                    </div>
                                    </div>
                                    </td>
                               </tr>
                               
                               <?php } ?>
                               
                               
                              
                           </tbody>
                        
                        <tfoot>
                          <tr>
                           <th>PAC</th>
                                   <th>Description</th>
                                   <th>Sales MTD</th>
                                   <th>Target</th>
                                   <th>Progress</th>
                          </tr>
                        </tfoot>
                      </table>
                    
                </div><!-- /.col -->
              </div><!-- /.row -->
           
          </div><!-- /.tab-pane -->
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
      
  </div>
</section>

