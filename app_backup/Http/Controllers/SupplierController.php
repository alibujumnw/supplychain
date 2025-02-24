<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller
{
    public function create_supplier(Request $request)
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
        $supplier = $user->supplier()->create([
            'surname' => $request->surname,
            'name' => $request->name,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'phone_number' => $request->phone_number,
            ]);

        if ($user && $supplier) {
            return response()->json(['message' => 'User created successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function edit_supplier(Request $request)
    {
        $validate = $request->validate([
            'surname' => 'nullable|string',
            'name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'supplier_id' => 'required'
        ]);
    
        $user = Supplier::where('supplier_id',$request->supplier_id)->first();
        if(!$user)
        {
            return response()->json(['message'=>'user not found'],500);
        }
        else{
        $user->fill($validate);
        $model = $user->save($validate);
    
        $user = Supplier::where('supplier_id',$request->supplier_id)->first();
        $name = array($user->name);

        if( $model ) {
            $usr = User::where('id',$request->supplier_id)->first();
            $usr->fill($name);
            $usr->save();
            return response()->json(['message'=> 'supplier info updates','user'=> $user],200);
        }
    
        }
        
    }

public function view_suppliers()
{
    
    $users = User::where('type', 'supplier')->with('supplier')->get();
    return response()->json(['data'=>$users]);
}




}

