{{-- City Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Sub-Category</h5>
                <div class="ml-auto">
                    <a href="#" class="btn btn-success btn-sm waves-effect waves-light sub-category-date-modal">
                        <i class="fa fa-plus"></i> Add Sub-Category
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="SubCategoryTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Sub-Category Modal --}}
<div class="modal fade" id="SubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="SubCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SubCategoryModalLabel">Sub-Category</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sub-category.store') }}" method="POST" id="subCategoryForm">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id_hidden">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error sub-category-category-error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="sub_category_name" required>
                                <span class="text-danger error sub-category-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="saveSubCategory">Save</button>
            </div>
        </div>
    </div>
</div>