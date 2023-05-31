<form id="newCustomerRepForm" name="newCustomerRepForm" method="post" onsubmit="return customerRepform(event);" enctype="multipart/form-data">
	<input type="hidden" id="account" name="account" value="<?= !!$account ? $account : 0; ?>">
	<div class="form-group">
		<label for="repcode">Repcode</label>
		<select class="form-control" id="repcode" name="repcode">
			<option value="">--Select a sales repcode --</option>
<?php
			foreach ($salesrep as $opt)
			{
?>
				<option value="<?= $opt['repcode']; ?>"><?= $opt['name']." (".$opt['repcode'].")" ;?></option>
<?php
			}
?>
		</select>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-info" value="submit">Submit</button>
	</div>
</form>