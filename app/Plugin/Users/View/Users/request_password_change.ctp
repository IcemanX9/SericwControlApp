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
<div class="users loginForm">
	<div class="loginHeader">Forgot your password?</div>
	<fieldset> 
		<span class="resetInstructions">Please enter the email you used for registration and you'll get an email with further instructions.</span>
		<?php
			echo $this->Form->create($model, array(
				'url' => array(
					'admin' => false,
					'action' => 'reset_password')));
			echo $this->Form->input('email', array(
				'label' => false,
				'placeholder' => 'Your e-mail address',
				'class'=>'form-control',
				'style'=>'margin-top: 15px;'));
			echo $this->Form->submit(__d('users', 'Reset'));
			echo $this->Form->end();
	?>
	</fieldset>
	<div class="loginFooter">Don't have an account? Visit our website!</div>
</div>
