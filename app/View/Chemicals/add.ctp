<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="chemicals form">

<?php echo $this->Form->create('Chemical'); ?>
	<header class="panel-heading"><?php echo __('Add Chemical'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The full name of the chemical', 'humanName'=>'Chemical name'));
		echo $this->Form->input('code', array('class'=>'tooltipped form-control', 'label'=>'L Code', 'validation'=>'notnull unique-chemicals-code', 'title'=>'The industry/regulatory code for the chemical. Must be unique.', 'humanName'=>'Chemical code'));
		echo $this->Form->input('activeIngredients', array('class'=>'form-control'));
		echo $this->Form->input('targetPests', array('class'=>'form-control'));
		echo $this->Form->input('common', array('label'=>'Commonly used chemical? Will be automatically selected for new clients.'));
		echo $this->Form->input('notes', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Additional notes about the chemical (not visible to clients)'));
	?>
	<div class="form-element-float">
			<label for="fileselect">Choose chemical information file to upload (mandatory):</label>
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
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Chemical information file'));
			echo $this->Form->input('size', array('id'=>'DocumentSize'));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType'));
			echo $this->Form->input('mime', array('id'=>'DocumentMime'));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta'));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'10'));
		?>
		</div>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#ChemicalAddForm')[0]);
});

function uploadSuccess() {
	$.pnotify({
    		title: 'Document successfully uploaded',
  		text: 'Once you click submit the document will be ready for insertion in client files',
	});}
</script>