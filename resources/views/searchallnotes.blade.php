@extends('layouts.admin')
@section('title', 'Search Vendor Notes')
@section('content')
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

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<div style="margin-left:2rem;margin-right:2rem;padding-top:2%;">
    <div id="overlayDelete" style="display:none;">
        <div class="spinner"></div>
        <br />
        Deleting...
    </div>
    <h3 style="margin-bottom:2rem">Search Vendor Notes</h3>

    <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>Country</th>
                <th>Operator</th>
                <th>Vendor</th>
                <th>View</th>
                <th>Notes</th>
                <th>Files</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) == 0)
            <tr>
                <td colspan="6" style="text-align:center">no data available in table</td>
                <td style="display: none"></td>
                <td style="display: none"></td>
                <td style="display: none"></td>
                <td style="display: none"></td>
                <td style="display: none"></td>
                <td style="display: none"></td>
            </tr>
            @endif
            @foreach ($data as $d)
            <tr id='tr_{{$d[0]}}_{{$d[1]}}'>
                <td><input type='checkbox' class="checkbox" data-id="{{$d[0]}}_{{$d[1]}}"></td>
                <td>{{ $d[4] }}</td>
                <td>{{ $d[3] }}</td>
                <td>{{ $d[2] }}</td>
                <td style="text-align:center;color:blue">
                    <a href="{{ url('/vendornotes?vendor='. $d[0].'&op_id='.$d[1].'&country='.$d[4]) }}" title="View vendor" >
                        <i id="btnSun"class="bi bi-eye"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="24"
                                fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                         <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                         <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                            </svg>
                        </i>
                    </a>
                </td>
                @if($d[5]=="true")
                <td style="text-align:center;color:green"><i class="fas fa-check"></i></td>
                @else
                <td style="text-align:center;color:red"><i class="fas fa-times"></i></i></td>
                @endif
                @if($d[6]=="true")
                <td style="text-align:center;color:green"><i class="fas fa-check"></i></td>
                @else
                <td style="text-align:center;color:red"><i class="fas fa-times"></i></i></td>
                @endif


            </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>Country</th>
                <th>Operator</th>
                <th>Vendor</th>
                <th>Notes</th>
                <th>Files</th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
    $(document).ready(function() {
    // Setup - add a text input to each footer cell

 
    var table = $('#example').DataTable({
            "scrollX": true,
            "select": true,
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
                if(confirm("Are you sure, you want to delete the selected Rows?")){  
                    $('#overlayDelete').fadeIn();
                    var strIds = idsArr.join(","); 
                    $.ajax({
                        url: 'deleteNotesFiles',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "nf_ids": strIds,
                },
                dataType: 'JSON',
                        success: function (data) {
                                $(".checkbox:checked").each(function() {  
                                    var tr=$(this).parents("tr").remove();
                                    table.row(tr).remove().draw();
                                });
                                $("#checkAll").prop("checked", false);
                                $('#overlayDelete').fadeOut();
                        },
                    });
                }  
            }  
        });
    $(document).ready(function() {
    
        $('#btnSun').submit(function(event){
        event.preventDefault();  
        }
        
    });
</script>
@endsection