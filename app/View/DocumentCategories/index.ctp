<div class="panel">

	<div class="documentCategories index">
		<header class="panel-heading">
			<?php echo __('Document Categories'); ?>
		</header>
		<table class="table-hover" cellpadding="0" cellspacing="0">
			<tr>
				<th><?php echo $this->Paginator->sort('id'); ?>
				</th>
				<th><?php echo $this->Paginator->sort('name'); ?>
				</th>
				<th><?php echo $this->Paginator->sort('order'); ?>
				</th>
				<th><?php echo $this->Paginator->sort('Policies only'); ?>
				</th>
				<th class="actions"><?php echo __('Actions'); ?>
				</th>
			</tr>
			<?php foreach ($documentCategories as $documentCategory): ?>
			<tr>
				<td><?php echo h($documentCategory['DocumentCategory']['id']); ?>&nbsp;</td>
				<td><?php echo h($documentCategory['DocumentCategory']['name']); ?>&nbsp;</td>
				<td style="text-align: center;"><?php echo h($documentCategory['DocumentCategory']['order']); ?>&nbsp;</td>
				<td><?php if ($documentCategory['DocumentCategory']['policyOnlyCategory']) echo "Yes"; else echo "No"; ?></td>
				<td class="actions">
					<?php echo $this->Html->link(__(''), array('action' => 'edit', $documentCategory['DocumentCategory']['id']), array('title'=>'Click to edit this category', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
					<?php if ($documentCategory['DocumentCategory']['persistent'] == 0) echo $this->Form->postLink(__(''), array('action' => 'delete', $documentCategory['DocumentCategory']['id']), array('title'=>'Click to permanently delete this category', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $documentCategory['DocumentCategory']['id'])); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<p class="page-info">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>
		</p>
		<div class="paging">
			<?php
			echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
			echo $this->Paginator->numbers(array('separator' => ''));
			echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
			?>
		</div>
	</div>
</div>
