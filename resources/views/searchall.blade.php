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
        .Outside {
            padding-right: 6%;
            padding-left: 6%;
            padding-bottom: 0.2%;
            padding-top: 1%;
        }
        .btnexcel
        {
            display: flex;
  justify-content: center;
  align-items: center;
  border: 1px solid green; 
        }
        .Tabl {
            background-color: #F7F7F7;
            word-wrap: break-word;
        }

        #mytable23 {
            height: 60vh
        }

        /* .pagination>li {
            padding-top: 1%;

        } */

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
    <div class="Outside">
        <h3 style="margin-bottom:2rem">Search All Senders</h3>
        <table bordercolor="#001e5f" id="example" class="table table-striped table-bordered Tabl" style="width:100%">
            <col style="width:5%">
            <col style="width:25%">
            <col style="width:15%">
            <col style="width:25%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:10%">
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
        "serverside": true,     
        "dataSrc": "tableData",
        "bDestroy": true,
        "autoWidth": true, // might need this
        "select": true,
        "scrollX": true,
        "orderCellsTop": true,
        "fixedHeader": true,
        "initComplete": function(settings, json) {
                        $('#overlay').fadeOut();
                    },
        "language": {
           "emptyTable": "No files to show..."
                    },
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
        dom: 'lBfrtip',
        buttons: [
            {
        text: 'Export Excel',
        extend: 'excelHtml5',
        exportOptions: {
            columns: ':visible',
            page: 'all'
            },
        className:'btn btn-success btnexcel'
            }
        ],
            });
      
        });

        </script>
</body>

</html>
@endsection