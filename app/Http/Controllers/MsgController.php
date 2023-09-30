<?php

namespace App\Http\Controllers;


use App\Models\Msg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class MsgController extends Controller
{
    public function addMsg(Request $req, $id)
    {
        $validator = Validator::make($req->all(), [
            'Msg' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'Msg' => $validator->errors(),
                'status' => 401
            ]);
        }
    
        $d = new Msg;
        $d->Msg = $req->input('Msg');
        $d->Accid = $id;
        $d->Dated = Carbon::now()->format('Y-m-d'); // Current date in 'Y-m-d' format
    
        $d->save();
    
        return $d;
    }

public function fetchMsgsByAccid($id)
{
    $msgs = Msg::where('Accid', $id)->get();
    return $msgs;
    // return response()->json([
    //     'data' => $msgs,
    //     'status' => 200
    // ]);
}

public function ListAllMsg()
{
    $msgs = Msg::all();
    return $msgs;
    // return response()->json([
    //     'data' => $msgs,
    //     'status' => 200
    // ]);
}
public function msgDelete($trid){
    $res = Msg::where('Trid',$trid)->delete();
    if($res)
     {
        return ["res"=>"Data has been deleted"];
     }else{
        return ["res"=>"fail to delete"];
     }
   
    }
    

    public function msgEdit($trid, Request $req)
    {
        $Replys = $req->input('Replys');
    
        if (!empty($Replys)) {
            // Append current date to the Replys field
            $currentDate = Carbon::now()->toDateString(); // Get the current date in YYYY-MM-DD format
            $updatedReplys = $Replys . " (" . $currentDate . ")";
            
            Msg::where('Trid', $trid)->update(['Replys' => $updatedReplys]);
        }
    
        $d = Msg::where('trid', $trid)->first(); // Retrieve the updated record
        return $d;
    }
    

}
