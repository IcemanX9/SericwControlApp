<?php
	echo $this->Html->script('form-validation');
?>
<section class="panel">
<div class="companies form">
<?php echo $this->Form->create('Company', array('enctype' => 'multipart/form-data')); ?>
	<header class="panel-heading"><?php echo __('Edit Company'); ?></header>
		<div class="panel-body">
	<fieldset>
	<?php 
		echo $this->Form->input('id');
		echo $this->Form->input('name', array('label'=>'Company name', 'validation'=>'notnull unique-companies-name nottooshort','class'=>'form-control'));
		echo $this->Form->input('logoMime', array('id'=>'logoMime', 'type'=>'hidden'));
		?>
		<label class='free-label'>Choose a file to update logo or leave to keep current logo. Must be 200 x 100px, and must be .png or .jpg format.</label><br>
		<?php 
		echo $this->Form->file('imgFile');
		echo $this->Form->input('fileChanged', array('type'=>'hidden', 'id'=>'fileChanged','value'=>"false"));
		echo $this->Form->input('headerMime', array('id'=>'headerMime', 'type'=>'hidden'));
		?>
	<br><br>
	<label class='free-label'>Choose header file. Should be 720 x 80px, or some equivalent aspect ratio (e.g. 360x40px, or 1440x160px, etc.).</label><br>
		<?php 
		echo $this->Form->file('headerFile', array('label'=>false, 'validation'=>'notnull'));
		echo $this->Form->input('headerFileChanged', array('type'=>'hidden', 'id'=>'headerFileChanged','value'=>"false"));
	?><br><br>
	<input type="button" value="Submit" id="submitButton"></form>
	</fieldset>
</div>
</div>
</section>
<script>
$('#CompanyImgFile').before("<br/>")
$(document).ready(function() {
	initValidation($('#submitButton')[0], $('#CompanyEditForm')[0]);
	$('#CompanyHeaderFile').change(function () {
		file = $('#CompanyHeaderFile')[0].files[0];
		fileType = file.type;
		error = false;
		if (!(fileType == "image/jpeg" || fileType == "image/png")) error = "Incorrect file type. Must be png or jpg";
		if (error) {
			$.pnotify({'title':'There is an error with your image', 'text':error});
			$('#CompanyHeaderFile')[0].value = null;
			$('#CompanyHeaderFile')[0].files = null;
		}
		else {
			$('#headerFileChanged').val("true");
			$('#headerMime').val(fileType);
		}
		validateForm();
	});
	$('#CompanyImgFile').change(function () {
			var height;
			var width;
			error = false;
			file = $('#CompanyImgFile')[0].files[0];
			fileType = file.type;
			if (fileType == "image/jpeg" || fileType == "image/png") {
				var reader = new FileReader();
		    	var image  = new Image();
		    	reader.readAsDataURL(file);
		    	reader.onload = function(_file) {
		            image.src    = _file.target.result;              // url.createObjectURL(file);
		            image.onload = function() {
		                width = this.width;
		               	height = this.height;
		        	}
		    	}
			}
			else error = "Incorrect file type. Must be png or jpg";
			setTimeout(function () {
				if (!error) if (height != 100 || width != 200) error = "Incorrect size. Image must be 200 x 100px.";
				if (error) {
					$.pnotify({'title':'There is an error with your image', 'text':error});
					$('#CompanyImgFile')[0].value = null;
					$('#CompanyImgFile')[0].files = null;
				}
				else {
					$('#logoMime').val(fileType);
					$('#fileChanged').val("true");
				} 
				validateForm();
			}, 200); //give it a chance to find file info
		});
});
</script>