<div class="devices view">
<h2><?php echo __('Device'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($device['Device']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Location'); ?></dt>
		<dd>
			<?php echo h($device['Device']['location']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Latitude'); ?></dt>
		<dd>
			<?php echo h($device['Device']['latitude']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Longitude'); ?></dt>
		<dd>
			<?php echo h($device['Device']['longitude']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client'); ?></dt>
		<dd>
			<?php echo $this->Html->link($device['Client']['name'], array('controller' => 'clients', 'action' => 'view', $device['Client']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($device['Device']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('CheckFrequency'); ?></dt>
		<dd>
			<?php echo h($device['Device']['checkFrequency']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('LastChecked'); ?></dt>
		<dd>
			<?php echo h($device['Device']['lastChecked']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Damaged'); ?></dt>
		<dd>
			<?php echo h($device['Device']['damaged']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('DamagedDate'); ?></dt>
		<dd>
			<?php echo h($device['Device']['damagedDate']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Missing'); ?></dt>
		<dd>
			<?php echo h($device['Device']['missing']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('MissingDate'); ?></dt>
		<dd>
			<?php echo h($device['Device']['missingDate']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($device['Device']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Archived'); ?></dt>
		<dd>
			<?php echo h($device['Device']['archived']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Installed'); ?></dt>
		<dd>
			<?php echo h($device['Device']['installed']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($device['Device']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($device['Device']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Notes'); ?></dt>
		<dd>
			<?php echo h($device['Device']['notes']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Device'), array('action' => 'edit', $device['Device']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Device'), array('action' => 'delete', $device['Device']['id']), null, __('Are you sure you want to delete # %s?', $device['Device']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Devices'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Device'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
	</ul>
</div>
