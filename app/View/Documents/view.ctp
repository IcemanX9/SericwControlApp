<div class="documents view">
<h2><?php echo __('Document'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($document['Document']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($document['Document']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Filename'); ?></dt>
		<dd>
			<?php echo h($document['Document']['filename']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('File Type'); ?></dt>
		<dd>
			<?php echo h($document['Document']['file_type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Document Category'); ?></dt>
		<dd>
			<?php echo $this->Html->link($document['DocumentCategory']['name'], array('controller' => 'document_categories', 'action' => 'view', $document['DocumentCategory']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Order'); ?></dt>
		<dd>
			<?php echo h($document['Document']['order']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mime'); ?></dt>
		<dd>
			<?php echo h($document['Document']['mime']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Meta'); ?></dt>
		<dd>
			<?php echo h($document['Document']['meta']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Size'); ?></dt>
		<dd>
			<?php echo h($document['Document']['size']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($document['Document']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($document['Document']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($document['Document']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($document['User']['id'], array('controller' => 'users', 'action' => 'view', $document['User']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Document'), array('action' => 'edit', $document['Document']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Document'), array('action' => 'delete', $document['Document']['id']), null, __('Are you sure you want to delete # %s?', $document['Document']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Documents'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Document Categories'), array('controller' => 'document_categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document Category'), array('controller' => 'document_categories', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Technicians'), array('controller' => 'technicians', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Technician'), array('controller' => 'technicians', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Technicians'); ?></h3>
	<?php if (!empty($document['Technician'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Username'); ?></th>
		<th><?php echo __('Pin'); ?></th>
		<th><?php echo __('Active'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Document Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($document['Technician'] as $technician): ?>
		<tr>
			<td><?php echo $technician['id']; ?></td>
			<td><?php echo $technician['name']; ?></td>
			<td><?php echo $technician['username']; ?></td>
			<td><?php echo $technician['pin']; ?></td>
			<td><?php echo $technician['active']; ?></td>
			<td><?php echo $technician['created']; ?></td>
			<td><?php echo $technician['modified']; ?></td>
			<td><?php echo $technician['document_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'technicians', 'action' => 'view', $technician['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'technicians', 'action' => 'edit', $technician['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'technicians', 'action' => 'delete', $technician['id']), null, __('Are you sure you want to delete # %s?', $technician['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Technician'), array('controller' => 'technicians', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Clients'); ?></h3>
	<?php if (!empty($document['Client'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Code'); ?></th>
		<th><?php echo __('Phone'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Latitude'); ?></th>
		<th><?php echo __('Longitude'); ?></th>
		<th><?php echo __('Active'); ?></th>
		<th><?php echo __('Company Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($document['Client'] as $client): ?>
		<tr>
			<td><?php echo $client['id']; ?></td>
			<td><?php echo $client['name']; ?></td>
			<td><?php echo $client['code']; ?></td>
			<td><?php echo $client['phone']; ?></td>
			<td><?php echo $client['address']; ?></td>
			<td><?php echo $client['latitude']; ?></td>
			<td><?php echo $client['longitude']; ?></td>
			<td><?php echo $client['active']; ?></td>
			<td><?php echo $client['company_id']; ?></td>
			<td><?php echo $client['created']; ?></td>
			<td><?php echo $client['modified']; ?></td>
			<td><?php echo $client['user_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'clients', 'action' => 'view', $client['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'clients', 'action' => 'edit', $client['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'clients', 'action' => 'delete', $client['id']), null, __('Are you sure you want to delete # %s?', $client['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
