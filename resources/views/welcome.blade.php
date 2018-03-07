<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">

        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        
    </head>
    <body>
        <div class="container gpsminicontainer">
            <div class="row">
                <!-- Left Side Section Div -->
                <div class="col-sm-3">
                    <!-- Realtime Location Div -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="realtime-location">
                          <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#realtime" aria-expanded="true" aria-controls="realtime" >
                              Realtime Location <i class="fa fa-angle-down pull-right"></i>
                            </a>
                          </h4>
                        </div>
                        <div id="realtime" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="realtime-location">
                          <div class="panel-body">
                            <p> <a href="{{url('/launchRealtime')}}"> Launch </a> </p>
                            <p> <a href="{{url('/launchRealtime')}}"> Reload </a> </p>
                          </div>
                        </div>
                    </div>
                    <!-- Path Div -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="path-collapse">
                          <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#path" aria-expanded="true" aria-controls="path" >
                              Path <i class="fa fa-angle-down pull-right"></i>
                            </a>
                          </h4>
                        </div>
                        <div id="path" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="path-collapse">
                          <div class="panel-body">

                            <form action="{{ url('/applyDate') }}" method="POST" >
                              @if($errors->any())
                                <h5 style="color:red;">{{$errors->first()}}</h5>
                              @endif
                              {{ csrf_field() }}
                              <div class="form-group">
                                <label for="pathdate" >Date</label>
                                <input type="date" class="form-control" name="pathDate" id="pathDate" placeholder="Date">
                              </div>
                              <div class="form-group">
                                <label for="departure-time">Departure Time</label>
                                <input type="time" class="form-control" name="departureTime" id="departureTime" placeholder="00:00">
                              </div>
                              <div class="form-group">
                                <label for="end-time">End Time</label>
                                <input type="time" class="form-control" name="endTime" id="endTime" placeholder="00:00">
                              </div>
                              <div class="form-group">
                                <label for="sel1">Select User:</label>
                                <select class="form-control" id="users" name="users" >
                                  <!-- <option value="0"> All </option> -->
                                  @foreach($users as $user)
                                    <option value="{{$user->id}}" > {{$user->name}} </option>
                                  @endforeach
                                </select>
                              </div>
                              <button type="submit" class="btn btn-default col-sm-12 border-button" > Apply </button>
                            </form>
                          </div>
                        </div>
                    </div>
                    <!-- Account Div -->
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="account-collapse">
                          <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#account" aria-expanded="true" aria-controls="account" >
                              Accounts <i class="fa fa-angle-down pull-right"></i>
                            </a>
                          </h4>
                        </div>
                        <div id="account" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="account-collapse">
                        <div class="panel-body account-body">
                            <p> <a class="userClass" href="#" value="0"> All </a> </p>
                            <!-- Get List of All Users -->
                            @foreach($users as $user)
                                <p> <a class="userClass" value="{{$user->id}}"> {{$user->name}} </a> </p>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9 mapDiv">
                     <div id="map"></div>           
                </div>
            </div>
        </div>  
        <script type="text/javascript" src="{{asset('js/custom-map.js')}}"></script>
        <script>

        $('.account-body').on('click',function(e){
            var val = e.target;
            console.log(val);
            var getVal = $(val).attr('value');

            $('.userClass').removeClass('active');
            $(val).addClass('active');
            customMap.setUserId = getVal;
            customMap.url = "{{url('')}}";
            customMap.recursiveCall();
            window.setInterval(customMap.recursiveCall,60000);
            return false;
        });
          // This example displays a marker at the center of Australia.
          // When the user clicks the marker, an info window opens.
          // The maximum width of the info window is set to 200 pixels.

        function initMap() {
            
        map = new
        google.maps.Map(document.getElementById("map"));    
            
            calculateAndDisplayRoute();
            
            // var contentString = 'Rohit';

            // var infowindow = new google.maps.InfoWindow({
            //   content: contentString,
            //   maxWidth: 200
            // });

            // var iconUrl = "{{asset('images/map-marker-icon.png')}}";
            // var iconBase = {
            //     url: iconUrl,
            //    scaledSize: new google.maps.Size(35, 35),
            // }
            // console.log(iconBase);
            // var marker = new google.maps.Marker({
            //   position: centerCoordinates,
            //   map: map,
            //   title: 'Not Found',
            //   icon: iconBase
            // });
            
            // marker.addListener('click', function() {
            //   infowindow.open(map, marker);
            // });
        }
        
        function renderDirections(result) {

          var directionsRenderer = new google.maps.DirectionsRenderer;
          directionsRenderer.setMap(map);
          directionsRenderer.setDirections(result);
        }
        
        function requestDirections(directionsService, waypts, start, end) {
          directionsService.route({
              origin: start,
              destination: end,
              waypoints: waypts,
              optimizeWaypoints: false,
              travelMode: 'DRIVING'
          }, function(result) {
              renderDirections(result);
          });
          /*directionsService.route({
            origin: start,
            destination: end,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
          }, function(result) {
            renderDirections(result);
          });*/
        }
        function calculateAndDisplayRoute() {
            directionsService = new google.maps.DirectionsService;
            @if(isset($locationPath))
              @foreach($locationPath as $locationInfo)
                  var waypts = [];
                  var checkboxArray = [];
                  @foreach($locationInfo as $key => $loc)
                      @if($key==0)
                          var start = new google.maps.LatLng("{{$loc->lat}}","{{$loc->lng}}");
                          var end = new google.maps.LatLng("{{$loc->lat}}","{{$loc->lng}}");
                      @endif
                      var object = { value : new google.maps.LatLng("{{$loc->lat}}","{{$loc->lng}}")};
                      checkboxArray.push(object);
                  @endforeach
                  for (var i = 0; i < checkboxArray.length; i++) {
                      waypts.push({
                          location: checkboxArray[i].value,
                          stopover: true
                      });
                  }
                  requestDirections(directionsService, waypts, start, end);
              @endforeach
            @endif
            // requestDirections(directionsService, 'Huntsville, AL', 'Boston, MA');
            // requestDirections(directionsService, 'Bakersfield, CA', 'Vancouver, BC');
            
        }

        function initRealTime(){
            
            var locations = [];
            var centerLat,centerLng;
            @foreach($location as $key => $loc)
                @if($key == 0)
                    centerLat = {{$loc->lat}} ;
                    centerLng = {{$loc->lng}} ;
                @endif
                var html_Info = '<h3>{{!empty($loc->user)?$loc->user:''}}</h3><h5> {{$loc->created_at}} </h5><h5>Speed: {{!empty($loc->speed)?$loc->speed:''}} </h5>'
                var markerArray = [html_Info,{{$loc->lat}},{{$loc->lng}},{{$key+1}}];
                locations.push(markerArray);
            @endforeach
            var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 10,
                  center: new google.maps.LatLng(centerLat, centerLng),
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                });
            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            var iconUrl = "{{asset('images/map-marker-icon.png')}}";
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
            customMap.url = "{{url('')}}";
            window.setInterval(customMap.recursiveCall,60000);
        }
        function checkFunction(){
            console.log({{$isRealTime}});
            var isRealTime = {{$isRealTime}}
            console.log(isRealTime);
            if(!isRealTime) {
                initMap();
            } else if (isRealTime) {
                initRealTime();
            }
        }
        </script>

        <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7k5boSDK_Oy4d7ZQtIVVMBRWtwcL2W5Y&callback=checkFunction">
        </script>
        
        <!-- <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7k5boSDK_Oy4d7ZQtIVVMBRWtwcL2W5Y&callback=">
        </script> -->
    </body>
</html>
