<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="policies form">
<?php echo $this->Form->create('Policy'); ?>
	<header class="panel-heading"><?php echo __('Edit Policy'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort unique-policies-name', 'title'=>'The full name of the policy. Must be unique.', 'humanName'=>'Policy name'));
		echo $this->Form->input('notes', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Additional notes about the policy'));
		echo $this->Html->link(__('Click to download the current policy information document'), array('controller'=>'Documents', 'action' => 'download', $this->request->data['Policy']['document_id']), array('id'=>'downloadCurrent', 'title'=>'Click to download this document', 'style'=>'font-size:25px;', 'class'=>'plainLink tooltipped fa fa-file-text')); 
		echo $this->Form->input('documentChanged', array("id"=>"documentChanged","type"=>"hidden"));
		?>
		<h3></h3>
		<label class="free-label">Choose between straight document upload or inserting text into a template</label><br>
		<div class="radioSection">
			<?php echo $this->Form->radio('templated', array('0'=>'Direct Document Upload', '1'=>'Text into template'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'templatedRadio', 'value'=>'0', 'legend'=>false)); ?>
		</div>
		<div id="direct-file-upload">
		<div class="form-element-float">
			<label for="fileselect">Or choose a new policy information document (replaces current file):</label> <input type="file"
				id="fileselect" name="fileselect" />
			<div id="filedrag">or drop file here</div>
		</div>
		<div class="fileInformation form-element-float">
			<label for="fileinfo">File Information</label><br> <span
				style="font-weight: bold">File type: </span><span id="file_type"></span><br>
			<span style="font-weight: bold">Size: </span><span id="file_size"></span><br>
			<div id="progress">
				<p class="transitionable"></p>
			</div>
		</div>
		</div>
		<div id="template-file-upload" style="display: none;">
				<?php
				$this->request->data['Policy']['document_text'] = "";
				if ($this->request->data['Document']['templated']) $this->request->data['Policy']['document_text'] = file_get_contents('files/'.Configure::read('file_upload_prefix').$this->request->data['Document']['filename']);
				echo $this->Form->input('document_text', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Enter and format the text to be used with the document template.', 'id'=>'freetext')); 
				?>
			</div>
		<div id="hiddenelements" style="display: none;">
			<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE"
				value="1000000000" />
			<?php 
		echo $this->Form->input('filename', array('id'=>'DocumentFilename', 'validation'=>'notnull', 'humanName'=>'Policy information file', 'type'=>'hidden', 'value'=>$this->request->data['Document']['filename']));
		echo $this->Form->input('size', array('id'=>'DocumentSize', 'value'=>$this->request->data['Document']['size']));
		echo $this->Form->input('file_type', array('id'=>'DocumentFileType', 'value'=>$this->request->data['Document']['file_type']));
		echo $this->Form->input('mime', array('id'=>'DocumentMime', 'value'=>$this->request->data['Document']['mime']));
		echo $this->Form->input('admin_user_id', array("type"=>"hidden", "value"=>$userData['id']));
		echo $this->Form->input('meta', array('id'=>'DocumentMeta', 'value'=>$this->request->data['Document']['meta']));
		echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'8'));
		echo $this->Form->input('document_id', array('type'=>'hidden'));
	?>
			</fieldset>
	<input type="button" value="Submit" id="submitButton"></form>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#PolicyEditForm')[0]);
	var templated = <?php if ($this->request->data['Document']['templated']) echo "1"; else echo "0"; ?>;
	if (templated) {
		$('#TemplatedRadio1').prop("checked", true); //check the right box
		$('#TemplatedRadio0').prop("checked", false); //uncheck the right box
		$('#template-file-upload').css("display","block"); 
		$("#direct-file-upload").css("display","none");
	}
	else {
		$('#TemplatedRadio0').prop("checked", true); //check the right box
		$('#TemplatedRadio1').prop("checked", false); //uncheck the right box
	}
	$('.visibleRadio').click(function () {
		if ($('#TemplatedRadio1')[0].checked) {
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
	$('#downloadCurrent').html('This document will be replaced with your new upload when you click submit.');
	$('#downloadCurrent').addClass('documentReplaced');
	$('#documentChanged')[0].value = 1;
	$.pnotify({
    		title: 'Warning: new document uploaded',
  		text: 'Once you submit the form the document will be replaced by the new document you have uploaded. The old document will be permanently removed.',
   		type: 'error',
		hide: false
	});}

tinymce.init({
    selector: "#freetext",
    setup : function(ed) {
    	ed.on('change', function(e) {		//update filename when text changes. This is a trick to make form valid if typing directly
    		$('#DocumentFilename').val(tinyMCE.activeEditor.getContent().substr(0,1));
    		$('#documentChanged')[0].value = 1;
    		validateForm();
    	});
   	},
	plugins: "table",
    tools: "inserttable"
});
</script>