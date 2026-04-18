@extends('admin.layouts.app')
@section('title', 'Edit Item')
@section('content')
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class="fas fa-box mr-2 text-teal"></i>Edit Item</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
					<li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
					<li class="breadcrumb-item active">Edit</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<div class="pull-card">
	<div class="container-fluid">
		<div class="main-card mt-4">
			<div class="main-card-head">
				<div class="main-card-title"><i class="fas fa-edit"></i> Edit Item</div>
			</div>
			<div class="main-card-body">
				<form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Item Name <span class="text-danger">*</span></label>
								<input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control @error('name') is-invalid @enderror" required>
								@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Article Number <span class="text-danger">*</span></label>
								<input type="text" name="article_number" value="{{ old('article_number', $item->article_number) }}" class="form-control @error('article_number') is-invalid @enderror" required>
								@error('article_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Item Code</label>
								<input type="text" name="item_code" value="{{ old('item_code', $item->item_code) }}" class="form-control" readonly>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label>Description</label>
								<textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Group</label>
								<select name="group_id" class="form-control">
									<option value="">-- Select --</option>
									@foreach($groups as $g)
									<option value="{{ $g->id }}" {{ (old('group_id', $item->group_id) == $g->id) ? 'selected' : '' }}>{{ $g->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Sub-Group</label>
								<select name="sub_group" class="form-control">
									<option value="">-- Select --</option>
									@foreach($sub_groups as $subG)
									<option value="{{ $subG->id }}" {{ (old('sub_group', $item->sub_group) == $subG->id) ? 'selected' : '' }}>{{ $subG->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Category</label>
								<select name="category_id" class="form-control">
									<option value="">-- Select --</option>
									@foreach($categories as $cat)
									<option value="{{ $cat->id }}" {{ (old('category_id', $item->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Sub-Category</label>
								<select name="sub_category" class="form-control">
									<option value="">-- Select --</option>
									@foreach($sub_categories as $subCat)
									<option value="{{ $subCat->id }}" {{ (old('sub_category', $item->sub_category) == $subCat->id) ? 'selected' : '' }}>{{ $subCat->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Colors</label>
								<select name="colors[]" class="form-control" multiple>
									@foreach($colors as $color)
									<option value="{{ $color->id }}" {{ in_array($color->id, (array) old('colors', $item->colors->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $color->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						{{-- Sizes --}}
						<div class="col-md-12">
							<div class="form-group">
								<label>Sizes</label>
								@php
								$sizesList     = $sizes ?? [28,30,32,34,36,38,40,42,44,46,48];
								$selectedSizes = old('sizes', []);
								if (empty($selectedSizes) && !empty($item->sizes)) {
								    $selectedSizes = is_array($item->sizes)
								        ? $item->sizes
								        : array_map('trim', explode(',', $item->sizes));
								}
								$selectedSizes = array_map('strval', (array) $selectedSizes);
								@endphp
								<div>
									@foreach($sizesList as $sz)
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" name="sizes[]" id="size_{{ $sz }}" value="{{ $sz }}"
											{{ in_array((string)$sz, $selectedSizes) ? 'checked' : '' }}>
										<label class="form-check-label" for="size_{{ $sz }}">{{ $sz }}</label>
									</div>
									@endforeach
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>MRP</label>
								<input type="number" step="0.01" name="price" value="{{ old('price', $item->price) }}" class="form-control @error('price') is-invalid @enderror">
								@error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Tax %</label>
								<input type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', $item->tax_percent) }}" class="form-control">
							</div>
						</div>

						{{-- ✅ MULTIPLE IMAGES: up to 5, jpg/png only, max 2 MB each --}}
						<div class="col-md-12">
							<div class="form-group">
								<label>
									Images
									<small class="text-muted">(Max 5 images &bull; JPG / PNG only &bull; Max 2 MB each &bull; Uploading new images replaces all existing ones)</small>
								</label>

								{{-- Existing images --}}
								@php
								$existingImages = [];
								if (!empty($item->images) && is_array($item->images)) {
								    $existingImages = $item->images;
								} elseif (!empty($item->image)) {
								    $existingImages = [$item->image];
								}
								@endphp
								@if(!empty($existingImages))
								<div class="d-flex flex-wrap mb-2" style="gap:8px;">
									@foreach($existingImages as $ei => $img)
									<div style="position:relative;display:inline-block;">
										<input type="radio" name="primary_exist" value="{{ $img }}" id="primary_exist_{{ $ei }}"
											{{ ($item->image == $img) ? 'checked' : '' }} style="position:absolute;bottom:4px;left:6px;z-index:2;">
										<img src="{{ asset('storage/' . $img) }}" alt=""
										 style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
									</div>
									@endforeach
								</div>
								@endif

								<div class="custom-file">
									<input type="file"
										   id="itemImages"
										   name="images[]"
										   class="custom-file-input @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
										   multiple
										   accept=".jpg,.jpeg,.png">
									<label class="custom-file-label" for="itemImages">Choose new images&hellip;</label>
								</div>
								@error('images')
									<div class="text-danger small mt-1">{{ $message }}</div>
								@enderror
								@error('images.*')
									<div class="text-danger small mt-1">{{ $message }}</div>
								@enderror
								<input type="hidden" name="primary_image" id="primary_image" value="{{ old('primary_image', $item->image ?? '') }}">
								<div id="imagePreviewContainer" class="d-flex flex-wrap mt-2" style="gap:8px;"></div>
								<div id="imageError" class="text-danger small mt-1" style="display:none;"></div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Status</label>
								<select name="status" class="form-control">
									<option value="1" {{ (old('status', $item->status ? 1 : 0) == 1) ? 'selected' : '' }}>Active</option>
									<option value="0" {{ (old('status', $item->status ? 1 : 0) == 0) ? 'selected' : '' }}>Inactive</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 p-4">
							<div class="form-group">
								<input type="hidden" name="show_item_on_web" value="0">
								<div class="custom-control custom-switch">
									<input type="checkbox" class="custom-control-input" id="show_item_on_web"
										   name="show_item_on_web" value="1"
										   {{ old('show_item_on_web', $item->show_item_on_web) ? 'checked' : '' }}>
									<label class="custom-control-label" for="show_item_on_web">Show Item on Web</label>
								</div>
							</div>
						</div>
					</div>

					<div class="mt-3 text-right">
						<a href="{{ route('items.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
						<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update Item</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('pageScript')
<script>
(function () {
    const input     = document.getElementById('itemImages');
    const preview   = document.getElementById('imagePreviewContainer');
    const errorBox  = document.getElementById('imageError');
    const MAX_FILES = 5;
    const MAX_MB    = 2;
    let   accepted  = [];

    input.addEventListener('change', function () {
        errorBox.style.display = 'none';
        errorBox.textContent   = '';

        const newFiles = Array.from(this.files);
        const errors   = [];

        newFiles.forEach(function (file) {
            const ext = file.name.split('.').pop().toLowerCase();

            if (!['jpg', 'jpeg', 'png'].includes(ext)) {
                errors.push(file.name + ': only JPG / PNG allowed.');
                return;
            }
            if (file.size > MAX_MB * 1024 * 1024) {
                errors.push(file.name + ': exceeds ' + MAX_MB + ' MB limit.');
                return;
            }
            if (accepted.length >= MAX_FILES) {
                errors.push('Maximum ' + MAX_FILES + ' images allowed. "' + file.name + '" skipped.');
                return;
            }
            accepted.push(file);
        });

        if (errors.length) {
            errorBox.textContent   = errors.join(' ');
            errorBox.style.display = 'block';
        }

        syncInput();
        renderPreviews();
    });

    function syncInput() {
        const dt = new DataTransfer();
        accepted.forEach(function (f) { dt.items.add(f); });
        input.files = dt.files;

        const label = input.nextElementSibling;
        if (label && label.classList.contains('custom-file-label')) {
            label.textContent = accepted.length
                ? accepted.length + ' file(s) selected'
                : 'Choose new images\u2026';
        }
    }

	function renderPreviews() {
		preview.innerHTML = '';
		accepted.forEach(function (file, idx) {
			const reader = new FileReader();
			reader.onload = function (e) {
				const wrapper = document.createElement('div');
				wrapper.style.cssText = 'position:relative;display:inline-block;margin-right:8px;';

				const img = document.createElement('img');
				img.src   = e.target.result;
				img.style.cssText = 'width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #ddd;cursor:pointer;';

				// primary marker for new uploads
				const radio = document.createElement('input');
				radio.type = 'radio';
				radio.name = 'primary_select_new';
				radio.style.cssText = 'position:absolute;bottom:4px;left:6px;z-index:2;';
				radio.addEventListener('change', function () {
					document.getElementById('primary_image').value = 'new-' + idx;
					// clear any existing-image radios
					const exist = document.getElementsByName('primary_exist');
					exist.forEach ? exist.forEach(function (e) { e.checked = false; }) : Array.from(exist).forEach(function (e) { e.checked = false; });
				});

				const btn = document.createElement('button');
				btn.type        = 'button';
				btn.innerHTML   = '&times;';
				btn.title       = 'Remove';
				btn.style.cssText =
					'position:absolute;top:2px;right:2px;width:20px;height:20px;line-height:18px;' +
					'text-align:center;border-radius:50%;border:none;background:rgba(220,53,69,.85);' +
					'color:#fff;font-size:14px;cursor:pointer;padding:0;';
				btn.addEventListener('click', function () {
					accepted.splice(idx, 1);
					syncInput();
					renderPreviews();
				});

				wrapper.appendChild(img);
				wrapper.appendChild(radio);
				wrapper.appendChild(btn);
				preview.appendChild(wrapper);
			};
			reader.readAsDataURL(file);
		});
	}

	// Sync existing-image radios into the hidden `primary_image` field
	const existingRadios = document.querySelectorAll('input[name="primary_exist"]');
	existingRadios.forEach(function (r) {
		r.addEventListener('change', function () {
			if (this.checked) {
				// set hidden primary value to existing path
				document.getElementById('primary_image').value = this.value;
				// clear any new-upload radios
				const newRadios = document.getElementsByName('primary_select_new');
				newRadios.forEach ? newRadios.forEach(function (nr) { nr.checked = false; }) : Array.from(newRadios).forEach(function (nr) { nr.checked = false; });
			}
		});
	});
})();
</script>
@endpush