// script designed to handle uploading file interface. assumes the use of the correct style classes in the html. Only one file upload field supported per page.

// call initialization file once the page is loaded
$(document).ready(function() {
	if (window.File && window.FileList && window.FileReader) {
	InitFileHandling();
	}
});


//FOR DISPLAYING THE FORMS
// getElementById
function $id(id) {
	return document.getElementById(id);
}

//
// initialize
function InitFileHandling() {

	var fileselect = $id("fileselect"),
	    filedrag = $id("filedrag"),
	    submitbutton = $id("submitbutton");

	// file select
	fileselect.addEventListener("change", FileSelectHandler, false);

	// is XHR2 available?
	var xhr = new XMLHttpRequest();
	if (xhr.upload) {
		// file drop
		filedrag.addEventListener("dragover", FileDragHover, false);
		filedrag.addEventListener("dragleave", FileDragHover, false);
		filedrag.addEventListener("drop", FileSelectHandler, false);
		filedrag.style.display = "block";
	}
}

// file drag hover
function FileDragHover(e) {
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
}


function ParseFile(file) {
	$(".fileInformation").css("display", "block");
	switch(file.type) {
		case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
		  file.humanType = "Excel Spreadsheet";
		  break;
		case "application/vnd.ms-excel":
		  file.humanType = "Excel Spreadsheet";
		  break;
		case "application/pdf":
		  file.humanType = "PDF Document";
		  break;
		case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
		  file.humanType = "Word Document";
		  break;
		case "application/msword":
		  file.humanType = "Word Document";
		  break;
		case "image/jpeg":
		  file.humanType = "Image";
		  break;
		case "image/png":
		  file.humanType = "Image";
		  break;
		case "image/tiff":
		  file.humanType = "Image";
		  break;
		case "application/x-zip-compressed":
		  file.humanType = "Zip file";
		  break;
		case "application/vnd.ms-powerpoint":
		  file.humanType = "Powerpoint Presentation";
		  break;
		case "application/vnd.ms-powerpoint":
		  file.humanType = "Powerpoint Presentation";
		  break;
		case "text/html":
		  file.humanType = "Direct text / html";
		  break;
		case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
		  file.humanType = "Powerpoint Presentation";
		  break;
		default:
		  file.humanType = "Unknown";
	}
	$("#file_size").html(file.size + " bytes");
	$("#file_type").html(file.humanType);
	$("#DocumentSize")[0].value = file.size;
	$("#DocumentFileType")[0].value = file.humanType;
	$("#DocumentMime")[0].value = file.type;
	$("#DocumentMeta")[0].value = "/";
}

//FOR UPLOADING THE FILES
// file selection
function FileSelectHandler(e) {

	// cancel event and hover styling
	FileDragHover(e);

	// fetch FileList object
	var files = e.target.files || e.dataTransfer.files;

	// process all File objects
	for (var i = 0, f; f = files[i]; i++) {
		ParseFile(f);
		resetProgressBar();
		UploadFile(f);
	}

}

//resets the progress bar in the case of adding a different file
function resetProgressBar() {
	$("#progress p")[0].className = "transitionable";
	$("#progress")[0].className = "";
	$("#progress p").html("uploading file...");
	$("#progress p")[0].style.backgroundPosition = "0px";
}

// upload JPEG files
function UploadFile(file) {
	var xhr = new XMLHttpRequest();
	if (file.size <= parseInt($('#MAX_FILE_SIZE')[0].value)) {
		var fd = new FormData();
		fd.append("thefile", file);
		xhr.upload.addEventListener("progress", function(e) {
				var pc = parseInt((e.loaded / e.total * 100) * ($("#progress p")[0].offsetWidth / 100));
				$("#progress p")[0].style.backgroundPosition = pc + "px";
			}, false);

		// file received/failed
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4) {
				if (xhr.status == 200 && xhr.response.substr(0,3) == "!1!") {
					uploadsuccess = true;
					$("#DocumentFilename")[0].value = xhr.response.substr(4);
					if (validatingForm) validateForm();
					uploadSuccess();
				}
				else uploadsuccess = false;
				$("#progress p")[0].className = (uploadsuccess  ? "success" : "failure");
				$("#progress p").html(uploadsuccess  ? "upload complete" : "upload failed");
			}
		};
		
		// start upload
		xhr.open("POST", siteUrl + "Documents/upload/", true);
		xhr.setRequestHeader("Accept", "text/html, */*; q=0.01");
		xhr.setRequestHeader("X_FILENAME", file.name);
		xhr.send(fd);
	}
}