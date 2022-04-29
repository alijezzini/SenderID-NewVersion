<?php

namespace App\Http\Controllers;
use App\Models\File;
use App\Models\Note;
use App\Models\Operator;
use App\Models\Vendor;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class noteController extends Controller
{
    function index(Request $req){


        
        $notes = DB::table('notes')
        ->where('operator', '=', $req->op_id)
        ->where('vendor', '=', $req->vendor)
        ->get();

        $files = DB::table('files')
        ->where('operator', '=', $req->op_id)
        ->where('vendor', '=', $req->vendor)
        ->get();

        $country = $req->country;
        $selectedOptions = [$req->country,$req->op_id,$req->vendor];
        $operators = DB::table('operators')->where('country', $country)->get();
        $req->session()->flash('noteselectedOptions',$selectedOptions);
        $req->session()->flash('noteoperators',$operators);
        $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
        $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;

        if ($notes->count() > 0) {
            if ($files->count() > 0) {
                return view('addNote',[
                    'countries'=>$countries,'vendors'=>$vendors,
                     'operators'=>$operators,  'notes'=>$notes,'files'=>$files
                   ]);
            }else{
                return view('addNote',[
                    'countries'=>$countries,'vendors'=>$vendors, 
                    'operators'=>$operators,  'notes'=>$notes,'files'=>$files
                   ]);
            }

    } else {       
        if ($files->count() > 0) {
            return view('addNote',[
                'countries'=>$countries,'vendors'=>$vendors, 
                'operators'=>$operators,  'notes'=>$notes,'files'=>$files
               ]);
        }else{
            return view('addNote',[
                'countries'=>$countries,'vendors'=>$vendors, 
                'operators'=>$operators,  
               ]);
        }}

    
    }

    
    
    
    function getOperator(Request $req){

        $operators = DB::table('operators')->where('country', $req->country)->get();
        if($req->page =="searchsender"){
        Session::put('tempoperators', $operators);
        }
        if($req->page =="searchnote"){
            session()->flash('notetempoperators',$operators);;
        }
        return response()->json($operators);
    }

function getnote(Request $req){

    $vendor = $req->vendor;
    $operator = $req->operator;
    $country = $req->country;
    
    $notes = DB::table('notes')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $files = DB::table('files')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $selectedOptions = [$req->country,$req->operator,$req->vendor];
    $out = new \Symfony\Component\Console\Output\ConsoleOutput();
    $out->writeln($selectedOptions);
    // $req->session()->flash('noteselectedOptions',$selectedOptions);
    // $req->session()->flash('noteoperators',Session::get('notetempoperators'));
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    // return view('addNote',['countries'=>$countries,'vendors'=>$vendors,'notes'=>$notes,'files'=>$files]);
    $output = '';
    $output_file = '';
    
    if ($notes->count() > 0) {
        $output .= '  <h4>Notes</h4>';
        foreach ($notes as $note) {
        $output .= '  
         <div id="note_row_'.$note->nt_id.'" style="max-height:100px; overflow-y: scroll; padding:1rem; background-color:#E7E7E7;border-radius:10px;margin-bottom:0.5rem;border: 1px solid grey;">
           <div class="row">
              <div class="col-md-10"><span ><div id="notecontent_'.$note->nt_id.'">'. $note->note .' </div></span></div>
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
        <h4>Notes</h4>
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
        <div id="file_row_'.$file->fl_id.'" style="padding:1rem; background-color:#E7E7E7;border-radius:10px;margin-bottom:0.5rem;border: 1px solid grey;">
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
        <h4>Files</h4>   
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem">
           <div class="row">
             <div class="col-md-12" style="text-align:center"><span>No Files to Display</span></div>
           </div>
       </div>
    ';  
    echo $output_file;
    }

  }

    function submit(Request $req){
        $successmsg="";
        $failmsg="";
        $files = $req->file('attachment');

        if($req->note != ""){
          
            if($req->hasFile('attachment'))
            {
                foreach ($files as $file) {
                    $uniqueFileName = uniqid() . $file->getClientOriginalName();
                    $opname = DB::table('operators')->where('op_id',$req->operator )->first()->operator;
                    $vname = DB::table('vendors')->where('vn_id', $req->vendor)->first()->vendor;
                    $file->move('files/'.$req->country.'/'.$opname.'/'.$vname.'/',$uniqueFileName);
                    $completePath = 'files/'.$req->country.'/'.$opname.'/'.$vname.'/'.$uniqueFileName;
                    $fl = new file;
                    $fl->file_name = $file->getClientOriginalName();
                    $fl->file_url = $completePath;
                    $fl->vendor = $req->vendor;
                    $fl->operator = $req->operator;
                    $fl->save();
                    }
                    $note = new note;
                    $note->vendor = $req->vendor;
                    $note->operator = $req->operator;
                    $note->note = $req->note;
                    $note->save();
                        return response()->json([
                            'success'=> true,
                            'status' => 200,
                            'message' => 'Note & File Added!',
                            'fl' => $fl,
                            'note' => $note,
                ]);

            }else{
                $note = new note;
                $note->vendor = $req->vendor;
                $note->operator = $req->operator;
                $note->note = $req->note;
                $note->save();
                        return response()->json([
                            'success'=> true,
                            'status' => 200,
                            'message' => 'Note Added!',
                            'note' => $note,     ]);

        }
      
        }
        else{
            if($req->hasFile('attachment')){
                    foreach ($files as $file) {
                    $uniqueFileName = uniqid() . $file->getClientOriginalName();
                    $opname = DB::table('operators')->where('op_id',$req->operator )->first()->operator;
                    $vname = DB::table('vendors')->where('vn_id', $req->vendor)->first()->vendor;
                    $file->move('files/'.$req->country.'/'.$opname.'/'.$vname.'/',$uniqueFileName);
                    $completePath = 'files/'.$req->country.'/'.$opname.'/'.$vname.'/'.$uniqueFileName;
                    $fl = new file;
                    $fl->file_name = $file->getClientOriginalName();
                    $fl->file_url = $completePath;
                    $fl->vendor = $req->vendor;
                    $fl->operator = $req->operator;
                    $fl->save();
                    }
                            return response()->json([
                                'success'=> true,
                                'status' => 200,
                                'message' => 'File Added!',
                                'fl' => $fl,
                
                    ]);
            }
                  else{
                        return response()->json([
                            'success'=> false,
                            'status' => 400,
                            'message' => 'please enter a valid file and notes!',
                        ]);
            }
        }
      
    
    }


    function deleteNote(Request $req){
        DB::table('notes')->where('nt_id', $req->nt_id)->delete();
        return response()->json("success");
    }
    function deleteFile(Request $req){
        DB::table('files')->where('fl_id', $req->fl_id)->delete();
        return response()->json("success");
    }
    function editNote(Request $req){
        DB::table('notes')
            ->where('nt_id', $req->nt_id)
            ->update([
                'note' => $req->note
                    ]);
        $resp = [$req->nt_id,$req->note];
        return response()->json($resp);
    }
    function deleteNotesFiles(Request $req){
        $ids = explode(",",$req->nf_ids);
        foreach($ids as $vn_op){
            $arr = explode("_",$vn_op);
             DB::table('notes')->where('vendor', $arr[0])->where('operator', $arr[1])->delete();
             DB::table('files')->where('vendor', $arr[0])->where('operator', $arr[1])->delete();
        }
        return response()->json("success");
    }
}
