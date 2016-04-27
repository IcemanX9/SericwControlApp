<?php 
function translatePurpose($purpose) {
	switch ($purpose) {
		case 'routine_visit':
			return "Incomplete routine service";
			break;
		case 'follow_up':
			return "Follow up";
			break;
		case 'sighting':
			return "Sighting";
			break;
		default: return $purpose;
	}
}
?>
<section class="panel">
	<header class="panel-heading">
		<?php echo __('Week visit schedule and overdue services'); ?>
	</header>
	<div class="panel-body">
		<div>
				<?php echo $this->Html->link(__(' View seven day schedule'), array('controller'=>'Visits', 'action' => 'viewSchedule'), array('title'=>'Click to full schedule and outstanding visits', 'style'=>'float: left; font-size: 18px; margin-left: 15px; margin-top: 5px; margin-right:10px;', 'class'=>'tooltipped fa fa-magnify actionButton actionButtonDownload')); ?>
				<?php echo $this->Form->input('nextService', array("id"=>"scheduleDateChooser","placeholder"=>"select date","style"=>"width: 100px; margin-top:5px;","label"=>"Show schedule for date:","title"=>"Choose a date to see specific schedule","type"=>"text", "class"=>"datepicker client-search-bar tooltipped")); ?>
		</div>
		<br>
		<?php if (!$onedayonly) { ?>
		<div class="sub-panel">
			<label class="free-label" style="font-size: 16px;">Overdue Routine
				Services</label><br>
			<table class="table-hover" cellpadding="0" cellspacing="0">
				<tr style="background-color: #FBB;">
					<th>Client Id</th>
					<th>Client name</th>
					<th>Next Service Expected (click to edit)</th>
					<th>Next Service Scheduled</th>
					<th class="actions"><?php echo __('Actions'); ?>
					</th>
				</tr>
				<?php foreach ($overdueServices as $client): ?>
				<tr class="cell-error">
					<td style="text-align: center;"><?php echo h($client['Client']['id']); ?>&nbsp;</td>
					<td><a target="_blank"
						href="<?php echo Router::url(array('controller'=>'Clients','action'=>'view', $client['Client']['id']));?>"><?php echo h($client['Client']['name']); ?>
					</a>&nbsp;</td>
					<?php if ($client['Client']['nextService'] != null) $client['Client']['nextService'] = date("Y-m-d", strtotime($client['Client']['nextService'])); ?>
					<td style="text-align: center;"><a target="_blank"
						href="<?php echo Router::url(array('controller'=>'Clients','action'=>'edit', $client['Client']['id'])); ?>"
								class="tooltipped" title="WARNING: changing this date will reset the base date for future services for this client - i.e. the following service date will be automatically calculated on the new date.">
								<?php echo h($client['Client']['nextService']); ?></a>&nbsp;</td>
					<td><?php echo $this->Form->input('nextServiceScheduled', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker scheduled form-control tooltipped", 
							"title"=>"This will set the date this service is expected to take place. It will not affect future service dates.",
							"value"=>$client['Client']['nextServiceScheduled'], "id"=>"visitDateScheduled".$client['Client']['id'], "clientId"=>$client['Client']['id'])); ?>&nbsp;</td>
					<td style="text-align: center;"><?php echo $this->Form->postLink(__(''), array('action' => 'skipToNextVisit', $client['Client']['id'], 1), array('title'=>'Click to skip this service period manually', 'class'=>'tooltipped fa fa-step-forward actionButton actionButtonDownload'), __('WARNING: no service report will be produced for this period for this client. You will only be reminded again when the next period service is due.')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<br />
		</div>
		<br>
		<div class="sub-panel">
			<label class="free-label" style="font-size: 16px;">Overdue Follow up
				and Sighting Visits</label><br>
			<table class="table-hover" cellpadding="0" cellspacing="0">
				<tr style="background-color: #FBB;">
					<th>Client Id</th>
					<th>Client Name</th>
					<th>Purpose</th>
					<th>Date visit requested</th>
					<th>Date visit due</th>
					<th>Status (choose to change)</th>
					<th class="actions"><?php echo __('Actions'); ?>
					</th>
				</tr>
				<?php foreach ($lateAdhocVisits as $visit): ?>
				<tr
				<?php if ($visit['Visit']['timeDue'] != null && strtotime($visit['Visit']['timeDue']) < time()) echo "class='cell-error'"; ?>>
					<td><?php echo h($visit['Visit']['id']); ?>&nbsp;</td>
					<td><a target="_blank"
						href="<?php echo Router::url(array('controller'=>'Clients','action'=>'view', $visit['Client']['id']));?>"><?php echo h($visit['Client']['name']); ?>
					</a>&nbsp;</td>
					<td><?php echo h(translatePurpose($visit['Visit']['purpose'])); ?>&nbsp;</td>
					<td style="text-align: center;"><?php echo h(date("Y-m-d", strtotime($visit['Visit']['timeRequested']))); ?>&nbsp;</td>
					<?php if ($visit['Visit']['timeDue'] != null) $visit['Visit']['timeDue'] = date("Y-m-d", strtotime($visit['Visit']['timeDue'])); ?>
					<td><?php echo $this->Form->input('lastChecked', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker duedate form-control", 
							"value"=>$visit['Visit']['timeDue'], "id"=>"visitDate".$visit['Visit']['id'], "visitId"=>$visit['Visit']['id'])); ?>&nbsp;</td>
					<td style="text-align: center;"><select class="statuschooser"
						id="statusIn<?php echo $visit['Visit']['id']; ?>"
						visitId="<?php echo $visit['Visit']['id']; ?>">
							<option value="created"
							<?php if ($visit['Visit']['status']=="created") echo " selected";?>>Created</option>
							<option value="in progress"
							<?php if ($visit['Visit']['status']=="in progress") echo " selected";?>>In
								Progress</option>
							<option value="complete"
							<?php if ($visit['Visit']['status']=="complete") echo " selected";?>>Complete</option>
					</select></td>
					<td style="text-align: center;"><?php echo $this->Form->postLink(__(''), array('action' => 'delete', $visit['Visit']['id'], 1), array('title'=>'Click to permanently delete this visit', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('WARNING: deleting this visit could result in a technician missing an important service')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<br>
		</div>
		<?php } ?>
		<div
			style="font-size: 16px; font-weight: bold; margin-left: 15px; padding-top: 15px;">
			<?php if ($onedayonly) echo "Visits scheduled for selected date";
					else echo "Upcoming visits and services for the next seven days";
					?>
			<p style="font-size: 12px; font-weight: normal;"> Rows highlighted in red are indicate overdue services</p> 
			<?php 
			$lastDate = "";
			foreach ($upcomingVisits as $visit) {
				if (date("Y-m-d", strtotime($visit['Visit']['timeDue'])) != date("Y-m-d", strtotime($lastDate))) {
					if ($lastDate!="") echo "</table><br>";
					$lastDate = $visit['Visit']['timeDue'];
					echo "</div><br><div class='sub-panel'><label class='free-label'>Schedule for ". date('l, j F', strtotime($lastDate)) ."</label><br>";
					echo '<table class="table-hover" cellpadding="0" cellspacing="0"><tr><th>Client Name</th><th>Purpose</th><th>Date visit requested</th><th>Date visit expected</th><th>Date visit scheduled</th><th>Status (choose to change)</th><th class="actions">'. __('Actions') .'</th></tr>';
				}
				?>
			<tr
			<?php if (isset($visit['Visit']['serviceDate']) && $visit['Visit']['serviceDate'] != null && strtotime($visit['Visit']['serviceDate']) < time()) echo "class='cell-error'"; ?>>
				<td><a target="_blank"
					href="<?php echo Router::url(array('controller'=>'Clients','action'=>'view', $visit['Client']['id']));?>"><?php echo h($visit['Client']['name']); ?>
				</a>&nbsp;</td>
				<td><?php echo h(translatePurpose($visit['Visit']['purpose'])); ?>&nbsp;</td>
				<td style="text-align: center;"><?php if ($visit['Visit']['status'] == "n/a") echo "Repeating"; else echo h(date("Y-m-d", strtotime($visit['Visit']['timeRequested']))); ?>&nbsp;</td>
				<?php if ($visit['Visit']['timeDue'] != null) $visit['Visit']['timeDue'] = date("Y-m-d", strtotime($visit['Visit']['timeDue'])); ?>
				<td style='text-align: center;'>
				<?php 
				if ($visit['Visit']['purpose'] != "Routine Visit") echo "n/a";
				else { ?><a target="_blank"
						href="<?php echo Router::url(array('controller'=>'Clients','action'=>'edit', $visit['Client']['id'])); ?>"
								class="tooltipped" title="WARNING: changing this date will reset the base date for future services for this client - i.e. the following service date will be automatically calculated on the new date.">
								<?php echo h($visit['Client']['nextService']); ?></a>&nbsp;</td>
					<?php } ?>&nbsp;</td>
				<td><?php 
				if ($visit['Visit']['status'] != "n/a") echo $this->Form->input('lastChecked', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker duedate form-control",
								"value"=>$visit['Visit']['timeDue'], "id"=>"visitDate".$visit['Visit']['id'], "visitId"=>$visit['Visit']['id']));
				else echo $this->Form->input('nextServiceScheduled', array("style"=>"width:75%;margin-top:3px;margin-bottom:-23px;text-align:center;","label"=>false,"type"=>"text", "class"=>"datepicker scheduled form-control tooltipped",
								"title"=>"This will set the date this service is expected to take place. It will not affect future service dates.",
								 "value"=>$visit['Visit']['timeDue'], "id"=>"visitDateScheduled".$visit['Client']['id'], "clientId"=>$visit['Client']['id']));
					?>&nbsp;</td>
				<td style="text-align: center;"><?php if ($visit['Visit']['status'] == "n/a") echo "Not started"; else {?>
					<select class="statuschooser"
					id="statusIn<?php echo $visit['Visit']['id']; ?>"
					visitId="<?php echo $visit['Visit']['id']; ?>">
						<option value="created"
						<?php if ($visit['Visit']['status']=="created") echo " selected";?>>Created</option>
						<option value="in progress"
						<?php if ($visit['Visit']['status']=="in progress") echo " selected";?>>In
							Progress</option>
						<option value="complete"
						<?php if ($visit['Visit']['status']=="complete") echo " selected";?>>Complete</option>
				</select> <?php } ?></td>
				<td style="text-align: center;"><?php if ($visit['Visit']['status'] != "n/a") echo $this->Form->postLink(__(''), array('action' => 'delete', $visit['Visit']['id'], 1), array('title'=>'Click to permanently delete this visit', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('WARNING: deleting this visit could result in a technician missing an important service')); 
				else echo $this->Form->postLink(__(''), array('action' => 'skipToNextVisit', $visit['Client']['id'], 1), array('title'=>'Click to skip this service period manually', 'class'=>'tooltipped fa fa-step-forward actionButton actionButtonDownload'), __('WARNING: no service report will be produced for this period for this client. You will only be reminded again when the next period service is due.'));
				?></td>
			</tr>
			<?php } ?>
			</table>
		</div>
	</div>
</section>
<script>
$(document).ready(function() {
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
});
servicedates = $('.servicedate');
scheduledates = $('.scheduled');
for (i=0;i!=servicedates.length;i++) {
	clientId = $(servicedates[i]).attr('clientId');
	(function (clientId) {
		$(servicedates[i]).change(function () {
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
								nmessage = "You have successfully changed the next service date for this client to " + data +". The schedule will only update when you refresh the page.";
		       				}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(clientId);
}
for (i=0;i!=scheduledates.length;i++) {
	clientId = $(scheduledates[i]).attr('clientId');
	(function (clientId) {
		$(scheduledates[i]).change(function () {
			setTimeout(function() {
				value = $('#visitDateScheduled'+clientId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Visits','action'=>'saveClientNextServiceScheduled'));?>/' + clientId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
		       				if (data == "pastdate") {
		       					ntitle = "Client's next service date NOT scheduled.";
								nmessage = "Your selected date is in the past. You must choose a date in the future.";
		       				}
		       				else {
								ntitle = "Client's next service date scheduled.";
								nmessage = "You have successfully changed the next scheduled service date for this client to " + data +". The schedule will only update when you refresh the page.";
		       				}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(clientId);
}
duedates = $('.duedate');
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
		       					ntitle = "Visit date NOT saved.";
								nmessage = "Your selected date is in the past. You must choose a date in the future.";
		       				}
		       				else {
								ntitle = "Visit date saved.";
								nmessage = "You have successfully changed the scheduled date for this visit to " + data +". The schedule will only update when you refresh the page.";
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
							nmessage = "You have successfully changed the status for this visit to " + data +". The schedule will only update when you refresh the page.";
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(visitId);
}
$('#scheduleDateChooser').change(function() {
	window.location.href = '<?php echo Router::url(array('controller'=>'Visits','action'=>'viewSchedule')); ?>' + "/" + $('#scheduleDateChooser').val();
});
</script>
