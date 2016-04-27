<div class="panel">
<div class="technicians index">
	<header class="panel-heading"><?php echo __('Technicians'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('regNo', 'Registration Number'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id', 'Username'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('modified', 'Last Modified'); ?></th>
			<th><?php echo $this->Paginator->sort('document_id', "Document"); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($technicians as $technician): ?>
	<tr>
		<td><?php echo h($technician['Technician']['id']); ?>&nbsp;</td>
		<td><?php echo h($technician['Technician']['name']); ?>&nbsp;</td>
		<td><?php echo h($technician['Technician']['regNo']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($technician['User']['username'], array('controller' => 'users', 'action' => 'view', $technician['User']['id'])); ?>
		</td>
		<td><div class="activeinactiveswitchCompact transitionable"><?php if ($technician['Technician']['active']==1) $checkState="checked"; else $checkState=""; ?>
			<input name="data[Technician][active]" type="checkbox" id="TechnicianActive<?php echo $technician['Technician']['id']; ?>" value="1" <?php echo $checkState; ?>>
			<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" for="TechnicianActive<?php echo $technician['Technician']['id']; ?>" id="activeToggle<?php echo $technician['Technician']['id']; ?>" TechnicianId="<?php echo $technician['Technician']['id']; ?>"> </label></div></td>
		<td><?php echo h($technician['Technician']['modified']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($technician['Document']['name'], array('controller' => 'documents', 'action' => 'edit', $technician['Document']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'download', $technician['Technician']['document_id']), array('title'=>"Click to download the technician's certification document", 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $technician['Technician']['id']), array('title'=>'Click to edit this technician', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $technician['Technician']['id']), array('title'=>'Click to permanently delete this technician and associated document', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s and associated certification document?', $technician['Technician']['id'])); ?>
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
<script>
//now let's set our buttons to click and make an ajax call
toggles = $('.indexActiveToggle');
for (i=0;i!=toggles.length;i++) {
	technicianId = $(toggles[i]).attr('technicianId');
	(function (technicianId) {
		$(toggles[i]).click(function () {
			setTimeout(function() {
				state = $('#TechnicianActive'+technicianId)[0].checked;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Technicians','action'=>'toggleActive'));?>/' + technicianId + '/' + state + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
						if (data=="0") {
							ntitle = "Technician " + technicianId + " set to inactive";
							nmessage = "The technician is now inactive. They will not be able to log into the mobile app.";
						}
						else {
							ntitle = "Technician " + technicianId + " set to active";
							nmessage = "You have successfully activated the technician. They are now able to access the mobile app.";
						}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(technicianId);
}
</script>