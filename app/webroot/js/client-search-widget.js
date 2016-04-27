var useWidget = false;
var widgetId = 'client-search-widget';
var useSearchAddButton = true;
var ajaxQueue = [];
var searchWidgetTitle = "Client Search Results";
var searchWidgetSubtitle = "Click on a client to add to your form or edit.";
var currentFormClients = [];

function initClientSearchWidget(inputElement, urlRoute, widget, addToForm) {
	useSearchAddButton = addToForm;
	useWidget = widget;

	if ($('#'+widgetId).length == 0) {
		//create the widget
		$('#container').append("<div class='search-widget search-widget-off-screen transitionable' id='" + widgetId + "'>" +
									"<div class='loginHeader'>"+searchWidgetTitle+"<br/><span class='headerSubtitle'>"+searchWidgetSubtitle+"</span></div>" +
									"<div class='search-widget-results'></div>" +
								"</div>");
	}
	$('#'+inputElement).keyup(function() {
		partialName = $('#'+inputElement)[0].value;
		if (partialName.length > 0) {
			xhr = $.ajax({
				type: "POST",
				dataType: 'HTML',
				url: urlRoute + '/' + partialName + '/',
				data: ({type:'original'}),
				success: updateData,
				error: function (request, status, error) {
					if (status != "abort") $.pnotify({title: 'Failed to get data', text: 'You have been logged out or the search function is currently not working. Please log in or contact the System Administrator.', type: 'error'});
				}
			});
			abortAjax();
			ajaxQueue.push(xhr);
		}
		else {
			$('#'+widgetId).addClass("search-widget-off-screen");
			abortAjax(); //prevents unfinished ajax calls from re-opening the widget when the search bar is empty
		}
	});
}


function updateData(data, textStatus) {
	$('#'+widgetId).removeClass("search-widget-off-screen"); //unhide the element
	if (data.length > 2) {
		response = JSON.parse(data);
		searchHtml = "<div class='loginHeader'>"+searchWidgetTitle+"<br/><span class='headerSubtitle'>"+searchWidgetSubtitle+"</span></div>" +
									"<div class='search-widget-results'>";
		if (!useWidget) {
			return response;
		}
		else {
			for (i=0; i!=response.length; i++) {
				if (useSearchAddButton) {
					//checking here if its already added to a multi select box, in which case we give the opportunity to remove it instead
					if (currentFormClients.indexOf(response[i]['Client']['id']) == -1) {
						classes = "fa-plus actionButtonAdd";
						removeValue = "false";
					} 
					else {
						classes = "fa-minus actionButtonDelete";
						removeValue = "true";
					}
					clientName = '<a href="#" remove="'+ removeValue +'" clientname="' + response[i]['Client']['name'] + '" clientid="' + response[i]['Client']['id'] + '" title="Click to add this client to your form" id="client-search-add-' + response[i]['Client']['id'] + '" class="client-search-add-button tooltipped fa ' + classes + ' actionButton"> ' + response[i]['Client']['name'] + '</a>';
				}
				else clientName = response[i]['Client']['name'];
				searchHtml = searchHtml
							+ '<a target="_clientsearchslave" href="' + siteUrl + 'clients/edit/' + response[i]['Client']['id'] + '" title="Click to edit this client" class="tooltipped fa fa-pencil actionButton actionButtonEdit"></a>'							
							+ '<a target="_clientsearchslave" href="' + siteUrl + 'clients/view/' + response[i]['Client']['id'] + '" title="Click to view or delete this client" class="tooltipped fa fa-search actionButton actionButtonView"></a>'
							+ clientName
							+ '<br/>';
			}
			$('#'+widgetId).html(searchHtml + "</div>");
			//now activate the click on each button
			for (i=0; i!=response.length; i++) {
				if (useSearchAddButton) $('#client-search-add-' + response[i]['Client']['id']).click(addToForm);
				else $('#client-search-add-' + response[i]['Client']['id']).css("display", "none"); 
			}
		}
	}
	else $('#'+widgetId).html("<div class='loginHeader'>Client Search Results</div><div class='search-widget-results'>No matches found</div>");
	//refreshes tooltips
	$(function(){
		$(".tooltipped").tipTip();
	});
	//let's place a close icon on the widget and set an event
	$('#'+widgetId).append("<div id='search-widget-close' class='fa fa-times-circle close-button'></div>");
	$('#search-widget-close').click(function() {$('#'+widgetId).addClass("search-widget-off-screen");});
}


function addToForm() {
	console.log("Adding to form");
}

function abortAjax() {
	for (i=0;i!=ajaxQueue.length;i++) ajaxQueue[i].abort();
	ajaxQueue=[];
}

function setClientSearchWidgetResultsSingle(idinput, nameinput) {
	$('#'+widgetId).bind('DOMSubtreeModified', function () {
		$('.client-search-add-button').off('click');
		$('.client-search-add-button').click(function(event) {
			$('#'+idinput)[0].value = event.target.attributes.clientid.nodeValue;
			$('#'+nameinput)[0].value = event.target.attributes.clientname.nodeValue;
		});
	});
}

function setClientSearchWidgetResultsSelectBox(selectElement) {
	$('#'+widgetId).bind('DOMSubtreeModified', function () {
		$('.client-search-add-button').off('click');
		$('.client-search-add-button').click(function(event) {
			options = $('#' + selectElement + " option");
			if (event.target.attributes.remove.nodeValue == "false") {
				html = '<option style="color:black;" value="'+ event.target.attributes.clientid.nodeValue +'">' + event.target.attributes.clientname.nodeValue + '</option>';
				$('#' + selectElement).append(html);
				$('#' + selectElement + " option").prop("selected", true);
				currentFormClients.push(event.target.attributes.clientid.nodeValue);
				$(event.target).attr("remove", "true");
				$(event.target).removeClass("fa-plus");
				$(event.target).removeClass("actionButtonAdd");
				$(event.target).addClass("fa-minus");
				$(event.target).addClass("actionButtonDelete");
			}
			else {
				for (i=0;i!=options.length;i++) if (options[i].value == event.target.attributes.clientid.nodeValue) $(options[i]).remove();
				$(event.target).attr("remove", "false");
				$(event.target).addClass("fa-plus");
				$(event.target).addClass("actionButtonAdd");
				$(event.target).removeClass("fa-minus");
				$(event.target).removeClass("actionButtonDelete");
				currentFormClients.splice(currentFormClients.indexOf(event.target.attributes.clientid.nodeValue), 1);
			}
			$('#' + selectElement).css("height", parseInt($('#' + selectElement + " option").length) * 20);
		});
	});
}