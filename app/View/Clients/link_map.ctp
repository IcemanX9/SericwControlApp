<?php
	echo $this->Html->script('documents-file-handling');
	echo $this->Html->script('form-validation');
	echo $this->Html->script('client-search-widget');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create('Document'); ?>
	<header class="panel-heading"><?php echo __('Link Site Plan Document'); ?></header>
	<div class="panel-body"><fieldset>
	Here you may add a site plan document to the client's file. To edit or remove a site plan document, please go to the client "View" page and check under the heading "Important Documents".<br/><br/>
	<?php
		echo $this->Form->input('name', array('value'=>'Site Plan', 'class'=>'tooltipped form-control', 'validation'=>'uppercasefirst notnull nottooshort', 'title'=>'The name of the document which will be seen by the client', 'humanName'=>'Document name'));
	?>	
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
		<div id="hiddenelements" style="display:none;">
			<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1000000000" />
		<?php 
			echo $this->Form->input('filename', array('validation'=>'notnull', 'humanName'=>'File or file text'));
			echo $this->Form->input('templated', array('value'=>'0'));
			echo $this->Form->input('size');
			echo $this->Form->input('file_type');
			echo $this->Form->input('mime');
			echo $this->Form->input('meta');
			echo $this->Form->input('user_id', array("type"=>"hidden", "value"=>$userData['id']));
	?>
		</div>
	<?php
		echo $this->Form->input('document_category_id', array('type'=>'hidden', 'value'=>'9', 'class'=>'form-control', 'style'=>'width: auto;'));
		echo $this->Form->input('order', array('class'=>'tooltipped form-control', 'default'=>'1', 'title'=>'Order in which document will be displayed. Lower numbers will appear higher in the list. Documents with the same order number will be sorted alphabetically.'));
	?>	<label for="DocumentActive" class="free-label">Active</label><br/>
		<input type="hidden" name="data[Document][active]" id="DocumentActive_" value="1"/>
		<div class="activeinactiveswitch transitionable"><input name="data[Document][active]" type="checkbox" id="DocumentActive" value="1" checked>
		<label class="transitionable" for="DocumentActive" id="activeToggle"> </label></div>
		<label class="free-label">Select who has access to this document (you may only choose a single client)</label><br>
		<?php 
			echo $this->Form->input('clientSearch', array('label'=>'Client Name', 'style'=>'margin-left:0px;margin-bottom:-1.5em;', 'class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'placeholder'=>'Start typing a client name to search'));
			echo $this->Form->input('Client', array('id'=>'client-id-result', 'type'=>'hidden', 'validation'=>'notnull', 'humanName'=>'Client name'));
			echo $this->Form->input('client_name', array('label'=>false,'disabled'=>'disabled','id'=>'client-id-name', 'class'=>'tooltipped form-control disabled', 'title'=>'You cannot enter a client name directly. Use the search bar above to find the right client and add them from the search results.'));
		?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div></section>
<script>
//we want to be able to modify the look of some input elements
$('#DocumentDocumentCategoryId').before("<br/>")
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DocumentLinkMapForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('DocumentActive', 'activeToggle', "Document set to active", "Document set to inactive", "Selected clients will be able to view the document.", "Clients will not be able to see the document while it remains inactive.");
	//now let's deal with the list of clients
	$('#clientListSection').css("display","none"); //hides current list section
	initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, true);
	setClientSearchWidgetResultsSingle('client-id-result', 'client-id-name');
});

function uploadSuccess() {
	$.pnotify({
    		title: 'Document successfully uploaded',
  		text: 'Once you click submit the document will be ready to download by clients!',
	});}

</script>
