<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="devices form">
<?php echo $this->Form->create('Device'); ?>
	<header class="panel-heading"><?php echo __('Edit Device'); ?></header>
		<div class="panel-body"><fieldset>
		<label for="" class="free-label">Barcode</label><br/>
		<img src="<?php echo $this->Html->url(array("controller" => "devices", "action" => "getBarcode", $this->request->data['Device']['id'])); ?>" style="vertical-align: middle; margin-left: 8px; padding-top: 5px; padding-bottom: 5px">
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('label', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull', 'title'=>'A label to differentiate this device from others in the same location. Will be printed on the device barcode.', 'humanName'=>'Device label'));
		echo $this->Form->input('location_id', array('class'=>'tooltipped form-control', 'validation'=>'notnull', 'title'=>'The area in which this device should be placed on client site.', 'humanName'=>'Device location'));
		echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
		echo $this->Form->input('client_id', array('id'=>'client-id-result', 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
		echo $this->Form->input('client_name', array('value'=>$this->request->data['Client']['name'],'label'=>false,'disabled'=>'disabled','id'=>'client-id-name', 'class'=>'tooltipped form-control disabled', 'title'=>'You cannot enter a client name directly. Use the search bar above to find the right client and add them from the search results.'));
		echo $this->Form->input('latitude', array('class'=>'tooltipped form-control', 'title'=>'A guess at the precise location of the device. Do not edit unless you have good reason'));
		echo $this->Form->input('longitude', array('class'=>'tooltipped form-control', 'title'=>'A guess at the precise location of the device. Do not edit unless you have good reason'));
		echo $this->Form->input('device_type_id', array('class'=>'tooltipped form-control', 'validation'=>'notnull', 'title'=>'The type of device to which this is linked. This will determine reporting options for the technician on the mobile app.', 'humanName'=>'Device type'));
		echo $this->Form->input('lastChecked', array("type"=>"text", "class"=>"datepicker form-control", "validation"=>"notnull"));
		echo $this->Form->input('damaged');
		echo $this->Form->input('damagedDate', array("type"=>"text", "class"=>"datepicker form-control"));
		echo $this->Form->input('missing');
		echo $this->Form->input('missingDate', array("type"=>"text", "class"=>"datepicker form-control"));
		echo $this->Form->input('obscured');
		echo $this->Form->input('obscuredDate', array("type"=>"text", "class"=>"datepicker form-control"));
	?>
		<label for="DeviceActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Device][active]" id="DeviceActive_" value="1"/>
		<div class="activeinactiveswitch transitionable"><input name="data[Device][active]" type="checkbox" id="DeviceActive" value="1" checked>
		<label class="transitionable" for="DeviceActive" id="activeToggle"> </label></div>
	<?php 
		echo $this->Form->input('installed');
		echo $this->Form->input('notes', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The name of the document which will be seen by the client', 'type'=>'textarea', 'humanName'=>'Document name'));
	?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSingle('client-id-result', 'client-id-name');
	initValidation($('#submitButton')[0], $('#DeviceEditForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('DeviceActive', 'activeToggle', "Device set to active", "Device set to inactive", "The device can be installed and monitored by technicians.", "The device cannot be installed or monitored and will not generate notifications.");
	//add a proper date picker
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});
</script>