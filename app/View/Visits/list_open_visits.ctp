<div class="panel">

<div class="visits index">
	<header class="panel-heading"><?php echo __('Incomplete Visits'); ?></header>
	<p style="margin-left: 15px; padding-top:15px;font-size:14px;">
	Below is a list of visits which have been automatically opened by the system or manually created by an administrator. These have not been completed correctly by a technician, and will continue to show up in 
	notifications until they have been completed. You can delete these visits or manually set the visit to complete (better). Manually setting a visit status to complete <i>will not</i> generate a service report for the
	visit.
	<?php if (isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] == 1) echo "<br><br>Showing OVERDUE VISITS ONLY."; ?>
	</p>
	<label class="free-label" style="margin-left: 15px; margin-top:10px;font-size:14px;">Rows highlighted in red are OVERDUE.</label>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('client_id'); ?></th>
			<th><?php echo $this->Paginator->sort('purpose'); ?></th>
			<th><?php echo $this->Paginator->sort('timeRequested', "Date Requested"); ?></th>
			<th><?php echo $this->Paginator->sort('timeDue', "Date due (click to edit)"); ?></th>
			<th><?php echo $this->Paginator->sort('status', "Status (choose to change)"); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($visits as $visit): ?>
	<tr <?php if ($visit['Visit']['timeDue'] != null && strtotime($visit['Visit']['timeDue']) < time()) echo "class='cell-error'"; ?>>
		<td><?php echo h($visit['Visit']['id']); ?>&nbsp;</td>
		<td><?php echo h($visit['Client']['name']); ?>&nbsp;</td>
		<td><?php echo h($visit['Visit']['purpose']); ?>&nbsp;</td>
		<td><?php echo h(date("Y-m-d", strtotime($visit['Visit']['timeRequested']))); ?>&nbsp;</td>
		<?php if ($visit['Visit']['timeDue'] != null) $visit['Visit']['timeDue'] = date("Y-m-d", strtotime($visit['Visit']['timeDue'])); ?>
		<td><?php echo $this->Form->input('lastChecked', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker form-control", 
				"value"=>$visit['Visit']['timeDue'], "id"=>"visitDate".$visit['Visit']['id'], "visitId"=>$visit['Visit']['id'])); ?>&nbsp;</td>
		<td style="text-align: center;">
		<select class="statuschooser" id="statusIn<?php echo $visit['Visit']['id']; ?>" visitId="<?php echo $visit['Visit']['id']; ?>">
			<option value="created"<?php if ($visit['Visit']['status']=="created") echo " selected";?>>Created</option>
			<option value="in progress"<?php if ($visit['Visit']['status']=="in progress") echo " selected";?>>In Progress</option>
			<option value="complete"<?php if ($visit['Visit']['status']=="complete") echo " selected";?>>Complete</option>
		</select>
		</td>
		<td style="text-align: center;">
			<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $visit['Visit']['id']), array('title'=>'Click to permanently delete this visit', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('WARNING: deleting this visit could result in a technician missing an important service')); ?>
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
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});
duedates = $('.datepicker');
statuses = $('.statuschooser');
for (i=0;i!=duedates.length;i++) {
	visitId = $(duedates[i]).attr('visitId');
	(function (visitId) {
		$(duedates[i]).change(function () {
			setTimeout(function() {
				value = $('#visitDate'+visitId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Visits','action'=>'saveDueDate'));?>/' + visitId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
		       				if (data == "pastdate") {
		       					ntitle = "Visit due date NOT saved.";
								nmessage = "Your selected date is in the past. You must choose a date in the future.";
		       				}
		       				else {
								ntitle = "Visit due date saved.";
								nmessage = "You have successfully changed the due date for this visit to " + data +".";
		       				}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(visitId);
}
for (i=0;i!=statuses.length;i++) {
	visitId = $(statuses[i]).attr('visitId');
	(function (visitId) {
		$(statuses[i]).change(function () {
			setTimeout(function() {
				value = $('#statusIn'+visitId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Visits','action'=>'saveStatus'));?>/' + visitId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
							ntitle = "Visit status saved.";
							nmessage = "You have successfully changed the status for this visit to " + data +".";
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(visitId);
}
</script>