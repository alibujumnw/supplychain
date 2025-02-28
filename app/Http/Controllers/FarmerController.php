<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Crop;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Delivery;
use App\Models\Livestock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FarmerController extends Controller
{
    public function create_farmer(Request $request)
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
        $farmer = $user->farmer()->create([
            'surname' => $request->surname,
            'name' => $request->name,
            'farm_name' => $request->farm_name,
            'farm_location' => $request->farm_location,
            'farm_size' => $request->farm_size,
            ]);

        if ($user && $farmer) {
            return response()->json(['message' => 'User created successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }


    
    public function edit_farmer(Request $request)
    {
        $validate = $request->validate([
            'surname' => 'nullable',
            'name' => 'nullable',
            'farm_name' => 'nullable',
            'farm_location' => 'nullable',
            'farm_size' => 'nullable',
        ]);
    
        $user = Farmer::where('farmer_id',$request->farmer_id)->first();
        if(!$user)
        {
            return response()->json(['message'=>'user not found'],500);
        }
        else{
        $user->fill($validate);
        $model = $user->save($validate);
    
        $user = Farmer::where('farmer_id',$request->farmer_id)->first();
        $name = array($user->name);

        if( $model ) {
            $usr = User::where('id',$request->farmer_id)->first();
            $usr->fill($name);
            $usr->save();
            return response()->json(['message'=> 'farmer info updates','user'=> $user],200);
        }
    
        }
        
    }


public function view_farmers($userId)
{    
 $users = User::where('id', $userId)->with('farmer')->get();
 return response()->json(['data'=>$users]);
}

public function delete_farmer($id)
{
  
try{
  $user = User::findOrFail($id);
  $users = User::where('id', $id)->with('farmer')->delete();
  return response()->json(['message','user deleted successful']);

  }
  catch (ModelNotFoundException $e) {
   return response()->json(['message' => 'User not found'], 404);
}
  
}

public function change_farmer_password(Request $request)
{
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed', 
    ]);

    // Check if the current password is correct
    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return response()->json(['current_password' => ' The current password is incorrect.']);
    }

    // Update the user's password
    $user = Auth::user();
    $user->password = Hash::make($request->new_password); // Hash the new password
    $user->save();

    return response()->json(['states'=>'password changed successfully'],200);
    
}

/**
 * crop infomation
 */

 //create
 
 public function crop_details(Request $request)
 {
        $data = $request->validate([
            'crop_type'=> 'required',
            'harvest_timeline'=> 'required',
            'quantity'=>'required',
            'quality'=>'required',
        ]);
        Crop::create($data);
        return response()->json(['message'=>'crop added successfully'],200);
 }
public function update_crop(Request $request)
{
    $data = $request->validate([
        'crop_type'=> 'nullable',
        'harvest_timeline'=> 'nullable',
        'quantity'=>'nullable',
        'quality'=>'nullable',
        'id' => 'required'
    ]);

    $crop = Crop::findOrFail($request->id);
    $crop->fill($data);
    $model = $crop->save();
    if($model)
    {
        return response()->json(['message'=>'update successfully'],200);
    }
}
public function delete_crop($id)
{
    Crop::findOrFail($id)->delete();
    return response()->json(['message'=> 'crop deleted'],200);
}



public function view_crop($id)
{
    $data = Crop::where('farmer_id',$id)->get();
    return response()->json(['data'=> $data],200);
}

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
                'message' => 'farmer has been sign-in successfully',
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

/**
 * view real time data 
 */

/**
 * delivery status
 */

 public function updateStatus(Request $request)
    {
        $delivery = Delivery::find($request->id);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        $delivery->status = $request->input('status');
        
        // If the status is 'Delivered', we also record the actual delivery time
        if ($delivery->status == 'Delivered') {
            $delivery->actual_delivery_time = now();
        }

        $delivery->save();

        // Notify the user about the status change
        // For example, send a notification (see below)

        return response()->json(['message' => 'Delivery status updated successfully', 'delivery' => $delivery]);
    }

    // Display the current delivery status
    public function show($id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        return response()->json(['delivery' => $delivery],200);
    }
        
    
    public function create_crop(Request $request)
    {
        $data = $request->validate(
            [
                'product_name' => 'required',
                'quantity' => 'required',
                'kilograms' => 'required',
                'price_per_unit' => 'required',
                'storage_date' => 'required',
                'storage_last_date' => 'required',
                'soil_type' => 'required',
                'irrigation-method' => 'required',
                 'fertilizers_used' => 'required',
                 'description'=> 'required',
                 'farmer_id' => 'required',
                 'temp_min' => 'required',
                  'temp_max' => 'required',
                'humidity_min' => 'required',
                'humidity_max' => 'required',
                 'humidity' => 'required',
                'shelf_life' => 'required',
                'warehouse_id' => 'required'
            ]
            );

            $model = Crop::create($data);
            if($model)
            {
                return response()->json(['message'=>'crop created successfully'],200);
            }
            else
            {
                return response()->json(['message'=>'failed to create data']);
            }
    }



    public function create_livestock(Request $request)
    {
        $data = $request->validate([
            'create_product' => 'required',
            'quantity' => 'required',
            'units' => 'required',
            'price_per_unit' => 'required',
            'breed' => 'required',
            'age' => 'required',
            'feed_type' => 'required',
            'health_status' => 'required',
            'vaccination_status' => 'required',
            'description' => 'required',
            'farmer_id' => 'required',
            'temp_min' => 'required',
            'temp_max'=>'required',
            'humidity_min'=>'required',
            'humidity_max' => 'required',
            'warehouse_id' => 'required',
            
        ]);

        $model = Livestock::create($data);
        if($model)
        {
            return response()->json(['message'=>'livestock created successfully'],200);
        }
        else
        {
            return response()->json(['message'=>'failed to create data']);
        }

    }
    
    public function view_livestock($id)
    {
        $data = Livestock::where('farmer_id',$id)->get();
        return response()->json(['data'=> $data],200);
    }

    public function create_warehouse(Request $request){
        $data = $request->validate([
            'warehouse_name' => 'required',
            'warehouse_size' => 'required',
            'temp_min' => 'required',
            'temp_max' => 'required',
            'IoT_device_id' => 'required',
            'warehouse_type' => 'required',
            'farmer_id' => 'required',
        ]);

        $model = Warehouse::create($data);
        if($model)
        {
            return response()->json(['message'=>'livestock created successfully'],200);
        }
        else
        {
            return response()->json(['message'=>'failed to create data']);
        }
    }
    
    public function view_warehouse($id)
    {
        $date = Warehouse::where('farmer_id',$id)->get();
        return response()->json(['warehouse' => $date],200);
    }

}