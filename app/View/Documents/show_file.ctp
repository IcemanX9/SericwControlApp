<?php 
function sanitizeb($string = '', $is_filename = FALSE)
{
	// Replace all weird characters with dashes
	$string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);

	// Only allow one dash separator at a time (and make string lowercase)
	return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
}
?>
<div class="panel">
	<div class="documents index">
		<header class="panel-heading">
			<?php echo __('Client documents'); ?>
		</header>
		<div>
			<header class="category-heading">
				<?php echo $this->Html->link(__('Document Index (click to download)'), array('action' => 'downloadIndexFile', $clientId), 
													array('target'=>'document','id'=>'downloadCurrent', 'title'=>'Click to download the file', 
													'class'=>'indexDownloadLink tooltipped fa fa-file-text')); ?>
				<img
					src="<?php echo $this->Html->url(array("controller" => "companies", "action" => "getLogo", $companyId)); ?>"
					style="float: right;">
			</header>
			<?php 
			$currentCat = "empty";
			foreach ($documentList as $document)  {
				if ($document['all_client_documents']['size'] == '-555') { $isReport = true; $downloadAction = "downloadReport"; $document['all_client_documents']['id'] = substr($document['all_client_documents']['id'], 1);}
				else { $isReport = false; $downloadAction = "download"; }
				if ($document['all_client_documents']['catName'] != $currentCat) {
					$currentCat = $document['all_client_documents']['catName'];
					?>
		</div>
		<header class="category-heading">
			<a id="<?php echo sanitizeb($currentCat); ?>"><?php echo $currentCat; ?></a>
		</header>
		<div class="accordion">
			<?php
				} 
		?>

			<h3>
				<?php echo $document['all_client_documents']['name']; ?>
				(updated <?php if ($isReport) echo " now"; else echo $document['all_client_documents']['modified']; ?>)
			</h3>
			<div>
				<?php echo $this->Html->link(__('Click to download'), array('action' => $downloadAction, $document['all_client_documents']['id'], $clientId), 
													array('target'=>'document','id'=>'downloadCurrent', 'title'=>'Click to download the file', 
													'class'=>'fileDownloadLink tooltipped fa fa-file-text')); ?>
				<div class="fileFileInformation">
					<label for="fileinfo">File Information</label><br> <span
						style="font-weight: normal">File type: </span><span id="file_type"><?php if ($isReport) echo "PDF Report"; else echo $document['all_client_documents']['file_type']; ?>
					</span><br> <span style="font-weight: normal">Size: </span><span
						id="file_size"><?php if ($isReport) echo "Unknown"; else echo $document['all_client_documents']['size']; ?>
						bytes</span><br> <span style="font-weight: normal">Originally
						created: </span><span id="file_size"><?php if ($isReport) echo "Generated now"; else echo $document['all_client_documents']['created']; ?>
					</span><br>
				</div>
			</div>

			<?php 
		    }
		    ?>
		</div>
	</div>
	<div id="reportSighting" style="display: none;">
		<div class="panel">
			<?php echo $this->Form->create('Sighting'); ?>
			<header class="category-heading"><a id="reportsighting">Report a Sighting</a></header>
			<fieldset>
				<?php 
				echo $this->Form->input('typeOfPest', array('class'=>'tooltipped form-control', 'title'=>'Brief description of pest or insect seen.', 'label'=>'Type of pest or insect sighted'));
				echo $this->Form->input('areaOfSighting', array('class'=>'tooltipped form-control', 'title'=>'Where did you see the pest or insect?', 'label'=>'Area of sighting'));
				echo $this->Form->input('reportedBy', array('class'=>'tooltipped form-control', 'title'=>'Please enter your name', 'label'=>'Your name'));
				echo $this->form->input('client_id', array('type'=>'hidden', 'value'=>$clientId));
				echo $this->form->input('client_user_id', array('type'=>'hidden', 'value'=>$clientUserId));
				?>

				<span class="free-label" style="font-weight:bold;">Please note: submitting this form will
					trigger a visit from a serviceman which could result in additional
					charges. Please check with your pest control operator if you are
					unsure.</span><br> 
				<?php echo $this->Form->end(array("label"=>__d('sightings', 'Submit'), "id"=>"submitButton"));?>
			</fieldset>
		</div>
	</div>

<script>
	$(function() {$(".accordion" ).accordion({active: false, collapsible: true});});
	$('#sighting-link').click(function() {
		$('#reportSighting').css("display","block");
	});
	$(document).ready(function() {
	<?php 
		foreach ($messages as $message) {
			$text = $message['Message']['message'];
			$from = $message['Message']['from'];
			$when = date("Y-m-d", strtotime($message['Message']['created']));
			$id = $message['Message']['id'];
			echo "$.pnotify({title: 'New message posted', text: '$text <br><br> Close to remove permanently', hide: false, after_close: function(){setMessageSeen($id);}});";
		}
	?>
	});
	function setMessageSeen(id) {
		$.ajax({
   			type: "POST",
			dataType: 'HTML',
	       	url: '<?php echo Router::url(array('controller'=>'Documents','action'=>'setMessageSeen'));?>/' + id,
			data: ({type:'original'})				
		});
	}
</script>