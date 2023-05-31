<form id="newTaskForm" name="newTaskForm" method="post" onsubmit="return taskform(event);" enctype="multipart/form-data">
<input type="hidden" id="taskid" name="taskid" value="<?php echo !!$t_taskid?$t_taskid:0;?>">
  <div class="form-group">
    <label for="account">Account</label>
    <?php if(!empty($new) && "1"==$new) {?>
    <input type="text" class="form-control" id="account_selector" name="account_selector" value="<?php echo !empty($t_account)?$t_account:""; ?>" <?php echo !empty($t_account)?"readonly":""; ?> disabled="true" >
    <span id="Suggestions"></span>
    <?php } else {?>
    <?php if(empty($t_account)) {?>
      <input type="text" class="form-control" id="account_selector" name="account_selector" value="<?php echo !empty($t_account)?$t_account:""; ?>" <?php echo !empty($t_account)?"readonly":""; ?> disabled="true" >
      <span id="Suggestions"></span>
    <?php } else {?>
    <span class="form-control"><?php echo $c_name;?></span>
    <?php }?>
    <?php }?>
    <input type="hidden" id="account" name="account" value="<?php echo !empty($t_account)?$t_account:""; ?>" <?php echo !empty($t_account)?"readonly":""; ?> >
  </div>
  <div class="form-group hidden">
    <label for="userid">User ID</label>
    <input type="hidden" class="form-control" id="userid" name="userid" value="<?php echo $userId;?>">
  </div>
  <div class="form-group">
    <label for="contactno">Contact no.</label>
    <?php echo $contactno_input;?>
  </div>
  <div class="form-group">
    <label for="date">Date</label>
    <input type="date" class="form-control" id="date" name="date" placeholder="" value="<?php echo !empty($t_date)?$t_date:""; ?>">
  </div>
  <div class="form-group">
    <label for="completed">Completed </label>
    <input type="checkbox" id="completed" name="completed" value="<?php echo intval($t_complete); ?>" 
      <?php echo (!empty($t_complete) && "0"!=($t_complete))?"checked ":""; ?> />

      <script>
      <?php if($t_complete) {?>
        checkthecheckbox(true);
     <?php } else {?>
        checkthecheckbox(false);
     <?php }?>
      </script>
  </div>
  <div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3"><?php echo !empty($t_description)?stripslashes($t_description):""; ?></textarea>
  </div>
  <div class="form-group">
    <label for="notes">Notes</label>
    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo !empty($t_notes)?stripslashes($t_notes):""; ?></textarea>
  </div>
  <?php $imageListLabel = "";
  if(!empty($edit) && "1"==$edit) {
    $imageListLabel = "View / Edit Uploaded files";  
  ?>
    <?php  if(!empty($new) && "1"==$new) {?>
      <div class="form-group">
        <label for="documents">Upload document</label>
        <input type="file" class="form-control-file" id="documents" name="documents">
        <span class="bg-warning">Allowed file types are .gif, .jpg, .png, .pdf, .doc, .docx, .xls and .xlsx</span>
      </div>
    <?php } ?>
  <div class="form-group">
    <button type="submit" class="btn btn-info" value="submit">Submit</button>
  </div>
  <?php } else {
    $imageListLabel = "View Uploaded files";  
  }?>

</form>
<?php  if(empty($new) || "0"==$new) {?>
<a href="javascript:goToUploadedFiles(<?php echo $edit;?>, <?php echo $t_taskid;?>);" class="next"><?php echo $imageListLabel;?> &raquo;</a>
<?php } ?>