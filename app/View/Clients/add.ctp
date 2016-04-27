<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo Configure::read("googleMapsKey"); ?>&sensor=false"></script>
<?php
	echo $this->Html->script('form-validation');
	echo $this->Html->script('maps-functions');
	echo $this->Html->script('documents-file-handling');
?>
<section class="panel">
<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
	<header class="panel-heading"><?php echo __('Add Client'); ?></header>
	<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort unique-clients-name', 'title'=>'The name of the client - must be unique', 'humanName'=>'Client name'));
		echo $this->Form->input('code', array('class'=>'tooltipped form-control', 'validation'=>'notnull nottooshort unique-clients-code', 'title'=>'A short unique code used to quickly identify the client', 'humanName'=>'Code'));
		echo $this->Form->input('primaryContact', array('label'=>'Full name of primary contact', 'class'=>'tooltipped form-control', 'validation'=>'notnull nottooshort', 'title'=>'Full name of the primary contact at this client. Name will appear on reports', 'humanName'=>'Primary Contact Name'));
		echo $this->Form->input('serviceFrequency',
				array(
						'type' => 'select',
						'options' => array_combine(array("Monthly", "Every two weeks", "Every week", "Every six weeks", "Every two months"), array("Monthly", "Every two weeks", "Every week", "Every six weeks", "Every two months"))
				)
		);
		echo $this->Form->input('nextService', array("label"=>"Date of first service (the system will predict future services based on this date)", 
				"type"=>"text", "class"=>"datepicker form-control", "validation"=>"notnull", "humanName"=>"Next Service Date", 
				"value"=>date("Y-m-d", time())));
		echo $this->Form->input('username', array(
				'label' => __d('users', 'Username for client login (must be unique)'),
				'id' => 'client-username',
				'class'=>'form-control',
				'humanName'=>'Username',
				'validation'=>'notnull alphanumeric unique-users-username'));
		echo $this->Form->input('password', array(
				'label' => __d('users', 'Default login password for client'),
				'id' => 'client-password',
				'class'=>'form-control',
				'humanName'=>'Password',
				'type'=>'text',
				'validation'=>'notnull password'));
		echo $this->Form->input('email', array(
				'label' => __d('users', 'E-mail address (used as for client notifications)'),
				'id' => 'client-email',
				'type' => 'email',
				'class'=>'form-control',
				'humanName'=>'E-mail address',
				'validation'=>'notnull nottooshort email'));
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
	<label for="ClientActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Client][active]" id="ClientActive_" value="1"/>
		<div class="activeinactiveswitch transitionable"><input name="data[Client][active]" type="checkbox" id="ClientActive" value="1" checked>
		<label class="transitionable" for="ClientActive" id="activeToggle"> </label></div>
	<?php
		echo $this->Form->input('company_id');		
		?>
		<div class="form-element-float">
			<label for="fileselect">(Mandatory) Choose a file containing the client's signed Service Agreement.</label>
			<br />
			<input type="file" id="fileselect" name="fileselect"/>
			<div id="filedrag">or drop file here</div>
		</div>
		<div class="fileInformation form-element-float">
			<label for="fileinfo">File Information</label><br>
			<span style="font-weight: bold">File type: </span><span id="file_type"></span><br>
			<span style="font-weight: bold">Size: </span><span id="file_size"></span><br>
			<div id="progress"><p class="transitionable"></p></div>
		</div>
		<div id="hiddenelements" style="display:none;">
			<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1000000000" />
		<?php 
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Client Service Agreement file'));
			echo $this->Form->input('size', array('id'=>'DocumentSize'));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType'));
			echo $this->Form->input('mime', array('id'=>'DocumentMime'));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta'));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'8'));
		?>
		</div>
		<?php 
		echo $this->Form->input('Policy', array('label'=>'Policies applicable to client', 'class'=>'tooltipped form-control', 'title'=>'Choose the policies you wish to associate with the client. This will add relevant documentation to the client file automatically.', 'humanName'=>'Client policies'));
		echo $this->Form->input('Chemical', array('label'=>'Chemicals allowed (commonly used chemicals have been preselected)', 'class'=>'tooltipped form-control', 'title'=>'Choose the chemicals allowed to be used with this client. This will add relevant documentation to the client file automatically and prevent technicians from logging any chemicals not on the list.', 'humanName'=>'Client chemicals'));
		echo $this->Form->input('Document', array('label'=>'Additional documents visible to client', 'class'=>'tooltipped form-control', 'title'=>'Choose the documents you wish to be visible to the client. This list excludes documents visible to all clients and policy documents, as these are included automatically.', 'humanName'=>'Attached documents'));
		$chemicalArrayString = "[";
		foreach ($commonChemicals as $chemical) $chemicalArrayString = $chemicalArrayString . '"' . $chemical . '",';
		echo "<script>var chemicalArray = $chemicalArrayString];</script>";
	?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div></div>
</section>
<script>
$('#ClientServiceFrequency').before("<br/>")
//lets select all common chemicals
chemicalOptions = $('#ChemicalChemical option');
for (i=0;i!=chemicalOptions.length;i++) if (chemicalArray.indexOf(chemicalOptions[i].value) != -1) $(chemicalOptions[i]).prop("selected", true);

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
	initValidation($('#submitButton')[0], $('#ClientAddForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('ClientActive', 'activeToggle', "Client set to active", "Client set to inactive", "Clients will be able to log in and view documents.", "Clients will not be able to login or see documents while they remain inactive.");
});

function uploadSuccess() {
	$.pnotify({
    		title: 'Service Agreement successfully uploaded',
  		text: 'Once you click submit the service agreement will be ready for insertion in client files',
	});}
//add a proper date picker
$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
</script>