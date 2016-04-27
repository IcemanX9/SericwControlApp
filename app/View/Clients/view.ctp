<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key=<?php echo Configure::read("googleMapsKey"); ?>&sensor=false"></script>
<?php
echo $this->Html->script('maps-functions');
?>
<section class="panel">
	<div class="clients view">
		<?php echo $this->Form->create('Client'); ?>
		<header class="panel-heading">
			<?php echo __('Client Information'); ?>
		</header>
		<div class="panel-body">
			<div>
				<?php echo $this->Html->link(__(' Open Client Document File'), array('controller'=>'Documents', 'action' => 'showFile', $client['User']['id']), array('target'=>'_clientfile', 'title'=>'Click to view client file', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
				<?php echo $this->Html->link(__(' Edit Client'), array('action' => 'edit', $client['Client']['id']), array('title'=>'Click to edit this client', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
				<?php echo $this->Html->link(__(' Download Barcodes'), array('action' => 'downloadBarcodes', $client['Client']['id']), array('target'=>'_clientfile', 'title'=>'Download all barcodes in a PDF document', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
				<?php echo $this->Form->postLink(__(' Delete Client'), array('action' => 'delete', $client['Client']['id']), array('title'=>'Click to permanently delete this client', 'style'=>'font-size: 18px;', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $client['Client']['id'])); ?>
			</div>
			<fieldset>
				<dl>
					<dt>
						<?php echo __('Id'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['id']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Name'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['name']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Code'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['code']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Primary Contact'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['primaryContact']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Service Frequency'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['serviceFrequency']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Next Service'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['nextService']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Phone'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['phone']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Address'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['address']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Latitude'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['latitude']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Longitude'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['longitude']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Active'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['active']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Company'); ?>
					</dt>
					<dd>
						<?php echo $this->Html->link($client['Company']['name'], array('controller' => 'companies', 'action' => 'view', $client['Company']['id'])); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Created'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['created']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('Modified'); ?>
					</dt>
					<dd>
						<?php echo h($client['Client']['modified']); ?>
						&nbsp;
					</dd>
					<dt>
						<?php echo __('User'); ?>
					</dt>
					<dd>
						<?php echo $this->Html->link($client['User']['id'], array('controller' => 'users', 'action' => 'view', $client['User']['id'])); ?>
						&nbsp;
					</dd>
				</dl>
		
		</div>
		<div id="restOfInformation" style="margin-left: 20px;">
			<div id="googleMapsHider">
				<h2>Map</h2>
				<label>The map shows where we think your client is located.</label>
				<div id="googleMaps" class="gMapsLarge"></div>
			</div>
			<h2>Documents</h2>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Name</th>
					<th>File Type</th>
					<th>Category</th>					
					<th>Active</th>
					<th class="actions"><?php echo __('Actions'); ?></th>
				</tr>
				<?php foreach ($client['Document'] as $document): ?>
				<tr>
					<td><?php echo h($document['name']); ?>&nbsp;</td>
					<td><?php echo h($document['file_type']); ?>&nbsp;</td>
					<td><?php echo h($document['DocumentCategory']['name']); ?>&nbsp;</td>
					<td><?php if ($document['active']) echo "Yes"; else echo "No"; ?></td>
					<td class="actions"><?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'download', $document['id']), array('title'=>'Click to download this document', 'class'=>'tooltipped fa fa-download actionButton actionButtonDownload')); ?>
						<?php echo $this->Html->link(__(''), array('controller'=>'Documents', 'action' => 'edit', $document['id']), array('target'=>'_blank', 'title'=>'Click to edit this document', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
						<?php echo $this->Form->postLink(__(''), array('controller'=>'Documents', 'action' => 'delete', $document['id']), array('title'=>'Click to permanently delete this document', 'class'=>'tooltipped fa fa-trash-o actionButton actionButtonDelete'), __('Are you sure you want to delete # %s?', $document['id'])); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<h2>Devices</h2>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Barcode</th>
					<th>Label</th>
					<th>Location</th>
					<th>Type</th>
					<th>Last Checked</th>
					<th>Damaged</th>
					<th>Missing</th>
					<th>Installed</th>
					<th>Active</th>
					<th class="actions"><?php echo __('Actions'); ?></th>
				</tr>
				<?php foreach ($client['Device'] as $device): ?>
				<tr>
					<td><img src="<?php echo $this->Html->url(array("controller" => "devices", "action" => "getBarcode", $device['id'])); ?>" style="vertical-align: middle; padding-top: 5px; padding-bottom: 5px"></td>
					<td><?php echo h($device['label']); ?>&nbsp;</td>
					<td><?php echo h($device['Location']['name']); ?>&nbsp;</td>
					<td><?php echo h($device['DeviceType']['name']); ?>&nbsp;</td>
					<td><?php echo h($device['lastChecked']); ?>&nbsp;</td>
					<td class="<?php if ($device['damaged']) echo "cell-error"; ?>"><?php if ($device['damaged']) echo "Yes"; else echo "No"; ?></td>
					<td class="<?php if ($device['missing']) echo "cell-error"; ?>"><?php if ($device['missing']) echo "Yes"; else echo "No"; ?></td>
					<td><?php if ($device['installed']) echo "Yes"; else echo "No"; ?></td>
					<td><div class="activeinactiveswitchCompact transitionable"><?php if ($device['active']==1) $checkState="checked"; else $checkState=""; ?>
						<input name="data[Device][active]" type="checkbox" id="DeviceActive<?php echo $device['id']; ?>" value="1" <?php echo $checkState; ?>>
						<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" for="DeviceActive<?php echo $device['id']; ?>" id="activeToggle<?php echo $device['id']; ?>" deviceId="<?php echo $device['id']; ?>"> </label></div>
					</td>
					<td class="actions"><?php echo $this->Html->link(__(''), array('controller'=>'Devices', 'action' => 'edit', $device['id']), array('title'=>'Click to edit this device', 'class'=>'tooltipped fa fa-pencil actionButton actionButtonEdit')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<h2>Visits</h2>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Technician</th>
					<th>Status</th>
					<th>Date Requested</th>
					<th>Date Due</th>
					<th>Date Started</th>
				</tr>
				<?php foreach ($client['Visit'] as $visit): ?>
				<tr>
					<td><?php if ($visit['technician_id'] == -1) echo h("Not yet assigned"); else echo h($visit['Technician']['name']); ?>&nbsp;</td>
					<?php if ($visit['status'] == "in progress") $class = "cell-warning"; else $class = ""; ?>
					<td class="<?php echo $class; ?>">
					<?php if ($visit['status'] == 'complete') echo h($visit['status']);
						else { ?>
								<select style="margin-top: 6px" class="statuschooser" id="statusIn<?php echo $visit['id']; ?>" visitId="<?php echo $visit['id']; ?>">
									<option value="created"<?php if ($visit['status']=="created") echo " selected";?>>Created</option>
									<option value="in progress"<?php if ($visit['status']=="in progress") echo " selected";?>>In Progress</option>
									<option value="complete"<?php if ($visit['status']=="complete") echo " selected";?>>Complete</option>
								</select>
						<?php } ?>
					&nbsp;
					</td>
					<td><?php echo h($visit['timeRequested']); ?>&nbsp;</td>
					<td <?php if ($visit['timeDue'] != null && $visit['status'] != 'complete' && strtotime($visit['timeDue'])<time()) echo "class='cell-error'";?>><?php if ($visit['status'] == 'complete') echo h(date("Y-m-d", strtotime($visit['timeDue']))); 
						else { 
							if ($visit['timeDue'] != null) $visit['timeDue'] = date("Y-m-d", strtotime($visit['timeDue'])); ?>
							<input name="data[lastChecked]" class="datepicker hasDatepicker" value="<?php echo $visit['timeDue']; ?>" id="visitDate<?php echo $visit['id']; ?>" visitid="<?php echo $visit['id']; ?>" type="text">
						<?php } ?>
					&nbsp;</td>
					<td><?php echo h($visit['timeStarted']); ?>&nbsp;</td>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<br><br><br><br>
		</div>
	</div>
</section>
<script>
var map; //declare global
$(document).ready(function() {
	$(".datepicker").datepicker({'dateFormat':'yy-mm-dd'});
	//initialise the maps
    var mapOptions = {
          center: new google.maps.LatLng(<?php echo $client['Client']['latitude'] ?>, <?php echo $client['Client']['longitude'] ?>),
          zoom: 12
        };
	initMaps("googleMaps", mapOptions);
    addMapMarker(<?php echo $client['Client']['latitude'] ?>, <?php echo $client['Client']['longitude'] ?>);
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
//setup for device active toggle
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
