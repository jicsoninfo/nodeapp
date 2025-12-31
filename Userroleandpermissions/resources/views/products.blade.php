@extends('layout')
   
@section('content') 
<div class="row mt-4">
    @foreach($products as $product)
        <div class="col-md-3">
            <div class="card text-center">
                <img src="{{ $product->image }}" alt="" class="card-img-top">
                <div class="caption card-body">
                    <h4>{{ $product->name }}</h4>
                    <p>{{ $product->description }}</p>
                    <p><strong>Price: </strong> $ {{ $product->price }}</p>
                    <a href="{{ route('add.to.cart', $product->id) }}" class="btn btn-warning btn-block text-center" role="button">Add to cart</a>

                    <!-- <a href="{{ route('add.to.cart', $product->id) }}" class="btn btn-warning btn-block text-center" role="button">Custmize the product</a> -->
                </div>
            </div>
        </div>



    @endforeach
</div>
@endsection