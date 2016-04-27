<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="bugs form">
<?php echo $this->Form->create('Bug'); ?>
	<header class="panel-heading"><?php echo __('Log a System Bug'); ?></header>
	<div class="panel-body"><fieldset>
	<div class="free-label">If you come across an error or something that is not working, please leave a description of the error below in as much detail as possible. A message will be sent to the system administrator.</div>
	<?php
		echo $this->Form->input('user_id', array('type'=>'hidden', "value"=>$userData['id']));
		echo $this->Form->input('description', array('class'=>'form-control', 'type'=>'textarea', 'humanName'=>'Bug description', 'id'=>'freetext'));
	?>	
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
</div></section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#BugAddForm')[0]);
	tinymce.init({
	    selector: "#freetext"
	   	});
});

</script>
