<?php

namespace App\Http\Controllers;
use App\Imports\SendersImport;
use App\Models\Operator;
use App\Models\Sender;
use App\Models\Vendor;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Log;

class senderController extends Controller
{
    function index(){
        $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
        $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
        return view('addsender',['countries'=>$countries,'vendors'=>$vendors]);
    }
    public function c(Request $request)
    {
        
        $input = $request->all();
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln($req);  
        Log::info($input);
     
        return response()->json(['success'=>'Got Simple Ajax Request.']);
    }

    function submit(Request $req){
        if($req->radiocheck == "add"){
                $sender = new sender;
                $sender->senderid =  $req->senderid;
                $sender->content =  $req->content;
                $sender->website =  $req->website;
                $sender->note =  $req->note;
                $sender->operator =  $req->operator;
                $sender->vendor = $req->vendor;
                $sender->created_by = Auth::user()->name;
                $sender->save();
                $selectedOptions = [$req->country,$req->operator,$req->vendor];
        // $req->session()->flash('selectedOptions',$selectedOptions);
        // $req->session()->flash('operators',Session::get('tempoperators'));
        // $req->session()->flash('alert-success', 'Sender Successfully Added!');
        return response()->json([
                'success'=> true,
                'status' => 200,
                'message' => 'Sender Successfully Added!',
                'sender' => $sender
            ]);
            
        // return redirect()->route('searchsender');
        // return response()->json($sender);
        // return $sender;
        }

        else if($req->radiocheck == "import"){

            $message = "Sheet Successfully Imported";
            $color = "alert-success";
            $path1 = $req->file('senderidExcel')->store('temp');
            $path=storage_path('app').'/'.$path1;
            $extension = pathinfo(storage_path($path), PATHINFO_EXTENSION);
            try{
            if($extension=="xlsx"){
            $rows= Excel::toArray(new SendersImport,$path);
            $emptysender = false;
            $rowcounter=0;
            foreach($rows[0] as $row){
                if($row['senderid']==""){
                    $emptysender=true;
                    break;
                }
                $rowcounter++;
            }
            if(!$emptysender){
                 foreach($rows[0] as $row){
                    $sender = new sender;
                    $sender->senderid =  $row['senderid'];
                    $sender->content =  $row['content'];
                    $sender->website =  $row['website'];
                    $sender->note =  $row['note'];
                    $sender->operator =  $req->operator;
                    $sender->vendor = $req->vendor;
                    $sender->created_by = Auth::user()->name;
                    $sender->save();
                 }
             }
             else{
                $message = "Sender ID can't be empty at row ".($rowcounter+2);
                $color = "alert-danger";
             }
        }
        else{
            $message = "Please import .xlsx file";
            $color = "alert-danger";
        }
    }
    catch(\Exception $e){
        $message = "Invalid Sheet Content";
            $color = "alert-danger";
    }
        $selectedOptions = [$req->country,$req->operator,$req->vendor];
        $req->session()->flash('selectedOptions',$selectedOptions);
        $req->session()->flash('operators',Session::get('tempoperators'));
            $req->session()->flash($color, $message);
            // return redirect()->route('searchsender');
            return response()->json([
                'success'=> true,
                'status' => 200,
                'message' => 'Sender Successfully Added!',
            ]);
        }
    }

    
    function getOperator(Request $req){
        $operators = DB::table('operators')->where('country', $req->country)->get();
        if($req->page =="searchsender"){
        Session::put('tempoperators', $operators);
        }
        if($req->page =="searchnote"){
            Session::put('notetempoperators', $operators);
        }
        return response()->json($operators);
    }


    function deleteSender(Request $req){

    try 
    {  
        $ids = explode(",",$req->sn_ids);
        foreach($ids as $sn_id){
             DB::table('senders')->where('sn_id', $sn_id)->delete();
         }
         return response()->json([
            'status'=>'success', 
            'msg'=>'Success!.'
        ]);
    } 
    catch (Exception $exception) {
        return response()->json(array('status'=>'error', 'msg'=>'Error!'), 500);

    }
    }
    function editSender(Request $req){
        DB::table('senders')
            ->where('sn_id', $req->sn_id)
            ->update([
                'senderid' => $req->senderid,
                'content' => $req->content,
                'website' => $req->website,
                'note' => $req->note
                    ]);
                    
        $resp = [$req->senderid,$req->content,$req->website,$req->note,$req->tr_row];
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln($resp);  
        return response()->json($resp);
    }
}
