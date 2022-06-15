<?php

namespace App\Http\Controllers;
use App\Models\File;
use App\Imports\VendorsImport;
use App\Models\Note;
use App\Models\Sender;
use App\Models\Vendor;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class vendorController extends Controller
{
    function index(){
        $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get() ;
        return view('addvendor',['vendors'=>$vendors]);
    }

    function submit(Request $req){
        if($req->radiocheck == "add"){
            $ven = Vendor::where('vendor', '=', $req->vendorname)->first();
            if ($ven === null) {
                 $vendor = new vendor();
                 $vendor->vendor = $req->vendorname;
                 $vendor->created_by = Auth::user()->name;
                 $vendor->save();
                 $req->session()->flash('alert-success', 'Vendor Successfully Added!');
            }
        else{
            $req->session()->flash('alert-danger', 'Vendor Already exists!');
        }

        return redirect()->route('addvendor');

        }

        else if($req->radiocheck == "import"){
            $message = "Sheet Successfully Imported";
            $color = "alert-success";
            $path1 = $req->file('vendorExcel')->store('temp');
            $path=storage_path('app').'/'.$path1;
            $extension = pathinfo(storage_path($path), PATHINFO_EXTENSION);
            if($extension=="xlsx"){
            $rows= Excel::toArray(new VendorsImport,$path);

            foreach($rows[0] as $row){
                $ven = Vendor::where('vendor', '=', $row['vendor'])->first();
            if ($ven === null) {
                $vendor = new vendor();
                $vendor->vendor =  $row['vendor'];
                $vendor->created_by = Auth::user()->name;
                $vendor->save();
                }
            }
        }
        else{
            $message = "Please import .xlsx file";
            $color = "alert-danger";
        }
            $req->session()->flash($color, $message);
            return redirect()->route('addvendor');
        }
    }

    
    function editVendor(Request $req){
        DB::table('vendors')
            ->where('vn_id', $req->vn_id)
            ->update([
                'vendor' => $req->vendor
                    ]);
        $resp = [$req->vendor,$req->tr_row];
        return response()->json($resp);
    }
    function deleteVendor(Request $req){
        $ids = explode(",",$req->vn_ids);
        $restricted = array();
        foreach($ids as $vn_id){
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $out->writeln($req->vn_ids);  
            $file = File::where('vendor', '=', $vn_id)->first();
            $note = Note::where('vendor', '=', $vn_id)->first();
            $sender = Sender::where('vendor', '=', $vn_id)->first();
            if ($file != null || $note!=null || $sender!=null) {
                $vname = DB::table('vendors')->where('vn_id', $vn_id)->first()->vendor;
                array_push($restricted,$vname);
            }
        }
        if(empty($restricted)){
            foreach($ids as $vn_id){
                DB::table('vendors')->where('vn_id', $vn_id)->delete();
            }
            return response()->json("success");
        }
        else{
            return response()->json($restricted);
        }


    }
}
