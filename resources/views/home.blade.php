@extends('layouts.app')
@section('title','Home')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="my-4">
                    Please Make A Order Payment
                </h1>
                <a href="{{ route('payment.index') }}" class="mt-4 btn btn-dark text-uppercase">
                    Order Now
                </a>
            </div>
        </div>
    </div>
@endsection