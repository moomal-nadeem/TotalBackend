<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyPro;
use Illuminate\Support\Facades\Validator;
class CompanyProController extends Controller
{
    //

    public function logins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'WebUser' => 'required',
            'WebPass' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors(),
            ], 401);
        }
    
        $user = CompanyPro::where('WebUser', $request->input('WebUser'))->first();
    
        if ($user && $request->input('WebPass') === $user->WebPass) {
            // You can add your custom logic for status checking here
            // For this example, we're assuming status is always 'allow'
            // $token = $user->createToken($request->input('WebUser'))->plainTextToken;
    
            return response([
                // 'token' => $token,
                'prank' => $user->Accid,
                'name' => $user->name,
                'WebStatus' => $user->WebStatus,
                'message' => 'Login successfully',
            ], 200);
        }
    
        return response([
            'message' => 'Provided username or password is wrong',
            'status' => 'failed',
        ], 401);
    }
    
}
