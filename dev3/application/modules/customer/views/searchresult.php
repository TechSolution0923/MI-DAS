<?php if(sizeof($searchresult)>0) { ?>
    <ul id="ui-id-a" tabindex="0" class="ui-menu ui-widget ui-widget-content ui-autocomplete ui-front">
        <?php for($i=0; $i<sizeof($searchresult); $i++) { ?>
            <li class="ui-menu-item ui-menu-item-wrapper noborder" tabindex="-1" onkeyup="selectOption(event);"><?php echo $searchresult[$i];?></li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <ul id="ui-id-a" tabindex="0" class="ui-menu ui-widget ui-widget-content ui-autocomplete ui-front">
        <li class="ui-menu-item ui-menu-item-wrapper noborder" tabindex="-1">No product code found!!</li>
    </ul>
<?php }?>