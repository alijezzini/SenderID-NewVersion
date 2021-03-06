@extends('layouts.admin')
@section('title', 'Vendor Notes')
@section('content')
<!-- Adding a script for dropzone -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel='stylesheet'
      href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css' />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
  
</head>

<style>
    #overlay,
    #overlayDelete,
    #overlayEdit {
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

    .spinner {
        margin: 0 auto;
        height: 64px;
        width: 64px;
        animation: rotate 0.8s infinite linear;
        border: 5px solid #0055FF;
        border-right-color: transparent;
        border-radius: 50%;
    }

    .toast {
    top: 70px;
    width: 64px;
}
    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .bootstrap-select>.dropdown-toggle.bs-placeholder,
    .bootstrap-select>.dropdown-toggle.bs-placeholder:active,
    .bootstrap-select>.dropdown-toggle.bs-placeholder:focus,
    .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
        color: #495057 !important;
    }

    .btn-light {
        color: black !important;
        background-color: white !important;
        border-color: lightgrey !important;
    }

div::-webkit-scrollbar {
width: 0px; 
}


</style>


<div id="ModalEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
    <form id="AdddForm" method="post" action="add">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background-color: #E7E7E7">
                <div class="modal-header">
                    <h3 id="myModalLabel">Edit Vendor</h3>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button> --}}
                </div>
                <div class="modal-body">
                    <input type="hidden" id="noteID">
                    <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control"
                            type="text" id="editnote" name="editnote" rows="15"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button class="btn"  style="background-color: #747474;color:#ffebb5;cursor:pointer;font-size:11.5pt;font-weight:bold;" data-dismiss="modal" aria-hidden="true">Close</button>
                    <br/> <button id="myFormSubmit"  style="background-color: #138496;color:#ffebb5;font-weight:bold" class="btn" type="submit">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="ModalAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
    <form id="AddForm" method="post" action="submit" enctype="multipart/form-data">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background-color: #E9ECEF">
                <div class="modal-header">
                    <h3 id="myModalLabel">Add Vendor</h3>


                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                </div>
                <div class="modal-body">
                    <input type="hidden" id="hiddenvendor" name="vendor">
                    <input type="hidden" id="hiddenoperator" name="operator">
                    <input type="hidden" id="hiddencountry" name="country">
                    <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control"
                            type="text" id="note" name="note" rows="15"></textarea></div>
                    <div class="form-group"><label for="content"><b>Attach Files</b></label><br><input type="file"
                            name="attachment[]" multiple></div>

                </div>
                <div class="modal-footer">
                    <button class="btn" style="background-color: #747474;color:#ffebb5;cursor:pointer;font-size:11.5pt;font-weight:bold;" data-dismiss="modal" aria-hidden="true">Close</button>
                      <br/>
                    <button id="myFormSubmitadd" style="background-color: #138496;color:#ffebb5;font-weight:bold" class="btn " type="submit">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div style=" margin-left:2rem;margin-right:2rem">
    <h3 style="margin-bottom:2rem;padding-top:2%;">Vendor Notes</h3>
    {{-- <h5>vendor: {{$vendor}}</h5>
    <h5>operator: {{$operator}}</h5>
    <h5>country:{{$country}}</h5> --}}
    <div id="overlay" style="display:none;">
        <div class="spinner"></div>
        <br />
        Loading Operators...
    </div>
    <div id="overlayDelete" style="display:none;">
        <div class="spinner"></div>
        <br />
        Deleting...
    </div>
    <div id="overlayEdit" style="display:none;">
        <div class="spinner"></div>
        <br />
        Saving Changes...
    </div>
    <form id="searchnote">
        @csrf
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;">
            <label><b>Select Country, Operator and Vendor</b></label>
           
            <div class="row">

                <div class="col-md-4 py-1">
                    <select data-live-search="true" name ="country" class="form-control"  id="countryselect" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{$country['country']}}" 
                        @if(Session::has('noteselectedOptions'))
                        {{(Session::get('noteselectedOptions')[0] == $country['country']) ? 'selected' : ''}} 
                        @endif 
                        >{{$country['country']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control" name="operator" id="operatorselect" required>
                        <option value="" disabled selected>Select Operator</option>
                        @if(Session::has('noteoperators'))
                        
                        @foreach(Session::get('noteoperators') as $operator)

                        <option value="{{$operator->op_id}}"
                             {{Session::get('noteselectedOptions')[1] == $operator->op_id ? 'selected' : ''}}>
                            {{$operator->operator}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4 py-1" >
                    <select data-live-search="true" class="form-control selectpicker" name="vendor" id="vendorselect" required>
                        <option value="" disabled selected>Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option style="font-size:7pt" value="{{$vendor['vn_id']}}"
                        @if(Session()->has('noteselectedOptions')) 
                        {{Session::get('noteselectedOptions')[2] == $vendor['vn_id'] ? 'selected' : ''}}
                        @endif
                        >{{$vendor['vendor']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
  
        <div class="row" style="margin-bottom:2rem">
            <div class="col-md-10 py-2">
                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                            data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-2 py-2" style="margin-top:1rem">
                <button id="submitbutton" style="float:right" type="submit" class="btn btn-info btn-lg">Search</button>
            </div>
        </div>
     </form>
            <div id="lol" style="display:none">
                <div class="row">
   
                    <div class="col-md-12" style="color:green"><span id="addspan" style="cursor:pointer"><b>
                        <i class="fas fa-plus"></i>??Add</b></span></div>
                    </div>
                 <hr>
                <div class="card-body" id="shownotes">
                </div>
            </div>

            <div id="yala" style="display:bloc">
    @if(!empty($notes))
    <div class="row">
        <div class="col-md-12" style="color:green"><span id="addspan" style="cursor:pointer"><b>
            <i class="fas fa-plus"></i>??Add</b></span></div>
        </div>
    <hr>
    <h4>Notes</h4>
    
		@if ($notes->count() == 0)
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem;color:">
    <div class="row">
        <div class="col-md-12" style="text-align:center"><span >No Notes to Display</span></div>
    </div>
    </div>
    
        @endif
        @foreach ($notes as $note)
        <div id="note_row_{{$note->nt_id}}" style="overflow-y: scroll; padding:1rem;border: 1px solid #8d8d8d; background-color:#E7E7E7;border-radius:10px;margin-bottom:0.5rem">
        <div class="row">
        <div class="col-md-10"><span ><pre id="notecontent_{{$note->nt_id}}">{{ $note->note }}</pre></span></div>
        <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-edit icon-edit"  data-val="{{$note->nt_id}}" style="margin-right:5px;color:green;cursor:pointer;font-size:18pt"></i>
                    <i class="fas fa-trash-alt note icon-delete icon-delete-note"  data-val="{{$note->nt_id}}" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
    </div></div>  
    </div>
    </div>
        @endforeach    
    @endif 

    @if(!empty($files))
    <hr>
    <h4>Files</h4>
    
		@if ($files->count() == 0)
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem">
    <div class="row">
        <div class="col-md-12" style="text-align:center"><span>No Files to Display</span></div>
    </div>
    </div>
        @endif
        @foreach ($files as $file) 
        <div id="file_row_{{$file->fl_id}}" style="padding:1rem;border: 1px solid #8d8d8d; background-color:#E7E7E7;border-radius:10px;margin-bottom:0.5rem">
        <div class="row">
        <div class="col-md-10"><a href="{{$file->file_url}}">{{ $file->file_name }}</a></span></div>
        <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-trash-alt icon-delete icon-delete-file"  data-val="{{$file->fl_id}}" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
    </div></div>  
    </div>
    </div>
        @endforeach
    @endif 
</div>
    <script>
        $(document).ready(function() {

        $('button').click(function(){
        	});
    
        $("#searchnote").submit(function(event){
        event.preventDefault();  
            fetchAll();
            });

        function fetchAll() {
            $("#submitbutton").text('Searching...');
            $.ajax({
                type: 'POST',
                url: '/fetchnotes',
                data: $('#searchnote').serialize(),
                success: function (response) {
                    document.getElementById('yala').style.display = 'none';
                    document.getElementById('lol').style.display = 'block';
     
                    $("#shownotes").html(response);
                    $("#submitbutton").text('Search');
                
                }
            });
         }

         $("#AddForm").submit(function(event){
                  event.preventDefault();
                  const fd = new FormData(this);
                  $("#myFormSubmitadd").text('Adding...');
                //   $('#ModalAdd').modal('hide');
            
                                  $.ajax({
                                url: "submitNote",
                                type:"POST",
                                data: fd,
                                cache: false,
                                contentType: false,
                                processData: false,
                                dataType: 'json',                           
                                success: function (data) {
                                    // console.log(data);
                                    if(data.success == true){
                                    fetchAll();
                                    $("#AddForm")[0].reset();
                              
                                      toastr.success(data.message);
                                      $("#myFormSubmitadd").text('Save');
                                    } else {
                                        toastr.error(data.message);
                                        $("#myFormSubmitadd").text('Save');
                                    }
                                },                        
                                error: function(err){
                                    toastr.error("Error with Server!");
                                    $("#myFormSubmitadd").text('Save');
                                     
                                },
                                  
                            })
                            toastr.options = {
                                "closeButton": true,
                                "newestOnTop": true,
                                "positionClass": "toast-top-right"
                                };             
                        });

  

    $("select").on('change', function() {
        $('#content').remove();
        });
        $("#countryselect").on('change', function() {
            $('#overlay').fadeIn();
            var country = $('#countryselect').find(":selected").text();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                /* the route pointing to the post function */
                url: 'getOperators',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "country": country,
                    "page":"searchnote"
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $('#overlay').fadeOut();
                    $('#operatorselect').empty();
                    $('#operatorselect').prepend('<option value="" disabled="disabled" selected>Select Operator</option>');
                    $.each(data, function(key, val) {
                        $('#operatorselect')
                            .append($("<option></option>")
                                .attr("value", val.op_id)
                                .text(val.operator));
                                //   console.log(val.operator);
                    })
                }
            });
        });

    
            $(document).on('click', '.icon-delete-note', function(e) {
        e.preventDefault();
    var r = confirm("Are You Sure you want to delete this Note?");
   
        if (r == true) {
            $('#overlayDelete').fadeIn();
            var nt_id=$(this).data('val');
            var row_id = "note_row_"+nt_id;
            $.ajax({
                /* the route pointing to the post function */
                url: 'deleteNote',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "nt_id": nt_id,
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $("#"+row_id).remove();
                    $('#overlayDelete').fadeOut();
                }
            });
            
        }
    
} );

    $(document).on('click', '.icon-delete-file', function(e) {
        e.preventDefault();
    var r = confirm("Are You Sure you want to delete this File?");
   
        if (r == true) {
            $('#overlayDelete').fadeIn();
            var fl_id=$(this).data('val');
            var row_id = "file_row_"+fl_id;
            $.ajax({
                /* the route pointing to the post function */
                url: 'deleteFile',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "fl_id": fl_id,
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $("#"+row_id).remove();
                    $('#overlayDelete').fadeOut();
                }
            });
            
        }
    
} );
$(document).on('click', '#addspan', function(e) {
            $("#hiddencountry").val($( "#countryselect" ).val()) ;
           $("#hiddenoperator").val($( "#operatorselect" ).val()) ;
           $("#hiddenvendor").val($( "#vendorselect" ).val()) ;
        $('#ModalAdd').modal('show');
    });

 
        $(document).on('click', '.icon-edit', function(e) {
        e.preventDefault();
        
        var nt_id=$(this).data('val');
        var note = document.getElementById("notecontent_"+nt_id).innerText;
        $('#editnote').val(note);
        $('#noteID').val(nt_id);
        $('#ModalEdit').modal('show');
    
} );

$("#ModalEdit").submit(function(e){
    e.preventDefault();
    $("#myFormSubmit").text('Updating...');
    $('#ModalEdit').modal('hide');
    $('#overlayEdit').fadeIn();
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
                /* the route pointing to the post function */
                url: 'editNote',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "nt_id":$("#noteID").val(),
                    "note": $("#editnote").val(),
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    var nt_id = data[0];
                    var value = data[1];
                    $('#notecontent_'+nt_id).text(value);
                    toastr.info("Updaded successfully!");
                    $("#myFormSubmit").text('Update');
                    $('#overlayEdit').fadeOut();

                }
            });
            toastr.options = {
                                "closeButton": true,
                                "newestOnTop": true,
                                "positionClass": "toast-top-right"
                                };    
  });
    });
    @if(Session::has ('message'))
             var type = "{{ Session::get('alert-type','info') }}"
              switch(type){
              case 'info':
              toastr.info(" {{ Session::get('message') }} ");
              break;
              case 'success':
              toastr.success(" {{ Session::get('message') }} ");
              break;
              case 'warning':
              toastr.warning(" {{ Session::get('message') }} ");
              break;
              case 'error':
              toastr.error(" {{ Session::get('message') }} ");
              break;
           }
           @endif
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection