<div class="panel">
<div class="users index">
	<header class="panel-heading"><?php echo __('Users'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('username'); ?></th>
		<th><?php echo $this->Paginator->sort('role'); ?></th>
		<th><?php echo $this->Paginator->sort('created'); ?></th>
		<th class="actions"><?php echo __d('users', 'Actions'); ?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		?>
		<tr<?php echo $class; ?>>
			<td><?php echo $this->Html->link($user[$model]['username'], array('action' => 'view', $user[$model]['id'])); ?></td>
			<td><?php echo $user[$model]['role']; ?></td>
			<td><?php echo $user[$model]['created']; ?></td>
			<td class="actions"> 
				<?php echo $this->Html->link(__(''), array('action' => 'edit', $user['User']['id']), array('title'=>'Click to edit this user', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
				<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $user['User']['id']), array('title'=>'Click to permanently delete this user', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $user['User']['username'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<p class="page-info">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
</div>