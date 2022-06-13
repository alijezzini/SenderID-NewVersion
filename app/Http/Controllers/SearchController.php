<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Vendor;
use App\Models\Sender;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{


    public function searchall()
    {

        return view('searchall');
    }

    public function lol(Request $request)
    {



        $countries = DB::table('operators')->select('country')->groupBy('country')->orderBy('country')->get()->toArray();
        $vendors = DB::table('vendors')->select('vendor')->orderBy('vendor')->get()->toArray();
        $senders = DB::table('senders')
            ->join('operators', 'operators.op_id', '=', 'senders.operator')
            ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
            ->get();

        $all = DataTables::of($senders, $countries, $vendors)
            ->addIndexColumn()
            ->make(true);

        //Log::error('Return Message2=> ', $all);
        return $all;
    }

    public function searchsender()
    {

        $countries = Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray();
        $vendors = Vendor::select('vn_id', 'vendor')->orderBy('vendor')->get()->toArray();
        return view('searchsender', ['countries' => $countries, 'vendors' => $vendors]);
    }

    public function searchallnotes()
    {
        $notes = DB::table('notes')
            ->select('vendors.vendor', 'operators.operator', 'country', 'vendors.vn_id', 'operators.op_id')
            ->join('operators', 'operators.op_id', '=', 'notes.operator')
            ->join('vendors', 'vendors.vn_id', '=', 'notes.vendor')
            ->get()->toArray();

        $files = DB::table('files')
            ->select('vendors.vendor', 'operators.operator', 'country', 'vendors.vn_id', 'operators.op_id')
            ->join('operators', 'operators.op_id', '=', 'files.operator')
            ->join('vendors', 'vendors.vn_id', '=', 'files.vendor')
            ->get()->toArray();

        $merge = array_merge($notes, $files);
        $output = array_map("unserialize", array_unique(array_map("serialize", $merge)));
        $final = array();

        foreach ($output as $o) {

            $row = array();
            array_push($row, $o->vn_id);
            array_push($row, $o->op_id);
            array_push($row, $o->vendor);
            array_push($row, $o->operator);
            array_push($row, $o->country);
            $temp_note = "false";
            $temp_file = "false";
            foreach ($notes as $n) {
                if ($o == $n) {
                    $temp_note = "true";
                    break;
                }
            }
            foreach ($files as $f) {
                if ($o == $f) {
                    $temp_file = "true";
                    break;
                }
            }
            array_push($row, $temp_note);
            array_push($row, $temp_file);
            array_push($final, $row);
        }
        return view('searchallnotes', ['data' => $final]);
    }



    public function getsenderTable(Request $req)
    {


        $emps = DB::table('senders')
            ->join('operators', 'operators.op_id', '=', 'senders.operator')
            ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
            ->where('operators.op_id', '=', $req->operator)
            ->where('vendors.vn_id', '=', $req->vendor)
            ->get();
        $selectedOptions = [$req->country, $req->operator, $req->vendor];
        $req->session()->flash('selectedOptions', $selectedOptions);
        $req->session()->flash('operators', Session::get('tempoperators'));
        $countries = Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray();
        $vendors = Vendor::select('vn_id', 'vendor')->orderBy('vendor')->get()->toArray();
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
                $output .= '<tr id="tr_' . $emp->sn_id . '">
         
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
}
