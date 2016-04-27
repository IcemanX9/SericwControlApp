<?php
//various constants
$cakeDescription = __d('service control manager', 'The Pest Control Management Software');
$siteUrl = "http://www.servicecontrol.co.za/servicecontrol/";

function sanitize($string = '', $is_filename = FALSE)
{
	// Replace all weird characters with dashes
	$string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);

	// Only allow one dash separator at a time (and make string lowercase)
	return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<?php echo $this->Html->charset(); ?>
	<title>
		Service Control Manager - Client Portal
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('pestman.generic');
		echo $this->Html->css('engage.itoggle');
		echo $this->Html->css('tipTip');
		echo $this->Html->css('font-awesome');
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
	<script>init();</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php echo $this->Html->image('Hamburger.png', array('id'=>'hide-nav-button', 'class'=>'tooltipped', 'data-placement'=>'right', 'title'=>'Toggle Navigation')); ?>
			<span id="logoPartOne">SERVICE</span><span id="logoPartTwo">CONTROL</span><span id="headerCaption"></<?php echo $this->Html->link($cakeDescription, ''); ?></span>
		</div>
		<div id="main">
			<div id="menu">
				<script>
				activeLink = "<?php if (isset($_GET['link'])) echo $_GET['link']; else echo "dashboard-link"; ?>";
				</script>
				<ul class="menu-list">
					<li id="sighting-link" class="menu-item"><a href="#reportsighting" style="text-decoration: none; color: #aeb2b7;"><?php echo $this->Html->image('icons/configuration.png', array('class'=>'menu-icon')); ?>Report a sighting</a></li>
				
					<li id="dashboard-link" class="menu-item"><?php echo $this->Html->image('icons/disc.png', array('class'=>'menu-icon')); ?>Table	of contents</li>
					
					<?php $count=0; foreach ($documentCategories as $cat) {?>	
						
					<li id="documents-link-<?php echo $count; ?>" class="menu-item scrolly transitionable-slow">
						<a href="#<?php echo sanitize($cat); ?>" style="text-decoration: none; color: #aeb2b7;"><?php echo $this->Html->image('icons/copy-item.png', array('class'=>'menu-icon')); ?><?php echo $cat; ?></a>
					</li>
						
					<?php $count++; } ?>
					
					<a href="<?php echo $siteUrl .'logout'; ?>"
						style="text-decoration: none;">
						<li id="dashboard-link" class="menu-item"><?php echo $this->Html->image('icons/lock.png', array('class'=>'menu-icon')); ?>Log Out</li>
					</a>
				</ul>
			</div>
			<div id="content">
				<?php echo $this->Session->flash(); ?>
				<?php echo $this->fetch('content'); ?>
				<div class="verticalSpace"></div>
			</div>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
	<?php //echo $this->element('sql_dump'); ?>
	<script>
	setup();
	$('.scrolly').hover(function (e) {
							$(e.currentTarget).css('margin-left', Math.min(140 - $(e.currentTarget).find('a').width(), 12) + 'px');
							$(e.currentTarget).addClass('menu-item-hover');
						},
						function (e) {
							$(e.currentTarget).css('margin-left', '');
							$(e.currentTarget).removeClass('menu-item-hover');
						}
	);
	</script>
</body>
</html>
