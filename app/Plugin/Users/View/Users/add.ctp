<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="users form">
	<header class="panel-heading"><?php echo __('Add User'); ?></header>
	<div class="panel-body">
	<fieldset>
		<?php
			echo $this->Form->create($model);
			echo $this->Form->input('username', array(
				'class'=>'form-control', 
				'validation'=>'alphanumeric unique-users-username notnull nottooshort', 
				'humanName'=>"User's Name", 
				'label' => __d('users', "Usename  (used as login, no spaces)")));
			echo $this->Form->input('email', array(
				'label' => __d('users', 'E-mail'),
				'type' => 'email',
				'class'=>'form-control',
				'validation'=>'email notnull nottooshort',
				'error' => array('isValid' => __d('users', 'Must be a valid email address'),
				'isUnique' => __d('users', 'An account with that email already exists'))));
			echo $this->Form->input('role', array(
					'label' => 'Access',
					'options' => array('user'=>'User', 'admin'=>'Administrator'),
					'class'=>'form-control',
			));
			echo $this->Form->input('password', array(
				'label' => __d('users', 'Password'),
				'class'=>'form-control',
				'validation'=>'password',
				'type' => 'password'));
			echo $this->Form->input('temppassword', array(
				'class'=>'tooltipped form-control',
				'validation'=>'password',
				'title'=>'Confirm the password by entering it again',
				'humanName'=>'Confirm Password',
				'label' => __d('users', 'Password (confirm)'),
				'type' => 'password'));
			echo $this->Form->end(array("div"=>false, "wrap"=>false, "id"=>"submitButton", "type"=>"button"));
			?>
		</fieldset>
	</div>
</div>
</section>
<script>
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#UserAddForm')[0]);
});
</script>