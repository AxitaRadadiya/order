<style>
.upload-box {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 10px;
    background: #fafafa;
	min-height: 140px;
}

.preview-item {
    width: 120px;
    height: 120px;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-btn {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ff4d4f;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    cursor: pointer;
    font-size: 16px;
}

.upload-placeholder {
    width: 120px;
    height: 120px;
    border: 2px dashed #7F53AC;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #7F53AC;
    font-weight: 600;
    transition: 0.3s;
}

.upload-placeholder:hover {
    background: #f3efff;
}

/* Buttons */
.btn-submit {
    background: linear-gradient(90deg, #7F53AC, #647DEE);
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
}

.btn-cancel {
    background: #ddd;
    padding: 8px 20px;
    border-radius: 6px;
    color: #333;
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
		<div class="card card-outline card-primary">
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
						<div class="col-md-12">
    						<div class="form-group">
        						<label class="font-weight-bold">
           						 	Upload Images 
           							 <small class="text-muted">(Max 5 • JPG/PNG • 2MB)</small>
        						</label>

        						<div id="uploadBox" class="upload-box"></div>
									<input type="file" id="itemImages" name="images[]" accept="image/jpeg,image/png" multiple hidden>
									<small id="imageError" class="text-danger d-block mt-2"></small>
    							</div>
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
						<div class="col-md-6 d-flex align-items-end">
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

					<div class="row">
						<div class="col-12 mt-3 mb-1 text-right">
							<a href="{{ route('items.index') }}" class="btn-cancel mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
							<button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Save Item</button>
						</div>
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

	const fileInput  = document.getElementById('itemImages');
	const uploadBox  = document.getElementById('uploadBox');
	const errorBox   = document.getElementById('imageError');

    const MAX_FILES = 5;
    const MAX_SIZE  = 2 * 1024 * 1024;
    const ALLOWED_TYPES = ['image/jpeg', 'image/png'];

    let filesList = [];

    function renderPreview() {
        uploadBox.innerHTML = '';

        filesList.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'preview-item';

                div.innerHTML = `
                    <img src="${e.target.result}">
                    <button type="button" class="remove-btn">&times;</button>
                `;

                div.querySelector('.remove-btn').onclick = () => removeFile(index);

                uploadBox.appendChild(div);
            };

            reader.readAsDataURL(file);
        });

        renderPlaceholder();
    }

    function renderPlaceholder() {
        if (filesList.length >= MAX_FILES) return;

        const div = document.createElement('div');
        div.className = 'upload-placeholder';
        div.innerHTML = '+ Upload';

		div.onclick = () => {
			fileInput.value = '';
			fileInput.click();
		};

        uploadBox.appendChild(div);
    }

	fileInput.addEventListener('change', function () {
		handleFiles(this.files);
	});

    function handleFiles(files) {
        hideError();

        Array.from(files).forEach(file => {

            if (!ALLOWED_TYPES.includes(file.type)) {
                return showError('Only JPG & PNG allowed');
            }

            if (file.size > MAX_SIZE) {
                return showError('Max size 2MB');
            }

            if (filesList.length >= MAX_FILES) {
                return showError('Max 5 images allowed');
            }

            if (filesList.some(f => f.name === file.name && f.size === file.size)) {
                return showError('Duplicate image');
            }

            filesList.push(file);
        });

        syncInputs();
        renderPreview();
    }

    function removeFile(index) {
        filesList.splice(index, 1);
        syncInputs();
        renderPreview();
    }

	function syncInputs() {
		const dt = new DataTransfer();
		filesList.forEach(file => {
			dt.items.add(file);
		});
		fileInput.files = dt.files;
	}

    function showError(msg) {
        errorBox.textContent = msg;
    }

    function hideError() {
        errorBox.textContent = '';
    }

    renderPreview();
});
</script>