<?php 
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();
?>
<tr>
	<td><?=$result['account'];?></td>
	<td><a href='#'><?=$result['name'];?></a></td>";
	<td><?=$result['postcode'];?></td>
	<td><?=IS_NULL($result['qtymtd'])?0:$result['qtymtd'];?></td>
	<td><?=IS_NULL($result['salesmtd'])?0:$result['salesmtd'];?></td>
	<?php if($canSeeMargins) { ?>
		<td><?=$result['marginmtdpc'];?></td>
	<?php } ?>
	<td><?=$result['qtyytd'];?></td>
	<td><?=$result['salesytd'];?></td>
	<?php if($canSeeMargins) { ?>
		<td><?=$result['marginytdpc'];?></td>
	<?php } ?>
</tr>