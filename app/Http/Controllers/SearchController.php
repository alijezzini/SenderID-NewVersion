<?php

namespace App\Http\Controllers;
use App\Models\Operator;
use App\Models\Vendor;
use App\Models\Sender;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class SearchController extends Controller
{


    public function searchall(){

        return view('searchall');

    }

    public function lol(Request $request){
        if($request->ajax()) {
      
             $countries= DB::table('operators')->select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
              $vendors = DB::table('vendors')->select('vendor')->orderBy('vendor')->get()->toArray() ;
              $senders = DB::table('senders')
              ->join('operators', 'operators.op_id', '=', 'senders.operator')
              ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
              ->get();
         return DataTables::of($senders,$countries,$vendors)
             ->addIndexColumn()
//             ->addColumn('action',function (){
//                 // action does goes here
//             })
//             ->rowColumns(['action'])
             ->make(true);
    }

    }
//    public function searchall()
//{
//    $senders = DB::table('senders')
//    ->join('operators', 'operators.op_id', '=', 'senders.operator')
//    ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
//    ->get();
//    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
//    $vendors = Vendor::select('vendor')->orderBy('vendor')->get()->toArray() ;
//    return view('searchall',['countries'=>$countries,'vendors'=>$vendors,'senders'=>$senders]);
//}

public function searchsender()
{

    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    return view('searchsender',['countries'=>$countries,'vendors'=>$vendors]);
}
public function searchallnotes()
{
    $notes = DB::table('notes')
    ->select('vendors.vendor','operators.operator','country','vendors.vn_id','operators.op_id')
    ->join('operators', 'operators.op_id', '=', 'notes.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'notes.vendor')
    ->get()->toArray();

    $files = DB::table('files')
    ->select('vendors.vendor','operators.operator','country','vendors.vn_id','operators.op_id')
    ->join('operators', 'operators.op_id', '=', 'files.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'files.vendor')
    ->get()->toArray();

    $merge = array_merge($notes,$files);
    $output = array_map("unserialize", array_unique(array_map("serialize", $merge)));
    $final = array();
    foreach($output as $o){

        $row=array();
        array_push($row,$o->vn_id);
        array_push($row,$o->op_id);
        array_push($row,$o->vendor);
        array_push($row,$o->operator);
        array_push($row,$o->country);
        $temp_note = "false";
        $temp_file = "false";
        foreach($notes as $n){
            if($o==$n){
                $temp_note = "true";
                break;
            }
        }
        foreach($files as $f){
            if($o==$f){
                $temp_file = "true";
                break;
            }
        }
        array_push($row,$temp_note);
        array_push($row,$temp_file);
        array_push($final,$row);
    }
    return view('searchallnotes',['data'=>$final]);
}
 public function getsenderTable(Request $req){

    $emps = DB::table('senders')
    ->join('operators', 'operators.op_id', '=', 'senders.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
    ->where('operators.op_id', '=', $req->operator)
    ->where('vendors.vn_id', '=', $req->vendor)
    ->get();
    $selectedOptions = [$req->country,$req->operator,$req->vendor];
    $req->session()->flash('selectedOptions',$selectedOptions);
    $req->session()->flash('operators',Session::get('tempoperators'));
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    $output = '';
	if ($emps->count() > 0) {
        $output .= '<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
          <tr>
            <td><input type="checkbox" id="checkAll"></td>
            <th>SenderID</th>
            <th>Content</th>
            <th>Website</th>
            <th>Note</th>
            <td></td>
          </tr>
        </thead>
        <tbody id="tablebody">';
        foreach ($emps as $emp) {
            $output .= '<tr id="tr_'.$emp->sn_id.'">
         
            <td><input id="delete" type="checkbox" class="checkbox" data-id="' . $emp->sn_id . '" ></td>
     
            <td>' . $emp->senderid . '</td>
            <td>' . $emp->content . '</td>
            <td>' . $emp->website . '</td>
            <td>' . $emp->note . '</td>
            <td><div >
            <i class="fas fa-edit icon-edit" data-val="' . $emp->sn_id . '" style="margin-right:0px;color:green;cursor:pointer;font-size:18pt"></i>
            </div></td>
          </tr>';
        }
        $output .= '</tbody></table>';
        echo $output;
    } else {
        echo '<h1 class="text-center text-secondary my-5">No SenderIDs to display.</h1>';
    }
}

function getnote(Request $req){

    $notes = DB::table('notes')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $files = DB::table('files')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $selectedOptions = [$req->country,$req->operator,$req->vendor];
    $req->session()->flash('noteselectedOptions',$selectedOptions);
    $req->session()->flash('noteoperators',Session::get('notetempoperators'));
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    // return view('addNote',['countries'=>$countries,'vendors'=>$vendors,'notes'=>$notes,'files'=>$files]);
    $output = '';
    $output_file = '';
    
    if ($notes->count() > 0) {
        $output .= '  <h4>Notes</h4>';
        foreach ($notes as $note) {
        $output .= '  
         <div id="note_row_'.$note->nt_id.'" style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-bottom:0.5rem">
           <div class="row">
              <div class="col-md-10"><span ><pre id="notecontent_'.$note->nt_id.'">'. $note->note .' </pre></span></div>
               <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-edit icon-edit"  data-val="' .$note->nt_id .'" style="margin-right:5px;color:green;cursor:pointer;font-size:18pt"></i>
                    <i class="fas fa-trash-alt note icon-delete icon-delete-note"  data-val="'. $note->nt_id .'" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
                  </div>
              </div>  
            </div>
        </div>';
        }
        
        echo $output;
    } else {
        $output .= ' 
         <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem;color:">
              <div class="row">
                 <div class="col-md-12" style="text-align:center"><span >No Notes to Display</span></div>
              </div>
         </div>
    ';  
    echo $output;
    }
    if ($files->count() > 0) {
        $output_file .= ' <h4>Files</h4>';
        foreach ($files as $file) {
        $output_file .= '
        <div id="file_row_'.$file->fl_id.'" style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-bottom:0.5rem">
             <div class="row">
                  <div class="col-md-10"><a href="'.$file->file_url.'">'.$file->file_name.'</a></span></div>
                      <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-trash-alt icon-delete icon-delete-file"  data-val="'.$file->fl_id.'" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
                       </div>
                  </div>  
                </div>
            </div>';
        }
        
        echo $output_file;
    } else {
        $output_file .= '         
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem">
           <div class="row">
             <div class="col-md-12" style="text-align:center"><span>No Files to Display</span></div>
           </div>
       </div>
    ';  
    echo $output_file;
    }
    // return response()->json([
    //     'success'=> true,
    //     'status' => 200,
    //     'message' => 'Successfully!',
    //     'notes' => $notes,
    //     'files' => $files,
    // ]);
}

}
