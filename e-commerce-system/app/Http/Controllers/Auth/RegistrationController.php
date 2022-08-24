<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email' , 'unique:users,email'],
            'password' => ['required', 'min:6']
        ]);
        $user = User::create([
            'name'=>request('name'),
           //or we can use this  'name' => $request->name,
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);
        $authToken = $user->createToken('access-token')->plainTextToken;
        return response()->json([
            'access_token' => $authToken,
            'message' => 'user authorized'
        ]);
    }
}
