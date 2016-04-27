<?php
	echo $this->Html->script('client-search-widget');
?>
<div class="panel">
<div class="clients index">
	<header class="panel-heading"><?php echo __('Clients'); ?></header>
	<form><?php echo $this->Form->input('clientSearch', array('class'=>'form-control client-search-bar', 'id'=>'clientSearch', 'label'=>'', 'placeholder'=>'Start typing a client name to search')); ?></form>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('serviceFrequency', "Service Frequency"); ?></th>
			<th><?php echo $this->Paginator->sort('phone'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('company_id'); ?></th>
			<th><?php echo $this->Paginator->sort('modified', "Last modified"); ?></th>
			<th><?php echo $this->Paginator->sort('user_id', "Username"); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($clients as $client): ?>
	<tr>
		<td><?php echo h($client['Client']['id']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['name']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['code']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['serviceFrequency']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['phone']); ?>&nbsp;</td>
		<td><div class="activeinactiveswitchCompact transitionable"><?php if ($client['Client']['active']==1) $checkState="checked"; else $checkState=""; ?>
			<input name="data[Client][active]" type="checkbox" id="ClientActive<?php echo $client['Client']['id']; ?>" value="1" <?php echo $checkState; ?>>
			<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" for="ClientActive<?php echo $client['Client']['id']; ?>" id="activeToggle<?php echo $client['Client']['id']; ?>" clientId="<?php echo $client['Client']['id']; ?>"> </label></div></td>
		<td>
			<?php echo $this->Html->link($client['Company']['name'], array('controller' => 'companies', 'action' => 'view', $client['Company']['id'])); ?>
		</td>
		<td><?php echo h($client['Client']['modified']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($client['User']['username'], array('controller' => 'users', 'action' => 'view', $client['User']['id'])); ?>
		</td>		
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'showFile', $client['Client']['user_id']), array('target'=>'_clientfile', 'title'=>'Click to view client file', 'class'=>'tooltipped fa fa-folder-open actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(''), array('controller'=>'Clients', 'action' => 'view', $client['Client']['id']), array('title'=>'Click to view client details', 'class'=>'tooltipped fa fa-search actionButton actionButtonView')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $client['Client']['id']), array('title'=>'Click to edit this client', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $client['Client']['id']), array('title'=>'Click to permanently delete this client', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $client['Client']['id'])); ?>
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
//init the search widget
initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, false);

//now let's set our buttons to click and make an ajax call
toggles = $('.indexActiveToggle');
for (i=0;i!=toggles.length;i++) {
	clientId = $(toggles[i]).attr('clientId');
	(function (clientId) {
		$(toggles[i]).click(function () {
			setTimeout(function() {
				state = $('#ClientActive'+clientId)[0].checked;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Clients','action'=>'toggleActive'));?>/' + clientId + '/' + state + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
						if (data=="0") {
							ntitle = "Client " + clientId + " set to inactive";
							nmessage = "The client is now inactive. They will not have access to their file or documents, and will receive an error message if they try to log in.";
						}
						else {
							ntitle = "Client " + clientId + " set to active";
							nmessage = "You have successfully activated the client. Clients will now be able to log in and view their documents and reports.";
						}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(clientId);
}
</script>
