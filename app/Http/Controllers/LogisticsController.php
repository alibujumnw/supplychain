<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Logistic;
use Illuminate\Http\Request;
use App\Models\LogisticsRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogisticsController extends Controller
{

  //logistics account
  public function create_logistic(Request $request)
  {
    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'type'=>'required|string',
    ]);
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'type' => $request->type,
    ]);
    $logistic = $user->logistic()->create(
        [
        'company_name' => $request->company_name, 
        'company_location'=>$request->company_location, 
        'company_phone' =>$request->company_phone,
        'vehicle_type' => $request->vehecle_type, 
        'vehicle_number' => $request->vehecle_number,
        'driver_full_name' => $request->vehecle->driver,
        'driver_phone' =>$request->driver_phone
        ]
        );

    if ($user && $logistic) {
        return response()->json(['message' => 'User created successfully'], 200);
    } else {
        return response()->json(['message' => 'Failed to create user'], 500);
    }
  }

  public function edit_logistic(Request $request)
  {
      $validate = $request->validate([
        'company_name' => 'nullable',
        'company_location' => 'nullable', 
        'company_phone' => 'nullable',
        'vihecle_type' => 'nullable', 
        'vihecle_number' => 'nullable',
        'driver' => 'nullable',
        'driver_phone' => 'nullable'
    ]);
  
      $user = Logistic::where('logistic_id',$request->logistic_id)->first();
      if(!$user)
      {
          return response()->json(['message'=>'user not found'],500);
      }
      else{
      $user->fill($validate);
      $model = $user->save($validate);
  
      $user = Logistic::where('logistic_id',$request->logistic_id)->first();
      $name = array($user->name);

      if( $model ) {
          $usr = User::where('id',$request->logistic_id)->first();
          $usr->fill($name);
          $usr->save();
          return response()->json(['message'=> 'logistic info updates','user'=> $user],200);
      }
  
      }
      
  }
  public function view_logistic(Request $request)
  {
  $users = User::where('type', 'supplier')->with('supplier')->get();
  return response()->json(['data'=>$users]);
  }


  public function delete_logistic(Request $request)
  {
    
  try{
    $user = User::findOrFail($request->supplier_id);
    $users = User::where('id', $request->id)->with('logistic')->delete();
    return response()->json(['message','user deleted successful']);
  
    }
    catch (ModelNotFoundException $e) {
     return response()->json(['message' => 'User not found'], 404);
  }
    
  }




    /**
     * Create a new logistics route.
     */
    public function createRoute(Request $request)
    {
        $request->validate([
            // Define validation rules for route creation (e.g., origin, destination, distance)
        ]);

        $route = LogisticsRoute::create($request->all());

        return response()->json($route, 201); 
    }

    /**
     * Get a list of all logistics routes.
     */
    public function getRoutes()
    {
        $routes = LogisticsRoute::all(); 
        return response()->json($routes);
    }

    /**
     * Get a specific logistics route.
     */
    public function getRoute($id)
    {
        $route = LogisticsRoute::findOrFail($id);
        return response()->json($route);
    }

    /**
     * Update a logistics route.
     */
    public function updateRoute(Request $request, $id)
    {
        $request->validate([
            // Define validation rules for route updates
        ]);

        $route = LogisticsRoute::findOrFail($id);
        $route->update($request->all());

        return response()->json($route);
    }

    /**
     * Delete a logistics route.
     */
    public function deleteRoute($id)
    {
        $route = LogisticsRoute::findOrFail($id);
        $route->delete();

        return response()->json(['message' => 'Route deleted successfully']);
    }
    /**
     * Get real-time vehicle data (using IoT integration).
     */
    public function getVehicleData()
    {
        // Logic to fetch real-time data from IoT devices (e.g., GPS trackers)
        // This might involve interacting with an external API or a database
        // Example:
        // $vehicleData = $this->fetchVehicleDataFromIoT(); 
        // return response()->json($vehicleData); 
    }

    /**
     * Monitor transportation costs, routes, and delivery times.
     */
    public function getTransportationMetrics()
    {
        // Logic to calculate and retrieve metrics (e.g., average delivery time, cost per mile)
        // This might involve querying the database and performing calculations
        // Example:
        // $metrics = $this->calculateTransportationMetrics(); 
        // return response()->json($metrics); 
    }

    // ... Add other methods for updating schedules, managing unforeseen events, etc. ...

    // Example:
    public function updateSchedule($orderId)
    {
        // Logic to update the delivery schedule for a specific order
    }

    // .

}


