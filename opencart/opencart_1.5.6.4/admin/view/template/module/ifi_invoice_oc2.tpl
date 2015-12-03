<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a onclick="$('#ifirma-form').submit();" name="<?php echo $submit_name; ?>" class="btn btn-primary" title="" data-toggle="tooltip" data-original-title="<?php echo $save ?>"><i class="fa fa-floppy-o"></i></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="btn btn-default" title="" data-toggle="tooltip" data-original-title="<?php echo $button_cancel; ?>"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning_info ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<div class="alert alert-info"><i class="fa fa-info-circle"></i> Blog ifirma.pl - <a target="_blank" href="http://blog.ifirma.pl/"><?php echo $visit; ?></a>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-cog"></i> <?php echo $settings ?></h3>
			</div>
			<div class="panel-body">
				
				<form id="ifirma-form" action="<?php echo $form_action; ?>" method="post" class="form-horizontal">
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="<?php echo $api_vat_name; ?>"><?php echo $is_vat_payer ?></label>
						<div class="col-sm-5 checkbox">
							<label>
								<input type="checkbox" id="<?php echo $api_vat_name; ?>" name="<?php echo $api_vat_name; ?>" <?php echo $api_vat_checked; ?> />
								<?php echo $is_vat_payer ?>
							</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="<?php echo $api_bill_name; ?>"><span data-toggle="tooltip" title="<?php echo $api_key_bill_description ?>"><?php echo $api_key_bill ?></span></label>
						<div class="col-sm-10">
							<input type="text" id="<?php echo $api_bill_name; ?>" name="<?php echo $api_bill_name; ?>" value="<?php echo $api_bill_value ?>" size="50" class="form-control" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="<?php echo $api_invoice_name; ?>"><span data-toggle="tooltip" title="<?php echo $api_key_invoice_description ?>"><?php echo $api_key_invoice?></span></label>
						<div class="col-sm-10">
							<input type="text" id="<?php echo $api_invoice_name; ?>" name="<?php echo $api_invoice_name; ?>" value="<?php echo $api_invoice_value; ?>" size="50" class="form-control" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="<?php echo $api_subscriber_name; ?>"><span data-toggle="tooltip" title="<?php echo $api_key_subscriber_description ?>"><?php echo $api_key_subscriber ?></span></label>
						<div class="col-sm-10">
							<input type="text" id="<?php echo $api_subscriber_name; ?>" name="<?php echo $api_subscriber_name; ?>" value="<?php echo $api_subscriber_value; ?>" size="50" class="form-control" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="<?php echo $api_login_name; ?>"><span data-toggle="tooltip" title="<?php echo $api_login_description ?>"><?php echo $api_login ?></span></label>
						<div class="col-sm-10">
							<input type="text" id="<?php echo $api_login_name; ?>" name="<?php echo $api_login_name; ?>" value="<?php echo $api_login_value; ?>" size="50" class="form-control" />
						</div>
					</div>
					
				</form>
				
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>