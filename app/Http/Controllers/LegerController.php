<?php

namespace App\Http\Controllers;
use App\Models\Leger;
use App\Models\AccReg;
use App\Models\GroupReg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import Carbon class

class LegerController extends Controller
{
    //


    public function ListLeger()
    {
        return Leger::all();
    }
    
 
    public function OpenBlc($id ,Request $request)
    {
        $date = $request->query('date');
        $results = DB::table('Leger')
            ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->select(
                DB::raw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS OpeningBal'),
                'Leger.Accid',
                DB::raw('MAX(Leger.Dated) AS TillDt'),
                'AccReg.AccNo',
                'AccReg.AccName',
                'AccReg.Urdo',
                'AccReg.Address',
                'AccReg.Ph',
                'GroupReg.GroupName'
            )
            ->where('Leger.Dated', '<', $date)
            ->where('Leger.Accid', $id)
            ->groupBy(
                'Leger.Accid',
                'AccReg.AccNo',
                'AccReg.AccName',
                'AccReg.Urdo',
                'AccReg.Address',
                'AccReg.Ph',
                'GroupReg.GroupName'
            )
            ->get();

        return response()->json($results);
    }

    public function OneLeger($id)
    {

        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('m-d-y');
        // return   $formattedDate;
        $results = DB::table('Leger')
            ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->select(
                DB::raw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS ClosingBal'),
                'Leger.Accid',
                DB::raw('MAX(Leger.Dated) AS TillDt'),
                'AccReg.AccNo',
                'AccReg.AccName',
                'AccReg.Urdo',
                'AccReg.Address',
                'AccReg.Ph',
                'AccReg.GroupId',
                'GroupReg.GroupName'
            )
            ->where('Leger.Dated', '<=', $formattedDate)
            ->where('Leger.Accid', $id)
            ->groupBy(
                'Leger.Accid',
                'AccReg.AccNo',
                'AccReg.AccName',
                'AccReg.Urdo',
                'AccReg.Address',
                'AccReg.Ph',
                'AccReg.GroupId',
                'GroupReg.GroupName'
            )
            ->get();

        return response()->json($results);
    }
    
    public function getGroupBalances(Request $request, $id)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('m-d-y');
        
        $results = DB::table('Leger')
            ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->select(
                DB::raw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS Balance'),
                DB::raw('SUM(ISNULL(Leger.Debit, 0)) AS Debit'),
                DB::raw('SUM(ISNULL(Leger.Credit, 0)) AS Credit'),
                'AccReg.GroupId',
                'GroupReg.GroupName'
            )
            ->where('Leger.Dated', '<=', $formattedDate)
            ->where('AccReg.GroupId', '=', $id)
            ->groupBy('AccReg.GroupId', 'GroupReg.GroupName')
            ->get();
    
        return $results;
    }
    
   
// '''''''''''''''''''''''''''''''''''


public function getLedgerEntries(Request $request, $id)
{
    $startDate = $request->query('startDate');
    $endDate = $request->query('endDate');
    
    $results = DB::table('Leger as p')
        ->select('p.Accid', 'p.Dated', 'p.Description', 'p.RefNo', 'p.Debit', 'p.Credit')
        ->selectSub(function ($query) {
            $query->selectRaw('COALESCE(SUM(ISNULL(Debit, 0)) - SUM(ISNULL(Credit, 0)), 0)')
                ->from('Leger as c')
                ->whereColumn('c.Trid', '<=', 'p.Trid')
                ->whereColumn('c.Accid', 'p.Accid');
        }, 'RunningBalance')
        ->addSelect('p.VNo', 'p.Type','p.MVNo')
        ->where('p.Dated', '>=',  $startDate)
        ->where('p.Dated', '<=', $endDate)
        ->where('p.Accid', $id)
        ->orderBy('p.Dated')
        ->orderBy('p.Trid')
        ->get();

    return response()->json($results);
}

public function getAccountBalances(Request $request, $groupId)
{
    $currentDate = Carbon::now();
    $formattedDate = $currentDate->format('m-d-y');

    $results = DB::table('Leger')
        ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
        ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
        ->select(
            DB::raw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS LastBal'),
            'Leger.Accid',
            DB::raw('MAX(Leger.Dated) AS MaxDT'),
            'AccReg.AccName',
            'AccReg.Urdo',
            'AccReg.Address',
            'AccReg.Ph',
            'GroupReg.GroupName',
            'AccReg.AccNo',
            'AccReg.Description',
            'AccReg.OtherInfo',
            'AccReg.SrNo'
        )
        ->where('Leger.Dated', '<=', $formattedDate)
        ->where('AccReg.GroupId', $groupId)
        ->groupBy(
            'Leger.Accid',
            'AccReg.AccName',
            'AccReg.Urdo',
            'AccReg.Address',
            'AccReg.Ph',
            'GroupReg.GroupName',
            'AccReg.AccNo',
            'AccReg.Description',
            'AccReg.OtherInfo',
            'AccReg.SrNo'
        )
        ->get();

    return $results;
}


public function getAccountSummary(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $results = DB::table('Leger')
            ->select(
                DB::raw('SUM(ISNULL(Debit, 0)) AS Debit'),
                DB::raw('SUM(ISNULL(Credit, 0)) AS Credit'),
                DB::raw('MIN(Dated) AS MinDt'),
                DB::raw('MAX(Dated) AS MaxDt'),
                'Accid',
                DB::raw('SUM(ISNULL(HSD, 0)) AS HSD'),
                DB::raw('SUM(ISNULL(PMG, 0)) AS PMG')
            )
            ->where('Dated', '>=', $startDate)
            ->where('Dated', '<=', $endDate)
            ->groupBy('Accid')
            ->get();

        return $results;
    }


    public function getLedgerGroup(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $query = Leger::select('Leger.Accid', 'Leger.Dated', 'Leger.VNo', 'Leger.MVNo', 'Leger.Type', 'Leger.RefNo', 'Leger.Description', 
             \DB::raw('ISNULL(Leger.Debit, 0) AS Debit'), \DB::raw('ISNULL(Leger.Credit, 0) AS Credit'), 'Leger.Timed', 
             'AccReg.AccNo', 'AccReg.AccName', 'AccReg.GroupId', 'GroupReg.GroupName')
            ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->where('Leger.Dated', '>=', $startDate)
            ->where('Leger.Dated', '<=', $endDate)
            ->orderBy('Leger.Trid')
            ->get();

        return response()->json($query);
    }


    public function getLedgerEntriesByGroup(Request $request, $id)
    {
        $startDate = '2023-08-08'; // Adjust the format as needed
        $endDate = '2023-08-08';   // Adjust the format as needed

        $query = Leger::select('Leger.Accid', 'Leger.Dated', 'Leger.VNo', 'Leger.MVNo', 'Leger.Type', 'Leger.RefNo', 'Leger.Description', 
             \DB::raw('ISNULL(Leger.Debit, 0) AS Debit'), \DB::raw('ISNULL(Leger.Credit, 0) AS Credit'), 'Leger.Timed', 
             'AccReg.AccNo', 'AccReg.AccName', 'AccReg.GroupId', 'GroupReg.GroupName')
            ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->where('AccReg.GroupId', $id)
            ->where('Leger.Dated', '>=', $startDate)
            ->where('Leger.Dated', '<=', $endDate)
            ->orderBy('Leger.Trid')
            ->get();

        return $query;
    }


    public function ChartAccount(){
        $balances = DB::table('Leger')
    ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
    ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
    ->join('ChartAcc', 'GroupReg.ChartId', '=', 'ChartAcc.ChartId')
    ->selectRaw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS Balance, GroupReg.ChartId, ChartAcc.ChartName')
    ->groupBy('GroupReg.ChartId', 'ChartAcc.ChartName')
    ->orderBy('GroupReg.ChartId')
    ->get();

return $balances;
    }


    
    public function getBalance($id)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('m-d-y');
        $balances = Leger::join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
            ->join('GroupReg', 'AccReg.GroupId', '=', 'GroupReg.GroupId')
            ->where('Leger.Dated', '<=',  $formattedDate)
            ->where('ChartId', '=', $id) // Filter based on ChartId
            ->groupBy('AccReg.GroupId', 'GroupReg.GroupName', 'ChartId')
            ->selectRaw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS Balance, 
                         SUM(ISNULL(Leger.Debit, 0)) AS Debit, 
                         SUM(ISNULL(Leger.Credit, 0)) AS Credit, 
                         AccReg.GroupId, GroupReg.GroupName, ChartId')
            ->get();
    
        return $balances;
    }
    
//    =======================================================================================


public function ActiveAccounts(){
    $results = DB::table('GroupReg')
    ->join('AccReg', 'GroupReg.GroupId', '=', 'AccReg.GroupId')
    ->leftJoin('Leger', 'AccReg.Accid', '=', 'Leger.Accid')
    ->selectRaw('SUM(ISNULL(Leger.Debit, 0)) - SUM(ISNULL(Leger.Credit, 0)) AS Balance')
    ->addSelect('Leger.Accid AS Accid1', 'AccReg.Accid', 'AccReg.AccNo', 'AccReg.AccName')
    ->addSelect(DB::raw('ISNULL(AccReg.Ph, 0) AS Ph'))
    ->addSelect('AccReg.GroupId', 'GroupReg.GroupName')
    ->addSelect(DB::raw('AccReg.AccNo + AccReg.AccName + ISNULL(AccReg.Ph, 0) AS NewName'))
    ->groupBy('Leger.Accid', 'AccReg.Accid', 'AccReg.AccNo', 'AccReg.AccName', 'AccReg.Ph', 'AccReg.GroupId', 'GroupReg.GroupName')
    ->orderBy('AccReg.AccId')
    ->get();

return $results;
}

// public function insertForm(Request $request){
//     $nextVNo = DB::table('Leger')
//     ->where('RefNo', 'WEB')
//     ->count(DB::raw('DISTINCT VNo')) + 1;
    
// //    return  response()->json(['VNo' => $nextVNo]);
//     // return $nextVNo;
   

//     $dated = $request->input('Dated');
//     $compId = 1; // Assuming CompId is always 1
//     $userId = 1; // Assuming UserId is always 1
//     $AccName = $request->input('AccName');
//     $AccName2 = $request->input('AccName2');
//     $accId = $request->input('Accid');
//     $accId2 = $request->input('Accid2');
//     $AccNo = $request->input('AccNo');
//     $AccNo2 = $request->input('AccNo2');

//     $VNo = $nextVNo;
//     $type = $request->input('Type');
//     $refNo = $request->input('RefNo');
//     $debit = $request->input('Debit');
//     $description = $request->input('Description');
//     $remarks = $request->input('Remarks');
//     $timed = now();
//     $mvNo = $request->input('MvNo');
//     $bal = $request->input('Bal');
//     $Balance2 = $request->input('Balance2');
  
//     // Insert Debit record
//     Leger::create([
//         'Dated' => $dated,
//         'CompId' => $compId,
//         'UserId' => $userId,
//         'Accid' => $accId,
//         'AccNo' => $AccNo,
//         'VNo' => $nextVNo,
//         'Type' => $type,
//         'RefNo' => 'WEB',
//         'Debit' => $debit,
//         'Description' => $description,
//         'Remarks' => $remarks,
//         'Timed' => $timed,
//         'MvNo' => $mvNo,
//         'Bal' => $bal,
//     ]);

//     // Insert Credit record
//     Leger::create([
//         'Dated' => $dated,
//         'CompId' => $compId,
//         'UserId' => $userId,
//         'AccName' => $AccName2,
//         'Accid' => $accId2,
//         'AccNo' => $AccNo2,
//         'VNo' => $nextVNo,
//         'Type' => $type,
//         'RefNo' => 'WEB',
//         'Credit' => $debit, // Use the same value as Debit or adjust as needed
//         'Description' => $description,
//         'Remarks' => $remarks,
//         'Timed' => $timed,
//         'MvNo' => $mvNo,
//         'Bal' => $Balance2,
//     ]);

//     return response()->json(['message' => 'Records inserted successfully']);

// }
    


public function insertForm(Request $request)
{
    // Calculate the next VNo
    $nextVNo = DB::table('Leger')
        ->where('RefNo', 'WEB')
        ->count(DB::raw('DISTINCT VNo')) + 1;

        $result = DB::table('leger')
        ->selectRaw('ISNULL(MAX(DNo), 0) as DNo')
        ->get()[0]->DNo;
    

// return   $result;

    // Input values
    $dated = $request->input('Dated');
    $compId = 1; // Assuming CompId is always 1
    $userId = 1; // Assuming UserId is always 1
    $AccName = $request->input('AccName');
    $AccName2 = $request->input('AccName2');
    $accId = $request->input('Accid');
    $accId2 = $request->input('Accid2');
    $AccNo = $request->input('AccNo');
    $AccNo2 = $request->input('AccNo2');

    $VNo = $nextVNo;

    $DNo = $result;
    $type = $request->input('Type');
    $debit = $request->input('Debit');
    $description = $request->input('Description');
    $remarks = $request->input('Remarks');
    $timed = now();
    $mvNo = $request->input('MvNo');
    $Balance = $request->input('Balance');
    $Balance2 = $request->input('Balance2');

    // Insert Debit record
    $c1 = Leger::create([
        'Dated' => $dated,
        'CompId' => $compId,
        'UserId' => $userId,
        'Accid' => $accId,
        'AccNo' => $AccNo,
        'VNo' => $nextVNo,
        'DNo' => $result,
        'Type' => $type,
        'RefNo' => 'WEB',
        'Debit' => $debit,
        'Description' => $description,
        'Remarks' => $remarks,
        'Timed' => $timed,
        'MvNo' => $mvNo,
        'Balance' => $Balance,
    ]);

    // Insert Credit record
    $c2 = Leger::create([
        'Dated' => $dated,
        'CompId' => $compId,
        'UserId' => $userId,
        'AccName' => $AccName2,
        'DNo' => $result,
        'Accid' => $accId2,
        'AccNo' => $AccNo2,
        'VNo' => $nextVNo,
        'Type' => $type,
        'RefNo' => 'WEB',
        'Credit' => $debit, // Use the same value as Debit or adjust as needed
        'Description' => $description,
        'Remarks' => $remarks,
        'Timed' => $timed,
        'MvNo' => $mvNo,
        'Balance' => $Balance2,
    ]);

    return [$c1,$c2];
}


public function fetchLegerRecords(Request $request)
{
    $Dated = $request->query('Dated');
  
    // $results = DB::table('Leger')
    // ->select('Leger.Dated', 'Leger.VNo', 'Leger.Type', 'Leger.RefNo','AccReg.AccNo', 'Leger.Accid', 'Leger.MVNo', 'Leger.Description','Leger.Timed', 'Leger.Debit', 'Leger.Credit', 'Leger.Bal', 'AccReg.AccNo', 'AccReg.AccName', 'AccReg.Ph')
    // ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
    // ->where('Leger.RefNo', '=', 'WEB')
    // ->where('Leger.Dated', '=', $Dated)
    // ->get();
    // return $results;

    $Dated = $request->query('date');
  
    // Convert the date format to 'Y-m-d' format
    $formattedDate = date('Y-m-d', strtotime($Dated));

    $results = DB::table('Leger')
    ->select('Leger.Dated', 'Leger.VNo', 'Leger.Type', 'Leger.RefNo', 'AccReg.AccNo', 'Leger.Accid', 'Leger.MVNo', 'Leger.Description', 'Leger.Timed', 'Leger.Debit', 'Leger.Credit', 'Leger.Bal', 'AccReg.AccNo', 'AccReg.AccName', 'AccReg.Ph')
    ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
    ->where('Leger.RefNo', '=', 'WEB')
    ->where('Leger.Dated', '=', $formattedDate) // Use the formatted date
    ->orderBy('Leger.Trid')
    ->get();

    return $results;
}


public function fetchAllTrans(Request $request)
{
    $Dated = $request->query('Dated');
    $Dated = $request->query('date');
    $formattedDate = date('Y-m-d', strtotime($Dated));
    $results = DB::table('Leger')
    ->select('Leger.Dated', 'Leger.VNo', 'Leger.Type','Leger.DNo', 'Leger.RefNo', 'AccReg.AccNo', 'Leger.Accid', 'Leger.MVNo', 'Leger.Description', 'Leger.Timed', 'Leger.Debit', 'Leger.Credit', 'Leger.Bal', 'AccReg.AccNo', 'AccReg.AccName', 'AccReg.Ph')
    ->join('AccReg', 'Leger.Accid', '=', 'AccReg.Accid')
    ->where('Leger.Dated', '=', $formattedDate) // Use the formatted date
    ->orderBy('Leger.Trid')
    ->get();

    return $results;
}
}


