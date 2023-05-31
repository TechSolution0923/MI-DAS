<?php 
foreach($options as $key=>$opt) { 
  $value = $opt["value"];
  $name = "";
  if(!empty($opt["title"])) {
    $name .= $opt["title"].". ";
  }
  if(!empty($opt["firstname"])) {
    $name .= $opt["firstname"]." ";
  }
  if(!empty($opt["surname"])) {
    $name .= $opt["surname"];
  }

  if(""==$name) {
    $name = "NONE";
  }
  
  if($value==$selected) {?>
  <option value="<?php echo $value;?>" selected><?php echo $name;?></option>
  <?php } else { ?>
  <option value="<?php echo $value;?>"><?php echo $name;?></option>
  <?php } ?>
<?php }?>