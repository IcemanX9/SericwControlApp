<?php
/**
 * Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php echo $this->Session->flash('auth');?>
<div class="users loginForm">
	<div class="loginHeader">Sign In Now</div>
	<fieldset> 
		<?php
			echo $this->Form->create($model, array(
				'action' => 'login',
				'id' => 'LoginForm'));
			echo $this->Form->input('username', array('placeholder'=>'Username', 'label'=>false, 'class'=>'form-control'));
			echo $this->Form->input('password', array('placeholder'=>'Password', 'label'=>false, 'class'=>'form-control'));

			echo '<div class="rememberMeWrapper"><span style="float:left">' . $this->Form->input('remember_me', array('type' => 'checkbox', 'label' =>  __d('users', 'Remember Me'))) . "</span>";
			echo '<span style="float:right">' . $this->Html->link(__d('users', 'Forgot Password?'), array('action' => 'reset_password')) . '</span></div>';

			echo $this->Form->hidden('User.return_to', array(
				'value' => $return_to));
			echo $this->Form->end(__d('users', 'Sign In'));
		?>
	</fieldset>
	<div class="loginFooter">Don't have an account? Visit our website!</div>
</div>