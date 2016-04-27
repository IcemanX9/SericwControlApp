<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="technicians form">

<?php echo $this->Form->create('Technician'); ?>
	<header class="panel-heading"><?php echo __('Add Technician'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The full name of the technician', 'humanName'=>'Technician name'));
		echo $this->Form->input('username', array(
				'label' => __d('users', 'Username for technician login (must be unique - suggest using technicians full name, no spaces)'),
				'id' => 'technician-username',
				'class'=>'form-control',
				'humanName'=>'Username',
				'validation'=>'notnull alphanumeric unique-users-username'));
		echo $this->Form->input('regNo', array(
				'label' => __d('users', 'Registration Number'),
				'title' => "PCO's registration number as reflected on their certification",
				'class'=>'form-control',
				'validation'=>'notnull',
				'humanName'=>'Registration Number'));
		echo $this->Form->input('password', array(
				'label' => __d('users', '5-digit pin for technician'),
				'id' => 'technician-password',
				'class'=>'form-control pinInput',
				'humanName'=>'Pin',
				'type'=>'text',
				'validation'=>'notnull pin'));
		echo $this->Form->input('email', array(
				'id' => 'technician-email',
				'type' => 'hidden',
				'value' => 'technician')); ?>
				
	<div class="form-element-float">
			<label for="fileselect">Choose technician certification to upload (mandatory):</label>
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
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Technician certification file'));
			echo $this->Form->input('size', array('id'=>'DocumentSize'));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType'));
			echo $this->Form->input('mime', array('id'=>'DocumentMime'));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta'));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'5'));
		?>
		</div>
	<?php 
		echo $this->Form->input('documentExpires', array("label"=>"Choose the date on which the technician's certification document expires","type"=>"text", "class"=>"datepicker form-control", "value"=>date("Y-m-d", time() + (365 * 24 * 60 * 60))));
		echo $this->Form->input('notes', array('type'=>'textarea', 'class'=>'tooltipped form-control', 'title'=>'Any additional notes about the technician', 'humanName'=>'Notes'));
	?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#TechnicianAddForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('TechnicianActive', 'activeToggle', "Technician set to active", "Technician set to inactive", "This technician will be able to use the mobile app.", "This technician will NOT be able to use the mobile app.");
	//add a proper date picker
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});

function uploadSuccess() {
	$.pnotify({
    		title: 'Document successfully uploaded',
  		text: 'Once you click submit the document will be ready for insertion in client files',
	});}
</script>