<?php

namespace App\Http\Controllers;

use Hash;
use Exception;
use App\Models\User;
use App\Models\Route;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    //
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
                $role = $user->type;
                #otp nolonger send to employee
                return response()->json([
                    'status' => 'Request was successfull',
                    'message' => 'Admin has been sign-in successfully',
                    'data' => $token,
                    'role' => $role,
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


    /*
    The admin has the ability to create new users
    */
     public function create_user(Request $request)
     {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type'=>'required|string',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'],
        ]);

        $val = $request->validate([
            'surname' => 'required',
            'name' => 'required',
            'farm_name'=> 'require',
            'farm_location'=>'required',
            'farm_size'=> 'required',
            'crop_type' => 'required',
        ]);

        $usr = Farmer::create($val);

        if ($user) {
            return response()->json(['message' => 'User created successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
     }
/*
update user
*/

public function update_user(Request $request)
{
    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'email'=> 'nullable|string|max:255',
        'type'=> 'nullable|string|max:255',
        'id'=>'required'
    ]);

try {

    $model = User::findOrFail($request->id);
    $model->fill($validated);

    $saved = $model->save();

    if ($saved) {
        return response()->json(['message' => 'User updated successfully', 'user' => $model], 200);
    } else {
        return response()->json(['message' => 'Failed to update user'], 500);
    }
} catch (ModelNotFoundException $e) {
    // Catch if user with that ID is not found
    return response()->json(['message' => 'User not found'], 404);
} catch (Exception $e) {
    // Catch other unexpected errors
    return response()->json(['message' => 'Error updating user', 'error' => $e->getMessage()], 500);
}


}


/**
 * read all users
 * read one user
 */
   public function read_all_users()
   {
    $users = User::all();
    return response()->json(['users'=>$users],200);
   }

   public function read_user($id)
   {
    try {
        $user = User::findOrFail($id);
    
        return response()->json(['message' => 'User found', 'user' => $user], 200);
    
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'User not found'], 404);
    }
    

   }

 /**
  * delete user
  */
 public function delete_user(Request $request)
 {
   User::find($request->id)->delete();    
   return response()->json(['message'=> 'Account deleted'],200);
 }

/**
 * end of the user section
 */

 /**
  * LoT device section monitoring  
  */


  //create LoT

public function add_LoT(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'device_type' => 'required|string|max:255',
        'serial_number' => 'required|string|unique:devices,serial_number',
        'description' => 'nullable|string',
        'status' => 'nullable|string|in:active,inactive',
    ]);

    
    $device = Device::create($data);

   if($device){
    return response()->json([
        'message' => 'Device added successfully',
        'device' => $device
    ], 200);
   }
   else{
    return response()->json([
        'message' => 'Device Creation failed',
        'device' => $device
    ], 500);
   }
   
}


//update 
public function update_LoT(Request $request)
{
    // Validate the incoming data
    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'device_type' => 'nullable|string|max:255',
        'serial_number' => 'nullable|string|unique:devices,serial_number,', 
        'description' => 'nullable|string',
        'status' => 'nullable|string|in:active,inactive',
        'id'=>'required',
    ]);

 
    $device = Device::findOrFail($request->id);

    $device->fill($validated);

    if ($device->save()) {
        
        return response()->json([
            'message' => 'Device updated successfully',
            'device' => $device
        ], 200);
    }

    return response()->json(['message' => 'Failed to update device'], 500);
}

//delete
public function delete_LoT(Request $request)
{
    $device = Device::findOrFail($request->id);
    $device->delete();
    return response()->json(['message'=>'Device destroyed'],200);
}

//read

public function read_all_LoT()
{
    $loT = Device::all();
    return response()->json(['devices'=>$loT],200);
}

public function read_LoT($id)
{
    $loT = Device::findOrFail($id);
    return response()->json(['device'=>$loT],200);
}

/**
 * inseart the expected info 
 */
public function create_LoT_condition(Request $request)
{
        $validated = $request->validate([
            'temperature' => 'nullable|numeric',
            'soil_moisture' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'rainfall' => 'nulluable|numeric',  
        ]);

        $sensorData = SensorData::create($validated);

        return response()->json([
            'message' => 'Sensor data added successfully.',
            'data' => $sensorData
        ], 200);
    
}

public function delete_LoT_condition(Request $request)
{
    $device = SensorData::findOrFail($request->id);
    $device->delete();
    return response()->json(['message'=>'Sensor Data Destroyed'],200);
}

public function update_LoT_condition(Request $request)
{
    $validated = $request->validate([
        'temperature' => 'nullable|numeric',
        'soil_moisture' => 'nullable|numeric',
        'humidity' => 'nullable|numeric',
        'rainfall' => 'nulluable|numeric',  
    ]);

    $model = SensorData::findorfail($request->id);
    $model->fill($validated);
    if ($model->save()) {
        
        return response()->json([
            'message' => 'Sensor Data updated successfully',
            'device' => $model
        ], 200);
    }

    return response()->json(['message' => 'Failed to update device'], 500);
}

public function view_LoT_condition($id)
{
    $condition = SensorData::where('id',$id)->first();
    return response()->json(['data'=>$condition],200);
}
/**
 * routes
 * 
 */

 public function update_route(Request $request)
 {
    $validated = $request->validate([
        'origin'=>'required',
        'destination'=> 'required',
        'status'=>'required',
        'id'=> 'required',
    ]);
 $route = Route::findOrFail($request->id);
 $route->update($validated);
 if($route)
 {
    return response()->json(['message'=> 'route updated'],200);
 }
 else
 {
    return response()->json(['message'=> 'failed to update'],500);
 }
 }

 public function delete_route(Request $request)
 {
    $route = Route::findOrFail($request->route_id);
    $route->delete();
    return response()->json(['message'=> 'Route removed'],200);
 }
 public function view_routes()
 {
    $route = Route::all();
    return response()->json(['routes'=>$route],200);
 }
 public function view_route(Request $request)
{
    $route = Route::where($request->route_id)->first();
    return response()->json(['route'=>$route],200);
}



}
