@extends('layouts.app')
@section('title','Order')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title fw-bold fs-3">
                            Braintree Order Payment
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="checkoutForm">
                            <div class="messages"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fw-bold my-3">
                                        Customer Details
                                    </p>
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name">
                                        <p class="text-danger mt-1 customer_name_error"></p>
                                    </div>
                                   
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (Amount) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="price" id="price" placeholder="0.00">
                                        <p class="text-danger mt-1 price_error"></p>
                                    </div> 
                                    <div class="">
                                        <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    </div>
                                    <select class="form-select mb-3" name="currency_id" id="currency_id">
                                        <option value=''>Select Currency</option> 
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger mt-1 currency_id_error"></p>
                                    
                                </div>
                                <div class="col-md-6">
                                    <p class="fw-bold my-3">
                                        Payment 
                                    </p>
                                    <div class="mb-3">
                                        <label for="holder_name" class="form-label">Card Holder Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="holder_name" name="holder_name" placeholder="Card Holder Name">
                                        <p class="text-danger mt-1 holder_name_error"></p>
                                    </div>
                                    
                                    <div id="dropin-container"></div>

                                </div>
                               
                            </div>
                    
                            <div class="float-end mt-3">
                                <button id="checkout" class="btn btn-dark">
                                    ORDER 
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.braintreegateway.com/web/dropin/1.24.0/js/dropin.min.js"></script>

    <script>
            
            

            let button = document.querySelector('#checkout');
           

           
            braintree.dropin.create(
                        {
                        authorization: '{{$clientToken}}',
                        container: '#dropin-container',
                        paypal: {
                            flow : 'vault'
                        }
                        }, 
                        function (createErr, instance) {
                            button.addEventListener('click', function () {
                                instance.requestPaymentMethod(function (err, payload) {
                                    
                                        $(function() {
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });

                                            let customer_name = document.querySelector("#customer_name").value
                                            let price = document.querySelector("#price").value
                                            let currency_id = document.querySelector("#currency_id").value
                                            let holder_name = document.querySelector("#holder_name").value
                                            
                                            console.log(currency_id)

                                            $.ajax({
                                                type: "POST",
                                                url: "{{route('payment.checkout')}}",
                                                data: {
                                                    nonce : payload.nonce,
                                                    customer_name,
                                                    price,
                                                    currency_id,
                                                    holder_name
                                                },
                                                success: function (response) {
                                                    if(response.success){
                                                        $('.messages').addClass('alert alert-success').text(response.message)
                    
                                                        customer_name = ''
                                                        price = ''
                                                        currency_id = ''
                                                        holder_name = ''

                                                        $('.customer_name_error').text('')
                                                        $('.price_error').text('')
                                                        $('.holder_name_error').text('')
                                                        $('.currency_id_error').text('')
                                                    }
                                                    else if(response.errors){
                                                        let customer_name_error = response.errors?.customer_name ?? ''
                                                        let price_error = response.errors?.price ?? ''
                                                        let holder_name_error = response.errors?.holder_name ?? ''
                                                        let currency_id_error = response.errors?.currency_id ?? ''
                                                        
                                                        customer_name_error !== '' ? $('.customer_name_error').text(customer_name_error[0]) : $('.customer_name_error').text('')
                                                        price_error !== '' ? $('.price_error').text(price_error[0]) : $('.price_error').text('')
                                                        holder_name_error !== '' ? $('.holder_name_error').text(holder_name_error[0]) : $('.holder_name_error').text('')
                                                        currency_id_error !== '' ? $('.currency_id_error').text(currency_id_error[0]) : $('.currency_id_error').text('')
                                                    }
                                                },
                                                error: function (response) {
                                                    $('.messages').addClass('alert alert-danger').text(response.error)
                                                }
                                            });
                                            
                                        
                                        });
                                    
                                })
                            })
                        }
                   )

            
    </script>
@endpush