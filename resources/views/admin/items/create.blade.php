@extends('admin.layouts.app')
@section('title', 'Create Item')

@section('content')
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><i class="mr-2 text-teal"></i>Create Item</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
					<li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
					<li class="breadcrumb-item active">Create</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<section class="content">
	<div class="container-fluid">
		<div class="card card-outline card-primary h-100">
			<div class="card-header">
				<h3 class="card-title"><i class="fas fa-box mr-1"></i>Create Item</h3>
			</div>
			<div class="main-card-body">
				<form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Item Name <span class="text-danger">*</span></label>
								<input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
								@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Article Number <span class="text-danger">*</span></label>
								<input type="text" name="article_number" value="{{ old('article_number') }}" class="form-control @error('article_number') is-invalid @enderror" required>
								@error('article_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Item Code</label>
								<input type="text" name="item_code" value="{{ old('item_code', $generatedItemCode ?? '') }}" class="form-control @error('item_code') is-invalid @enderror" readonly>
								@error('item_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label>Description</label>
								<textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Group</label>
								<select name="group_id" class="form-control select2 @error('group_id') is-invalid @enderror">
									<option value="">-- Select --</option>
									@foreach($groups as $g)
									<option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
									@endforeach
								</select>
								@error('group_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sub-Group</label>
								<select name="sub_group" class="form-control select2 @error('sub_group') is-invalid @enderror">
									<option value="">-- Select --</option>
									@foreach($sub_groups as $subG)
									<option value="{{ $subG->id }}" {{ old('sub_group') == $subG->id ? 'selected' : '' }}>{{ $subG->name }}</option>
									@endforeach
								</select>
								@error('sub_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Category</label>
								<select name="category_id" class="form-control select2 @error('category_id') is-invalid @enderror">
									<option value="">-- Select --</option>
									@foreach($categories as $cat)
									<option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
									@endforeach
								</select>
								@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sub-Category</label>
								<select name="sub_category" class="form-control select2 @error('sub_category') is-invalid @enderror">
									<option value="">-- Select --</option>
									@foreach($sub_categories as $subCat)
									<option value="{{ $subCat->id }}" {{ old('sub_category') == $subCat->id ? 'selected' : '' }}>{{ $subCat->name }}</option>
									@endforeach
								</select>
								@error('sub_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<!-- <div class="col-md-4">
							<div class="form-group">
								<label>Colors</label>
								<select name="colors[]" class="form-control select2 @error('colors') is-invalid @enderror" multiple>
									@foreach($colors as $color)
									<option value="{{ $color->id }}" {{ in_array($color->id, (array) old('colors', [])) ? 'selected' : '' }}>{{ $color->color_code }}</option>
									@endforeach
								</select>
								@error('colors')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div> -->

						{{-- Sizes --}}
						<!-- <div class="col-md-12">
							<div class="form-group">
								<label>Sizes</label>
								@php
								$sizesList    = $sizes ?? [28,30,32,34,36,38,40,42,44,46,48];
								$selectedSizes = old('sizes', []);
								@endphp
								<div>
									@foreach($sizesList as $sz)
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" name="sizes[]" id="size_{{ $sz }}" value="{{ $sz }}"
											{{ in_array((string)$sz, array_map('strval', (array)$selectedSizes)) ? 'checked' : '' }}>
										<label class="form-check-label" for="size_{{ $sz }}">{{ $sz }}</label>
									</div>
									@endforeach
								</div>
							</div>
						</div> -->

						<div class="col-md-3">
							<div class="form-group">
								<label>MRP</label>
								<input type="number" step="1" name="price" value="{{ old('price', 0) }}" min="0" class="form-control @error('price') is-invalid @enderror">
								@error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Tax %</label>
								<select name="tax_id" class="form-control">
									<option value="">-- Select Tax --</option>
									@foreach($taxes as $tax)
										<option value="{{ $tax->id }}"
											@selected(old('tax_id', $item->tax_id ?? '') == $tax->id)>
											{{ $tax->tax_name }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Video Link</label>
								<input type="url" name="video_link" value="{{ old('video_link') }}" class="form-control">
							</div>
						</div>

						{{-- ✅ MULTIPLE IMAGES: up to 5, jpg/png only, max 2 MB each --}}
						<div class="col-md-12">
							<div class="form-group">
								<label>
									Images
									<small class="text-muted">(Max 5 images • JPG / PNG only • Max 2 MB each)</small>
								</label>

								<input type="file" id="itemImages" name="images[]" multiple accept=".jpg,.jpeg,.png" style="display:none">

								<div id="uploadDropzone" class="d-flex align-items-center p-3 mb-2" style="border:2px dashed #e6d9fb;border-radius:10px;min-height:140px;gap:16px;background:#fff;">
									<div id="uploadTile" style="width:110px;height:110px;border:2px dashed #caa7f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6b3fa8;cursor:pointer;flex-shrink:0;">
										<div style="text-align:center;font-size:16px;font-weight:600;">+ Upload</div>
									</div>

									<div id="imagePreviewContainer" class="d-flex flex-wrap" style="gap:8px;flex:1;">
										<!-- thumbnails go here -->
									</div>
								</div>

								@error('images')
									<div class="text-danger small mt-1">{{ $message }}</div>
								@enderror
								@error('images.*')
									<div class="text-danger small mt-1">{{ $message }}</div>
								@enderror

								<input type="hidden" name="primary_image" id="primary_image" value="">
								<div id="imageError" class="text-danger small mt-1" style="display:none;"></div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Status</label>
								<select name="status" class="form-control">
									<option value="1" {{ old('status',1) == 1 ? 'selected' : '' }}>Active</option>
									<option value="0" {{ old('status',1) == 0 ? 'selected' : '' }}>Inactive</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 p-4">
							<div class="form-group">
								<input type="hidden" name="show_item_on_web" value="0">
								<div class="custom-control custom-switch">
									<input type="checkbox" class="custom-control-input" id="show_item_on_web"
										   name="show_item_on_web" value="1"
										   {{ old('show_item_on_web', 1) ? 'checked' : '' }}>
									<label class="custom-control-label" for="show_item_on_web">Show Item on Web</label>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="card mt-3">
								<div class="card-header pl-0">
									<h5 class="mb-0">Item Variants</h5>
									<!-- <button type="button" class="btn btn-sm btn-create" id="addVariantRow">
										<i class="fas fa-plus"></i> Add Variant
									</button> -->
								</div>

								<div class="card-body p-0">
									@error('variants')
										<div class="alert alert-danger mt-2">
											{{ $message }}
										</div>
									@enderror
									<table class="table table-bordered mb-0" id="variantTable">
										<thead>
											<tr>
												<th width="35%">Color</th>
												<th width="35%">Size</th>
												<th width="20%">Quantity</th>
												<th width="10%">Action</th>
											</tr>
										</thead>

										<tbody>
											<tr>
												<td>
													<select name="variants[0][color_id]" class="form-control select2">
														<option value="">Select Color</option>
														@foreach($colors as $color)
															<option value="{{ $color->id }}">
																{{ $color->color_code }}
															</option>
														@endforeach
													</select>
												</td>

												<td>
													<select name="variants[0][size_id]" class="form-control select2">
														<option value="">Select Size</option>
														@foreach($sizes as $size)
															<option value="{{ $size->id }}">
																{{ $size->name }}
															</option>
														@endforeach
													</select>
												</td>

												<td>
													<input type="number"
														name="variants[0][quantity]"
														class="form-control"
														min="0"
														value="0">
												</td>

												<td class="text-center">
													<button type="button"
															class="btn btn-danger btn-sm removeRow">
														<i class="fas fa-trash"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="p-3 text-right">
										<button type="button" class="btn btn-sm btn-create" id="addVariantRow">
											<i class="fas fa-plus"></i> Add Variant
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="mt-3 mb-3 text-right">
						<a href="{{ route('items.index') }}" class="btn-cancel mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
						<button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Save Item</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection

@section('pageScript')
<script>
;(function () {
	const input     = document.getElementById('itemImages');
	const preview   = document.getElementById('imagePreviewContainer');
	const errorBox  = document.getElementById('imageError');
	const dropzone  = document.getElementById('uploadDropzone');
	const uploadTile = document.getElementById('uploadTile');
    const MAX_FILES = 5;
    const MAX_MB    = 2;
    let   accepted  = [];   // DataTransfer-backed file list

	function updateUploadVisibility() {
		if (!uploadTile) return;
		if (accepted.length >= MAX_FILES) {
			uploadTile.style.display = 'none';
		} else {
			uploadTile.style.display = 'flex';
		}
	}

	input.addEventListener('change', function () {
        errorBox.style.display = 'none';
        errorBox.textContent   = '';

        const newFiles   = Array.from(this.files);
        const errors     = [];

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

	// click tile opens file dialog
	uploadTile.addEventListener('click', function () { input.click(); });

	// drag & drop
	['dragenter','dragover'].forEach(function(ev){
		dropzone.addEventListener(ev, function(e){ e.preventDefault(); dropzone.style.background = '#fbf7ff'; });
	});
	['dragleave','drop'].forEach(function(ev){
		dropzone.addEventListener(ev, function(e){ e.preventDefault(); dropzone.style.background = '#fff'; });
	});
	dropzone.addEventListener('drop', function(e){
		e.preventDefault();
		errorBox.style.display = 'none';
		const files = Array.from(e.dataTransfer.files || []);
		if (!files.length) return;

		const newFiles = files.filter(function(f){
			const ext = f.name.split('.').pop().toLowerCase();
			return ['jpg','jpeg','png'].includes(ext);
		});

		// append respecting limits
		newFiles.forEach(function(f){ if (accepted.length < MAX_FILES) accepted.push(f); });
		syncInput(); renderPreviews();
	});

    function syncInput() {
        // Rebuild the FileList from accepted[] using DataTransfer
        const dt = new DataTransfer();
        accepted.forEach(function (f) { dt.items.add(f); });
        input.files = dt.files;

        // Update custom-file label
        const label = input.nextElementSibling;
        if (label && label.classList.contains('custom-file-label')) {
            label.textContent = accepted.length
                ? accepted.length + ' file(s) selected'
                : 'Choose images\u2026';
        }
		updateUploadVisibility();
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

				// primary marker
				const radio = document.createElement('input');
				radio.type = 'radio';
				radio.name = 'primary_select_new';
				radio.style.cssText = 'position:absolute;bottom:4px;left:6px;z-index:2;';
				radio.addEventListener('change', function () {
					document.getElementById('primary_image').value = 'new-' + idx;
					// clear existing-image radios if any (edit view compatibility)
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
		updateUploadVisibility();
	}
})();

let variantIndex = 1;

function initSelect2(context = document) {
    $(context).find('.select2').each(function () {

        // prevent re-initialization issues
        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).select2('destroy');
        }

        $(this).select2({
            width: '100%'
        });
    });
}

$(document).ready(function () {
    initSelect2();
});

document.getElementById('addVariantRow').addEventListener('click', function () {

    let row = `
        <tr>
            <td>
                <select name="variants[${variantIndex}][color_id]"
                        class="form-control select2">
                    <option value="">Select Color</option>

                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">
                            {{ $color->color_code }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>
                <select name="variants[${variantIndex}][size_id]"
                        class="form-control select2">
                    <option value="">Select Size</option>

                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}">
                            {{ $size->name }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>
                <input type="number"
                       name="variants[${variantIndex}][quantity]"
                       class="form-control"
                       min="0"
                       value="0">
            </td>

            <td class="text-center">
                <button type="button"
                        class="btn btn-danger btn-sm removeRow">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    document.querySelector('#variantTable tbody')
            .insertAdjacentHTML('beforeend', row);

	initSelect2(document.querySelector('#variantTable tbody'));
    variantIndex++;
});

document.addEventListener('click', function (e) {

    if (e.target.closest('.removeRow')) {

        let rows = document.querySelectorAll('#variantTable tbody tr');

        if (rows.length > 1) {
            e.target.closest('tr').remove();
			// destroy select2 before removing (clean memory)
            $(row).find('.select2').each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
            });

            row.remove();
        }
    }
});
</script>
@endsection