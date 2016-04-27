<div class="panel">

<div class="documents index">
	<header class="panel-heading">
	<?php echo __('Documents'); ?></header>
	<div style="margin-top: 15px; margin-left: 15px;">
		<?php 
		 if ($showAll) echo $this->Html->link(__(' Show only custom documents'), array('controller'=>'Documents', 'action' => 'index', 0), array('title'=>'Click to show only documents that you have added specifically (not those added through technicians, chemicals, policies, etc.)', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-magnify actionButton actionButtonDownload'));
		 else echo $this->Html->link(__(' Show all documents (including reports, service agreements, etc.)'), array('controller'=>'Documents', 'action' => 'index', 1), array('title'=>'Click to reveal all documents - including all service reports, service agreements, site maps, certification documents, information document, and so forth', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-magnify actionButton actionButtonDownload')); 
		?>
	</div>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('file_type'); ?></th>
			<th><?php echo $this->Paginator->sort('document_category_id'); ?></th>
			<th><?php echo $this->Paginator->sort('order'); ?></th>
			<th><?php echo $this->Paginator->sort('size'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($documents as $document): ?>
	<tr>
		<td><?php echo h($document['Document']['id']); ?>&nbsp;</td>
		<td><?php echo h($document['Document']['name']); ?>&nbsp;</td>
		<td><?php echo h($document['Document']['file_type']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($document['DocumentCategory']['name'], array('controller' => 'document_categories', 'action' => 'view', $document['DocumentCategory']['id'])); ?>
		</td>
		<td><?php echo h($document['Document']['order']); ?>&nbsp;</td>
		<td><?php echo h($document['Document']['size']); ?>&nbsp;</td>
		<td><div class="activeinactiveswitchCompact transitionable"><?php if ($document['Document']['active']==1) $checkState="checked"; else $checkState=""; ?>
			<input name="data[Document][active]" type="checkbox" id="DocumentActive<?php echo $document['Document']['id']; ?>" value="1" <?php echo $checkState; ?>>
			<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" for="DocumentActive<?php echo $document['Document']['id']; ?>" id="activeToggle<?php echo $document['Document']['id']; ?>" documentId="<?php echo $document['Document']['id']; ?>"> </label></div></td>
		<td><?php echo h($document['Document']['modified']); ?>&nbsp;</td>
		<td>
			<?php echo h($document['User']['username']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__(''), array('action' => 'download', $document['Document']['id']), array('title'=>'Click to download this document', 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $document['Document']['id']), array('title'=>'Click to edit this document', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $document['Document']['id']), array('title'=>'Click to permanently delete this document', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $document['Document']['id'])); ?>
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
//we want to be able to modify the look of some input elements
$(document).ready(function() {
	//change the value and add a notification to the active/deactive toggle checkbox
	//setActiveToggle('DocumentActive', 'activeToggle', "Document set to active", "Document set to inactive", "Selected clients will be able to view the document.", "Clients will not be able to see the document while it remains inactive.");
});
//now let's set our buttons to click and make an ajax call
toggles = $('.indexActiveToggle');
for (i=0;i!=toggles.length;i++) {
	documentId = $(toggles[i]).attr('documentId');
	(function (documentId) {
		$(toggles[i]).click(function () {
			setTimeout(function() {
				state = $('#DocumentActive'+documentId)[0].checked;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Documents','action'=>'toggleActive'));?>/' + documentId + '/' + state + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
						if (data=="0") {
							ntitle = "Document " + documentId + " set to inactive";
							nmessage = "The document is now inactive. Clients will no longer have access to this document.";
						}
						else {
							ntitle = "Document " + documentId + " set to active";
							nmessage = "You have successfully activated the document. Clients who have permission will now have access to this document.";
						}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(documentId);
}
</script>