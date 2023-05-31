<?php if(!empty($edit) && "1"==$edit) {?>
<form id="newUploadForm" name="newUploadForm" method="post" onsubmit="return uploadform(event, <?php echo $taskid;?>);" enctype="multipart/form-data">
    <div class="form-group">
        <label for="documents">Upload document</label>
        <input type="file" class="form-control-file" id="documents" name="documents">
        <span class="bg-warning">Allowed file types are .gif, .jpg, .png, .pdf, .doc, .docx, .xls and .xlsx</span>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-info" value="upload">Upload</button>
    </div>
</form>
<?php } ?>
<section>
<?php 
  if(sizeof($uploads) >0) {?>
    <h2>Uploaded documents for task id # <?php echo $taskid;?></h2>
    <ul class="list-group">
      <?php foreach($uploads as $k=>$doc) {?>
      <li id="<?php echo $taskid?>_<?php echo $k;?>" class="list-group-item"><a href="<?php echo base_url();?>tasks/documents/<?php echo $taskid;?>/<?php echo $doc;?>" target="_blank"><?php echo $doc;?></a> 
      <?php if(!empty($edit) && "1"==$edit) {?>
        <button class="close delete-file-button" aria-label="Close" onclick="return deleteConfirmation('<?php echo $taskid;?>', '<?php echo $doc;?>', '<?php echo $taskid?>_<?php echo $k;?>', event);">
          <span aria-hidden="true">&times;</span>
        </button>
        <?php } ?>
      </li>
      <?php }?>
    </ul>
  <?php  } else {?>
    <h2>There are no uploaded documents for task id # <?php echo $taskid;?></h2>
  <?php }?>
</section>