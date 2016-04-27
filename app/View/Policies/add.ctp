<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="policies form">
<?php echo $this->Form->create('Policy'); ?>
	<header class="panel-heading"><?php echo __('Add Policy'); ?></header>
		<div class="panel-body"><fieldset>
		<?php
			echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort unique-policies-name', 'title'=>'The full name of the policy. Must be unique.', 'humanName'=>'Policy name'));
			echo $this->Form->input('notes', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Additional notes about the policy'));
		?>
		<label class="free-label">Choose between straight document upload or inserting text into a template</label><br>
		<div class="radioSection">
			<?php echo $this->Form->radio('templated', array('0'=>'Direct Document Upload', '1'=>'Text into template'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'templatedRadio', 'value'=>'0', 'legend'=>false)); ?>
		</div>
		<div id="direct-file-upload">
		<div class="form-element-float">
			<label for="fileselect">Choose policy information document to upload (mandatory):</label>
			<input type="file" id="fileselect" name="fileselect"/>
			<div id="filedrag">or drop file here</div>
		</div>
		<div class="fileInformation form-element-float">
			<label for="fileinfo">File Information</label><br>
			<span style="font-weight: bold">File type: </span><span id="file_type"></span><br>
			<span style="font-weight: bold">Size: </span><span id="file_size"></span><br>
			<div id="progress"><p class="transitionable"></p></div>
		</div>
		</div>
		<div id="template-file-upload" style="display: none;">
		<?php echo $this->Form->input('document_text', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Enter and format the text to be used with the document template.', 'id'=>'freetext')); ?>
		</div>
		<div id="hiddenelements" style="display:none;">
			<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1000000000" />
		<?php 
			echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Document information file'));
			echo $this->Form->input('size', array('id'=>'DocumentSize'));
			echo $this->Form->input('file_type', array('id'=>'DocumentFileType'));
			echo $this->Form->input('mime', array('id'=>'DocumentMime'));
			echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
			echo $this->Form->input('meta', array('id'=>'DocumentMeta'));
			echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'8'));
		?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#PolicyAddForm')[0]);
	$('.visibleRadio').click(function () {
		if ($('#TemplatedRadio1')[0].checked) {
			//recreate the list section
			$('#template-file-upload').css("display","block"); 
			$("#direct-file-upload").css("display","none"); 
		}
		else {
			$("#direct-file-upload").css("display","block"); 
			$('#template-file-upload').css("display","none"); 
		}
	});
});

function uploadSuccess() {
	$.pnotify({
    		title: 'Document successfully uploaded',
  		text: 'Once you click submit the document will be ready for insertion in client files',
	});}

tinymce.init({
    selector: "#freetext",
    setup : function(ed) {
    	ed.on('change', function(e) {		//update filename when text changes. This is a trick to make form valid if typing directly
    		$('#DocumentFilename').val(tinyMCE.activeEditor.getContent().substr(0,1));
    		validateForm();
    	});
   	},
	plugins: "table",
    tools: "inserttable"
});
</script>