<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="users form">
<?php echo $this->Form->create('Users.User'); ?>
	<header class="panel-heading"><?php echo __('Edit Your Settings'); ?></header>
	<div class="panel-body"><fieldset>
	<?php
		echo $this->Form->input('id', array('value'=>$user['User']['id']));
		echo $this->Form->input('detailsId', array('type'=>'hidden','value'=>isset($details['UserDetail']['id']) ? $details['UserDetail']['id'] : ''));
		echo $this->Form->input('email', array('class'=>'form-control', 'value'=>$user['User']['email'], 'validation'=>'notnull email', 'humanName'=>'E-mail address'));
		echo $this->Form->input('realName', array('class'=>'form-control','value'=>isset($details['UserDetail']['realName']) ? $details['UserDetail']['realName'] : '' ));
		echo $this->Form->input('phone', array('class'=>'form-control','value'=>isset($details['UserDetail']['phone']) ? $details['UserDetail']['phone'] : '' ));
		echo $this->Form->end(array("div"=>false, "wrap"=>false, "id"=>"submitButton", "type"=>"button"));
	?>
	</fieldset>
</div>
</section>
<script>
//we want to be able to modify the look of some input elements
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#UserEditSettingsForm')[0]);
});
</script>