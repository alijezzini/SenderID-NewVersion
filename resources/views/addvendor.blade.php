@extends('layouts.admin')
@section('title', 'Add Sender')
@section('content')
<style>

#overlay,#overlayDelete,#overlayEdit
 {
  background: #ffffff;
  color: #666666;
  position: fixed;
  height: 100%;
  width: 100%;
  z-index: 5000;
  top: 0;
  left: 0;
  float: left;
  text-align: center;
  padding-top: 25%;
  opacity: .80;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: none;
  color: black!important;
  border-radius: 4px;
  border: 1px solid #828282;
}
.spinner {
    margin: 0 auto;
    height: 64px;
    width: 64px;
    animation: rotate 0.8s infinite linear;
    border: 5px solid #0055FF;
    border-right-color: transparent;
    border-radius: 50%;

}
.back{
    padding:10rem;
}
.pagination > li > a, .pagination > li > span{
   color:rgb(36, 170, 36)
}
@keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
<div style="padding-bottom:2rem;margin-left:2rem;margin-right:2rem;margin-top:2rem;">
<div id="overlayDelete" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Deleting...
</div>
<div id="overlayEdit" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Editing...
</div>
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form id="myForm" method="post" action="editVendor">
  @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Edit Vendor</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            <input type="hidden" name="vn_id" id="vn_id">
            <input type="hidden" name="tr_row" id="tr_row">
            <div class="form-group"><label for="senderid"><b>Vendor</b></label><input class="form-control" type="text" id="vendor" name="vendor"></div>
                    
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id="myFormSubmit" class="btn btn-success" type="submit">Save Changes</button>
            </div>
        </div>
    </div>
 </form>
</div>
    <form action="submitVendor" method="POST" enctype="multipart/form-data"> 
        @csrf

        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;">
            <label><input type="radio" name="radiocheck" value= "add" checked><b>  Insert New Vendor Name</b></label>

            <div class="row">
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="vendorname" placeholder="Enter Vendor Name" name="vendorname" required>
                </div>
            </div>
        </div>
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
            <label><input type="radio" name="radiocheck" value= "import"><b>  Import Multiple Vendor Names</b></label>
            <div class="row ">
                <div class="col-md-12 py-1">
                    <input type="file" id="importvendor" name="vendorExcel"  accept=".xlsx" disabled required>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-md-10 py-3">
        <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        @endforeach
    </div> 
        </div>
            <div class="col-md-2 py-3">
                <button id="submitbutton" style="float:right" type="submit" class="btn btn-success btn-lg">Submit</button>
            </div>
        </div>
    </form>
    <div>

</div>
<h3>Vendors List</h3>
<table id="example" class="table table-striped table-bordered" style="width:100%;">
        <thead>
            <tr>
            <td><input type="checkbox" id="checkAll"></td>
                <th>Vendor</th>
                <td></td>
            </tr>
        </thead>
        
        <tbody id="tablebody"> 

		@if ($vendors->count() == 0)
        <tr>
            <td colspan="3" style="text-align:center">No Vendors</td>
            <td style="display: none"></td>
             <td style="display: none"></td>
        </tr>
        @endif
        @foreach ($vendors as $vendor)
        <tr id='tr_{{$vendor->vn_id}}'>
        <td><input type='checkbox' class="checkbox" data-id="{{$vendor->vn_id}}" ></td>
            <td>{{ $vendor->vendor }}</td>
            <td style="text-align:right"><div class="btn-group">
                    <i class="fas fa-edit icon-edit" data-val="{{$vendor->vn_id}}" style="margin-right:5px;color:green;cursor:pointer;font-size:18pt"></i>
</div></td>
        </tr>
        @endforeach

        </tbody>
    </table>
</div>


<script>

$(document).ready(function() {
    // Setup - add a text input to each footer cell
    var table = $('#example').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
            "scrollX": true,
            "pagination": true,
            dom: 'lfr<"toolbar">tip',
            fnInitComplete: function(){
           $('div.toolbar').html('<span id="delete" style="color:#ef3535;cursor:pointer;font-size:13pt;font-weight:bold"><i class="fas fa-trash-alt icon-delete" ></i> Delete</span>');
         }
        }); 
        $("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
$('#delete').click(function() {
            var idsArr = [];  
            $(".checkbox:checked").each(function() {  
                idsArr.push($(this).attr('data-id'));
                console.log(idsArr);
            });  
            if(idsArr.length <=0)  
            {  
                alert("Please select atleast one record to delete.");  
            }  else {  
                if(confirm("Are you sure, you want to delete the selected Vendors?")){  
                    $('#overlayDelete').fadeIn();
                    var strIds = idsArr.join(","); 
                    $.ajax({
                        url: 'deleteVendor',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "vn_ids": strIds,
                },
                dataType: 'JSON',
                        success: function (data) {
                            if(data=="success"){
                                $(".checkbox:checked").each(function() {  
                                    var tr=$(this).parents("tr").remove();
                                    table.row(tr).remove().draw();
                                });
                                $('#overlayDelete').fadeOut();
                            }
                            else{
                                $('#overlayDelete').fadeOut();
                                var string = "Vendors: (";
                                data.forEach(function(item,index){
                                    string = string + item;
                                    if (data[index + 1]) string = string+", ";
                                });
                                string = string+") has linked tables."
                                alert(string);
                            }
                        },
                    });
                }  
            }  
        });
 
 
 $('#example tbody').on( 'click', '.icon-edit', function () {
     
     
     
     var tr = $(this).parents('tr');
     var row = $(this).parents('tr').index();
     var vn_id=$(this).data('val');
     var vendor = $(this).closest('tr').find('td:eq(1)').text();
     $("#vn_id").val(vn_id);
     $("#tr_row").val(row);
     $("#vendor").val(vendor);
     $('#myModal').modal('show');
     
 } );
 
 $("#myModal").submit(function(e){
     e.preventDefault();
     $('#myModal').modal('hide');
     $('#overlayEdit').fadeIn();
     var form = $(this);
     var url = form.attr('action');
     $.ajax({
                 /* the route pointing to the post function */
                 url: 'editVendor',
                 type: 'POST',
                 /* send the csrf-token and the input to the controller */
                 data: {
                     "_token": "{{ csrf_token() }}",
                     "vn_id": $("#vn_id").val(),
                     "vendor": $("#vendor").val(),
                     "tr_row": $("#tr_row").val()
                 },
                 dataType: 'JSON',
                 /* remind that 'data' is the response of the AjaxController */
                 success: function(dat) {
                     var table = $('#example').DataTable();
                     var temp = $("#example")[0];
                     var Num = parseInt(dat[1])+1;
                     var cell0 = temp.rows[Num].cells[1];
                     table.cell(cell0).data(dat[0]);
                     $('#overlayEdit').fadeOut();
                 }
             });
   });
   $('input[type=radio][name=radiocheck]').change(function() {
            if (this.value == 'add') {
                $('#vendorname').prop('disabled', false);
                $('#importvendor').prop('disabled', true);
            } else if (this.value == 'import') {
                $('#vendorname').prop('disabled', true);
                $('#importvendor').prop('disabled', false);
            }
        });
} );
</script>

@endsection