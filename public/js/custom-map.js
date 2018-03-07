
customMap = {
	recursiveCall : function (){
		
		if(customMap.url){
			if(customMap.setUserId){
				appendUrl= '/getRealTimeData/'+customMap.setUserId;
			} else {
				appendUrl= '/getRealTimeData/0';
			}
			$.get(customMap.url+appendUrl, function(data, status){
		        data = jQuery.parseJSON(data);
		        if(data.status == "OK"){
		        	console.log(data);
		        	customMap.showMarkerOnMap(data.location);
		        }
		    });			
		}

	},

	showMarkerOnMap : function (location){    
	    var locations = [];
	    var centerLat,centerLng;
	    for(var i = 0; i < location.length ; i++){
	        if(i == 0) {
	            centerLat = location[i].lat;
	            centerLng = location[i].lng;
	        }
	        var html_Info = '<h3>'+location[i].user+'</h3><h5> '+location[i].created_at+' </h5><h5>Speed: '+location[i].speed+' </h5>'
	        var markerArray = [html_Info,location[i].lat,location[i].lng,(i+1)];
	        locations.push(markerArray);
	    }
	    var map = new google.maps.Map(document.getElementById('map'), {
	          zoom: 10,
	          center: new google.maps.LatLng(centerLat, centerLng),
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	        });
	    var infowindow = new google.maps.InfoWindow();
	    var marker, i;
	    var iconUrl = customMap.url+'/images/map-marker-icon.png';
	    for (i = 0; i < locations.length; i++) { 
	      var iconBase = {
	          url: iconUrl,
	         scaledSize: new google.maps.Size(35, 35),
	      }
	      marker = new google.maps.Marker({
	        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
	        map: map,
	        icon: iconBase
	      });

	      google.maps.event.addListener(marker, 'click', (function(marker, i) {
	        return function() {
	          infowindow.setContent(locations[i][0]);
	          infowindow.open(map, marker);
	        }
	      })(marker, i));
	    }
	}
}