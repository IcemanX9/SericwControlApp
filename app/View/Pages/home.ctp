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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo Configure::read("googleMapsKey"); ?>&sensor=false"></script>
<?php echo $this->Html->script('client-search-widget'); ?>
<?php
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

//if (!Configure::read('debug')):
//	throw new NotFoundException();
//endif;

//App::uses('Debugger', 'Utility');
echo $this->Html->script('raphael'); //charts
echo $this->Html->script('morris.min'); //charts
echo $this->Html->script('maps-functions');

//get some stats
App::import('Model', 'Client');
App::import('Model', 'Document');
App::import('Model', 'Device');
App::import('Model', 'Visit');
$clientModel = new Client();
$numberClients = $clientModel->find('count', array("conditions"=>array("Client.active"=>1, "Client.archived"=>0)));
$docModel = new Document();
$numberDocs = $docModel->find('count', array("conditions"=>array("Document.active"=>1, "Document.archived"=>0)));
$deviceModel = new Device();
$numberDamagedDevices = $deviceModel->find('count', array("conditions"=>array("Device.active"=>1, "Device.archived"=>0, "OR"=>array(array('Device.damaged = 1'),array("Device.missing = 1")))));
$visitModel = new Visit();
$numberOverdueVisits = $visitModel->find('count', array("conditions"=>array("Visit.status != 'complete'", "Visit.timeDue < '" . date('Y-m-d H:i:s') . "'")));
$upcomingVisits = $visitModel->find('all', array("conditions"=>array("Visit.status != 'complete'", 
		"Visit.timeDue > '" . date('Y-m-d H:i:s') . "'", "Visit.timeDue < '" . date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)) . "'")));
$clientModel->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document'), 'belongsTo' => array('User', 'Company')));
$upcomingServices = $clientModel->find('all', array("conditions"=>array("Client.nextService >= '" . date('Y-m-d H:i:s') . "'", "Client.nextService < '" . date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)) . "'")));
for($i=0;$i!=sizeof($upcomingServices);$i++) {
	$upcomingServices[$i]['Visit']['purpose'] = "Routine Visit";
	$upcomingServices[$i]['Visit']['timeDue'] = $upcomingServices[$i]['Client']['nextService'];
}
$upcomingVisits = array_merge($upcomingVisits, $upcomingServices);
function sort_visits($a, $b) {	return strtotime($a['Visit']['timeDue']) - strtotime($b['Visit']['timeDue']); }
usort($upcomingVisits, 'sort_visits');
?>


<div class="commonActions">
	<div class="commonActionsHeader">
		Common Actions
	</div>
	<div class="commonActionsList">
		<a href="<?php echo Router::url(array('controller'=>'Clients','action'=>'add'));?>" class="fa fa-male actionButton actionButtonStandard">Add a new client</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Visits','action'=>'viewSchedule'));?>" class="fa fa-suitcase actionButton actionButtonStandard">View visit schedule</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Clients','action'=>'index'));?>" class="fa fa-group actionButton actionButtonStandard">List or search clients</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Devices','action'=>'add'));?>" class="fa fa-gears actionButton actionButtonStandard">Add a new device</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Technicians','action'=>'add'));?>" class="fa fa-truck actionButton actionButtonStandard">Add a new technician</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Companies','action'=>'index'));?>" class="fa fa-globe actionButton actionButtonStandard">Edit company information</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Clients','action'=>'add'));?>" class="fa fa-info actionButton actionButtonStandard">Edit your user profile</a><br>
		<a href="<?php echo Router::url(array('controller'=>'Chemicals','action'=>'index'));?>" class="fa fa-fire actionButton actionButtonStandard">Link chemicals to a client</a><br>
		<form><?php echo $this->Form->input('clientSearch', array('class'=>'form-control client-search-bar', 'style'=>'width: 235px;', 'id'=>'clientSearch', 'label'=>'Search Clients', 'placeholder'=>'Start typing a client name to search')); ?></form>
	</div>
</div>

<a href="<?php echo Router::url(array('controller'=>'Clients','action'=>'index'));?>">
<section class="infoPanel">
	<div class="infoPanelLeft bg-colour-blue fa fa-group"></div>
	<div class="infoPanelRight"><br/><span class="infoNumber"><?php echo $numberClients; ?></span><br/><span class="infoTitle">Active Clients</span></div>
</section>
</a>

<a href="<?php echo Router::url(array('controller'=>'Documents','action'=>'index'));?>">
<section class="infoPanel">
	<div class="infoPanelLeft bg-colour-green fa fa-file"></div>
	<div class="infoPanelRight"><br/><span class="infoNumber"><?php echo $numberDocs; ?></span><br/><span class="infoTitle">Active Documents</span></div>
</section>
</a>

<div style="width: 685px; float: left; height: 0px;"></div>
<p style="height: 106px;"></p>
<a href="<?php echo Router::url(array('controller'=>'Visits','action'=>'listOpenVisits', 1));?>">
<section class="infoPanel">
	<div class="infoPanelLeft bg-colour-orange fa fa-calendar"></div>
	<div class="infoPanelRight"><br/><span class="infoNumber"><?php echo $numberOverdueVisits; ?></span><br/><span class="infoTitle">Overdue Services</span></div>
</section>
</a>
<a href="<?php echo Router::url(array('controller'=>'Devices','action'=>'index', 'filterdamaged'));?>">
<section class="infoPanel">
	<div class="infoPanelLeft bg-colour-red fa fa-gear"></div>
	<div class="infoPanelRight"><br/><span class="infoNumber"><?php echo $numberDamagedDevices; ?></span><br/><span class="infoTitle">Devices need repair</span></div>
</section>
</a>
<div style="width: 685px; float: left; height: 0px;"></div>

<p style="height: 106px;"></p>
<a href="<?php echo Router::url(array('controller'=>'Visits','action'=>'viewSchedule'));?>" style="color: black;">
<div id="upcomingVisits" class="upcomingVisits">
	<div class="upcomingVisitsHeader">Upcoming Visits (due in next 7 days)<span style="font-size: 9px;"><br>Click to view full visit schedule</span></div>
	<div class="upcomingVisitsList">
		<table>
		<tr>
			<th style="min-width:220px; text-align: left;">Client</th><th style="min-width:180px;">Purpose</th><th style="min-width:140px;">Date Due</th>
		</tr>
		<?php 
		foreach ($upcomingVisits as $visit) {
			?>
			<tr>
			<td style="text-align: left;"><?php echo $visit['Client']['name']; ?></td><td><?php echo translatePurpose($visit['Visit']['purpose']); ?></td><td><?php echo date("Y-m-d", strtotime($visit['Visit']['timeDue'])); ?></td>
			</tr>
			<?php 
		}
		if (sizeof($upcomingVisits) == 0) echo "<td>No upcoming visits scheduled</td><td></td><td></td>"
		?>
		</table>
	</div>
</div>
</a>

<div id="lineChartA" class="lineChart">
</div>

<div id="googleMaps" class="gMapsDashboard"></div>

<script>
//init the search widget
initClientSearchWidget('clientSearch', "<?php echo Router::url(array('controller'=>'Clients','action'=>'getClientList'));?>", true, false);

new Morris.Line({
	  // ID of the element in which to draw the chart.
	  element: 'lineChartA',
	  hideHover: 'auto',
	  // Chart data records -- each entry in this array corresponds to a point on
	  // the chart.
	  data: [
	    { month: "2010", a: 20, b: 2 },
	    { month: "2011", a: 25, b: 5 },
	    { month: "2012", a: 12, b: 2 },
	    { month: "2013", a: 35, b: 10 },
	    { month: "2014", a: 20, b: 3 }
	  ],
	  // The name of the data record attribute that contains x-values.
	  xkey: 'month',
	  // A list of names of data record attributes that contain y-values.
	  ykeys: ['a', 'b'],
	  // Labels for the ykeys -- will be displayed when you hover over the
	  // chart.
	  labels: ['Services', 'Late Services']
	});

var map; //declare global
$(document).ready(function() {
	//initialise the maps
  var mapOptions = {
        center: defaultMapsLocation,
        zoom: 12
      };
	initMaps("googleMaps", mapOptions);
});

$.ajax({
	type: "POST",
	dataType: 'HTML',
	url: siteUrl + 'Clients/getClientLocations/',
	data: ({type:'original'}),
	success: function(data, status) {
		data = JSON.parse(data);
		for (i=0; i!=data.length; i++) {
			addMapMarker(data[i].Client.latitude, data[i].Client.longitude, data[i].Client.id);
		}
		zoomToFit();
	},
	error: function (request, status, error) {
		 
	}
});

</script>
