<div class="panel">
<div class="notifications index">
	<header class="panel-heading"><?php echo __('Notification Settings'); ?></header>
	<?php echo $this->Html->link(__(' Run all notifications now'), array('controller'=>'Notifications', 'action' => 'runAllNoFrequencyCheck'), 
			array('title'=>'Click to run all notifications now', 'style'=>'font-size: 18px;margin:15px;', 'class'=>'tooltipped fa fa-warning actionButton actionButtonDownload')); ?>
	<?php foreach ($notifications as $notification): ?>
	<br><div class="sub-panel"><label class="free-label" style="float:left; font-size: 18px;padding-top:10px;"><?php echo $notification['Notification']['name'];?></label>
	<div class="activeinactiveswitchCompact transitionable" style="margin-left: 15px; float:left;"> Active: <?php if ($notification['Notification']['active']==1) $checkState="checked"; else $checkState=""; ?>
			<input name="Notification" type="checkbox" id="NotificationActive<?php echo $notification['Notification']['id']; ?>" value="1" <?php echo $checkState; ?>>
			<label class="transitionable indexActiveToggle tooltipped" title="Click to toggle between active and inactive" style="margin-bottom: -6px; margin-top:6px;" 
					for="NotificationActive<?php echo $notification['Notification']['id']; ?>" id="activeToggle<?php echo $notification['Notification']['id']; ?>" 
					notificationId="<?php echo $notification['Notification']['id']; ?>">
			 </label>
	</div>
	<div style="margin-top:4px; padding-left: 15px;float:left;">
		<?php if ($notification['Notification']['runFrequency'] == -1) echo "<span style='line-height:2.1;'>Triggered by external event.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>"; else { ?>
		Runs every 
			<input validation="greaterthanzero notnull" humanname="Run Frequency" type="number" id="frequencyIn<?php echo $notification['Notification']['id']; ?>" style="border-radius: 10px;" 
			value="<?php echo $notification['Notification']['runFrequency']; ?>" notificationId="<?php echo $notification['Notification']['id']; ?>" class="frequencyInput"> hours &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php } 
			if ($notification['Notification']['email'] == "-1") echo "<span style='line-height:2.1;'>E-mail address for sending notification configured elsewhere.</span>"; else { ?>
		Send notification to: 
		<input validation="greaterthanzero notnull" humanname="Notification e-mail" type="email"  id="emailIn<?php echo $notification['Notification']['id']; ?>" class="emailInput"
								notificationId="<?php echo $notification['Notification']['id']; ?>" style="padding-left: 4px; width: 300px; border-radius: 10px;" value="<?php echo $notification['Notification']['email']; ?>">
		<?php } ?>
	</div>
	<div style="clear:both; padding-top:15px; padding-left:10px; padding-bottom:10px;"><?php echo $notification['Notification']['description'];?></div>	
				<?php	
				echo "</div>";
	endforeach; ?>

	
</div>
</div>
<script>
//we want to be able to modify the look of some input elements
$(document).ready(function() {
	
});
//now let's set our buttons to click and make an ajax call
toggles = $('.indexActiveToggle');
frequencies = $('.frequencyInput');
emails = $('.emailInput');
for (i=0;i!=toggles.length;i++) {
	notificationId = $(toggles[i]).attr('notificationId');
	(function (notificationId) {
		$(toggles[i]).click(function () {
			setTimeout(function() {
				state = $('#NotificationActive'+notificationId)[0].checked;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Notifications','action'=>'toggleActive'));?>/' + notificationId + '/' + state + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
						if (data=="0") {
							ntitle = "Notification set to inactive";
							nmessage = "The notification is now inactive. No e-mails will be sent out with updates.";
						}
						else {
							ntitle = "Notification set to active";
							nmessage = "E-mails will be sent regularly to the specified address with any new notifications.";
						}
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(notificationId);
}
for (i=0;i!=frequencies.length;i++) {
	notificationId = $(frequencies[i]).attr('notificationId');
	(function (notificationId) {
		$(frequencies[i]).change(function () {
			setTimeout(function() {
				value = $('#frequencyIn'+notificationId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Notifications','action'=>'saveFrequency'));?>/' + notificationId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
							ntitle = "Notification frequency saved.";
							nmessage = "Your changes to the frequency have been saved as " + data + " hours.";
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(notificationId);
}
for (i=0;i!=emails.length;i++) {
	notificationId = $(emails[i]).attr('notificationId');
	(function (notificationId) {
		$(emails[i]).change(function () {
			setTimeout(function() {
				value = $('#emailIn'+notificationId)[0].value;
				$.ajax({
	       			type: "POST",
					dataType: 'HTML',
			       	url: '<?php echo Router::url(array('controller'=>'Notifications','action'=>'saveEmail'));?>/' + notificationId + '/' + value + '/',
       				data: ({type:'original'}),
	       			success: function (data, textStatus){
							ntitle = "Notification e-mail saved.";
							nmessage = "Your changes to the notification e-mail address have been saved as " + data +".";
						$.pnotify({title:ntitle, text:nmessage});
					}
			}, 100);
			});
		});
	})(notificationId);
}
</script>