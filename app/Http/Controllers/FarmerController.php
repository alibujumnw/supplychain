<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Crop;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FarmerController extends Controller
{
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
            "farm_name"=> "require",
            "farm_location"=> "required",
            "farm_size"=> "required",
            "crop_type" => "required",
        ]);

        $usr = Farmer::create($val);

        if ($user) {
            return response()->json(['message' => 'User created successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
     }
public function edit_farmer(Request $request)
{
    $validate = $request->validate([
        'id'=> 'required',
        'farm_name'=> 'nullable|string',
        'farm_location'=> 'nullable|string',
        'farm_size'=> 'nullable|string',
        'crop_type' => 'nullable|string',
    ]);

    $user = Farmer::findOrFail($request->id);
    $user->fill($validate);
    $model = $user->save();

    if( $model ) {
        return response()->json(['message'=> 'Farmer info updates','user'=> $user],200);
    } else {
        return response()->json(['message'=> 'Failed to update Farmer'],500);
    }
}

public function view_farmer()
{
 $user = Farmer::where('id',request()->id)->first();

if($user)
{
    return response()->json(['user'=>$user],200);
}
else{
    return response()->json(['message'=>'failed'],200);
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
public function delete_crop(Request $request)
{
    Crop::findOrFail($request->id)->delete();
    return response()->json(['message'=> 'crop deleted'],200);
}

public function view_all_crops()
{
    $data =Crop::all();
    return response()->json(['data'=> $data],200);
} 

public function view_crop(Request $request)
{
    $data = Crop::where('id',$request->id)->first();
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
         

}
