<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create(); ?>
	<header class="panel-heading"><?php echo __('Add Multiple Devices'); ?></header>
	<div class="panel-body"><fieldset>
		<label class="free-label">Select the client for which you wish to add devices and traps (you may only choose a single client)</label><br>
		<?php 
			echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
			//echo $this->Form->input('Client', array('id'=>'clientList', 'label'=>'Clients who should have access to this document','class'=>'client-search-select form-control tooltipped', 'multiple'=>'false', 'title'=>'List of clients who will have access to this document. Choose "All Clients" to make visibile to everyone.'));
			//echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
			echo $this->Form->input('Client', array('id'=>'client-id-result', 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
			echo $this->Form->input('client_name', array('label'=>false,'disabled'=>'disabled','id'=>'client-id-name', 'class'=>'tooltipped form-control disabled', 'title'=>'You cannot enter a client name directly. Use the search bar above to find the right client and add them from the search results.'));
			echo $this->Form->input('number_devices', array('label'=>'Select number of devices to add', 'type'=>'number', 'validation'=>'greaterthanzero notnull', 'humanName'=>'Number of devices'));
			echo $this->Form->input('automaticCreation', array('label'=>'Create these devices automatically', 'type'=>'checkbox'));
		?>
		<div id="automaticOptions" style="display: none;">
		<label>This option will add multiple devices all of the same type. The devices will be labelled with the type of a device followed by a sequential number. The number sequencing will start
		depending on what you choose for "Label Numbering Start". You must find out what existing devices the client has before choosing this number, otherwise you risk having duplicate labels for devices.</label>
		<br/><br/>
		<?php 
			echo $this->Form->input('lastChecked', array('value'=>date('Y-m-d H:i:s'), 'type'=>'hidden'));
			echo $this->Form->input('labelNumberingStart', array('class'=>'tooltipped form-control', 'title'=>'At what number should the labels start?', 'value'=>'1'));
			echo $this->Form->input('location', array('class'=>'tooltipped form-control', 'title'=>'Default location for these devices.', 'default'=>'12'));
			echo $this->Form->input('device_type_id', array('class'=>'tooltipped form-control',
					'title'=>'The type of device to which this is linked. This will determine reporting options for the technician on the mobile app.'));
		?>
		</div>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div></section>
<script>
$('#DeviceNumberDevices').before("<br/>")
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DeviceAddMultipleForm')[0]);
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSingle('client-id-result', 'client-id-name');
	$('#DeviceAutomaticCreation').change(function() {
	    if(this.checked) $('#automaticOptions').show();
	    else $('#automaticOptions').hide();
	});
});
</script>
