<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create(); ?>
	<header class="panel-heading"><?php echo __('Add Multiple Devices'); ?></header>
	<div class="panel-body"><fieldset>
		<?php 
			echo $this->Form->input('client_id', array('value'=>$clientId, 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
			echo $this->Form->input('numberDevices', array('value'=>$numberDevices, 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
			echo $this->Form->input('lastChecked', array('value'=>date('Y-m-d H:i:s'), 'type'=>'hidden'));
			for ($i = 0; $i != $numberDevices; $i++) {
				?>
				<br><div class="sub-panel"><label class="free-label">Enter details for device <?php echo $i+1; ?></label><br>
				<?php
				echo $this->Form->input('label', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull', 'id'=>'label'.$i, 'name'=>"data[Device][label$i]",
												'title'=>'A label to differentiate this device from others in the same location. Will be printed on the device barcode.', 'humanName'=>'Device label'));
				echo $this->Form->input('location', array('class'=>'tooltipped form-control', 'id'=>'location'.$i, 'name'=>"data[Device][location$i]",
														 'validation'=>'uppercasefirst notnull', 'title'=>'The area in which this device should be placed on client site.', 'humanName'=>'Device location'));
				echo $this->Form->input('device_type_id', array('class'=>'tooltipped form-control', 'validation'=>'notnull', 'id'=>'type'.$i,
						'title'=>'The type of device to which this is linked. This will determine reporting options for the technician on the mobile app.', 
						'name'=>"data[Device][device_type_id$i]",
						'humanName'=>'Device type'));
				echo "</div>";
			}
		?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div></section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DeviceAddMultipleDetailsForm')[0]);
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSingle('client-id-result', 'client-id-name');
});
</script>
