<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="users form">
<header class="panel-heading"><?php echo __('Change your password'); ?></header>
<div class="panel-body"><fieldset>
<p><?php echo __d('users', 'Please enter your old password because of security reasons and then your new password twice.'); ?></p>
	<?php
		echo $this->Form->create($model, array('action' => 'change_password'));
		echo $this->Form->input('old_password', array(
			'label' => __d('users', 'Old Password'),
			'class'=>'form-control',
			'validation' => 'notnull',
			'humanName'=>'Old Password',
			'type' => 'password'));
		echo $this->Form->input('new_password', array(
			'label' => __d('users', 'New Password'),
			'class'=>'form-control',
			'validation' => 'notnull password',
			'humanName'=>'New Password',
			'type' => 'password'));
		echo $this->Form->input('confirm_password', array(
			'label' => __d('users', 'Confirm'),
			'validation' => 'notnull password',
			'humanName'=>'Retype New Password',
			'class'=>'form-control',
			'type' => 'password'));
		echo $this->Form->end(array("div"=>false, "wrap"=>false, "id"=>"submitButton", "type"=>"button"));
	?>
</div>
</fieldset>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#UserChangePasswordForm')[0]);
});
</script>