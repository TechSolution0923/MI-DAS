<?php 
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<div class="nav-tabs-custom left-tab">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation"  class="active"><a href="#proTDetail" role="tab" data-toggle="tab" aria-expanded="false">Detail</a></li>
		<li role="presentation" class=""><a href="#proTText" role="tab" data-toggle="tab" aria-expanded="false">Text</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="proTDetail">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-10">
					<form class="form-horizontal">
						<div class="form-group">
							<label for="AccountCode" class="col-sm-4 col-md-2 control-label">Account Code</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $account; ?>" readonly>
							</div>
						</div>
						<div class="form-group">
							<label for="CustomerName" class="col-sm-4 col-md-2 control-label">Name</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $customername; ?>" readonly>
							</div>
							<label for="DeliveryLocation" class="col-sm-4 col-md-2 control-label">Delivery</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $dellocn; ?>" readonly>
							</div>
						</div>
						<div class="form-group">
							<label for="Address" class="col-sm-4 col-md-2 control-label">Address</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $address1; ?>" readonly>
								<input type="text" class="form-control" id="input-readonly" value="<?= $address2; ?>" readonly>
								<input type="text" class="form-control" id="input-readonly" value="<?= $address3; ?>" readonly>
								<input type="text" class="form-control" id="input-readonly" value="<?= $address4; ?>" readonly>
								<input type="text" class="form-control" id="input-readonly" value="<?= $address5; ?>" readonly>
								<input type="text" class="form-control" id="input-readonly" value="<?= $postcode; ?>" readonly>
							</div>

							<label for="SalesRep" class="col-sm-4 col-md-2 control-label hidden">Sales Rep</label>
							<div class="col-sm-8 col-md-4 hidden">
								<input type="text" class="form-control" id="input-readonly" value="<?= $salesrep; ?>" readonly>
							</div>

							<label for="userdef1" class="col-sm-4 col-md-2 control-label">User Defined #1</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" id="userdef1" class="form-control" id="input-readonly" value="<?= $userdef1; ?>" readonly>
							</div>

							<label for="userdef2" class="col-sm-4 col-md-2 control-label">User Defined #2</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" id="userdef2" class="form-control" id="input-readonly" value="<?= $userdef2; ?>" readonly>
							</div>

							<label for="userdef3" class="col-sm-4 col-md-2 control-label">User Defined #3</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" id="userdef3" class="form-control" id="input-readonly" value="<?= $userdef3; ?>" readonly>
							</div>

							<label for="currency" class="col-sm-4 col-md-2 control-label" style="margin-top: 14px;">Currency</label>
							<div class="col-sm-8 col-md-4" style="margin-top: 14px;">
								<input type="text" id="currency" class="form-control" id="input-readonly" value="<?= $currency; ?>" readonly>
							</div>
						</div>
						<div class="form-group">
							<label for="Phone" class="col-sm-4 col-md-2 control-label">Phone</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $phone; ?>" readonly>
							</div>
						</div>
						<div class="form-group">
							<label for="Fax" class="col-sm-4 col-md-2 control-label">Fax</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $fax; ?>" readonly>
							</div>
						</div>
						<div class="form-group">
							<label for="Email" class="col-sm-4 col-md-2 control-label">Email</label>
							<div class="col-sm-8 col-md-4">
								<input type="text" class="form-control" id="input-readonly" value="<?= $email; ?>" readonly>
							</div>
						</div>
					</form>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.tab-pane -->

		<div class="tab-pane" id="proTText">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-10">
					<form id="frm_custinternaltext" class="form-horizontal">
						<div class="form-group">
							<label for="internaltext" class="col-sm-4 col-md-2 control-label">Internal</label>
							<div class="col-sm-8 col-md-8">
								<div class="form-group">
									<a xeditable href="javascript:;" id="internaltext" data-type="textarea" data-name="internaltext" data-ng-model="internaltext" e-onChange="alert('I am Fired')" data-placement="left" data-placeholder="Required" data-showbuttons="true" data-original-title="Internal text" class="editable editable-click" style="display: inline; padding: 10px; width: 80%;"><?= (count(trim($internaltext)) > 0) ? $internaltext : 'Empty'; ?></a>
								</div>
							</div>
						</div>
					</form>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.tab-pane -->
	</div><!-- /.tab-content -->
</div>
