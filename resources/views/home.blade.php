@extends('layouts.admin')
@section('title', 'Home')
@section('content')


<body>
   
<div class="container">
    <div class="row justify-content-center p-5  ">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
    </body>
@endsection
