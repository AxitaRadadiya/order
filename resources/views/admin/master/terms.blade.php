<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Terms & Conditions</h5>
            </div>
            <div class="card-body">

                <form action="{{ route('master.settings.save') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Terms & Conditions</label>
                        <textarea name="terms" class="form-control" rows="10">{{ old('terms', $terms ?? '') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Terms & Conditions</button>
                </form>
            </div>
        </div>
    </div>
</div>
