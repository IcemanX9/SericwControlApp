<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="visits form">
<?php echo $this->Form->create('Visit'); ?>
	<header class="panel-heading"><?php echo __('Create a manual visit'); ?></header>
	<p style="margin-left: 15px; padding-top:15px;font-size:14px;">
	To add a new sighting manually, you must do so via the client file. Open the client document file and click the "Report a sighting" in the left menu.
	</p>
	<div class="panel-body">
	<fieldset>
	<?php
		echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
		echo $this->Form->input('client_id', array('id'=>'client-id-result', 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
		echo $this->Form->input('client_name', array('label'=>false,'disabled'=>'disabled','id'=>'client-id-name', 'class'=>'tooltipped form-control disabled', 'title'=>'You cannot enter a client name directly. Use the search bar above to find the right client and add them from the search results.'));
		echo $this->Form->input('purpose', array('class'=>'form-control', 'type'=>'select', 'options'=>array("follow_up"=>"Follow Up Visit", "routine_visit"=>"Routine Visit")));
		echo $this->Form->input('technician_id', array('type'=>'hidden', 'value'=>-1));
		echo $this->Form->input('status', array('type'=>'hidden', 'value'=>'created'));
		echo $this->Form->input('timeRequested', array('type'=>'hidden', 'value'=>date("Y-m-d H:i:s", time())));
		echo $this->Form->input('timeDue', array("type"=>"text", "class"=>"datepicker form-control"));
	?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
<script>
$(document).ready(function() {
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSingle('client-id-result', 'client-id-name');
	initValidation($('#submitButton')[0], $('#VisitAddForm')[0]);
});
</script>