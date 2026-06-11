@extends('layouts.frontend.app')
@section('title', $product->name)
@section('meta_description', $product->short_description ?? Str::limit(strip_tags($product->description ?? ''), 160))

@section('content')

<div class="breadcrumb-section">
  <div class="container">
    <nav><ol class="breadcrumb mb-0" style="font-size:.85rem">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
      @if($product->category)
        <li class="breadcrumb-item"><a href="{{ route('products.category',$product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
      @endif
      <li class="breadcrumb-item active text-truncate" style="max-width:180px">{{ $product->name }}</li>
    </ol></nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-5">

      {{-- Images --}}
      <div class="col-12 col-lg-5">
        <div class="position-sticky" style="top:80px">
          <div class="rounded-4 overflow-hidden border mb-3 bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:1">
            <img id="mainImage" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}"
              class="w-100 h-100" style="object-fit:contain;cursor:zoom-in" onclick="openLightbox(this.src)">
          </div>
          @if($product->images->count() > 0)
          <div class="d-flex gap-2 flex-wrap">
            <div class="rounded-3 border border-primary overflow-hidden cursor-pointer" style="width:72px;height:72px;cursor:pointer"
              onclick="switchImage('{{ $product->thumbnail_url }}',this)">
              <img src="{{ $product->thumbnail_url }}" class="w-100 h-100" style="object-fit:cover">
            </div>
            @foreach($product->images as $img)
            <div class="rounded-3 border overflow-hidden cursor-pointer" style="width:72px;height:72px;cursor:pointer"
              onclick="switchImage('{{ $img->url }}',this)">
              <img src="{{ $img->url }}" class="w-100 h-100" style="object-fit:cover">
            </div>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      {{-- Info --}}
      <div class="col-12 col-lg-7">
        <div class="d-flex flex-wrap gap-2 mb-2">
          @if($product->category)<a href="{{ route('products.category',$product->category->slug) }}" class="badge bg-primary-subtle text-primary text-decoration-none" style="font-size:.75rem">{{ $product->category->name }}</a>@endif
          @if($product->brand)<span class="badge bg-secondary-subtle text-secondary" style="font-size:.75rem">{{ $product->brand }}</span>@endif
          @if($product->is_featured)<span class="badge bg-warning-subtle text-warning" style="font-size:.75rem"><i class="bi bi-star-fill me-1"></i>Featured</span>@endif
        </div>

        <h1 class="fw-800 mb-3" style="font-size:1.65rem;line-height:1.3">{{ $product->name }}</h1>

        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
          <div class="star-rating">
            @php $avg = round($product->average_rating * 2) / 2; @endphp
            @for($i=1;$i<=5;$i++)
              <i class="bi bi-star{{ $i<=$avg?'-fill':($i-$avg<1?'-half':'') }}"></i>
            @endfor
          </div>
          <span class="text-muted small">{{ number_format($product->average_rating,1) }} ({{ $product->review_count }} reviews)</span>
          <span class="text-muted">·</span>
          <span class="text-muted small">SKU: {{ $product->sku ?? 'N/A' }}</span>
        </div>

        <div class="p-4 rounded-3 mb-4" style="background:var(--bg-light);border:1px solid var(--border)">
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <span style="font-size:2rem;font-weight:800;color:var(--primary)">৳{{ number_format($product->current_price,0) }}</span>
            @if($product->sale_price && $product->sale_price < $product->price)
              <del class="text-muted fs-5">৳{{ number_format($product->price,0) }}</del>
              <span class="badge bg-danger fs-6">-{{ $product->discount_percent }}% OFF</span>
            @endif
          </div>
          @if($product->isInStock())
            <p class="mb-0 mt-2 fw-600" style="color:var(--accent)"><i class="bi bi-check-circle-fill me-1"></i>In Stock ({{ $product->stock }} available)</p>
          @else
            <p class="mb-0 mt-2 fw-600 text-danger"><i class="bi bi-x-circle-fill me-1"></i>Out of Stock</p>
          @endif
        </div>

        @if($product->short_description)
          <p class="text-muted mb-4" style="line-height:1.9">{{ $product->short_description }}</p>
        @endif

        {{-- Variants --}}
        @if($product->variants->count() > 0)
          @foreach($product->variants->groupBy('name') as $varName => $variants)
          <div class="mb-3">
            <label class="fw-600 mb-2 d-block">{{ $varName }}</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach($variants as $v)
              <button type="button" class="btn btn-outline-secondary btn-sm variant-btn" data-name="{{ $v->name }}" data-value="{{ $v->value }}">
                {{ $v->value }}@if($v->price_modifier != 0)<small class="ms-1">({{ $v->price_modifier>0?'+':'' }}৳{{ number_format($v->price_modifier,0) }})</small>@endif
              </button>
              @endforeach
            </div>
          </div>
          @endforeach
        @endif

        {{-- Add to Cart --}}
        @if($product->isInStock())
        <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
            <label class="fw-600 mb-0">Quantity:</label>
            <div class="d-flex align-items-center border rounded-3 overflow-hidden">
              <button type="button" class="btn border-0 px-3 py-2 fw-bold" onclick="changeQty(-1)">−</button>
              <input type="number" name="qty" id="qtyInput" value="1"
                min="{{ $product->min_order_qty }}"
                max="{{ min($product->stock, $product->max_order_qty ?? $product->stock) }}"
                class="form-control border-0 text-center fw-bold" style="width:64px">
              <button type="button" class="btn border-0 px-3 py-2 fw-bold" onclick="changeQty(1)">+</button>
            </div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary btn-lg flex-fill fw-600">
              <i class="bi bi-cart-plus me-2"></i>Add to Cart
            </button>
            @auth
            <form action="{{ $inWishlist ? route('wishlist.remove',$product) : route('wishlist.add',$product) }}" method="POST" class="d-inline">
              @csrf @if($inWishlist)@method('DELETE')@endif
              <button type="submit" class="btn btn-lg {{ $inWishlist?'btn-danger':'btn-outline-secondary' }}" title="{{ $inWishlist?'Remove from Wishlist':'Add to Wishlist' }}">
                <i class="bi bi-heart{{ $inWishlist?'-fill':'' }}"></i>
              </button>
            </form>
            @endauth
          </div>
        </form>
        @else
        <button class="btn btn-secondary btn-lg w-100 mb-4" disabled>Out of Stock — Notify Me</button>
        @endif

        {{-- Trust --}}
        <div class="row g-2 mb-4">
          @foreach([['bi-truck','Free delivery over ৳1000'],['bi-arrow-counterclockwise','7-day returns'],['bi-shield-check','Secure checkout']] as $b)
          <div class="col-4">
            <div class="text-center p-2 rounded-3" style="background:var(--bg-light);font-size:11px">
              <i class="bi {{ $b[0] }} d-block mb-1" style="color:var(--primary);font-size:18px"></i>{{ $b[1] }}
            </div>
          </div>
          @endforeach
        </div>

        {{-- Share --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="text-muted small fw-600">Share:</span>
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center"><i class="bi bi-facebook"></i></a>
          <a href="https://wa.me/?text={{ urlencode($product->name.' - '.request()->url()) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
    </div>

    {{-- ══ Product Tabs ══ --}}
    <div class="mt-5">
      <ul class="nav nav-tabs border-bottom-0" id="productTabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active fw-600 px-4" data-bs-toggle="tab" data-bs-target="#tab-features" type="button">
            <i class="bi bi-list-check me-1"></i> Features
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link fw-600 px-4" data-bs-toggle="tab" data-bs-target="#tab-description" type="button">
            <i class="bi bi-file-text me-1"></i> Description
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link fw-600 px-4" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button">
            <i class="bi bi-star me-1"></i> Reviews
            <span class="badge bg-primary ms-1" style="font-size:.7rem">{{ $product->review_count }}</span>
          </button>
        </li>
      </ul>

      <div class="tab-content border border-top-0 rounded-bottom-4 p-4 bg-white">

        {{-- Features Tab --}}
        <div class="tab-pane fade show active" id="tab-features">
          <div class="row g-4">
            <div class="col-md-6">
              <h6 class="fw-700 mb-3 text-primary">Product Specifications</h6>
              <table class="table table-sm table-borderless">
                @if($product->brand)<tr><td class="text-muted" style="width:40%">Brand</td><td class="fw-600">{{ $product->brand }}</td></tr>@endif
                @if($product->sku)<tr><td class="text-muted">SKU</td><td class="fw-600">{{ $product->sku }}</td></tr>@endif
                @if($product->category)<tr><td class="text-muted">Category</td><td class="fw-600">{{ $product->category->name }}</td></tr>@endif
                @if($product->weight)<tr><td class="text-muted">Weight</td><td class="fw-600">{{ $product->weight }}</td></tr>@endif
                @if($product->dimensions)<tr><td class="text-muted">Dimensions</td><td class="fw-600">{{ $product->dimensions }}</td></tr>@endif
                <tr><td class="text-muted">Availability</td>
                  <td><span class="badge {{ $product->isInStock()?'bg-success':'bg-danger' }}">{{ $product->isInStock()?'In Stock':'Out of Stock' }}</span></td>
                </tr>
                <tr><td class="text-muted">Condition</td><td class="fw-600">Brand New</td></tr>
              </table>
            </div>
            @if($product->tags && count($product->tags) > 0)
            <div class="col-md-6">
              <h6 class="fw-700 mb-3 text-primary">Tags</h6>
              <div class="d-flex flex-wrap gap-2">
                @foreach($product->tags as $tag)
                  <a href="{{ route('products.search',['q'=>$tag]) }}" class="badge bg-secondary-subtle text-secondary text-decoration-none" style="font-size:.8rem;padding:6px 12px">{{ $tag }}</a>
                @endforeach
              </div>
            </div>
            @endif
          </div>
        </div>

        {{-- Description Tab --}}
        <div class="tab-pane fade" id="tab-description">
          @if($product->description)
            <div style="line-height:2;color:#444">{!! nl2br(e($product->description)) !!}</div>
          @else
            <div class="text-center py-4 text-muted"><i class="bi bi-file-text fs-2 d-block mb-2 opacity-25"></i>No description available.</div>
          @endif
        </div>

        {{-- Reviews Tab --}}
        <div class="tab-pane fade" id="tab-reviews">
          {{-- Rating Summary --}}
          <div class="row g-4 mb-5 align-items-center">
            <div class="col-md-3 text-center">
              <div style="font-size:3.5rem;font-weight:900;color:var(--primary);line-height:1">{{ number_format($product->average_rating,1) }}</div>
              <div class="star-rating fs-5 my-2">
                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$product->average_rating?'-fill':'' }}"></i>@endfor
              </div>
              <small class="text-muted">{{ $product->review_count }} {{ Str::plural('review',$product->review_count) }}</small>
            </div>
            <div class="col-md-9">
              @for($star=5;$star>=1;$star--)
                @php $cnt=$product->reviews->where('rating',$star)->count(); $pct=$product->review_count>0?($cnt/$product->review_count)*100:0; @endphp
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="text-muted small fw-600" style="width:14px">{{ $star }}</span>
                  <i class="bi bi-star-fill" style="color:#ffc107;font-size:11px"></i>
                  <div class="progress flex-grow-1 rounded-pill" style="height:10px;background:#f0f0f0">
                    <div class="progress-bar rounded-pill" style="width:{{ $pct }}%;background:#ffc107"></div>
                  </div>
                  <span class="text-muted small" style="width:28px;text-align:right">{{ $cnt }}</span>
                </div>
              @endfor
            </div>
          </div>

          {{-- Review List --}}
          @forelse($product->reviews as $review)
          <div class="border-bottom pb-4 mb-4">
            <div class="d-flex gap-3 align-items-start">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-700" style="width:44px;height:44px;background:var(--primary);font-size:1.1rem">
                {{ strtoupper(substr($review->user->name,0,1)) }}
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                  <div>
                    <p class="mb-0 fw-700">{{ $review->user->name }}</p>
                    <div class="star-rating my-1">
                      @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$review->rating?'-fill':'' }}"></i>@endfor
                    </div>
                    @if($review->title)<p class="mb-1 fw-600">{{ $review->title }}</p>@endif
                  </div>
                  <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 text-muted" style="line-height:1.8">{{ $review->comment }}</p>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-square-text fs-2 d-block mb-2 opacity-25"></i>
            No reviews yet. Be the first to share your experience!
          </div>
          @endforelse

          {{-- Write Review --}}
          <div class="mt-4 p-4 rounded-4" style="background:var(--bg-light);border:1px solid var(--border)">
            <h6 class="fw-700 mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Write a Review</h6>
            @auth
            <form action="{{ route('review.store',$product) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="fw-600 mb-2 d-block">Your Rating <span class="text-danger">*</span></label>
                <div class="d-flex gap-1" id="starPicker">
                  @for($i=1;$i<=5;$i++)
                  <label class="cursor-pointer" style="cursor:pointer">
                    <input type="radio" name="rating" value="{{ $i }}" class="d-none" required>
                    <i class="bi bi-star-fill" style="font-size:1.8rem;color:#dee2e6;transition:color .15s" data-star="{{ $i }}"></i>
                  </label>
                  @endfor
                </div>
              </div>
              <div class="mb-3">
                <label class="fw-600 mb-1">Review Title</label>
                <input type="text" name="title" class="form-control" placeholder="Summarize your experience">
              </div>
              <div class="mb-4">
                <label class="fw-600 mb-1">Your Review</label>
                <textarea name="comment" class="form-control" rows="4" placeholder="Tell others what you think about this product..."></textarea>
              </div>
              <button type="submit" class="btn btn-primary px-4 fw-600">
                <i class="bi bi-send me-2"></i>Submit Review
              </button>
            </form>
            @else
            <div class="text-center py-3">
              <p class="text-muted mb-3">Please login to write a review.</p>
              <a href="{{ route('login') }}" class="btn btn-primary px-4">Login to Review</a>
            </div>
            @endauth
          </div>
        </div>

      </div>
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
    <div class="mt-5">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-800 mb-1">Related <span style="color:var(--primary)">Products</span></h3>
          <div style="width:50px;height:4px;background:linear-gradient(90deg,var(--primary),var(--secondary));border-radius:2px"></div>
        </div>
        <a href="{{ route('products.category', $product->category->slug ?? 'all') }}" class="btn btn-outline-primary btn-sm">See More</a>
      </div>
      <div class="swiper related-swiper">
        <div class="swiper-wrapper pb-2">
          @foreach($relatedProducts as $relProduct)
          <div class="swiper-slide">
            @include('frontend.products._card', ['product' => $relProduct])
          </div>
          @endforeach
        </div>
        <div class="swiper-pagination mt-2"></div>
      </div>
    </div>
    @endif

  </div>
</section>

{{-- Lightbox --}}
<div class="modal fade" id="lightboxModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:700px">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 text-center position-relative">
        <img id="lightboxImg" src="" class="img-fluid rounded-4 shadow-lg" style="max-height:85vh">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function changeQty(d) {
  const el=document.getElementById('qtyInput');
  const v=parseInt(el.value)+d;
  if(v>=parseInt(el.min)&&v<=parseInt(el.max)) el.value=v;
}
function switchImage(src,el) {
  document.getElementById('mainImage').src=src;
  document.querySelectorAll('[onclick*="switchImage"]').forEach(e=>e.classList.remove('border-primary'));
  el.classList.add('border-primary');
}
function openLightbox(src) {
  document.getElementById('lightboxImg').src=src;
  new bootstrap.Modal(document.getElementById('lightboxModal')).show();
}
// Variants
document.querySelectorAll('.variant-btn').forEach(btn=>{
  btn.addEventListener('click',function(){
    this.closest('.mb-3').querySelectorAll('.variant-btn').forEach(b=>{b.classList.remove('btn-primary','text-white');b.classList.add('btn-outline-secondary');});
    this.classList.remove('btn-outline-secondary');
    this.classList.add('btn-primary','text-white');
  });
});
// Star picker
const stars=document.querySelectorAll('#starPicker i');
document.querySelectorAll('#starPicker input').forEach((input,idx)=>{
  input.addEventListener('change',()=>{
    stars.forEach((s,i)=>s.style.color=i<=idx?'#ffc107':'#dee2e6');
  });
});
document.querySelectorAll('#starPicker label').forEach((label,idx)=>{
  label.addEventListener('mouseenter',()=>stars.forEach((s,i)=>s.style.color=i<=idx?'#ffc107':'#dee2e6'));
  label.addEventListener('mouseleave',()=>{
    const checked=document.querySelector('#starPicker input:checked');
    const ci=checked?parseInt(checked.value)-1:-1;
    stars.forEach((s,i)=>s.style.color=i<=ci?'#ffc107':'#dee2e6');
  });
});
// Related swiper
new Swiper('.related-swiper',{
  slidesPerView:2,spaceBetween:16,
  pagination:{el:'.related-swiper .swiper-pagination',clickable:true},
  breakpoints:{576:{slidesPerView:3},992:{slidesPerView:4}}
});
</script>
@endpush
