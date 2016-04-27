var validationSubmitButton;
var formElement;
var validatingForm = true;
var formCurrentlyValid = false;
var currentAjax;
var validationErrors;
var formEnabled = false;
var reenableForm = false;

function initValidation(submit, form) {
	//set the right form to submit later on
	formElement = $(form);
	//set up the submit button to receive validation errors in tooltip
	validationSubmitButton = $(submit);
	validationSubmitButton.addClass('tooltipped');
	validationSubmitButton.addClass('disabledButton');

	validateForm();
	if (!(formCurrentlyValid)){
		validationSubmitButton.click(function() {
			$.pnotify({
				title: 'Form not valid',
				text: validationErrors,
				type: 'error'
			});
		});
	}

	//create something that checks on events
	$(document).click(function() {validateForm();});
	$(document).keyup(function(e){validateForm();})  
}


function validateForm() {
	errors = false;
	fErrors = [];
	validationErrors = "You need to address these issues before submitting: <br/>";
	inputs = $('input');
	for (i=0; i!=inputs.length;i++) {
		if (!(inputs[i].getAttribute('validation') == null)) {	
			validationKeys = inputs[i].getAttribute('validation').split(" ");
			fErrors[i] = false;
			for (j=0;j!=validationKeys.length; j++) {
				if (null == inputs[i].getAttribute('humanName')) humanName = inputs[i].getAttribute('id');
				else humanName = inputs[i].getAttribute('humanName');
				switch (validationKeys[j]) {
					case "notnull":
						if (inputs[i].value == "") {
							validationErrors = validationErrors + ' - ' + humanName + " cannot be empty<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "nottooshort":
						if (inputs[i].value.length < 5) {
							validationErrors = validationErrors + ' - ' + humanName + " should be sensible and longer than 4 characters<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "greaterthanzero":
						if (inputs[i].value < 1) {
							validationErrors = validationErrors + ' - ' + humanName + " must be greater than zero<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "password":
						if (inputs[i].value.length < 6) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be 6 or more characters<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "password-reset":
						if (inputs[i].value.length < 6 && inputs[i].value.length != 0) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be left blank or be 6 or more characters<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "uppercasefirst":
						if (inputs[i].value.substr(0,1) == inputs[i].value.substr(0,1).toLowerCase()) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to start with an upper case letter<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "email":
						if (!(isValidEmailAddress(inputs[i].value))) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be a valid e-mail address<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "alphanumeric":
						if (!(isAlphanumeric(inputs[i].value))) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be alphanumeric (no spaces!)<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "pin":
						if (!(isNumeric(inputs[i].value) && inputs[i].value.length == 5)) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be 5 digits long and only numbers<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
					case "pin-reset":
						if (!((isNumeric(inputs[i].value) && (inputs[i].value.length == 5) || inputs[i].value.length == 0))) {
							validationErrors = validationErrors + ' - ' + humanName + " needs to be left BLANK or be 5 digits long and only numbers<br/>";
							errors=true;
							fErrors[i]=true;
						}
						break;
				}
				//special case for the "unique" check, which takes extra parameters and makes ajax xalls
				if (validationKeys[j].substr(0,6) == "unique") {
					//first check if our last call to ajax determined that this field was unique
					if ($(inputs[i]).attr("uniqueness") == "false") {
						validationErrors = validationErrors + ' - ' + humanName + " needs to be unique<br/>";
						errors=true;
						fErrors[i]=true;
					}
					//remember the original value of this field (in case of edits)
					if ($(inputs[i]).attr("originalValue") == null) $(inputs[i]).attr("originalValue", $(inputs[i]).attr("value")); 
					//rebind an event to this field
					$(inputs[i]).off("keyup");
					$(inputs[i]).keyup({element: inputs[i], validationKey: validationKeys[j]},  function(event){checkUnique(event.data.element, event.data.validationKey, event.target.value);});  
				}
				//change the css for the field to show valid / invalid
				if (fErrors[i]) {
					$(inputs[i]).addClass('error');
					$(inputs[i]).removeClass('valid');
				}
				else {
					$(inputs[i]).removeClass('error');
					$(inputs[i]).addClass('valid');
				}
			}
		}
	}
	showErrors(errors); //if we are making an ajax call, this will be called once it's finished
}

function showErrors(errors) {
	if (!(errors)) {
		validationErrors = "Great! All fields are valid. Click to submit the form.";
		//allow the form to be submitted
		if (!(formCurrentlyValid)){
			enableForm();
			$.pnotify({
				title: 'Form valid',
				text: 'All fields are correct and this form is ready to submit!',
				type: 'success'
			});
		}
	}
	else {
		formEnabled = false;
		if (formCurrentlyValid) {
			formCurrentlyValid = false;
			disableForm();
			$.pnotify({
				title: 'Form no longer valid',
				text: validationErrors,
				type: 'error'
			});

		}
	}
	validationSubmitButton[0].setAttribute('title', validationErrors);
	validationSubmitButton.tipTip({maxWidth: "auto"});
}


function disableForm() {
	formEnabled = false;
	validationSubmitButton.addClass('disabledButton');
	validationSubmitButton.unbind('click');
	validationSubmitButton.click(function() {
		$.pnotify({
			title: 'Form not valid',
			text: validationErrors,
			type: 'error'
		});
	});
}

function enableForm() {
	formCurrentlyValid = true;
	formEnabled = true;
	validationSubmitButton[0].disabled = false;
	validationSubmitButton.unbind('click');
	validationSubmitButton.click(function() {
		if (typeof beforeSubmit == "function") beforeSubmit(); 
		formElement.submit();
		});
	validationSubmitButton.removeClass('disabledButton');
}

function checkUnique(element, validationKey, value) {
	if ($(element).attr("originalValue") != value) { //only do this if the value has changed from the original
		if (formEnabled) reenableForm = true; else reenableForm = false;
		setTimeout(function() {disableForm}, 200); //wait a short while for rest of validation to finish, then disable the button to be sure that uniqueness is checked before submitting
		if (currentAjax!=null) {
			currentAjax.abort(); //we don't want to keep showing errors
		}
		//find the parameters
		uParameterObject = validationKey.substr(7);
		separator = uParameterObject.indexOf("-");
		uParameterField = uParameterObject.substr(separator + 1);
		uParameterObject = uParameterObject.substr(0, separator);
		//now we'll make an ajax call assuming that the controller has a checkUnique action defined
		currentAjax = $.ajax({
			type: "POST",
			dataType: 'HTML',
			url: siteUrl + uParameterObject + '/checkUniqueness/' + uParameterField + '/' + value,
			data: ({type:'original'}),
			success: function(data, status) {
				currentAjax = null;
				if (data=="true") {
					validationErrors = validationErrors + ' - Your choice for ' + humanName + " already exists in the database but needs to be unique<br/>";
					$.pnotify({id: 'atitle', title: 'Field not unique', text: 'This value already exists in the database and needs to be unique.', type: 'error'});
					$(element).addClass('error');
					$(element).removeClass('valid');
					$(element).attr("uniqueness", "false");
					validateForm();
				}
				else {
					$(element).removeClass('error');
					$(element).addClass('valid');
					if (reenableForm) enableForm();
					$(element).attr("uniqueness", "true");
					validateForm();
				}
			},
			error: function (request, status, error) {
				 currentAjax = null;
				 if (status != "abort") {
					$.pnotify({id: 'atitle', title: 'Failed to check uniqueness', text: 'You have been logged out or an error has occured. Please log in or contact the System Administrator.', type: 'error'});
				 }
			}
		});
	}
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};


function isAlphanumeric(inputtxt) {  				
	var letterNumber = /^[0-9a-zA-Z]+$/;  
	return inputtxt.match(letterNumber);  
}

function isNumeric(inputtxt) {  				
	var number = /^[0-9]+$/;  
	return inputtxt.match(number);  
}