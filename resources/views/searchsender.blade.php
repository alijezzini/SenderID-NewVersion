@extends('layouts.admin')
@section('title', 'Senders')
@section('content')

<style>
    .toast {
    top: 70px;
}
    #overlay,
    #overlayDelete,
    #overlayEdit,
    #loader,
    #adding {
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


    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    td {
        font-size: 12px;
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
</style>

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

<div style="padding-bottom:2rem;margin-left:2rem;margin-right:2rem;margin-top:2rem;">
    <h3 style="margin-bottom:2rem">Senders</h3>
    <div id="overlay" style="display:none;">
        <div class="spinner"></div>
        <br />
        Operators Loading ...
    </div>
    <div id="overlayDelete" style="display:none;">
        <div class="spinner"></div>
        <br />
        Deleting...
    </div>
    <div id="overlayEdit" style="display:none;">
        <div class="spinner"></div>
        <br />
        Editing...
    </div>
    <div id="adding" style="display:none;">
        <div class="spinner"></div>
        <br />
        Adding...
    </div>


    <div id="ModalAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel"
        aria-hidden="true"  >
        <form id="AddForm" method="post" action="submit" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background-color: #E9ECEF">
                    <div class="modal-header">
                        <h3 id="myModalLabel">Add Sender</h3>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button> --}}
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hiddenvendor" name="vendor">
                        <input type="hidden" id="hiddenoperator" name="operator">
                        <input type="hidden" id="hiddencountry" name="country">
                        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
                            <label><input type="radio" class="message_pri" name="radiocheck" value="add" checked><b>Add
                                    SenderID</b></label>
                            <div class="row">
                                <div class="col-md-6 py-1">
                                    <input type="text" class="form-control" id="addsenderid"
                                        placeholder="Enter SenderID" name="senderid" required>
                                </div>
                                <div class="col-md-6 py-1">
                                    <input type="text" class="form-control" id="addcontent" placeholder="Enter Content"
                                        name="content">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 py-1">
                                    <input type="text" class="form-control" id="addwebsite" placeholder="Enter Website"
                                        name="website">
                                </div>
                                <div class="col-md-6 py-1">
                                    <input type="text" class="form-control" id="addnote" placeholder="Enter Note"
                                        name="note">
                                </div>
                            </div>
                        </div>
                        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
                            <label><input type="radio" class="message_pri" name="radiocheck" value="import"><b>  Import
                                    Multiple SenderIDs</b></label>
                            <div class="row ">
                                <div class="col-md-12 py-1">
                                    <input type="file" id="importsender" name="senderidExcel" accept=".xlsx" disabled
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" style="background-color: #747474;color:#ffebb5;cursor:pointer;font-size:11.5pt;font-weight:bold;" data-bs-dismiss="modal">Close</button>
                        <br/>
                        <button class="btn" value="Add" id="add_btn"  style="background-color: #138496;color:#ffebb5;font-weight:bold" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form id="myForm" method="post" action="editSender">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background-color: #E7E7E7">
                    <div class="modal-header">
                        <h3 id="myModalLabel">Edit Sender</h3>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button> --}}
                    </div>
                    <div class="modal-body"  style="background-color: #E7E7E7">
                        <input type="hidden" name="sn_id" id="sn_id">
                        <input type="hidden" name="tr_row" id="tr_row">
                        <div class="form-group"><label for="senderid"><b>SenderID</b></label><input class="form-control"
                                type="text" id="senderid" name="senderid"></div>
                        <div class="form-group"><label for="content"><b>Content</b></label><input class="form-control"
                                type="text" id="content" name="content"></div>
                        <div class="form-group"><label for="website"><b>Website</b></label><input class="form-control"
                                type="text" id="website" name="website"></div>
                        <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control"
                                type="text" id="note" name="note" rows="3"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" style="background-color: #747474;color:#ffebb5;cursor:pointer;font-size:11.5pt;font-weight:bold;" data-bs-dismiss="modal">Close</button>
                        <br/>
                        <button id="myFormSubmit"   style="background-color: #138496;color:#ffebb5;font-weight:bold" class="btn" type="submit">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <form id="search">
        @csrf
        <div style="padding:1rem; background-color:rgba(233, 231, 231, 0.26);border-radius:5px;">
            <label><b>Select Country, Operator and Vendor</b></label>
            <div class="row">

                <div class="col-md-4 py-1">
                    <select data-live-search="true" name="country" class="form-control" id="countryselect" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{$country['country']}}" @if(Session::has('selectedOptions'))
                            {{(Session::get('selectedOptions')[0]==$country['country']) ? 'selected' : '' }} @endif>
                            {{$country['country']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control" name="operator" id="operatorselect" required>
                        <option value="" disabled selected>Select Operator</option>
                        @if(Session::has('operators'))
                        @foreach(Session::get('operators') as $operator)
                        <option value="{{$operator->op_id}}" {{Session::get('selectedOptions')[1]==$operator->op_id ?
                            'selected' : ''}}>{{$operator->operator}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control selectpicker" name="vendor" id="vendorselect"
                        required>
                        <option value="" disabled selected>Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option style="font-size:7pt" value="{{$vendor['vn_id']}}" @if(Session()->
                            has('selectedOptions'))
                            {{Session::get('selectedOptions')[2] == $vendor['vn_id'] ? 'selected' : ''}}
                            @endif
                            >{{$vendor['vendor']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
     
        <div class="row" style="margin-bottom:2rem">
            <div class="col-md-10 py-2">
                <div id="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                            data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-2 py-2" style="margin-top:1rem">
                <button id="submitbutton" style="float:right" class="btn btn-info btn-lg">Search</button>
            </div>
        </div>
    </form>

    <div id="lol" style="display:none">
        <hr>
        <div class="row">
            <div class="col-md-12" style="color:green"><span id="addspan" style="cursor:pointer"><b>
                <i class="fas fa-plus"></i> Add</b></span></div>
            </div>
        <div class="card-body" id="showall">
        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
        $("#search").submit(function(event){
         event.preventDefault();  
          fetchSenders();
                });

        function fetchSenders() {
            // $('#loader').fadeIn();
            $("#submitbutton").text('Searching...');
            $.ajax({
                type: 'GET',
                url: '/fetchSenders',
                data: $('#search').serialize(),
                success: function(response) {
                    // $('#loader').fadeOut();
                    document.getElementById('lol').style.display = 'block';
                 
                $("#showall").html(response);
                $("#submitbutton").text('Search');
                $('#example').DataTable({
                  "scrollX": true,
                   dom: 'lfr<"toolbar">tip',
            fnInitComplete: function(){
           $('div.toolbar').html('<span id="not" style="color:#ef3535;cursor:pointer;font-size:13pt;font-weight:bold"><i class="fas fa-trash-alt icon-delete" ></i> Delete</span>');
                        }
                        });
                        }
                        });
                    }
            $(document).on('click', '#checkAll', function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
            });


        $(document).on('click', '#not', function(e) {
        e.preventDefault();
        
        var idsArr = [];  
            $(".checkbox:checked").each(function() {  
                idsArr.push($(this).attr('data-id'));
                console.log(idsArr);
            });  
            if(idsArr.length <=0)  
            {   
                Swal.fire({
                title: 'Please select atleast one record to delete.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok'
          })
            }  else {  
                
                if(confirm("Are you sure, you want to delete the selected Senders?")){  
                    $('#overlayDelete').fadeIn();
                    var strIds = idsArr.join(","); 
                    $.ajax({
                        url: 'deleteSender',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "sn_ids": strIds,
                },
                dataType: 'JSON',
                        success: function (response) {
            
                if (response.status == 'success') {
                        Swal.fire({'text': 'Your file has been deleted.', 
                        title: response.msg, 'type': 'success',
                        icon: 'success',
                         confirmButtonColor: '#3085d6',})
                        fetchSenders();
                        $('#overlayDelete').fadeOut();
                    } else {
                        Swal.fire({title: 'Error', 'text': response.msg, 'type': 'error'});
                    }          
                        },
                    });
                }  
            }  
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
                    "page":"searchsender"
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
                    })
                }
            });
        });




        $(document).on('click', '.icon-edit', function(e) {
        e.preventDefault();
 
            var tr = $(this).parents('tr');
            var row = $(this).parents('tr').index();
            var sn_id=$(this).data('val');
            var sender = $(this).closest('tr').find('td:eq(1)').text();
            var content = $(this).closest('tr').find('td:eq(2)').text();
            var website = $(this).closest('tr').find('td:eq(3)').text();
            var note = $(this).closest('tr').find('td:eq(4)').text();
            $("#sn_id").val(sn_id);
            $("#tr_row").val(row);
            $("#senderid").val(sender);
            $("#content").val(content);
            $("#website").val(website);
            $("#note").val(note);
            $('#myModal').modal('show');
    
             } );

            $("#myModal").submit(function(e){
                $("#myFormSubmit").text('Updating...');
                e.preventDefault();
                $('#myModal').modal('hide');
                $('#overlayEdit').fadeIn();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                            /* the route pointing to the post function */
                            url: 'editSender',
                            type: 'POST',
                            /* send the csrf-token and the input to the controller */
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "sn_id": $("#sn_id").val(),
                                "senderid": $("#senderid").val(),
                                "content": $("#content").val(),
                                "website": $("#website").val(),
                                "note": $("#note").val(),
                                "tr_row": $("#tr_row").val()
                            },
                            dataType: 'JSON',
                            /* remind that 'data' is the response of the AjaxController */
                            success: function(dat) {
                                console.log(dat)
                                fetchSenders();
                                $("#myFormSubmit").text('Update');
                                $('#overlayEdit').fadeOut();
                                toastr.info("Updaded successfully!");
                            }
                        });
            });
            
                $('#addspan').on( 'click', function (e) {
                        $("#hiddencountry").val($( "#countryselect" ).val()) ;
                        $("#hiddenoperator").val($( "#operatorselect" ).val()) ;
                        $("#hiddenvendor").val($( "#vendorselect" ).val()) ;
                        $('#ModalAdd').modal('show');
                        });

                $('input[type=radio][name=radiocheck]').change(function() {
                            if (this.value == 'add') {
                                $('#addsenderid').prop('disabled', false);
                                $('#addcontent').prop('disabled', false);
                                $('#addwebsite').prop('disabled', false);
                                $('#addnote').prop('disabled', false);
                                $('#importsender').prop('disabled', true);
                                $('#importsender').prop('required', true);
                            } else if (this.value == 'import') {
                                $('#addsenderid').prop('disabled', true);
                                $('#addcontent').prop('disabled', true);
                                $('#addwebsite').prop('disabled', true);
                                $('#addnote').prop('disabled', true);
                                $('#importsender').prop('disabled', false);
                            }      
                });


        $("#AddForm").submit(function(event){
                  event.preventDefault();
                  const fd = new FormData(this);
                  $("#add_btn").text('Saving...');
                  $('#ModalAdd').modal('hide');
                      $('#adding').fadeIn();
                                  $.ajax({
                                url: "submit",
                                type:"POST",
                                data: fd,
                                cache: false,
                                contentType: false,
                                processData: false,
                                dataType: 'json',                           
                                success: function (data) {
                                    console.log(data.sender);
                                    if(data.success == true){
                                    fetchSenders();
                                   
                                    $("#AddForm")[0].reset();
                                    
                                    $("#add_btn").text('Save');
                                    $("#AddForm")[0].reset();
                                      toastr.success(data.message);
                                    $('#adding').fadeOut();
                                    } else {
                                        toastr.error(data.message);
                                        $("#AddForm")[0].reset();
                                        $('#adding').fadeOut();
                                        $("#add_btn").text('Save'); 
                                    }
                                },                        
                                error: function(err){
                                    toastr.error("Error with Server!");
                                       $('#adding').fadeOut();
                                       $("#add_btn").text('Save');
                                },
                                  
                            })
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
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js'></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection