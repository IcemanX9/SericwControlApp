<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="documentCategories form">
<?php echo $this->Form->create('DocumentCategory'); ?>
	<header class="panel-heading"><?php echo __('Add Document Category'); ?></header>
	<div class="panel-body">
	<fieldset>
	<?php
		echo $this->Form->input('name', array('class'=>'form-control', 'validation'=>'notnull', 'humanName'=>'Category Name'));
		echo $this->Form->input('order', array('class'=>'form-control', 'validation'=>'notnull', 'humanName'=>'Order'));
		echo $this->Form->input('policyOnlyCategory', array('class'=>'tooltipped', 'title'=>'Indicating that this is policy only will prevent you from uploading documents manually into this category.'));
	?>
	</fieldset>
<input type="button" value="Submit" id="submitButton"></form>
</div>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#DocumentCategoryAddForm')[0]);
});
</script>