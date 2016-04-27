<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="technicians form">
<?php echo $this->Form->create('Technician'); ?>
	<header class="panel-heading"><?php echo __('Edit Technician'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('documentChanged', array("id"=>"documentChanged","type"=>"hidden"));
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The full name of the technician', 'humanName'=>'Technician name'));
		echo $this->Form->input('username', array(
				'label' => __d('users', 'Username for technician login (must be unique - suggest using technicians full name, no spaces)'),
				'id' => 'technician-username',
				'class'=>'form-control',
				'humanName'=>'Username',
				'validation'=>'notnull alphanumeric unique-users-username',
				'value'=> $this->request->data['User']['username']));
		echo $this->Form->input('regNo', array(
				'label' => __d('users', 'Registration Number'),
				'title' => "PCO's registration number as reflected on their certification",
				'validation'=>'notnull',
				'class'=>'form-control',
				'humanName'=>'Registration Number'));
		echo $this->Form->input('password', array(
				'label' => __d('users', 'Enter a new pin to reset. Leaving this blank will keep the current pin.'),
				'id' => 'technician-password',
				'class'=>'form-control pinInput',
				'humanName'=>'Pin Reset',
				'type'=>'text',
				'validation'=>'pin-reset'));
		echo $this->Form->input('email', array(
				'id' => 'technician-email',
				'type' => 'hidden',
				'value' => 'technician')); ?>
				<?php
				if ($this->request->data['Technician']['active']) $activeCheckboxParams = "value='1' checked";
				else $activeCheckboxParams = "value='0'";
				?>
				<label for="TechnicianActive" class="free-label">Active</label><br/>
				<input type="hidden" name="data[Technician][active]" id="TechnicianActive_" <?php echo $activeCheckboxParams; ?> />
				<div class="activeinactiveswitch transitionable"><input name="data[Technician][active]" type="checkbox" id="TechnicianActive" <?php echo $activeCheckboxParams; ?> />
				<label class="transitionable" for="TechnicianActive" id="activeToggle"> </label></div>	
	<?php echo $this->Html->link(__('Click to download the current technician certification document'), array('controller'=>'Documents', 'action' => 'download', $this->request->data['Technician']['document_id']), array('id'=>'downloadCurrent', 'title'=>'Click to download this document', 'style'=>'font-size:25px;', 'class'=>'plainLink tooltipped fa fa-file-text')); ?>
			<h3></h3>
			<div class="form-element-float">
				<label for="fileselect">Or choose a new technician certification file (replaces current file):</label>
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
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Technician certification file', 'type'=>'hidden', 'value'=>$this->request->data['Document']['filename']));
			echo $this->Form->input('size', array('id'=>'DocumentSize', 'value'=>$this->request->data['Document']['size']));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType', 'value'=>$this->request->data['Document']['file_type']));
			echo $this->Form->input('mime', array('id'=>'DocumentMime', 'value'=>$this->request->data['Document']['mime']));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta', 'value'=>$this->request->data['Document']['meta']));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'5'));
			echo $this->Form->input('user_id', array('type'=>'hidden'));
			echo $this->Form->input('document_id', array('type'=>'hidden'));
		?>
		</div>					
	<?php 
		echo $this->Form->input('documentExpires', array("label"=>"Choose the date on which the technician's certification document expires","type"=>"text", "class"=>"datepicker form-control"));
		echo $this->Form->input('notes', array('type'=>'textarea', 'class'=>'tooltipped form-control', 'title'=>'Any additional notes about the technician', 'humanName'=>'Notes'));
	?>
	</fieldset>
	<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#TechnicianEditForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('TechnicianActive', 'activeToggle', "Technician set to active", "Technician set to inactive", "This technician will be able to use the mobile app.", "This technician will NOT be able to use the mobile app.");
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});

function uploadSuccess() {
	$('#downloadCurrent').html('This document will be replaced with your new upload when you click submit.');
	$('#downloadCurrent').addClass('documentReplaced');
	$('#documentChanged')[0].value = 1;
	$.pnotify({
    		title: 'Warning: new document uploaded',
  		text: 'Once you submit the form the document will be replaced by the new document you have uploaded. The old document will be permanently removed.',
   		type: 'error',
		hide: false
	});}
</script>