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


public function view_farmers()
{    
 $users = User::where('type', 'supplier')->with('supplier')->get();
 return response()->json(['data'=>$users]);
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
