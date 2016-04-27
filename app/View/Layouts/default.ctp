<?php
//various constants
$cakeDescription = __d('service control manager', 'The Pest Control Management Software');
$siteUrl = "http://www.servicecontrol.co.za/servicecontrol/";
?>
<!DOCTYPE html>
<html>
<head>
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<?php echo $this->Html->charset(); ?>
	<title>
		Service Control Manager
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
		echo $this->Html->script('tinymce.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		
		App::import('Model', 'Users.UserDetail');
		$userDetailModel = new UserDetail();
		$details = $userDetailModel->find('first', array('conditions'=>array('user_id'=>$userData['id'])));
		if (isset($details['UserDetail']['realName']) && $details['UserDetail']['realName'] != '') $name = $details['UserDetail']['realName'];
		else $name = $userData['username'];
	?>
	<script>
	currentController = "<?php echo $this->params['controller']; ?>";
	init(currentController); 
	</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php echo $this->Html->image('Hamburger.png', array('id'=>'hide-nav-button', 'class'=>'tooltipped', 'data-placement'=>'right', 'title'=>'Toggle Navigation')); ?>
			<span id="logoPartOne">SERVICE</span><span id="logoPartTwo">CONTROL </span><span id="headerCaption"></<?php echo $this->Html->link($cakeDescription, ''); ?></span>
			<div class="headerUsername"><?php echo $name; ?><span class="fa fa-sort-asc headerdropdown"></span></div>
		</div>
		<div class="userOptions transitionable">
			<div class="userOptionsLinks">
				<a href="<?php echo $siteUrl .'users/change_password'; ?>" style="color: #777;">
					<div style="float:left; margin-top: 9px; margin-left:28px;"><span class="fa fa-key" style="font-size:22px;"></span><br><span style="font-size:12px">Change Password
					</div>
				</a>
				<a href="<?php echo $siteUrl .'users/edit_settings'; ?>" style="color: #777;">
					<div style="float:left; margin-top: 9px; margin-left:38px;"><span class="fa fa-cog" style="font-size:22px;"></span><br><span style="font-size:12px">Edit Settings
					</div>
				</a>
			</div>
			<a href = "<?php echo $siteUrl .'logout'; ?>" style="text-decoration: none;"><div class="userOptionsLogout"><div class="fa fa-lock"><br>LOG OUT</div></div></a>
		</div>
		<div id="main">
			<div id="menu">
				<ul class="menu-list">
					<a href = "<?php echo $siteUrl;?>" style="text-decoration: none;"><li id="dashboard-link" class="menu-item">
						<?php echo $this->Html->image('icons/disc.png', array('class'=>'menu-icon')); ?>Dashboard
					</li></a>
					<li id="clients-link" class="menu-item">
						<?php echo $this->Html->image('icons/ID.png', array('class'=>'menu-icon')); ?>Clients<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>clients">List Clients</a></li>
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>clients/add">New Client</a></li>
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>clients/linkMap">Link Site Plan to Client</a></li>
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>devices/addMultiple">Link Devices</a></li>
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>Policies">List Client Policies</a></li>
							<li class="submenu-item clients-submenu"><a href="<?php echo $siteUrl;?>Policies/add">New Client Policy</a></li>
						</ul>
					</li>
					<li id="visits-link" class="menu-item">
						<?php echo $this->Html->image('icons/calendar.png', array('class'=>'menu-icon')); ?>Visits<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item visits-submenu"><a href="<?php echo $siteUrl;?>visits/viewSchedule">View Schedule</a></li>
							<li class="submenu-item visits-submenu"><a href="<?php echo $siteUrl;?>visits/listOpenVisits">Incomplete Visits</a></li>
							<li class="submenu-item visits-submenu"><a href="<?php echo $siteUrl;?>visits/listOverdueServices">Overdue Services</a></li>
							<li class="submenu-item visits-submenu"><a href="<?php echo $siteUrl;?>visits/add">Add manual visit</a></li>
						</ul>
					</li>
					<li id="devices-link" class="menu-item">
						<?php echo $this->Html->image('icons/network-pc.png', array('class'=>'menu-icon')); ?>Devices<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item devices-submenu"><a href="<?php echo $siteUrl;?>devices">List Devices</a></li>
							<li class="submenu-item devices-submenu"><a href="<?php echo $siteUrl;?>devices/add">New Device</a></li>
						</ul>
					</li>
					<li id="chemicals-link" class="menu-item">
						<?php echo $this->Html->image('icons/lab.png', array('class'=>'menu-icon')); ?>Chemicals<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item chemicals-submenu"><a href="<?php echo $siteUrl;?>chemicals">List Chemicals</a></li>
							<li class="submenu-item chemicals-submenu"><a href="<?php echo $siteUrl;?>chemicals/add">New Chemical</a></li>
						</ul>
					</li>
					<li id="technicians-link" class="menu-item">
						<?php echo $this->Html->image('icons/configuration.png', array('class'=>'menu-icon')); ?>Technicians<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>technicians">List Technicians</a></li>
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>technicians/add">New Technician</a></li>
						</ul>
					</li>
					<li id="companies-link" class="menu-item">
						<?php echo $this->Html->image('icons/line-globe.png', array('class'=>'menu-icon')); ?>Companies<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>companies">List Companies</a></li>
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>companies/add">New Company</a></li>
						</ul>
					</li>
					<li id="documents-link" class="menu-item">
						<?php echo $this->Html->image('icons/copy-item.png', array('class'=>'menu-icon')); ?>Documents<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item documents-submenu"><a href="<?php echo $siteUrl;?>Documents">List Documents</a></li>
							<li class="submenu-item documents-submenu"><a href="<?php echo $siteUrl;?>Documents/add">New Document</a></li>
							<li class="submenu-item documents-submenu"><a href="<?php echo $siteUrl;?>document_categories/">List Categories</a></li>
							<li class="submenu-item documents-submenu"><a href="<?php echo $siteUrl;?>document_categories/add">New Category</a></li>
						</ul>
					</li>
					<?php 
						if ($userData['role'] == "admin") { ?>
					<li id="users-link" class="menu-item">
						<?php echo $this->Html->image('icons/multi-agents.png', array('class'=>'menu-icon')); ?>Users<div class='menu-expander'>+</div>
						<ul class="submenu">
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>users">List Users</a></li>
							<li class="submenu-item users-submenu"><a href="<?php echo $siteUrl;?>users/add">New User</a></li>
						</ul>
					</li>
					<a href = "<?php echo $siteUrl;?>notifications" style="text-decoration: none;"><li id="notifications-link" class="menu-item">
						<?php echo $this->Html->image('icons/flag.png', array('class'=>'menu-icon')); ?>Notifications
					</li></a>
					<?php } ?>
					<a href = "<?php echo $siteUrl;?>bugs/add" style="text-decoration: none;"><li id="notifications-link" class="menu-item">
						<?php echo $this->Html->image('icons/light.png', array('class'=>'menu-icon')); ?>Log a bug!
					</li></a>
					<a href = "<?php echo $siteUrl .'logout'; ?>" style="text-decoration: none;">
						<li id="dashboard-link" class="menu-item">
						<?php echo $this->Html->image('icons/lock.png', array('class'=>'menu-icon')); ?>Log Out
					</li></a>

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
	<script>setup();</script>
</body>
</html>
