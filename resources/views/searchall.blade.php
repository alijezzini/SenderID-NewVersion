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
        <script src="https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/export/bootstrap-table-export.min.js"></script>
        
    <style>
     
       .ff{
            padding-right:6%;
            padding-left:6%;
            padding-bottom:0.1%;
            padding-top:2.1%;
           } 

       .cont{
         width: 100%;
       }
       .d{
             table-layout: fixed !important;
              word-wrap:break-word;
           }
           #mytable23
           { height:60vh}

        
    </style>
    </head>

    <body >
   
 
    <div class="ff">
        <div>
        <h2>Search All </h2>
        </div>
        <div class="table table-responsive table-hover cont">
        <table id="mytable23" style="width:100%" class="table table-striped table-bordered jumbotron bg-white d">

        </table>
        </div>
    </div> 
      
    <script>

        $(document).ready(function() {
            $('#mytable23').DataTable({
                "autoWidth": false, // might need this
                "select": true,
                "initComplete": function (settings, json) {
                    $("#mytable23").show();
                },
                    "serverside": true,
                     "select": true,
                    "dataSrc": "tableData",
                    "bDestroy": true,
                "columns":  [
                    {  "data":"senderid",
                        "name": "senderid",
                        "title": "Sender Id",
                        "width": "10%",
                    },
                    {    "data":"content",
                        "name": "content",
                        "title": "Content",
                        "width":"30%",
                    },
                    {    "data":"website",
                        "name": "website",
                        "title": "Website",
                        "width": "10%",
                    },
                    {   "data":"note",
                        "name": "note",
                        "title": "note",
                        "width": "30%",
                    },
                    {    "data":"operator",
                        "name": "operator",
                        "title": "Operator",
                        "width": "10%",
                    },
                    {    "data":"vendor",
                        "name": "vendor",
                        "title": "vendor",
                        "width": "10%",
                    },
                ],
   
                    "language": {
                        "emptyTable": "No files to show..."
                    },
                        "ajax": "{{ url('searchsenders/lol')}}",
    });
        });
    </script>
    </body>
    </html>
@endsection