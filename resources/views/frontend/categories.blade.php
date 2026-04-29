@extends('layouts.frontend')

@section('title','Categories')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white p-2">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Categories</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-3 mb-4">
      <h5 class="mb-3">All Categories</h5>
      <div class="list-group">
        @foreach($categories as $cat)
          <a href="#" class="list-group-item list-group-item-action category-link" data-id="{{ $cat->id }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="col-lg-9">
      <h5 id="itemsTitle">Products</h5>
      <div id="itemsGrid" class="row mt-3">
        @if(!empty($items) && $items->count())
          @foreach($items as $item)
            <div class="col-lg-3 col-md-4 col-6 mb-4">
              <div class="card product-card shadow-sm h-100">
                <div class="product-img d-flex align-items-center justify-content-center bg-light" style="height:200px;">
                  <img src="{{ $item->image_urls[0] ?? asset('no-image.png') }}" alt="{{ $item->name }}" style="max-height:180px; max-width:100%; object-fit:contain;">
                </div>
                <div class="card-body text-center">
                  <small class="text-muted d-block">{{ $item->category->name ?? '' }}</small>
                  <h6 class="product-title mt-1">{{ $item->name }}</h6>
                  <p class="text-danger font-weight-bold">₹{{ number_format($item->price,2) }}</p>
                  <a href="{{ route('products.show', $item) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <div class="col-12 text-muted">No products available.</div>
        @endif
      </div>

      <div id="itemsPagination" class="d-flex justify-content-center mt-3">
        @if(!empty($items))
          {{ $items->links() }}
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.card { border-radius: 8px; }
.card img { max-width:100%; height:auto }
.category-tile:hover { transform: translateY(-3px); transition: .15s; }
.product-thumb { border:1px solid #eee; padding:8px; border-radius:6px; background:#fff }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  function renderItems(items) {
    var grid = document.getElementById('itemsGrid');
    grid.innerHTML = '';
    if (!items.length) {
      grid.innerHTML = '<div class="col-12 text-center text-muted">No products found.</div>';
      return;
    }
    items.forEach(function (it) {
      var col = document.createElement('div'); col.className = 'col-lg-3 col-md-4 col-6 mb-4';
      col.innerHTML = '\n        <div class="card product-card shadow-sm h-100">\n          <div class="product-img d-flex align-items-center justify-content-center bg-light" style="height:200px;">\n            <img src="' + it.image + '" alt="' + (it.name||'') + '" style="max-height:180px; max-width:100%; object-fit:contain;">\n          </div>\n          <div class="card-body text-center">\n            <small class="text-muted d-block">' + (it.category||'') + '</small>\n            <h6 class="product-title mt-1">' + it.name + '</h6>\n            <p class="text-danger font-weight-bold">₹' + it.price + '</p>\n            <a href="' + it.url + '" class="btn btn-sm btn-outline-primary">View Details</a>\n          </div>\n        </div>';
      grid.appendChild(col);
    });
  }

  function loadCategory(id, name) {
    var title = document.getElementById('itemsTitle');
    title.textContent = 'Products — ' + name;
    // clear server-side pagination when showing filtered results
    var pag = document.getElementById('itemsPagination'); if (pag) pag.innerHTML = '';
    fetch("{{ url('/') }}" + '/api/category/' + id + '/items')
      .then(function (r) { return r.json(); })
      .then(function (data) { renderItems(data.items || []); })
      .catch(function () { document.getElementById('itemsGrid').innerHTML = '<div class="col-12 text-danger">Failed to load items.</div>'; });
  }

  document.querySelectorAll('.category-link, .category-tile').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var id = this.dataset.id;
      var name = this.textContent.trim();
      loadCategory(id, name);
      // highlight
      document.querySelectorAll('.list-group .active').forEach(function(a){ a.classList.remove('active'); });
      if (this.classList.contains('list-group-item')) this.classList.add('active');
      window.scrollTo({ top: 200, behavior: 'smooth' });
    });
  });
});
</script>
@endpush
