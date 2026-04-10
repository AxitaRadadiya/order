<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="">
         
          <a href="#" class="btn btn-success waves-effect waves-light btn-sm float-right state-date-modal">Add State</a>
         
        </h3>
      </div>
      <div class="card-body">
        <table id="StateTable" class="table dt-responsive nowrap">
          <thead>
            <tr>
                <th></th>
              <th>Sr No.</th>
              <th>Name</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
                <th></th>
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

<!-- Modal for adding Project Type -->
<div class="modal fade" id="StateModal" tabindex="-1" role="dialog" aria-labelledby="followUpPersonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followUpPersonLabel">State</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('state.store') }}" method="POST" id="stateForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="state_id" id="state_id">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" name="name" id="state_name" value="" required>
                            <span class="text-danger error state-name-error"></span> <!-- Error message will be inserted here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="saveState">Save</button>
            </div>
        </div>
    </div>
</div>
