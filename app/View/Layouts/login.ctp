<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'Service Control Manager - Please log in');
?>
<!DOCTYPE html>
<html>
<head>
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('pestman.generic');
		echo $this->Html->css('engage.itoggle');
		echo $this->Html->css('tipTip');
		//echo $this->Html->css('bootstrap');
		echo $this->Html->css('jquery.pnotify.default');
		echo $this->Html->css('jquery-ui-1.10.4.custom');

		echo $this->Html->script('application');
		echo $this->Html->script('jquery-ui-1.10.4.custom.min');
		echo $this->Html->script('jquery.tipTip.minified');
		echo $this->Html->script('engage.itoggle-min');
		echo $this->Html->script('jquery.easing.compatibility');
		echo $this->Html->script('jquery.pnotify.min');
		//echo $this->Html->script('bootstrap.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="loginContainer">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>		
	</div>
	<?php //echo $this->element('sql_dump'); ?>
</body>
<script>setup();</script>
</html>
