<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;
use Illuminate\Http\Request;
use Hash;

class GpsController
{
    public function showHomePage() {

    	$users = DB::table('users')->where('role',2)->get();
    	$locationInfo = DB::table('location')->where('user_id',2)->get();
    	return view('welcome',['users'=>$users, "location" => $locationInfo]);
    }

    public function applyDate( Request $request ) {
    	if($request->departureTime > $request->endTime){
    		return redirect()->back()->withErrors(['msg' => ' Departure Time can not be less than the End Time ']);
    	} else if(empty($request->pathDate)) {
    		return redirect()->back()->withErrors(['msg' => ' Please enter the date ']);
    	} else {
    		// Fetching User Data Information from Database
    		$users = DB::table('users')->where('role',2)->get();
    		$locationInfo = DB::table('location')->whereBetween('time',[$request->departureTime,$request->endTime])->where('date',$request->pathDate)->where('status','1');
    		$locationUserCount = DB::table('location')->whereBetween('time',[$request->departureTime,$request->endTime])->where('date',$request->pathDate)->where('status','1')->groupBy('user_id');
    		$newLocationArray = [];
    		if($request->users == 0){

    			$locationInfo = $locationInfo->get();
    			$locationUserCount = $locationUserCount->get();
    			foreach ($locationUserCount as $key => $value) {
    				foreach ($locationInfo as $index => $loc) {
    					if($loc->user_id == $value->user_id) {
    						$newLocationArray[$key][] = $loc;	
    					}
    				}
    			}
    		} else { 
    			$locationInfo = $locationInfo->where('user_id',$request->users)->get();
    			$locationUserCount = $locationUserCount->where('user_id',$request->users)->get();
    			foreach ($locationInfo as $index => $loc) {
					$newLocationArray[0][] = $loc;
    			}
    		}
            $isRealTime = false;
            if(empty($locationInfo)) {
    			return redirect()->back()->withErrors(['msg' => ' No Data Found ']);
    		} else {
    			return view('welcome',['users'=>$users, "locationPath" => $newLocationArray,"location" => $locationInfo, "isRealTime" => $isRealTime]);	
    		}
    	}
    }

    public function launchRealtime() {
    	
    	$users = DB::table('users')->where('role',2)->get()->toArray();
    	
    	$MaxLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MAX(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1)';
    	$MinLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MIN(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1)';
    	$locationData = DB::select(DB::raw($MaxLocationquery));
    	$locationMinData = DB::select(DB::raw($MinLocationquery));
    	foreach($locationData as $key => $location){
    		foreach($users as $user){
    			if($user->id === $location->user_id){
    				$source_lat = $locationMinData[$key]->lat;
    				$source_lng = $locationMinData[$key]->lng;
    				$time1 = strtotime($locationMinData[$key]->time);
    				$time2 = strtotime($location->time); 
    				$totalTimeTaken = ($time1 - $time2)/60;
    				$distance = $this->distance($source_lat,$source_lng,$location->lat,$location->lng,"K");
    				$speed = ($totalTimeTaken == 0) ? 0 : ($distance/$totalTimeTaken);
    				$locationData[$key]->user = $user->name;
    				$locationData[$key]->speed = round($speed,2)." km/h";
    				$locationData[$key]->distance = round($distance,2);
    				$locationData[$key]->totalTimeTaken = round($totalTimeTaken,2);
    			} else {
    				continue;
    			}
    		}
    	}
    	$isRealTime = true;
        return view('welcome',["users"=>$users, "location" => $locationData, "isRealTime" => $isRealTime]); 
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);
      	if ($unit == "K") {
       		return ($miles * 1.609344);
      	} else if ($unit == "N") {
          	return ($miles * 0.8684);
     	} else {
            return $miles;
      	}
    }

    public function getRealTimeData($id){
    	
    	$users = DB::table('users')->where('role',2)->get()->toArray();
    	if($id==0){
    		$MaxLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MAX(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1)';
    		$MinLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MIN(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1)';
    	} else {
    		$MaxLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MAX(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1 AND t2.user_id = '.$id.')';
    		$MinLocationquery = 'SELECT t1.* FROM location t1 WHERE t1.id = (SELECT MIN(t2.id) FROM location t2 WHERE t2.user_id = t1.user_id AND t2.status = 1 AND t2.user_id = '.$id.')';
    	}
    	$locationData = DB::select(DB::raw($MaxLocationquery));
    	$locationMinData = DB::select(DB::raw($MinLocationquery));
    	foreach($locationData as $key => $location){
    		foreach($users as $user){
    			if($user->id === $location->user_id){
    				$source_lat = $locationMinData[$key]->lat;
    				$source_lng = $locationMinData[$key]->lng;
    				$time1 = strtotime($locationMinData[$key]->time);
    				$time2 = strtotime($location->time); 
    				$totalTimeTaken = ($time1 - $time2)/60;
    				$distance = $this->distance($source_lat,$source_lng,$location->lat,$location->lng,"K");
    				$speed = ($totalTimeTaken == 0) ? 0 : ($distance/$totalTimeTaken);
    				$locationData[$key]->user = $user->name;
    				$locationData[$key]->speed = round($speed,2)." km/h";
    				$locationData[$key]->distance = round($distance,2);
    				$locationData[$key]->totalTimeTaken = round($totalTimeTaken,2);
    			} else {
    				continue;
    			}
    		}
    	}
    	$isRealTime = true;
    	$locationInfo = array();

    	$locationInfo["location"] = $locationData;
    	$locationInfo["isRealTime"] = $isRealTime;
    	$locationInfo["status"] = "OK";
    	echo json_encode($locationInfo);
    }

    public function setMyAjaxHeader() {
        // Allow from any origin
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }


    public function authenticate(Request $request) {
        $this->setMyAjaxHeader();
        $response = array();
        $data = file_get_contents("php://input");
        $userData = json_decode($data, true);   
        $user = DB::table('users')->select('*')->where('email', $userData["username"])->first();
        if(!empty($user)) {
            if(Hash::check($userData["password"], $user->password)) {
                $response["status"] = "OK";
                $response["message"] = "Authentication Successfully Done.";
                $response["user"] = $user;
            } else {
                $response["status"] = "NOTOK";
                $response["message"] = "Not a Authenticated User.";
            }
            
        } else {
            $response["status"] = "NOTOK";
            $response["message"] = "User is not Registered.";
        }
        echo json_encode($response);
    }

    public function saveLocation() {
        $this->setMyAjaxHeader();
        $response = array();
        $data = file_get_contents("php://input");
        if(!empty($data)) {
            $location = json_decode($data, true);
            DB::table('location')->insert($location);
            $response["status"] = "Valid";
        } else {
            $response["status"] = "Invalid";
        }
    }
}
