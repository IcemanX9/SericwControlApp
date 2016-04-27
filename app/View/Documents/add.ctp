<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create('Document'); ?>
	<header class="panel-heading"><?php echo __('Add Document'); ?></header>
	<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The name of the document which will be seen by the client', 'humanName'=>'Document name'));
	?>	
		<label class="free-label">Choose between straight document upload or inserting text into a template</label><br>
		<div class="radioSection">
			<?php echo $this->Form->radio('templated', array('0'=>'Direct Document Upload', '1'=>'Text into template'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'templatedRadio', 'value'=>'0', 'legend'=>false)); ?>
		</div>
		<div id="direct-file-upload">
		<div class="form-element-float">
			<label for="fileselect">Files to upload:</label>
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
			echo $this->Form->input('filename', array('validation'=>'notnull', 'humanName'=>'File or file text'));
			echo $this->Form->input('size');
			echo $this->Form->input('file_type');
			echo $this->Form->input('mime');
			echo $this->Form->input('meta');
			echo $this->Form->input('user_id', array("type"=>"hidden", "value"=>$userData['id']));
	?>
		</div>
	<?php
		echo $this->Form->input('document_category_id', array('class'=>'form-control', 'style'=>'width: auto;'));
		echo $this->Form->input('company_id', array('label'=>'Company (if specified, will only show to clients who belong to this company)', 'class'=>'form-control', 'style'=>'width: auto;'));
		//echo $this->Form->input('active', array('class'=>'form-control', 'style'=>'width: auto;'));
		echo $this->Form->input('order', array('class'=>'tooltipped form-control', 'default'=>'1', 'title'=>'Order in which document will be displayed. Lower numbers will appear higher in the list. Documents with the same order number will be sorted alphabetically.'));
	?>	<label for="DocumentActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Document][active]" id="DocumentActive_" value="1"/>
		<div class="activeinactiveswitch transitionable"><input name="data[Document][active]" type="checkbox" id="DocumentActive" value="1" checked>
		<label class="transitionable" for="DocumentActive" id="activeToggle"> </label></div>
		<label class="free-label">Select who has access to this document</label><br>
		<div class="radioSection">
			<?php echo $this->Form->radio('visibleToAll', array('1'=>'Visible to all clients', '0'=>'Visible only to clients selected below'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'visibleToAllRadio', 'value'=>'1', 'legend'=>false)); ?>
		</div>
		<div id="clientListSection" style="margin-top: -15px;"> 
		<?php 
			echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
			echo $this->Form->input('Client', array('id'=>'clientList', 'disabled'=>'disabled', 'label'=>'Clients who should have access to this document','class'=>'client-search-select form-control tooltipped', 'title'=>'List of clients who will have access to this document. Choose "All Clients" to make visibile to everyone.'));
		?>
		</div>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div></section>
<script>
//we want to be able to modify the look of some input elements
$('#DocumentDocumentCategoryId').before("<br/>")
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DocumentAddForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('DocumentActive', 'activeToggle', "Document set to active", "Document set to inactive", "Selected clients will be able to view the document.", "Clients will not be able to see the document while it remains inactive.");
	//now let's deal with the list of clients
	$('#clientListSection').css("display","none"); //hides current list section
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSelectBox('clientList');
	$('.visibleRadio').click(function () {
		if ($('#VisibleToAllRadio0')[0].checked) {
			//recreate the list section
			$('#clientListSection').css("display","block"); //unhides the list section
			$("#clientList option").prop("selected", true); //select existing clients
		}
		else {
			$("#clientList option").prop("selected", false); //make sure nobody is currently selected
			$('#clientListSection').css("display","none"); //hides current list section
		}
	});
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

function beforeSubmit() {
	$('#clientList').prop("disabled", false);
}

function uploadSuccess() {
	$.pnotify({
    		title: 'Document successfully uploaded',
  		text: 'Once you click submit the document will be ready to download by clients!',
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
