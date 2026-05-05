@extends('layouts.frontend')

@section('title','Products')

@section('content')

<section class="py-4">
  <div class="container front">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>All Products</h2>
    </div>

    <div class="row">

      <div class="col-lg-3 mb-4">
        <h5 class="mb-3">All Categories</h5>
        <div class="list-group">
          @foreach($categories ?? [] as $cat)
            <a href="#" class="list-group-item list-group-item-action category-link" data-id="{{ $cat->id }}">{{ $cat->name }}</a>
          @endforeach
        </div>
      </div>

      <div class="col-lg-9">
       <!-- <h5 id="itemsTitle">All Products</h5>-->
        <div id="itemsGrid" class="row mt-3">
          @forelse($items as $item)
            <div class="col-md-4 mb-4">
              <div class="card product-card shadow-sm h-100">

                {{-- IMAGE --}}
                <div class="product-img">
                  @php
                    $images = data_get($item, 'image_urls', []);
                    $mainRaw = data_get($images, 0);
                    $secondRaw = data_get($images, 1);

                    $toSrc = function ($raw) {
                        if (!$raw) return null;
                        if (preg_match('/^https?:\/\//', $raw)) return $raw;
                        return asset('storage/' . ltrim($raw, '/'));
                    };

                    $srcMain = $toSrc($mainRaw) ?? asset('no-image.png');
                    $srcSecond = $toSrc($secondRaw);
                  @endphp

                  <a href="{{ route('products.show',$item) }}" class="product-link" aria-label="Open {{ $item->name }} details">
                    <img src="{{ $srcMain }}" alt="{{ $item->name }}" class="primary-img" loading="lazy">

                    @if($srcSecond)
                      <img src="{{ $srcSecond }}" alt="{{ $item->name }} - alternate" class="secondary-full" loading="lazy">
                      <img src="{{ $srcSecond }}" alt="thumb" class="secondary-thumb" loading="lazy">
                    @endif
                  </a>
                </div>

                {{-- BODY --}}
                <div class="card-body text-center" style="padding:0px;">

                 <!-- <small class="text-muted d-block">
                    {{ $item->category->name ?? 'General' }}
                  </small>-->

                  <h6 class="product-title mt-1">{{ $item->name }}</h6>

                  <p class="text-danger font-weight-bold">
                    ₹{{ number_format($item->price,2) }}
                  </p>

                </div>

              </div>
            </div>
          @empty
            <div class="col-12 text-center">
              <p>No products found</p>
            </div>
          @endforelse
        </div>

        <div id="itemsPagination" class="d-flex justify-content-center mt-4">
          {{ $items->links() }}
        </div>
      </div>

    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-4">
      {{ $items->links() }}
    </div>

  </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  function renderItems(items) {
    var grid = document.getElementById('itemsGrid');
    grid.innerHTML = '';
    if (!items || !items.length) {
      grid.innerHTML = '<div class="col-12 text-center text-muted">No products found.</div>';
      return;
    }
    items.forEach(function (it) {
      var col = document.createElement('div'); col.className = 'col-lg-3 col-md-4 col-6 mb-4';
      col.innerHTML = '\n        <div class="card product-card shadow-sm h-100">\n          <div class="product-img d-flex align-items-center justify-content-center bg-light" style="height:200px;">\n            <a href="' + (it.url||'#') + '" class="product-link"><img src="' + (it.image||'') + '" alt="' + (it.name||'') + '" style="max-height:180px; max-width:100%; object-fit:contain;"></a>\n          </div>\n          <div class="card-body text-center">\n            <small class="text-muted d-block">' + (it.category||'') + '</small>\n            <h6 class="product-title mt-1">' + (it.name||'') + '</h6>\n            <p class="text-danger font-weight-bold">₹' + (it.price||'') + '</p>\n          </div>\n        </div>';
      grid.appendChild(col);
    });
  }

  function loadCategory(id, name) {
    var title = document.getElementById('itemsTitle');
    title.textContent = 'Products — ' + name;
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
      document.querySelectorAll('.list-group .active').forEach(function(a){ a.classList.remove('active'); });
      if (this.classList.contains('list-group-item')) this.classList.add('active');
      window.scrollTo({ top: 200, behavior: 'smooth' });
    });
  });
});
</script>
@endpush