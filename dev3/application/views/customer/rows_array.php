<?php 
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();
foreach($result as $cdata) { ?>
	<tr>
		<td><?=$cdata['account'];?></td>
		<td><a href='#'><?=$cdata['name'];?></a></td>";
		<td><?=$cdata['postcode'];?></td>
		<td><?=IS_NULL($cdata['qtymtd'])?0:$cdata['qtymtd'];?></td>
		<td><?=IS_NULL($cdata['salesmtd'])?0:$cdata['salesmtd'];?></td>
		<?php if($canSeeMargins) { ?>
			<td><?=IS_NULL($cdata['marginmtdpc'])?0:$cdata['marginmtdpc'];?></td>
		<?php } ?>
		<td><?=IS_NULL($cdata['qtyytd'])?0:$cdata['qtyytd'];?></td>
		<td><?=IS_NULL($cdata['salesytd'])?0:$cdata['salesytd'];?></td>
		<?php if($canSeeMargins) { ?>
			<td><?=IS_NULL($cdata['marginytdpc'])?0:$cdata['marginytdpc'];?></td>
		<?php } ?>
	</tr>
 <?php }?>