<div class="panel">
<div class="devices index">
<header class="panel-heading"><?php echo __('Devices'); ?></header>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('label'); ?></th>
			<th><?php echo $this->Paginator->sort('location'); ?></th>
			<th><?php echo $this->Paginator->sort('client_id'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('lastChecked', "Last Checked"); ?></th>
			<th><?php echo $this->Paginator->sort('damaged'); ?></th>
			<th><?php echo $this->Paginator->sort('missing'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('installed'); ?></th>
			<th><?php echo $this->Paginator->sort('modified', "Last Modified"); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($devices as $device): ?>
	<tr>
		<td><?php echo h($device['Device']['id']); ?>&nbsp;</td>
		<td><?php echo h($device['Device']['label']); ?>&nbsp;</td>
		<td><?php echo h($device['Location']['name']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($device['Client']['name'], array('controller' => 'clients', 'action' => 'view', $device['Client']['id'])); ?>
		</td>
		<td><?php echo h($device['DeviceType']['name']); ?>&nbsp;</td>
		<td><?php echo h($device['Device']['lastChecked']); ?>&nbsp;</td>
		<td><?php if ($device['Device']['damaged']) echo "Yes"; else echo "No"; ?></td>
		<td><?php if ($device['Device']['missing']) echo "Yes"; else echo "No"; ?></td>
		<td><div class="activeinactiveswitchCompact transitionable"><?php if ($device['Device']['active']==1) $checkState="checked"; else $checkState=""; ?>
			<input name="data[Device][active]" type="checkbox" id="DeviceActive<?php echo $device['Device']['id']; ?>" value="1" <?php echo $checkState; ?>>
			<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" for="DeviceActive<?php echo $device['Device']['id']; ?>" id="activeToggle<?php echo $device['Device']['id']; ?>" deviceId="<?php echo $device['Device']['id']; ?>"> </label></div></td>
		<td><?php if ($device['Device']['installed']) echo "Yes"; else echo "No"; ?></td>
		<td><?php echo h($device['Device']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $device['Device']['id']), array('title'=>'Click to edit this device', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
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
<script>
//now let's set our buttons to click and make an ajax call
toggles = $('.indexActiveToggle');
for (i=0;i!=toggles.length;i++) {
	deviceId = $(toggles[i]).attr('deviceId');
	(function (deviceId) {
		$(toggles[i]).click(function () {
			setTimeout(function() {
				state = $('#DeviceActive'+deviceId)[0].checked;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Devices','action'=>'toggleActive'));?>/' + deviceId + '/' + state + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
						if (data=="0") {
							ntitle = "Device " + deviceId + " set to inactive";
							nmessage = "The device is now inactive. The device cannot be installed or monitored and will not generate notifications.";
						}
						else {
							ntitle = "Device " + deviceId + " set to active";
							nmessage = "You have successfully activated the device. The device can be installed and monitored by technicians..";
						}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(deviceId);
}
</script>