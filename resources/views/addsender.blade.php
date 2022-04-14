@extends('layouts.admin')
@section('title', 'Add Sender')
@section('content')
<style>
#overlay {
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
<div style="margin-left:2rem;margin-right:2rem">
<h3 style="margin-bottom:2rem">Add Sender</h3>
<div id="overlay" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Loading Operators...
</div>
    <form action="submit" method="POST" enctype="multipart/form-data"> 
        @csrf
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;">
            <label><b>Select Country and Operator</b></label>
            <div class="row">

                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control"  id="countryselect" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{$country['country']}}">{{$country['country']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control" name="operator" id="operatorselect" required>
                        <option value="" disabled selected>Select Operator</option>
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control selectpicker" name="vendor" id="vendorselect" required>
                        <option value="" disabled selected>Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option value="{{$vendor['vn_id']}}">{{$vendor['vendor']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
            <label><input type="radio" name="radiocheck" value= "add" checked><b>  Add SenderID</b></label>

            <div class="row">
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="senderid" placeholder="Enter SenderID" name="senderid" required>
                </div>
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="content" placeholder="Enter Content" name="content">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="website" placeholder="Enter Website" name="website">

                </div>
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="note" placeholder="Enter Note" name="note">
                </div>
            </div>
        </div>
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
            <label><input type="radio" name="radiocheck" value= "import"><b>  Import Multiple SenderIDs</b></label>
            <div class="row ">
                <div class="col-md-12 py-1">
                    <input type="file" id="importsender" name="senderidExcel"  accept=".xlsx" disabled required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 py-2">
               <div class="flash-message">
                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                      @if(Session::has('alert-' . $msg))

                       <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                     @endif
                   @endforeach
                 </div>
            </div>
            <div class="col-md-2 py-2">
                <button id="submitbutton" style="float:right" type="submit" class="btn btn-success btn-lg">Submit</button>
            </div>
        </div>
    </form>
    <div>

</div>
</div>

<script>

    $(document).ready(function() {
        $('button').click(function(){
		
	});
        $("#countryselect").on('change', function() {
            $('#overlay').fadeIn();
            var country = $('#countryselect').find(":selected").text();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                /* the route pointing to the post function */
                url: '/getOperators',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "country": country
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
                        console.log(val.operator);
                    })
                }
            });
        });

        $('input[type=radio][name=radiocheck]').change(function() {
            if (this.value == 'add') {
                $('#senderid').prop('disabled', false);
                $('#content').prop('disabled', false);
                $('#website').prop('disabled', false);
                $('#note').prop('disabled', false);
                $('#importsender').prop('disabled', true);
                $('#importsender').prop('required', true);
            } else if (this.value == 'import') {
                $('#senderid').prop('disabled', true);
                $('#content').prop('disabled', true);
                $('#website').prop('disabled', true);
                $('#note').prop('disabled', true);
                $('#importsender').prop('disabled', false);
            }
        });
    });
</script>

@endsection