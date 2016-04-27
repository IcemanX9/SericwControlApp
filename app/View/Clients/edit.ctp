<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo Configure::read("googleMapsKey"); ?>&sensor=false"></script>
<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('maps-functions');
?>
<section class="panel">
<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
	<header class="panel-heading"><?php echo __('Edit Client'); ?></header>
	<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('documentChanged', array("id"=>"documentChanged","type"=>"hidden"));
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort unique-clients-name', 'title'=>'The name of the client - must be unique', 'humanName'=>'Client name'));
		echo $this->Form->input('code', array('class'=>'tooltipped form-control', 'validation'=>'notnull nottooshort unique-clients-code', 'title'=>'A short unique code used to quickly identify the client', 'humanName'=>'Code'));
		echo $this->Form->input('primaryContact', array('label'=>'Full name of primary contact', 'class'=>'tooltipped form-control', 'validation'=>'notnull nottooshort', 'title'=>'Full name of the primary contact at this client. Name will appear on reports', 'humanName'=>'Primary Contact Name'));
		echo $this->Form->input('serviceFrequency',
				array(
						'type' => 'select',
						'options' => array_combine(array("Monthly", "Every two weeks", "Every week", "Every six weeks", "Every two months"), array("Monthly", "Every two weeks", "Every week", "Every six weeks", "Every two months"))
				)
		);
		echo $this->Form->input('nextService', array("label"=>"Manually set date of next service (the system will predict future services based on this new date)", "type"=>"text", "class"=>"datepicker form-control"));
		echo $this->Form->input('username', array(
				'label' => __d('users', 'Username for client login (must be unique)'),
				'id' => 'client-username',
				'class'=>'form-control',
				'humanName'=>'Username',
				'validation'=>'notnull nottooshort unique-users-username',
				'value'=>$this->request->data['User']['username']
				));
		echo $this->Form->input('password', array(
				'label' => __d('users', 'To reset password, type a new password. Leaving this blank will keep the current password.'),
				'id' => 'client-password',
				'class'=>'form-control',
				'humanName'=>'Password Reset',
				'type'=>'text',
				'validation'=>'password-reset'));
		echo $this->Form->input('email', array(
				'label' => __d('users', 'E-mail address (used as for client notifications)'),
				'id' => 'client-email',
				'type' => 'email',
				'class'=>'form-control',
				'humanName'=>'E-mail address',
				'validation'=>'notnull nottooshort email',
				'value'=>$this->request->data['User']['email']
		));
		echo $this->Form->input('user_id', array('type'=>'hidden', 'value'=>$this->request->data['User']['id']));
		echo $this->Form->input('phone', array('class'=>'form-control'));
		echo $this->Form->input('address', array('class'=>'tooltipped form-control', 'type'=>'textarea', 'validation'=>'nottooshort', 'title'=>'The physical address of the client', 'humanName'=>'Client Address'));
		echo $this->Form->input('latitude', array('type'=>'hidden'));
		echo $this->Form->input('longitude', array('type'=>'hidden'));
	?>
	<input type="button" id="geocodeButton" value="Find address on map" /><br/><br/>
	<div id="googleMapsHider" style="display: none;">
		<label>The map shows where we think your client is located. You can drag the marker to a new location if this is not correct.</label>
		<div id="googleMaps" class="gMapsLarge"></div>
	</div>
	<?php
		if ($this->request->data['Client']['active']) $activeCheckboxParams = "value='1' checked";
		else $activeCheckboxParams = "value='0'";
	?>	
		<label for="ClientActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Client][active]" id="ClientActive_" <?php echo $activeCheckboxParams; ?> />
		<div class="activeinactiveswitch transitionable"><input name="data[Client][active]" type="checkbox" id="ClientActive" <?php echo $activeCheckboxParams; ?> />
		<label class="transitionable" for="ClientActive" id="activeToggle"> </label></div>	
		<?php echo $this->Form->input('company_id'); ?>
		<label for="ClientActive" class="free-label">Important Client Documents</label><br/>
			<table class="table-hover" style="margin-bottom: 20px;" cell-padding="0" cellspacing="0">
			<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Category</th>
					<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
			<?php foreach ($importantDocuments as $document): ?>
			<tr>
				<td><?php echo h($document['Document']['id']); ?>&nbsp;</td>
				<td><?php echo h($document['Document']['name']); ?>&nbsp;</td>
				<td>
					<?php echo h($document['DocumentCategory']['name']); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'download', $document['Document']['id']), array('title'=>'Click to download this document', 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
					<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'edit', $document['Document']['id']), array('target'=>'_blank', 'title'=>'Click to edit this document', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
					<?php echo $this->Form->postLink(__(''), array('controller'=>'Documents', 'action' => 'delete', $document['Document']['id']), array('title'=>'Click to permanently delete this document', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $document['Document']['id'])); ?>
				</td>
			</tr>
			<?php endforeach; ?>
			</table>
		<?php 
		echo $this->Form->input('Policy', array('label'=>'Policies applicable to client', 'class'=>'tooltipped form-control', 'title'=>'Choose the policies you wish to associate with the client. This will add relevant documentation to the client file automatically.', 'humanName'=>'Client policies'));
		echo $this->Form->input('Chemical', array('label'=>'Chemicals allowed', 'class'=>'tooltipped form-control', 'title'=>'Choose the chemicals allowed to be used with this client. This will add relevant documentation to the client file automatically and prevent technicians from logging any chemicals not on the list.', 'humanName'=>'Client chemicals'));
		echo $this->Form->input('Document', array('label'=>'Additional documents visible to client', 'class'=>'tooltipped form-control', 'title'=>'Choose the documents you wish to be visible to the client. This list excludes documents visible to all clients, as these are included by default.', 'humanName'=>'Attached documents'));
	?>
	</fieldset>
	<input type="button" value="Submit" id="submitButton"></form>
</div></div>
</section>
<script>
$('#ClientServiceFrequency').before("<br/>")
var map; //declare global
//we want to be able to modify the look of some input elements
$('#ClientCompanyId').before("<br/>")
$(document).ready(function() {
	//initialise the maps
    var mapOptions = {
          center: defaultMapsLocation,
          zoom: 12
        };
	initMaps("googleMaps", mapOptions);
	$("#geocodeButton").click(function() {
		$('#googleMapsHider').css("display","block");
		google.maps.event.trigger(map, 'resize');
		codeAddress($('#ClientAddress')[0].value, "ClientLatitude", "ClientLongitude");
		});
    
    //initialise the form validation
	initValidation($('#submitButton')[0], $('#ClientEditForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('ClientActive', 'activeToggle', "Client set to active", "Client set to inactive", "Clients will be able to log in and view documents.", "Clients will not be able to login or see documents while they remain inactive.");
});
//add a proper date picker
$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
</script>