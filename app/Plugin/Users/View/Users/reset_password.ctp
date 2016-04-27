<div class="users loginForm">
	<div class="loginHeader">Reset your password</div>
	<fieldset> 
<?php
	echo $this->Form->create($model, array(
		'url' => array(
			'action' => 'reset_password',
			$token)));
	echo $this->Form->input('new_password', array(
		'label' => false,
		'placeholder' => 'Your new password',
		'class'=>'form-control',
		'style'=>'margin-top: 15px;',
		'type' => 'password'));
	echo $this->Form->input('confirm_password', array(
		'label' => false,
		'placeholder' => 'Confirm the password',
		'class'=>'form-control',
		'type' => 'password'));
	echo $this->Form->submit(__d('users', 'Submit'));
	echo $this->Form->end();
?>
	</fieldset>
	<div class="loginFooter"></div>
</div>