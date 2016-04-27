var geocoder;
var map;
var markers = [];
var defaultMapsLocation = new google.maps.LatLng(-26.2120991, 28.0300007);

function initMaps(elementId, mapOptions) {
  geocoder = new google.maps.Geocoder();
  map = new google.maps.Map(document.getElementById(elementId), mapOptions);
}

function codeAddress(address, latitudeElementId, longitudeElementId) {
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      clearMarkers();
      results;
      var marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location,
          draggable:true,
          animation: google.maps.Animation.DROP
      });
    } else {
      $.pnotify({title: "Could not find address", text: "We were unable to find the address. Check that it is correct or place the marker manually by dragging it on the map to the correct location.", hide: 10, type: "error"});
      map.setCenter(defaultMapsLocation);
      var marker = new google.maps.Marker({
          map: map,
          position: defaultMapsLocation,
          draggable:true,
          animation: google.maps.Animation.DROP
      });
      
    }
    markers.push(marker);
    saveMapLocation(latitudeElementId, longitudeElementId);
    google.maps.event.addListener(marker, 'dragend', function() {saveMapLocation(latitudeElementId, longitudeElementId);});
  });
}


//Sets the map on all markers in the array.
function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setAllMap(null);
  markers = [];
}


//saves the lat long of the current marker to appropriate form inputs
function saveMapLocation(latitudeElementId, longitudeElementId) {
	if (markers[0]) {
		$('#'+latitudeElementId)[0].value = markers[0].getPosition().lat();
		$('#'+longitudeElementId)[0].value = markers[0].getPosition().lng();
	}
}


function addMapMarker(lat, lon, id) {
	var marker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(lat, lon),
        draggable:true,
        animation: google.maps.Animation.DROP
    });
	if (typeof id !== 'undefined') marker.url = siteUrl + "Clients/view/" + id;
	markers.push(marker);
	if (typeof id !== 'undefined') google.maps.event.addListener(marker, 'click', function() {
			window.open(this.url, '_blank');
		});
}

function zoomToFit() {
	var bounds = new google.maps.LatLngBounds ();
	for (var i=0; i!=markers.length; i++) bounds.extend (markers[i].position);
	map.fitBounds (bounds);
}