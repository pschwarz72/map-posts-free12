/****
 * Creates the preview map in IPT Map Posts Pro.
 *
 ****/


//Initializes variables
var latDef;
var lngDef;
var map;
var initialZoom;
		
//Adds map to a <div> tag id = map  
jQuery( document ).ready( function($) { 

	//Sets variables to current values of form elements for latitude, longitude and map zoom
	latDef = $("#iptmp_map_lat").val();
	lngDef = $("#iptmp_map_long").val();
	initialZoom = $("#iptmp_map_zoom").val();
	
	//Defines the map container
	map = L.map("previewmap", { center: [latDef,lngDef], zoom: initialZoom});

	//Adds the background basemap
	L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png",{attribution:'Map data © OpenStreetMap contributors'}).addTo(map);

	//Adds marker with current location co-ordinates
	var iptmp_marker = L.marker([latDef,lngDef],{draggable:true}).addTo(map);
	
	//Associates click event with funtion that updates marker co-ordinates
	map.on('click', updateMarker);

	//Associates drag event with funtion that updates marker co-ordinates
	iptmp_marker.on('move',updateMarkerDrag);

	//Updates marker co-ordinates to click location on map
	function updateMarker(map_click_location){
		var marker_latlng, new_lat, new_lng, iptmp_lat_element, iptmp_lng_element;

		//Update marker lat long with click co-ordinates
		iptmp_marker.setLatLng(map_click_location.latlng);
		marker_latlng = map_click_location.latlng;
	
		//Assigns variables to equal latlng object latitude and longitude
		new_lat = marker_latlng.lat;
		new_lng = marker_latlng.lng;
	
		//Updates metabox latitude and longitude to new co-ordinates
		iptmp_lat_element = document.getElementById("iptmp_map_lat");
		iptmp_lng_element = document.getElementById("iptmp_map_long");
		iptmp_lat_element.setAttribute("value", new_lat);
		iptmp_lng_element.setAttribute("value", new_lng);
	}

	//Updates marker co-ordinates to dragged location on map
	function updateMarkerDrag(map_drag_location) {
		//var marker_latlng, new_lat, new_lng, iptmp_lat_element, iptmp_lng_element;
		var marker_latlng,new_lat, new_lng, iptmp_lat_element, iptmp_lng_element;

		marker_latlng = map_drag_location.latlng;

		//Assigns variables to equal latlng object latitude and longitude
		new_lat = marker_latlng.lat;
		new_lng = marker_latlng.lng;

		//Updates metabox latitude and longitude to new co-ordinates
		iptmp_lat_element = document.getElementById("iptmp_map_lat");
		iptmp_lng_element = document.getElementById("iptmp_map_long");
		iptmp_lat_element.setAttribute("value", new_lat);
		iptmp_lng_element.setAttribute("value", new_lng);
	}
} );