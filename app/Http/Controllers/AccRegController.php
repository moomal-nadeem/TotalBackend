<?php

namespace App\Http\Controllers;
use App\Models\AccReg;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class AccRegController extends Controller
{
    //
    public function ListAccReg()
    {
        return AccReg::all();   
    }


    public function login(Request $request)
    {
        

$request->validate([
    'WebUser'=>'required',
    'WebPass'=>'required',
]);
 return $request->input();
        // $validator = Validator::make($request->all(), [
        //     'WebUser' => 'required',
        //     'WebPass' => 'required',
        // ]);
    
        // if ($validator->fails()) {
        //     return response()->json([
        //         'msg' => $validator->errors(),
        //     ], 401);
        // }
    
        // $user = AccReg::where('WebUser', $request->input('WebUser'))->first();
    
        // if ($user && $request->input('WebPass') === $user->WebPass) {
        //     // You can add your custom logic for status checking here
        //     // For this example, we're assuming status is always 'allow'
        //     // $token = $user->createToken($request->input('WebUser'))->plainTextToken;
    
        //     return response([
        //         // 'token' => $token,
        //         'prank' => $user->Accid,
        //         'name' => $user->name,
        //         'WebStatus' => $user->WebStatus,
        //         'message' => 'Login successfully',
        //     ], 200);
        // }
    
        // return response([
        //     'message' => 'Provided username or password is wrong',
        //     'status' => 'failed',
        // ], 401);
    }
    

}
