<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create('Document'); ?>
		<header class="panel-heading"><?php echo __('Edit Document'); ?></header>
		<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name', array('class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The name of the document which will be seen by the client', 'humanName'=>'Document name'));
		?>
		<?php echo $this->Html->link(__('Click to download the current document'), array('action' => 'download', $this->request->data['Document']['id']), array('id'=>'downloadCurrent', 'title'=>'Click to download this document', 'style'=>'font-size:25px;', 'class'=>'plainLink tooltipped fa fa-file-text')); ?>
				<h3></h3>
				<label class="free-label">Choose between straight document upload or inserting text into a template</label><br>
				<div class="radioSection">
					<?php echo $this->Form->radio('templated', array('0'=>'Direct Document Upload', '1'=>'Text into template'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'templatedRadio', 'value'=>'0', 'legend'=>false)); ?>
				</div>
				<div id="direct-file-upload">
					<div class="form-element-float">
						<label for="fileselect">Or choose a new file (replaces current
							file):</label> <input type="file" id="fileselect"
							name="fileselect" />
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
				$this->request->data['Document']['document_text'] = "";
				if ($this->request->data['Document']['templated']) $this->request->data['Document']['document_text'] = file_get_contents('files/'.Configure::read('file_upload_prefix').$this->request->data['Document']['filename']);
				echo $this->Form->input('document_text', array('class'=>'tooltipped form-control','type'=>'textarea', 'title'=>'Enter and format the text to be used with the document template.', 'id'=>'freetext')); 
				?>
				</div>
				<div id="hiddenelements" style="display:none;">
					<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1000000000" />
	<?php
		echo $this->Form->input('filename');
		echo $this->Form->input('file_type');
		echo $this->Form->input('order');
		echo $this->Form->input('mime');
		echo $this->Form->input('meta');
		echo $this->Form->input('size');
		echo $this->Form->input('user_id');
		echo $this->Form->input('visibleToAll', array("type"=>"hidden"));
	?>
		</div>
	<?php
		if ($this->request->data['Document']['active']) $activeCheckboxParams = "value='1' checked";
		else $activeCheckboxParams = "value='0'";
		if ($this->request->data['Document']['policyDocument'])
			echo "<div class='free-label'>This is a policy document. You cannot directly edit the document category.</div>";
		else echo $this->Form->input('document_category_id', array('class'=>'form-control', 'style'=>'width: auto;'));
		echo $this->Form->input('company_id', array('label'=>'Company (if specified, will only show to clients who belong to this company)', 'class'=>'form-control', 'style'=>'width: auto;'));
		echo $this->Form->input('order', array('class'=>'tooltipped form-control', 'default'=>'1', 'title'=>'Order in which document will be displayed. Lower numbers will appear higher in the list. Documents with the same order number will be sorted alphabetically.'));
	?>	
		<label for="DocumentActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Document][active]" id="DocumentActive_" <?php echo $activeCheckboxParams; ?> />
		<div class="activeinactiveswitch transitionable"><input name="data[Document][active]" type="checkbox" id="DocumentActive" <?php echo $activeCheckboxParams; ?> />
		<label class="transitionable" for="DocumentActive" id="activeToggle"> </label></div>
		<label class="free-label">Select who has access to this document</label><br>
		<?php if ($this->request->data['Document']['policyDocument']) {
					echo "<div class='free-label'>This is a policy document. You cannot directly choose which clients have access. To add this document to a client file, you need to add the relevant policy in the client view. In the case of a technician, the file will be added to the client's file once a technician has completed a service.</div>";
					echo "<script>var allowClientChoice = false;</script>";
				}
			  else echo "<script>var allowClientChoice = true;</script>";
		?>
		<div id="choosingClients">
		<div class="radioSection">
			<?php echo $this->Form->radio('visibleToAll', array('1'=>'Visible to all clients', '0'=>'Visible only to clients selected below'), array('class'=>'visibleRadio', 'style'=>'margin-left:8px', 'id'=>'visibleToAllRadio', 'legend'=>false)); ?>
		</div>
		<div id="clientListSection" style="margin-top: -15px;"> 
	<?php
		//create options from the current clients
		$clientOptionsArray = array();
		$clientArrayString = "[";
		foreach ($this->request->data['Client'] as $client) {
			$clientOptionsArray[$client['id']] = $client['name'];
			$clientArrayString = $clientArrayString . '"' . $client['id'] . '",';
		}
		$clientArrayString = $clientArrayString . "]";
		echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
		echo $this->Form->input('Client', array('options'=>$clientOptionsArray, 'id'=>'clientList', 'disabled'=>'disabled', 'label'=>'Clients who should have access to this document','class'=>'client-search-select form-control tooltipped', 'title'=>'List of clients who will have access to this document. Choose "All Clients" to make visibile to everyone.'));
	?>
		</div>
		</div>
	</fieldset>
<input type="button" value="Submit" id="submitButton">
</div>
</section>
<script>
//we want to be able to modify the look of some input elements
$('#DocumentDocumentCategoryId').before("<br/>")
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DocumentEditForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('DocumentActive', 'activeToggle', "Document set to active", "Document set to inactive", "Selected clients will be able to view the document.", "Clients will not be able to see the document while it remains inactive.");

	//we only set up the client adding facilities if we are allowed (i.e. this is not a policy document)
	if (!allowClientChoice) {
		$("#clientList option").prop("selected", false);
		$('#VisibleToAllRadio0')[0].checked = true;
		$('#VisibleToAllRadio1')[0].checked = false;
		$('#choosingClients').css("display", "none");
	}
	else {
		if (!$('#VisibleToAllRadio0')[0].checked) {
			$('#clientListSection').css("display","none"); //hides current list section
			$("#clientList option").prop("selected", false); //make sure nobody is currently selected
		}
		else $("#clientList option").prop("selected", true);
		$("#clientList option").css("color", "black");
		$('#clientList').css("height", parseInt($("#clientList option").length) * 20); //set initial height
		currentFormClients = <?php echo $clientArrayString; ?>;
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
	}
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

function beforeSubmit() {
	$('#clientList').prop("disabled", false);
}

function uploadSuccess() {
	$('#downloadCurrent').html('This document will be replaced with your new upload when you click submit.');
	$('#downloadCurrent').addClass('documentReplaced');
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
    		validateForm();
    	});
   	},
	plugins: "table",
    tools: "inserttable"
});
</script>