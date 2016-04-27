<div class="panel">
<div class="companies index">
	<header class="panel-heading"><?php echo __('Companies'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th>Logo</th>
			<th>Document Header</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($companies as $company): ?>
	<tr>
		<td><?php echo h($company['Company']['id']); ?>&nbsp;</td>
		<td><?php echo h($company['Company']['name']); ?>&nbsp;</td>
		<td><img src="<?php echo $this->Html->url(array("controller" => "companies", "action" => "getLogo", $company['Company']['id'])); ?>" style="vertical-align: middle; width: 100px; height: 50px;"></td>
		<td><img src="<?php echo $this->Html->url(array("controller" => "companies", "action" => "getHeader", $company['Company']['id'])); ?>" style="vertical-align: middle; width: 240px; height: 27px;"></td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $company['Company']['id']), array('title'=>'Click to edit this company', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
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