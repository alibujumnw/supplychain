<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\DeliveryCondition;
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
public function warehouse_condition($id)
{
    $user = Auth::user();
    $warehouse = Warehouse::where('farmer_id',$user->id)->where('id',$id)->first();
    if(!$warehouse)
    {
        return response()->json(['message'=>'warehouse not found']);
    }
    $conditions = Condition::where('warehouse_id',$warehouse->id)->first();
    if(!$conditions)
    {
        return response()->json(['message'=>'no condition found for this warehouse']);
    }
    return response()->json(['data'=>$conditions],200);
}




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

 
 public function show($id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        return response()->json(['delivery' => $delivery],200);
    }
       
    //delivery conditions

    public function delivery_condition($id)
    {
        $user = Auth::user();
        $delivery = DeliveryCondition::findOrFail($id);
        $user_delivery = DeliveryCondition::where('id',$id)->where('farmer_id',$user->id)->first();
        if(!$user_delivery)
        {
            return response()->json(['message'=>'no delivery condtion']);
        }
        return response()->json(['data'=>$user_delivery],200);

    }
/**
 * Delivery end
 */


  /**
  * End of Real time data
  */


   /**
    *Crop data 
     */ 
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

public function delete_crop($id)
{

    $user = Auth::user();
    Crop::where('id',$id)->where('farmer_id',$user->id)->delete();
    return response()->json(['message'=> 'crop deleted'],200);
}



public function view_crop($id)
{
    $user = Auth::user();
    $data = Crop::where('farmer_id',$user->id)->where('id',$id)->get();
    return response()->json(['data'=> $data],200);
}
 
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
       'product_name' => 'nullable',
               'quantity' => 'nullable',
               'kilograms' => 'nullable',
               'price_per_unit' => 'nullable',
               'storage_date' => 'nullable',
               'storage_last_date' => 'nullable',
               'soil_type' => 'nullable',
               'irrigation-method' => 'nullable',
                'fertilizers_used' => 'nullable',
                'description'=> 'nullable',
                'farmer_id' => 'nullable',
                'temp_min' => 'nullable',
                 'temp_max' => 'nullable',
               'humidity_min' => 'nullable',
               'humidity_max' => 'nullable',
               'shelf_life' => 'nullable',
               'warehouse_id' => 'nullable'
           
   ]);

   $crop = Crop::findOrFail($request->id);
   $crop->fill($data);
   $model = $crop->save();
   if($model)
   {
       return response()->json(['message'=>'update successfully'],200);
   }
   else
   {
    return response()->json(['message'=>'failed to update crop']);
   }
}

    /**
     * End of crop data
     */

/**
 * Livestock data
 */

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

    public function update_livestock(Request $request)
    {
        $data = $request->validate([
            'create_product' => 'nullable',
            'quantity' => 'nullable',
            'units' => 'nullable',
            'price_per_unit' => 'nullable',
            'breed' => 'nullable',
            'age' => 'nullable',
            'feed_type' => 'nullable',
            'health_status' => 'nullable',
            'vaccination_status' => 'nullable',
            'description' => 'nullable',
            'farmer_id' => 'nullable',
            'temp_min' => 'nullable',
            'temp_max'=>'nullable',
            'humidity_min'=>'nullable',
            'humidity_max' => 'nullable',
            'warehouse_id' => 'nullable',
            
        ]);

        $user = Auth::user();
        $farmer = Livestock::findOrFail('id',$request->id);
        $livestock = Livestock::where('id', $request->id)->where('farmer_id',$user->id)->first();
        if(!$livestock)
        {
            return response()->json(['message'=>'livestock not found']);
        }
        
        $livestock->fill($data);
        $model = $livestock->save();
        if(!$model)
        {
            return response()->json(['message'=>'failed to update livestock']);
        }
        return response()->json(['message'=>'livestock updated'],200);
    }

    public function delete_livestock($id)
    {
        $user = Auth::user();
        $data = Livestock::where('id',$id)->where('farmer_id',$user->id)->delete();
        return response()->json(['message'=>'livestocl deleted'],200);
    }

/**
 * Livestock data end
 */


 /**
  * warehouse data
  */
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
        $data = Warehouse::all();
        return response()->json(['warehouse' => $data],200);
    }

  public function update_warehouse(Request $request)
  {
    $user = Auth::user();
    $data = $request->validate(
        [
            'warehouse_name' => 'nullable',
            'warehouse_size' => 'nullable',
            'temp_min' => 'nullable',
            'temp_max' => 'nullable',
            'IoT_device_id' => 'nullable',
            'warehouse_type' => 'nulluable',
        ]
        );
    $warehouse = Warehouse::where('id',$request->id)->where('farmer_id',$user->id)->first();

    if(!$warehouse)
    {
        return response()->json(['message'=>'no warehouse found']);
    }

    Warehouse::update($data);

  }
//delete warehouse
public function delete_warehouse($id)
{
    $user = Auth::user();
    $warehouse = Warehouse::where('id',$id)->where('farmer_id',$user->id)->delete();
    return response()->json(['message'=>'warehouse deleted']);
}

  //returns all the products in the warehouse
public function warehouse_products($id)
{
    $farmer = Warehouse::where('farmer_id',$id)->get();
    $product = [];
    foreach($farmer as $data)
    {
        $crops = Crop::where('warehouse_id', $data->id)->get();
        $livestock = Livestock::where('warehouse_id', $data->id)->get();
        $product = array_merge($product, $crops->toArray());
        $product = array_merge($product, $livestock->toArray());

    }

      return response()->json(['data'=>$product],200);
}

/**
 * End of warehouse
 */

 public function store(Request $request)
    {
        $validator = $request->validate([
          'temperature' => 'required', 
        'warehouse_id' => 'required', 
        'humidity' => 'required',  
        'recorded_at' => 'required',
        ]);

        if (!$validator) {
            return response()->json(['error' => 'unable to add conditions'], 400);
        }

        $reading = Condition::create([
        'temperature' => $validator['temperature'], 
        'warehouse_id' => $validator['warehouse_id'], 
        'humidity' => $validator['humidity'],  
        'recorded_at' => $validator['recorded_at'],
        ]);

        return response()->json([
            'message' => 'Temperature reading saved successfully',
            'data' => $reading
        ], 201);
    }
    


}