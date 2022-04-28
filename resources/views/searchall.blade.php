@extends('layouts.admin')
@section('title', 'Search Senders')
@section('content')


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>data table</title>
    <link href="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table-locale-all.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/export/bootstrap-table-export.min.js">
    </script>

    <style>
        .ff {
            padding-right: 6%;
            padding-left: 6%;
            padding-bottom: 0.1%;
            padding-top: 2.1%;
        }

        .cont {
            width: 100%;
        }

        .d {
            background-color: #F7F7F7;
            /* table-layout: fixed !important; */
            word-wrap: break-word;
        }

        #mytable23 {
            height: 60vh
        }

        td  { 
         font-size: 14px;
         color: rgb(10, 39, 77);
         font-weight:600;
         background-color: #E7E7E7;
        }

        tr  { 
         font-size: 17px;
         color: #001e5f;
         font-weight: bold;
         background-color: #E7E7E7;
        }
        
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
    </style>
</head>

<body>
    <div style="margin-left:2rem;margin-right:2rem">
        <div id="overlay">
            <img src="/30.gif" class="spiner" alt="" style="width: 100px;height:100px;">
            <br />
            Loading Table...
        </div>
    </div>
    <div class="ff">
        <h3 style="margin-bottom:2rem">Search All Senders</h3>
        <div class="table table-responsive table-hover cont">
            <table id="example" class="table table-striped table-bordered d" style="width:100%">
                <thead>
                    <tr>
                        <th>SenderID</th>
                        <th>Content</th>
                        <th>Website</th>
                        <th>Note</th>
                        <th>Operator</th>
                        <th>Vendor</th>
                        <th>Country</th>
                    </tr>
                </thead>
            </table>
    
        </div>
        <script>
            $(document).ready(function() {

    // Setup - add a text input to each footer cell

    $('#example thead tr').clone(true).appendTo( '#example thead' );
    $('#example thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Search" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var table = $('#example').DataTable({
        
                "autoWidth": false, // might need this
           
                "initComplete": function(settings, json) {
                        $('#overlay').delay(1000).fadeOut();
                    },
                "select": true,
                "scrollX": true,
                "orderCellsTop": true,
                "fixedHeader": true,
                "language": {
                        "emptyTable": "No files to show..."
                    },
                    "serverside": true,
                 
                    "dataSrc": "tableData",
                    "bDestroy": true,

                "columns":  [
                    {  "data":"senderid",
                        "name": "senderid",
                        "width": "10%",
                    },
                    {    "data":"content",
                        "name": "content",
                        "width":"25%",
                    },
                    {    "data":"website",
                        "name": "website",
                        "width": "10%",
                    },
                    {   "data":"note",
                        "name": "note",   
                        "width": "25%",
                    },
                    {    "data":"operator",
                        "name": "operator",
                        "width": "10%",
                    },
                    {    "data":"vendor",
                        "name": "vendor",    
                        "width": "10%",
                    },
                    {    "data":"country",
                        "name": "country",
                        "width": "10%",
                    },
                 ],
   
             "ajax": "{{ url('searchsenders/lol')}}",
  
            });
      
        });

</script>
</body>
</html>
@endsection