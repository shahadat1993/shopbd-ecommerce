@extends('layouts.frontend.app')
@section('title','My Wishlist')
@section('content')
<div class="breadcrumb-section">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item active">Wishlist</li>
    </ol></nav>
  </div>
</div>
<section class="py-5">
  <div class="container">
    <h2 class="fw-800 mb-4">My Wishlist <span class="badge bg-primary ms-2">{{ $items->total() }}</span></h2>
    @if($items->isEmpty())
    <div class="text-center py-5">
      <div style="font-size:72px;margin-bottom:16px">💔</div>
      <h4 class="text-muted">Your wishlist is empty</h4>
      <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Browse Products</a>
    </div>
    @else
    <div class="row g-3">
      @foreach($items as $item)
      <div class="col-6 col-md-4 col-lg-3">
        @php $product = $item->product @endphp
        @include('frontend.products._card', compact('product'))
      </div>
      @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
    @endif
  </div>
</section>
@endsection
