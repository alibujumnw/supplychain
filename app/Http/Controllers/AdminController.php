<?php

namespace App\Http\Controllers;

use Hash;
use Exception;
use App\Models\User;
use App\Models\Route;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\Logistic;
use App\Models\Supplier;
use App\Models\SensorData;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'password' => 'required|string|min:8',
            'type'=>'required|string',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);
        $user = User::where('email', $request->email)
        ->where('type', $request->type)
        ->firstOrFail();
        if($request->type == 'farmer'){
        $farmer = $user->farmer()->create([
        'surname' => $request->surname,
        'name' => $request->name,
        'farm_name' => $request->farm_name,
        'farm_location' => $request->farm_location,
        'farm_size' => $request->farm_size,
        ]);
        }
    elseif($request->type == 'supplier')
    {
    $supplier = $user->supplier()->create([
        'surname' => $request->surname,
        'name' => $request->name,
        'company_name' => $request->company_name,
        'company_address' => $request->company_address,
        'phone_number' => $request->phone_number,
        ]);
    }
    elseif($request->type == 'logistic')
    {
    $logistic = $user->logistic()->create(
        [
        'company_name' => $request->company_name, 
        'company_location'=>$request->company_location, 
        'company_phone' =>$request->company_phone,
        'vihecle_type' => $request->vehecle_type, 
        'vihecle_number' => $request->vehecle_number,
        'driver' => $request->vehecle->driver,
        'driver_phone' =>$request->driver_phone
        ]
        );
        }else
    {
   
    return response()->json(['message' => 'Failed to create user'], 500);

    }

    return response()->json(['message' => 'User created successfully'], 200);
        
}
/*
update user
*/

public function update_user(Request $request)
{
    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'email'=> 'nullable|string|max:255'. $request->id,
        'type'=> 'nullable|string|max:255',
        'id'=>'required'
    ]);

    $val = $request->validate([
        'surname' => 'nullable',
        'name' => 'nullable',
        'farm_name' => 'nullable',
        'farm_location' => 'nullable',
        'farm_size' => 'nullable',
    ]);

    $supplier = $request->validate([
        'surname' => 'nullable|string',
        'name' => 'nullable|string',
        'company_name' => 'nullable|string',
        'company_address' => 'nullable|string',
        'phone_number' => 'nullable|string',
    ]);

    $logistic = $request->validate([
        'company_name' => 'nullable',
        'company_location' => 'nullable', 
        'company_phone' => 'nullable',
        'vihecle_type' => 'nullable', 
        'vihecle_number' => 'nullable',
        'driver' => 'nullable',
        'driver_phone' => 'nullable'
    ]);

    try {

    $model = User::findOrFail($request->id);
    $email = $model->email;
    $type = $model->type;
    $id = $model->id;
    if($type == 'farmer')
    {
      $mod = Farmer::where('farmer_id',$id)->first();
      $val['email'] = $email;
      $mod->fill($val);
     $save = $mod->save();
    }
    else if($type == 'supplier')
    {
      $mod = Supplier::where('supplier_id',$id)->first();
    //   $val['email'] = $email;
      $mod->fill($supplier);
      $save = $mod->save($supplier);
    }
    else if ($type == 'logistic'){
        $mod = Logistic::where('logistic_id',$id)->first();
        $val['email'] = $email;
        $mod->fill($logistic);
        $save = $mod->save($logistic);
    }
 
    if ($save) {
        return response()->json(['message' => 'User updated successfully'], 200);
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
public function view_all_users($type)
{
    if ($type === 'farmer') {
        // Fetch farmers along with their related user data
        $users = User::where('type', 'farmer')->with('farmer')->get();
    } elseif ($type === 'supplier') {
        // Fetch suppliers along with their related user data
        $users = User::where('type', 'supplier')->with('supplier')->get();
    }elseif($type === 'logistic'){
            $user = User::where('type', 'logistic')->with('logistic')->get();
    } 
    else {
        // If the type is invalid, return an error response
        return response()->json(['message' => 'Invalid user type'], 400);
    }

    return response()->json(['data' => $users], 200);
}


 /**
  * delete user
  */
 public function delete_user($id)
 {
   
try{
   $user = User::findOrFail($id);
   $type = $user->type;

   $users = User::where('id', $id)->with($type)->delete();
   return response()->json(['message','user deleted successful']);

   }
   catch (ModelNotFoundException $e) {
    // Catch if user with that ID is not found
    return response()->json(['message' => 'User not found'], 404);
}
   
}

  //create LoT

public function add_LoT(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'device_type' => 'required|string|max:255',
        'serial_number' => 'required|string|unique:devices,serial_number',
        'description' => 'nullable|string',
        'status' => 'nullable|string|inactive,inactive',
        'user_id' => 'required',
        'device_location' => 'required',
        'location'=>'nullable',
        'reg_number' => 'nullable',

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
        'user_id' => 'required',
        'device_location' => 'required',
        'location'=>'nullable',
        'reg_number' => 'nullable',
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
public function delete_LoT($id)
{
    $device = Device::findOrFail($id);
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

public function delete_LoT_condition($id)
{
    $device = SensorData::findOrFail($id);
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

 public function delete_route($id)
 {
    $route = Route::findOrFail($id);
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
