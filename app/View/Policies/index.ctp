<div class="panel">
<div class="policies index">
<header class="panel-heading"><?php echo __('Policies'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('document_id'); ?></th>
			<th><?php echo $this->Paginator->sort('modified', "Last Modified"); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($policies as $policy): ?>
	<tr>
		<td><?php echo h($policy['Policy']['id']); ?>&nbsp;</td>
		<td><?php echo h($policy['Policy']['name']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($policy['Document']['name'], array('controller' => 'documents', 'action' => 'edit', $policy['Document']['id'])); ?>
		</td>
		<td><?php echo h($policy['Policy']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'download', $policy['Policy']['document_id']), array('title'=>"Click to download the policy's information document", 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $policy['Policy']['id']), array('title'=>'Click to edit this policy', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $policy['Policy']['id']), array('title'=>'Click to permanently delete this policy', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s and associated information document?', $policy['Policy']['id'])); ?>
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
