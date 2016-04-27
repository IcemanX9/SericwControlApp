<div class="panel">
<div class="chemicals index">
	<header class="panel-heading"><?php echo __('Chemicals'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('targetPests', 'Target Pests'); ?></th>
			<th><?php echo $this->Paginator->sort('common'); ?></th>
			<th><?php echo $this->Paginator->sort('document_id', "Document"); ?></th>
			<th><?php echo $this->Paginator->sort('modified', 'Last Modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($chemicals as $chemical): ?>
	<tr>
		<td><?php echo h($chemical['Chemical']['id']); ?>&nbsp;</td>
		<td><?php echo h($chemical['Chemical']['name']); ?>&nbsp;</td>
		<td><?php echo h($chemical['Chemical']['code']); ?>&nbsp;</td>
		<td><?php echo h($chemical['Chemical']['targetPests']); ?>&nbsp;</td>
		<td><?php if ($chemical['Chemical']['common']) echo "Yes"; else echo "No"; ?></td>
		<td>
			<?php echo $this->Html->link($chemical['Document']['name'], array('controller' => 'documents', 'action' => 'edit', $chemical['Document']['id'])); ?>
		</td>
		<td><?php echo h($chemical['Chemical']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'download', $chemical['Chemical']['document_id']), array('title'=>"Click to download the chemical's information document", 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $chemical['Chemical']['id']), array('title'=>'Click to edit this chemical', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $chemical['Chemical']['id']), array('title'=>'Click to permanently delete this chemical and associated document', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s and associated information document?', $chemical['Chemical']['id'])); ?>
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