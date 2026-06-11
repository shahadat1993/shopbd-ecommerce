<div class="product-card h-100">
  <div class="img-wrap">
    <a href="{{ route('products.show', $product) }}">
      <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" loading="lazy">
    </a>
    @if($product->discount_percent > 0)
      <span class="badge-discount">-{{ $product->discount_percent }}%</span>
    @endif
    @if($product->created_at->diffInDays(now()) < 14)
      <span class="badge-new-tag">NEW</span>
    @endif
    <div class="actions-overlay">
      @auth
      <form action="{{ route('wishlist.add', $product) }}" method="POST">
        @csrf
        <button type="submit" class="action-btn" title="Add to Wishlist"><i class="bi bi-heart"></i></button>
      </form>
      @endauth
      <a href="{{ route('products.show', $product) }}" class="action-btn" title="View Details">
        <i class="bi bi-eye"></i>
      </a>
    </div>
  </div>
  <div class="card-body">
    @if($product->category)
    <a href="{{ route('products.category', $product->category->slug) }}" class="text-muted text-decoration-none" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">
      {{ $product->category->name }}
    </a>
    @endif
    <p class="product-title mt-1">
      <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
    </p>
    @php $avg = $product->average_rating; @endphp
    @if($avg > 0)
    <div class="d-flex align-items-center gap-1 mb-2">
      @for($i=1;$i<=5;$i++)
        <i class="bi bi-star{{ $i<=$avg?'-fill':'' }}" style="color:#ffc107;font-size:11px"></i>
      @endfor
      <span class="text-muted" style="font-size:11px">({{ $product->review_count }})</span>
    </div>
    @endif
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <span class="price-current">৳{{ number_format($product->current_price, 0) }}</span>
        @if($product->sale_price && $product->sale_price < $product->price)
          <span class="price-original ms-1">৳{{ number_format($product->price, 0) }}</span>
        @endif
      </div>
      @if(!$product->isInStock())
        <span class="badge bg-danger-subtle text-danger" style="font-size:10px">Out</span>
      @elseif($product->stock <= 5)
        <span class="badge bg-warning-subtle text-warning" style="font-size:10px">{{ $product->stock }} left</span>
      @endif
    </div>
    @if($product->isInStock())
    <form action="{{ route('cart.add') }}" method="POST">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="hidden" name="qty" value="1">
      <button type="submit" class="btn-add-cart">
        <i class="bi bi-cart-plus me-1"></i> Add to Cart
      </button>
    </form>
    @else
    <button class="btn-add-cart" style="background:#adb5bd;cursor:not-allowed" disabled>Out of Stock</button>
    @endif
  </div>
</div>
