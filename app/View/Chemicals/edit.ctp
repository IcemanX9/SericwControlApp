<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="chemicals form">
<?php echo $this->Form->create('Chemical'); ?>
	<header class="panel-heading"><?php echo __('Edit Chemical'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('documentChanged', array("id"=>"documentChanged","type"=>"hidden"));
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The full name of the chemical', 'humanName'=>'Chemical name'));
		echo $this->Form->input('code', array('class'=>'tooltipped form-control', 'label'=>'L Code', 'validation'=>'notnull unique-chemicals-code', 'title'=>'The industry/regulatory code for the chemical. Must be unique.', 'humanName'=>'Chemical code'));
		echo $this->Form->input('activeIngredients', array('class'=>'form-control'));
		echo $this->Form->input('targetPests', array('class'=>'form-control'));
		echo $this->Form->input('common', array('label'=>'Commonly used chemical? Will be automatically selected for new clients.'));
		echo $this->Form->input('notes', array('class'=>'tooltipped form-control', 'type'=>'textarea', 'title'=>'Additional notes about the chemical (not visible to clients)'));
		echo $this->Html->link(__('Click to download the current chemical information document'), array('controller'=>'Documents', 'action' => 'download', $this->request->data['Chemical']['document_id']), array('id'=>'downloadCurrent', 'title'=>'Click to download this document', 'style'=>'font-size:25px;', 'class'=>'plainLink tooltipped fa fa-file-text')); ?>
			<h3></h3>
			<div class="form-element-float">
				<label for="fileselect">Or choose a new chemical information document (replaces current file):</label>
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
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Chemical information file', 'type'=>'hidden', 'value'=>$this->request->data['Document']['filename']));
			echo $this->Form->input('size', array('id'=>'DocumentSize', 'value'=>$this->request->data['Document']['size']));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType', 'value'=>$this->request->data['Document']['file_type']));
			echo $this->Form->input('mime', array('id'=>'DocumentMime', 'value'=>$this->request->data['Document']['mime']));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta', 'value'=>$this->request->data['Document']['meta']));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'10'));
			echo $this->Form->input('document_id', array('type'=>'hidden'));
		?>
	</fieldset>
	<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#ChemicalEditForm')[0]);
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