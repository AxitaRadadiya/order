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
			<div class="col-md-6">
				<div class="form-group">
					<label>Name <span class="text-danger">*</span></label>
					<input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control" required>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>SKU</label>
					<input type="text" name="sku" value="{{ old('sku', $item->sku) }}" class="form-control">
				</div>
			</div>

			<div class="col-md-12">
				<div class="form-group">
					<label>Description</label>
					<textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
				</div>
			</div>

			{{-- Category and Sub-category removed; sizes will be used instead --}}
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
					<label>Category</label>
					<select name="category_id" class="form-control">
						<option value="">-- Select --</option>
						@foreach($categories as $cat)
							<option value="{{ $cat->id }}" {{ (old('category_id', $item->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
						@endforeach
					</select>
				</div>
			</div>

			{{-- Sizes (master list) --}}
			<div class="col-md-12">
				<div class="form-group">
					<label>Sizes</label>
					@php
						// use sizes passed from controller, fallback to default list
						$sizesList = $sizes ?? [28,30,32,34,36,38,40,42,44,46,48];
						// support old input, or $item->sizes (array or comma-separated string)
						$selectedSizes = old('sizes', []);
						if (empty($selectedSizes)) {
							if (!empty($item->sizes)) {
								$selectedSizes = is_array($item->sizes) ? $item->sizes : explode(',', $item->sizes);
								$selectedSizes = array_map('trim', $selectedSizes);
							}
						}
					@endphp
					<div>
						@foreach($sizesList as $sz)
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="sizes[]" id="size_{{ $sz }}" value="{{ $sz }}" {{ in_array((string)$sz, array_map('strval', (array)$selectedSizes)) || in_array($sz, (array)$selectedSizes) ? 'checked' : '' }}>
								<label class="form-check-label" for="size_{{ $sz }}">{{ $sz }}</label>
							</div>
						@endforeach
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label>Price</label>
					<input type="number" step="0.01" name="price" value="{{ old('price', $item->price) }}" class="form-control">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label>Discount %</label>
					<input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $item->discount_percent) }}" class="form-control">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label>Tax %</label>
					<input type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', $item->tax_percent) }}" class="form-control">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label>Image</label>
					<input type="file" name="image" class="form-control-file">
					@if(!empty($item->image))
						<div class="mt-2"><img src="{{ asset('storage/' . $item->image) }}" alt="" style="max-width:120px;"></div>
					@endif
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label>Status</label>
					<select name="status" class="form-control">
						<option value="1" {{ (old('status', $item->status) == 1) ? 'selected' : '' }}>Active</option>
						<option value="0" {{ (old('status', $item->status) == 0) ? 'selected' : '' }}>Inactive</option>
					</select>
				</div>
			</div>
		  </div>

		  <div class="mt-3 text-right">
			<button class="btn btn-primary">Update</button>
			<a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
		  </div>
		</form>
	  </div>
	</div>
  </div>
</div>

@endsection
