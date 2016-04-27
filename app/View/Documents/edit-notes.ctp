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
			<p style="margin-left: 15px; padding-top:15px;font-size:14px;">
			You are unable to edit the contents or details of this document because they are protected. You may add or edit notes for this document if you wish to note some additional information or amendment.
			</p>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('notes', array('class'=>'form-control', 'type'=>'textarea'));
		?>
		</div>
		<?php echo $this->Form->end(array('label'=>'Submit', 'style'=>'padding: 6px 12px;font-size: 17px;text-align: center;vertical-align: middle;background-image: none;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;text-shadow: none;margin-left: 10px;background-color: #53bee6;border-color: #53BEE6;color: #FFFFFF;')); ?>
	</fieldset>
	<br><br><Br>
</div>
</section>
<script>
$(document).ready(function() {
</script>