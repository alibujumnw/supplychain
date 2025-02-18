<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\LogisticsRoute;
use Illuminate\Support\Facades\Auth;

class LogisticsController extends Controller
{
  public function login(Request $request)
  {
    try {


        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'invalid user credentials',
            ]);

        } 
        else {
            $user = Auth::user();

            $token = $user->createToken('token')->plainTextToken;
            $cooke = cookie('jwt', $token, 60 * 11);

            #otp nolonger send to employee
            return response()->json([
                'status' => 'Request was successfull',
                'message' => 'sign-in successfully',
                'data' => $token,
            ], 200)->withCookie($cooke);

        }
    } catch (Exception $e) {
        $massage = $e->getMessage();
        return response()->json([
            'status' => 'Request error',
            'message' => 'something went wrong',
            'data' => $massage,
        ], 400);
    }
  }

  //logistics account

  public function view_logistic(Request $request)
  {
    $model = Logistics::where('id',$request->id)->first();

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


