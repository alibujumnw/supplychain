<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Logistic;
use Illuminate\Http\Request;
use App\Models\LogisticsRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LogisticsController extends Controller
{

  //logistics account
  public function create_farmer(Request $request)
  {
     $validated = $request->validate([
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:8|confirmed',
         'type'=>'nulluable|string',
     ]);
     if($request->type=='admin')
     {
         return response()->json(['message'=>'unable to create user with such role']);
     }
     $user = User::create([
         'name' => $validated['name'],
         'email' => $validated['email'],
         'password' => Hash::make($validated['password']),
         'type' => $validated['type'],
     ]);

     $val = $request->validate([
       'company_name' => 'required',
        'company_location' => 'required',
        'company_phone' => 'required',
        'vihecle_type' => 'required', 
        'vihecle_number' => 'required',
        'driver' => 'required',
        'driver_phone' => 'required',
     ]);

     $usr = Logistic::create($val);

     if ($user) {
         return response()->json(['message' => 'User created successfully'], 200);
     } else {
         return response()->json(['message' => 'Failed to create user'], 500);
     }
  }




  public function view_logistic(Request $request)
  {
    $model = Logistic::where('id',$request->id)->first();

    if(!$model)
    {
        return response()->json(['message'=>'no user found'],500);
    }
    else
    {
        return response()->json(['user'=>$model],200);
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


