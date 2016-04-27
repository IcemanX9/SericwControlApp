var siteUrl = "http://www.servicecontrol.co.za/servicecontrol/";
var menuHidden = false;
var activeLink;
var menuLinkHeight = 38;
var menuItems = ['dashboard-link', 'documents-link', 'clients-link', 'devices-link', 'chemicals-link', 'technicians-link', 'companies-link', 'users-link', 'visits-link', 'notifications-link'];
var linkClick = false;


function init(currentController) {
	switch (currentController) {
	case 'Documents':
		activeLink = 'documents-link';
		break;
	case 'document_categories':
		activeLink = 'documents-link';
		break;
	case 'clients':
		activeLink = 'clients-link';
		break;
	case 'policies':
		activeLink = 'clients-link';
		break;
	case 'devices':
		activeLink = 'devices-link';
		break;
	case 'chemicals':
		activeLink = 'chemicals-link';
		break;
	case 'technicians':
		activeLink = 'technicians-link';
		break;
	case 'companies':
		activeLink = 'companies-link';
		break;
	case 'users':
		activeLink = 'users-link';
		break;
	case 'notifications':
		activeLink = 'notifications-link';
		break;
	case 'visits':
		activeLink = 'visits-link';
		break;
	default:
		activeLink = 'dashboard-link'; 
	}
}

function setup() {
	//set a global watch on clicks on the document to see if a link has been clicked. This could suppress further actions
	$('#container').click(function(e) {
		var elemntTagName = e.target.tagName;
		if(elemntTagName=='A') linkClick = true;
		else linkClick = false;
	});

	//set the content width based on window size
	$('#content').width($(window).width() - $('#menu').width() - 42);

	//set transitions on menu and content. wait 500 miliuseconds to allow all initial changes to complete.
	setTimeout(function(){
		$('#menu').addClass('transitionable');
		$('#content').addClass('transitionable');
	}, 500);

	//set up current open menu
	if (menuLinkHeight == $('#'+activeLink).height() && $('#'+activeLink)[0].scrollHeight > menuLinkHeight + 20) $('#'+activeLink).css("height", $('#'+activeLink)[0].scrollHeight + 'px');
	else $('#'+activeLink).css("height", menuLinkHeight + 'px');
	$('#'+activeLink).addClass('menu-list-active');
	$('#'+activeLink+ ' > .menu-expander').html('-');

	//add listeners to menu items for clicks and hover state. Also initiates animations on menus
	menuItems.forEach(function(item) {
		setTimeout(function(){$('#'+item).addClass('transitionable');}, 500);
		$('#'+item).click(function () {
			setTimeout(function() {
				if (!(linkClick)) {
					resetMenuLinks();
					$('#'+item + ' > .menu-expander').html('-');
					if (menuLinkHeight == $('#'+item).height() && $('#'+item)[0].scrollHeight > menuLinkHeight + 20) $('#'+item).css("height", $('#'+item)[0].scrollHeight + 'px');
					else $('#'+item).css("height", menuLinkHeight + 'px');
					$('#'+item).addClass('menu-list-active');
				}
			}, 20);
		});
		$('#'+item).hover(function() {$('#'+item).addClass('menu-item-hover');}, function() {$('#'+item).removeClass('menu-item-hover');});
	});

	//sets hover row background changes on tables
	$('.table-hover tr').hover(function() {$(this).addClass('table-row-hover');}, function() {$(this).removeClass('table-row-hover');});

	//sets the pines notifcation plugin to use jquery ui
	$.pnotify.defaults.styling = "jqueryui";
	$.pnotify.defaults.history = false;

	//initiates tooltips
	$(function(){
		$(".tooltipped").tipTip();
	});

	//add listener for menu open toggle and functionality to open and close side menu
	$('#hide-nav-button').click(function () {
		menuHidden  = !(menuHidden );
		$('#menu').toggleClass('menu-hidden', menuHidden );
		$('#content').toggleClass('content-menu-hidden', menuHidden);
		if (menuHidden) $('#content').width($(window).width() - 42);
		else $('#content').width($(window).width() - $('#menu').width() - 42);
	});
	
	userOptionsHidden = false;
	//add a listener for clicking username
	$('.headerUsername').click(function () {
		userOptionsHidden  = !(userOptionsHidden );
		$('.userOptions').toggleClass('userOptionsDown', userOptionsHidden);
	});
}

/*
Resets all menu links to there native closed and unselected state
*/
function resetMenuLinks() {
	for (i=0;i!=menuItems.length;i++) {
		$('#'+menuItems[i]).css("height", menuLinkHeight + 'px');
		$('#'+menuItems[i]).removeClass('menu-list-active');
	};
	$('.menu-item > .menu-expander').html('+');
}


/*
Sets notification for active toggles and ensures that the relevant values in the checkboxes are correctly set
*/
function setActiveToggle(checkboxId, labelId, activeNotificationTitle, inactiveNotificationTitle, activeNotificationText, inactiveNotificationText) {
	$('#'+labelId).click(function() {
			//timeout exists to let the browser first set the value of the checkbox
			setTimeout(function() {
				if (!($('#'+checkboxId)[0].checked)) {
					notificationTitle = inactiveNotificationTitle;
					notificationText = inactiveNotificationText;
					$('#'+checkboxId)[0].value = 0;
					$('#'+checkboxId+'_')[0].value = 0;
				}
				else {
					notificationTitle = activeNotificationTitle;
					notificationText = activeNotificationText;
					$('#'+checkboxId)[0].value = 1;
					$('#'+checkboxId+'_')[0].value = 1;
				}
				$.pnotify({
					title: notificationTitle,
					text: notificationText
				});
			}, 100);
	});
}

