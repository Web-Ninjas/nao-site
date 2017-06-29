		var map;
		var markers = [];
		var markerCluster = null;

		var largeInfoWindow;
		var defaultIcon;
		var highLightedIcon;

		function initMap()
		{
			/*
			var styles = [
				{
					featureType: ,
					stylers: []
				}
			];
			*/

			map = new google.maps.Map(document.getElementById('map'), {
				center: {lat: 48.856614, lng: 2.3522219000000177}, // coordonnées de Paris
				zoom: 7,
				// styles: styles,
				mapTypeControle: false,
				maxZoom: 11,
				streetViewControl: false
			});
			
			largeInfoWindow = new google.maps.InfoWindow();
			defaultIcon = makeMarkerIcon('9DC8C8');
			highLightedIcon = makeMarkerIcon('5C7EE5');		

			if (listObservations)
				setMarkers(map, listObservations);
				updateLabel(listObservations.length);	
		}

		function withDrawMarkers()
		{
			markers.forEach(function(e){
				e.setMap(null);
			});
			if (markerCluster)
				markerCluster.clearMarkers();
		}

		function setMarkers(map, observations)
		{
			markers = [];

			observations.forEach(function(observation, index)
			{

				var marker = new google.maps.Marker({
					map: map,
					position: {lat: parseFloat(observation.latitude), lng: parseFloat(observation.longitude) },
					title: observation.nomOiseau,
					icon: defaultIcon,
					animation: google.maps.Animation.DROP,
					id: index // si bug voir avec observation.id
				});
				markers.push(marker);

				// Fait apparaître les infos quand on clique sur un markeur
				marker.addListener('click', function(){
					populateInfoWindow(this, largeInfoWindow);
					populateTable(this);
				});

				// Change le style du markeur quand on passe la souris dessus
				marker.addListener('mouseover', function(){
					this.setIcon(highLightedIcon);
				});

				marker.addListener('mouseout', function(){
					this.setIcon(defaultIcon);
				});
			});

			// Rajoute un regroupement des markers quand ils sont trop proches
			markerCluster = new MarkerClusterer(map, markers,
            	{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

		}

		function populateInfoWindow(marker, infoWindow)
		{
			if(infoWindow.marker != marker)
			{
				infoWindow.marker = marker;
				infoWindow.setContent('<div>' + marker.title + '<img alt="" src="' + listObservations[marker.id].photoPath + '" width="80" height="80">' + '</div>');
				infoWindow.open(map, marker);
				infoWindow.addListener('closeclick', function(){
					infoWindow.setMarker(null);
				});
			}
		}

		function makeMarkerIcon(markerColor)
		{
			var markerImage = new google.maps.MarkerImage(
				'http://chart.googleapis.com/chart?chst=d_map_spin&chld=1.15|0|' + markerColor + '|40|_|%E2%80%A2',
				new google.maps.Size(21, 34),
				new google.maps.Point(0, 0),
				new google.maps.Point(10, 34),
				new google.maps.Size(21, 34),
				);
			return markerImage;
		}