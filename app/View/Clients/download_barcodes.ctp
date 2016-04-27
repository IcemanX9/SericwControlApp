<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="documents form">
<?php echo $this->Form->create(); ?>
	<header class="panel-heading"><?php echo __('Select barcodes to download'); ?></header>
	<div class="panel-body"><fieldset>
		<label class="free-label">Check the box next to each barcode you wish to download and click "Get Barcodes" to generate the PDF. Only barcodes for active devices can be downloaded.</label><br><br>
		<div class="free-label">
			<?php echo $this->Html->link(__(' Select all'), '#', array('id'=>'selectAllButton', 'style'=>'font-size: 18px;', 'class'=>'fa fa-check actionButton actionButtonDownload')); ?>
			<?php echo $this->Html->link(__(' Select none'), '#', array('id'=>'SelectNoneButton', 'style'=>'font-size: 18px;', 'class'=>'fa fa-times actionButton actionButtonDelete')); ?>
			<?php echo $this->Html->link(__(' Select not installed'), '#', array('id'=>'SelectUninstalledButton', 'style'=>'font-size: 18px;', 'class'=>'fa fa-chevron-down actionButton actionButtonView')); ?>
			<?php echo $this->Html->link(__(' Download All Barcodes'), array("controller"=>"Clients", "action"=>"downloadAllBarcodes", $clientId), array('style'=>'font-size: 18px;', 'class'=>'fa fa-download actionButton actionButtonEdit')); ?>
		</div>
		<div>
			<b>Output format</b> <?php echo $this->Form->radio('OutputFormat', array('A4 page', '70 x 40mm labels'), array('value'=>0, 'between'=>'', 'separator'=>' ', 'legend'=>false)) ?>
			<input type="button" value="Get Barcodes" id="submitButton"></form>
		</div>
		<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Check to download</th>
					<th>Label</th>
					<th>Location</th>
					<th>Type</th>
					<th>Last Checked</th>
					<th>Created</th>
					<th>Last Modified</th>
					<th>Damaged</th>
					<th>Missing</th>
					<th>Installed</th>
				</tr>
				<?php foreach ($devices as $device): ?>
				<tr style="text-align: center; vertical-align: center;">
					<?php 	$class = "selector"; 
							if (!$device['Device']['installed']) $class = $class. ' uninstalled'; ?>
					<td><?php echo $this->form->input('select'.$device['Device']['id'], array('type'=>'checkbox', 'label'=>false, 'checked'=>true, 'class'=>$class, 'div'=>false)); ?></td>
					<td><?php echo h($device['Device']['label']); ?>&nbsp;</td>
					<td><?php echo h($device['Location']['name']); ?>&nbsp;</td>
					<td><?php echo h($device['DeviceType']['name']); ?>&nbsp;</td>
					<td><?php echo h($device['Device']['lastChecked']); ?>&nbsp;</td>
					<td><?php echo h($device['Device']['created']); ?>&nbsp;</td>
					<td><?php echo h($device['Device']['modified']); ?>&nbsp;</td>
					<td class="<?php if ($device['Device']['damaged']) echo "cell-error"; ?>"><?php if ($device['Device']['damaged']) echo "Yes"; else echo "No"; ?></td>
					<td class="<?php if ($device['Device']['missing']) echo "cell-error"; ?>"><?php if ($device['Device']['missing']) echo "Yes"; else echo "No"; ?></td>
					<td><?php if ($device['Device']['installed']) echo "Yes"; else echo "No"; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
	</fieldset>
<input type="button" value="Get Barcodes" id="submitButton2"></form>
</div>
</div></section>
<script>
$('#selectAllButton').click(function() {
	$('.selector').prop('checked', true);
});
$('#SelectNoneButton').click(function() {
	$('.selector').prop('checked', false);
});
$('#SelectUninstalledButton').click(function() {
	$('.selector').prop('checked', false);
	$('.uninstalled').prop('checked', true);
});
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#ClientDownloadBarcodesForm')[0]);
	$('#submitButton2').click(function (){
			$( "#submitButton" ).trigger( "click" );
	});
});
</script>