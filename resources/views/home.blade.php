@extends('layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if (Auth::check())
                    <p>{{ __('You are logged in!') }}</p>
                    <p>{{ __('Welcome back:') }} {{Auth::user()->name}}</p>

                    <p>{{ __('Your roles: ') }} {{ Auth::user()->getRoleNames()->implode(', ') }}</p>
                    @else
                    <p>{{ __('You are not logged in!') }}</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Login') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
