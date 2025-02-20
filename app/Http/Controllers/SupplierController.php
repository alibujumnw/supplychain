<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{
    public function create_user(Request $request)
    {
       $validated = $request->validate([
           'name' => 'nullable|string|max:255',
           'email' => 'required|string|email|max:255|unique:users',
           'password' => 'required|string|min:8',
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
           'company_name'=> 'required',
           'company_address'=>'required',
           'phone_number'=> 'required',
       ]);

       $usr = Supplier::create($val);

       if ($user && $usr) {
           return response()->json(['message' => 'User created successfully'], 200);
       } else {
           return response()->json(['message' => 'Failed to create user'], 500);
       }
    }
}
