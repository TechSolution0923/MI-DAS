<!-- Content Header (Page header) -->
<style>
.transform-link {
    float: left;
    border: 0;
    color: #0088cc;
    background: transparent;
    cursor: pointer;
    padding: 0 13px;
}
</style>
<section class="content-header">
    <h1> Tasks </h1>
    <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Tasks</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">		  
    <?php echo form_open('tasks/details');?>
    <input type="hidden" name="userid" value="0" />
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary task-list-selectors active" data-overdue="true" onclick="onActivate(event, null, false);">
            <input type="radio" name="options" id="option1" autocomplete="off" checked> Overdue Tasks
        </label>
        <label class="btn btn-secondary task-list-selectors" data-overdue="false" onclick="onActivate(event, null, true);">
            <input type="radio" name="options" id="option2" autocomplete="off"> All Tasks
        </label>
    </div>
    <button type="submit" name="addnew" class="btn btn-success pull-right bottom5" onclick="return openModal('#addTaskModal', 'newTask');"><i class="fa fa-fw fa-plus-circle"></i>Add task</button>
    
    <?php echo form_close();?>
    <?php echo form_open('tasks/index');?>
        <div class="row">
        <div class="col-xs-12">
            <div class="box">
            <div class="box-body">
                <div class="alert alert-success alert-dismissible hidden" id="statusAndMessage">
                    <a href="#" class="close" aria-label="close" onclick="closemessage();">&times;</a>
                    <strong id="status">Success!</strong> <span id="statusMessage">Indicates a successful or positive action.</span>
                </div>
                
                <table class="table table-bordered table-striped" id="tasksTable">
                    <?php 
                    $headers = 
                    '<tr id="taskrow">
                        <th>Task ID</th>
                        <th>Contact name</th>
                        <th>Account</th>
                        <th>Acc. Name</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Completed</th>
                        <th width="100">Actions</th>
                    </tr>';
                    ?>
                <thead class="table_head">                                          
                    <?php echo $headers; ?>
                </thead>
                <tbody>
                </tbody>
                <tfoot class="table_head">
                    <?php echo $headers; ?>
                </tfoot>
                </table>
            </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
        </div><!-- /.row -->
    <?php echo form_close();?>
    </section><!-- /.content -->
<script>
var isAdmin = <?php echo $isAdmin;?>;
var loggedinuserid = '<?php echo $this->session->userdata('userid');?>';
</script>


<!--Modal HTML-->
<div class="modal fade bd-example-modal-lg" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <a href="javascript:hidePreviousBtn();" class="btn btn-info btn-sm previous hidden" id="backToDetails" data-taskid="" data-edit="">&raquo; Task Details</a>
        <h4 class="modal-title" id="myLargeModalLabel">...</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>