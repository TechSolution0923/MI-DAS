<?php 
  /* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
  $canSeeMargins = canSeeMargins();
  $canEditNotes = canEditNotes();
  $canEditTerms = canEditTerms();
?>
<div class="container">
	<div class="row">
  <?php echo form_open('customer/index', array("role"=>"form")); ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4 hidden">
            <label for="exampleInputEmail1">Name</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Name" disabled value="<?php echo $contactDetail["title"]." ".$contactDetail["firstname"]." ".$contactDetail["surname"]; ?>">
        </div>
        <div class="clearfix hidden"></div>
        <div class="clearfix"></div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Address 1" disabled value="<?php echo $contactDetail["address1"]; ?>">
            <span><?php echo $contactDetail["address1"]; ?></span>
        </div>
        <?php if(""!=trim($contactDetail["address2"])) { ?>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Address 2" disabled value="<?php echo $contactDetail["address2"]; ?>">
            <span><?php echo $contactDetail["address2"]; ?></span>
        </div>
        <?php if(""!=trim($contactDetail["address3"])) { ?>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Address 3" disabled value="<?php echo $contactDetail["address3"]; ?>">
            <span><?php echo $contactDetail["address3"]; ?></span>
        </div>
        <?php if(""!=trim($contactDetail["address4"])) { ?>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Address 4" disabled value="<?php echo $contactDetail["address4"]; ?>">
            <span><?php echo $contactDetail["address4"]; ?></span>
        </div>
        <?php if(""!=trim($contactDetail["address5"])) { ?>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Address 5" disabled value="<?php echo $contactDetail["address5"]; ?>">
            <span><?php echo $contactDetail["address5"]; ?></span>
        </div>
        <?php if(""!=trim($contactDetail["postcode"])) { ?>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Postcode" disabled value="<?php echo $contactDetail["postcode"]; ?>">
            <span><?php echo $contactDetail["postcode"]; ?></span>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1">Contact Type</label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Contact Type" disabled value="<?php echo $contactDetail["contacttype"]; ?>">
            <span><?php echo $contactDetail["contacttype"]?$contactDetail["contacttype"]:"Not mentioned"; ?></span>
        </div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1">Job Title</label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="Job Title" disabled value="<?php echo $contactDetail["jobtitle"]; ?>">
            <span><?php echo $contactDetail["jobtitle"]?$contactDetail["jobtitle"]:"Not mentioned"; ?></span>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-10 col-sm-10 col-md-4 col-lg-4">
            <label>
                <input type="checkbox" disabled selected="<?php echo $contactDetail["sensitivecontact"];?>"> Sensitive
            </label>
        </div>
        <div class="col-xs-10 col-sm-10 col-md-4 col-lg-4">
            <label>
                <input type="checkbox" disabled selected="<?php echo $contactDetail["donotcommunicate"];?>"> Do not contact
            </label>
        </div>

        <div class="clearfix"></div>
        <h2>Phone numbers</h2>
        
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["phone1desc"];?></label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="<?php echo $contactDetail["phone1desc"];?>" disabled value="<?php echo $contactDetail["phone1no"]; ?>">
            <div class="clearfix"></div>
            <a href="tel:<?php echo $contactDetail["phone1no"];?>"><?php echo $contactDetail["phone1no"]; ?></a>
        </div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["phone2desc"];?></label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="<?php echo $contactDetail["phone2desc"];?>" disabled value="<?php echo $contactDetail["phone2no"]; ?>">
            <div class="clearfix"></div>
            <a href="tel:<?php echo $contactDetail["phone2no"];?>"><?php echo $contactDetail["phone2no"]; ?></a>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["phone3desc"];?></label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="<?php echo $contactDetail["phone3desc"];?>" disabled value="<?php echo $contactDetail["phone3no"]; ?>">
            <div class="clearfix"></div>
            <a href="tel:<?php echo $contactDetail["phone3no"];?>"><?php echo $contactDetail["phone3no"]; ?></a>
        </div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["phone4desc"];?></label>
            <input type="text" class="form-control hidden"  id="exampleInputPassword1" placeholder="<?php echo $contactDetail["phone4desc"];?>" disabled value="<?php echo $contactDetail["phone4no"]; ?>">
            <div class="clearfix"></div>
            <a href="tel:<?php echo $contactDetail["phone4no"];?>"><?php echo $contactDetail["phone4no"]; ?></a>
        </div>



        <div class="clearfix"></div>
        <h2>Email address</h2>
        
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["email1desc"];?></label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="<?php echo $contactDetail["email1desc"];?>" disabled value="<?php echo $contactDetail["emailaddress1"]; ?>">
            <div class="clearfix"></div>
            <a href="mailto:<?php echo $contactDetail["emailaddress1"];?>"><?php echo $contactDetail["emailaddress1"]; ?></a>
        </div>
        <div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
            <label for="exampleInputPassword1"><?php echo $contactDetail["email2desc"];?></label>
            <input type="text" class="form-control hidden" id="exampleInputPassword1" placeholder="<?php echo $contactDetail["email2desc"];?>" disabled value="<?php echo $contactDetail["emailaddress2"]; ?>">
            <div class="clearfix"></div>
            <a href="mailto:<?php echo $contactDetail["emailaddress2"];?>"><?php echo $contactDetail["emailaddress2"]; ?></a>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-10 col-sm-4 col-md-4 col-lg-4 hidden">
            <button type="submit" class="btn btn-default">Submit</button>
        </div>
        <?php echo form_close(); ?>
    <div class="clearfix"></div>
    <br /><br />
	</div>
</div>
[ONLYNAME]
<?php echo $contactDetail["title"]." ".$contactDetail["firstname"]." ".$contactDetail["surname"]; ?>
