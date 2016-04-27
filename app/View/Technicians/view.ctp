<div class="technicians view">
<h2><?php echo __('Technician'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($technician['User']['id'], array('controller' => 'users', 'action' => 'view', $technician['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Archived'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['archived']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Notes'); ?></dt>
		<dd>
			<?php echo h($technician['Technician']['notes']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Document'); ?></dt>
		<dd>
			<?php echo $this->Html->link($technician['Document']['name'], array('controller' => 'documents', 'action' => 'view', $technician['Document']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Technician'), array('action' => 'edit', $technician['Technician']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Technician'), array('action' => 'delete', $technician['Technician']['id']), null, __('Are you sure you want to delete # %s?', $technician['Technician']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Technicians'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Technician'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Documents'), array('controller' => 'documents', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document'), array('controller' => 'documents', 'action' => 'add')); ?> </li>
	</ul>
</div>
