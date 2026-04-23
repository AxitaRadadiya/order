<style>
	.upload-box {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.preview-item {
    width: 120px;
    height: 120px;
    position: relative;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: red;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
}

.upload-placeholder {
    width: 120px;
    height: 120px;
    border: 2px dashed #7F53AC;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
</style>
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
								<input type="text" name="item_code" value="{{ old('item_code', $generatedItemCode ?? '') }}" class="form-control" readonly>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label>Description</label>
								<textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Group</label>
								<select name="group_id" class="form-control">
									<option value="">-- Select --</option>
									@foreach($groups as $g)
									<option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
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
									<option value="{{ $subG->id }}" {{ old('sub_group') == $subG->id ? 'selected' : '' }}>{{ $subG->name }}</option>
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
									<option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
									<option value="{{ $subCat->id }}" {{ old('sub_category') == $subCat->id ? 'selected' : '' }}>{{ $subCat->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Colors</label>
								<select name="colors[]" class="form-control" multiple>
									@foreach($colors as $color)
									<option value="{{ $color->id }}" {{ in_array($color->id, (array) old('colors', [])) ? 'selected' : '' }}>{{ $color->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						{{-- Sizes --}}
						<div class="col-md-12">
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
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>MRP</label>
								<input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" class="form-control @error('price') is-invalid @enderror">
								@error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Tax %</label>
								<input type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', 0) }}" class="form-control">
							</div>
						</div>

						{{-- ✅ MULTIPLE IMAGES: up to 5, jpg/png only, max 2 MB each --}}
		<div class="col-md-12">
    <div class="form-group">
        <label>Upload Images (Max 5 • JPG/PNG • 2MB)</label>

        <!-- Preview Box -->
        <div id="uploadBox" class="upload-box"></div>

        <!-- Hidden Input -->
        <input type="file" id="itemImages" accept="image/*" multiple hidden>

        <!-- Laravel Real Inputs -->
        <div id="realInputs"></div>

        <!-- Error -->
        <div id="imageError" class="text-danger small"></div>
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

@push('pageScript')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const fileInput   = document.getElementById('itemImages');
    const uploadBox   = document.getElementById('uploadBox');
    const errorBox    = document.getElementById('imageError');
    const realInputs  = document.getElementById('realInputs');

    const MAX_FILES = 5;
    const MAX_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png'];

    let filesList = [];

    /* =============================
       Render UI
    ============================= */
    function renderPreview() {
        uploadBox.innerHTML = '';

        filesList.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const preview = document.createElement('div');
                preview.className = 'preview-item';

                preview.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <button type="button" class="remove-btn">&times;</button>
                `;

                preview.querySelector('.remove-btn').addEventListener('click', () => {
                    removeFile(index);
                });

                uploadBox.appendChild(preview);
            };

            reader.readAsDataURL(file);
        });

        renderUploadPlaceholder();
    }

    /* =============================
       Upload Placeholder
    ============================= */
    function renderUploadPlaceholder() {
    if (filesList.length >= MAX_FILES) return;

    const placeholder = document.createElement('div');
    placeholder.className = 'upload-placeholder';

    placeholder.innerHTML = `
        <div>Click to Upload</div>
    `;

    placeholder.addEventListener('click', () => fileInput.click());

    uploadBox.appendChild(placeholder);
}

    /* =============================
       Handle File Selection
    ============================= */
    fileInput.addEventListener('change', function () {
        handleFiles(this.files);
        fileInput.value = '';
    });

    function handleFiles(selectedFiles) {
        hideError();

        Array.from(selectedFiles).forEach(file => {

            if (!ALLOWED_TYPES.includes(file.type)) {
                return showError('Only JPG & PNG allowed');
            }

            if (file.size > MAX_SIZE) {
                return showError('Max file size is 2MB');
            }

            if (filesList.length >= MAX_FILES) {
                return showError('Maximum 5 images allowed');
            }

            if (filesList.some(f => f.name === file.name && f.size === file.size)) {
                return showError('Duplicate image not allowed');
            }

            filesList.push(file);
        });

        syncInputs();
        renderPreview();
    }

    /* =============================
       Remove File
    ============================= */
    function removeFile(index) {
        filesList.splice(index, 1);
        syncInputs();
        renderPreview();
    }

    /* =============================
       Sync with Laravel Input
    ============================= */
    function syncInputs() {
        realInputs.innerHTML = '';

        const dataTransfer = new DataTransfer();

        filesList.forEach(file => {
            dataTransfer.items.add(file);

            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'images[]';

            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            realInputs.appendChild(input);
        });

        fileInput.files = dataTransfer.files;
    }

    /* =============================
       Error Handling
    ============================= */
    function showError(message) {
        errorBox.textContent = message;
        errorBox.style.display = 'block';
    }

    function hideError() {
        errorBox.style.display = 'none';
    }

    /* =============================
       Init
    ============================= */
    renderPreview();

});
</script>