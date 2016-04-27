<div class="panel">

<div class="visits index">
	<header class="panel-heading"><?php echo __('Overdue Routine Visits'); ?></header>
	<p style="margin-left: 15px; padding-top:15px;font-size:14px;">
	Below is a list of client's who have not had a routine service logged within the time expected by their previous visit and their policy. To remedy the situation a technician should visit the client and log a 
	routine	visit.<br>
	<i><b>Alternatively,</b></i> you can manually specify that the routine visit has taken place by clicking the relevant button under the action column. This will reset the next service date to the next period.<br>
	<i><b>Finally,</b></i> if the next service date is incorrect, you can edit this directly. Please note that doing so will reset the date on which future service reminders will be created.
	</p>
	<table class="table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th>Client Id</th>
			<th>Client name</th>
			<th>Next Service (click to edit)</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($clients as $client): ?>
	<tr>
		<td style="text-align: center;"><?php echo h($client['Client']['id']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['name']); ?>&nbsp;</td>
		<?php if ($client['Client']['nextService'] != null) $client['Client']['nextService'] = date("Y-m-d", strtotime($client['Client']['nextService'])); ?>
		<td><?php echo $this->Form->input('nextService', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker form-control tooltipped", 
				"title"=>"WARNING: changing this date will reset the base date for future services for this client - i.e. the following service date will be automatically calculated on the new date.",
				"value"=>$client['Client']['nextService'], "id"=>"visitDate".$client['Client']['id'], "clientId"=>$client['Client']['id'])); ?>&nbsp;</td>
		<td style="text-align: center;">
			<?php echo $this->Form->postLink(__(''), array('action' => 'skipToNextVisit', $client['Client']['id']), array('title'=>'Click to skip this service period manually', 'class'=>'tooltipped fa fa-step-forward actionButton actionButtonDownload'), __('WARNING: no service report will be produced for this period for this client. You will only be reminded again when the next period service is due.')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>
</div>
<script>
$(document).ready(function() {
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});
duedates = $('.datepicker');
for (i=0;i!=duedates.length;i++) {
	clientId = $(duedates[i]).attr('clientId');
	(function (clientId) {
		$(duedates[i]).change(function () {
			setTimeout(function() {
				value = $('#visitDate'+clientId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Visits','action'=>'saveClientNextService'));?>/' + clientId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
		       				if (data == "pastdate") {
		       					ntitle = "Client's next service date NOT saved.";
								nmessage = "Your selected date is in the past. You must choose a date in the future.";
		       				}
		       				else {
								ntitle = "Client's next service date saved.";
								nmessage = "You have successfully changed the next service date for this client to " + data +".";
		       				}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(clientId);
}
</script>