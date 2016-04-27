<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="users form">
<?php echo $this->Form->create($model); ?>
	<header class="panel-heading"><?php echo __('Edit User'); ?></header>
		<div class="panel-body">
			<fieldset>
			<?php
			echo $this->Form->input('id');
			echo $this->Form->input('username', array(
					'label' => __d('users', 'Username for user login (must be unique)'),
					'class'=>'form-control',
					'humanName'=>'Username',
					'validation'=>'notnull alphanumeric unique-users-username',
					'value'=> $this->request->data['User']['username']));
			echo $this->Form->input('email', array(
					'label' => __d('users', 'E-mail'),
					'type' => 'email',
					'class'=>'form-control',
					'validation'=>'email notnull nottooshort',
					'error' => array('isValid' => __d('users', 'Must be a valid email address'),
							'isUnique' => __d('users', 'An account with that email already exists'))));
			echo $this->Form->input('password', array(
					'label' => __d('users', 'Enter a new password to reset. Leaving this blank will keep the current password.'),
					'class'=>'form-control passwordInput',
					'humanName'=>'Password Reset',
					'value'=>'',
					'required'=>'',
					'validation'=>'password-reset',
					'type' => 'password'));
			echo $this->Form->input('temppassword', array(
					'class'=>'tooltipped form-control',
					'validation'=>'password-reset',
					'title'=>'Confirm the password by entering it again',
					'value'=>'',
					'required'=>'',
					'humanName'=>'Confirm Password',
					'label' => __d('users', 'Password (confirm)'),
					'type' => 'password'));
			echo $this->Form->input('role', array('class'=>'tooltipped form-control', 'title'=>'Choose the user role. Admin users have ability to add other users and reset passwords for other users.',
					'humanName'=>'User access',
					'label'=>'Access',
					'options' => array("user"=>"User", "admin"=>"Administrator"))); 
			echo $this->Form->end(array("div"=>false, "wrap"=>false, "id"=>"submitButton", "type"=>"button"));
			?> 
			</fieldset>
</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#UserEditForm')[0]);
	//change the value and add a notification to the active/deactive toggle checkbox
	setActiveToggle('UserActive', 'activeToggle', "User set to active", "User set to inactive", "This user will be able to log in to the system.", "This user will NOT be able to log in.");
});
</script>