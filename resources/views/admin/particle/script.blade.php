<!-- jQuery -->
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- ChartJS -->
{{-- <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script> --}}
<!-- Sparkline -->
<script src="{{ asset('admin/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
{{-- <script src="{{ asset('admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script> --}}
{{-- <script src="{{ asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> --}}
<!-- jQuery Knob Chart -->
<script src="{{ asset('admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('admin/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('admin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('admin/plugins/toastr/toastr.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('admin/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
{{-- <script src="{{ asset('admin/plugins/autocomplete/jquery.autocomplete.js') }}"></script> --}}
<!-- AdminLTE App -->
<script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
{{-- <script src="{{ asset('admin/dist/js/demo.js') }}"></script> --}}
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="{!!asset('admin/dist/js/numeric.js')!!}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>


<script>
    $(function () {
    })
</script>

<script>
$(document).ready(function () {

    $('.select2').select2();

    // Date range pickers
    $('.single_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: { format: 'MM/YYYY' }
    }).on('apply.daterangepicker', function (e, picker) {
        picker.element.val(picker.startDate.format(picker.locale.format));
    });

    $('.dob').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: { format: 'DD/MM/YYYY' }
    }).on('apply.daterangepicker', function (e, picker) {
        picker.element.val(picker.startDate.format(picker.locale.format));
    });

    bsCustomFileInput.init();

    var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

    @if (session('success'))
        Toast.fire({ icon: 'success', title: '{{ session('success') }}' })
    @endif
    @if (session('error'))
        Toast.fire({ icon: 'error', title: '{{ session('error') }}' })
    @endif
    @if (session('warning'))
        Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' })
    @endif

    $(document).on('click', '.deleteButton', function (event) {
        event.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });

    // Roles table
    $('#roleTable').DataTable({
        paging: true, lengthChange: false, searching: true, ordering: true, info: true,
        autoWidth: false, responsive: true, processing: true, serverSide: true,
        order: [0, 'desc'],
        ajax: { url: '{{ route('roles.list') }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}', route: 'roles.list' } },
        columns: [{ data: 'id' }, { data: 'name' }],
        aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
    });

    // Permissions table
    $('#permissionsTable').DataTable({
        paging: true, lengthChange: false, searching: true, ordering: true, info: true,
        autoWidth: false, responsive: true, processing: true, serverSide: true,
        order: [0, 'desc'],
        ajax: { url: '{{ route('permissions.list') }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}', route: 'permissions.list' } },
        columns: [{ data: 'id' }, { data: 'name' }, { data: 'action' }],
        aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
    });

    // Users table loader
    function load_user() {
        $('#userTable').DataTable({
            paging: true, lengthChange: false, searching: true, ordering: true, info: true,
            autoWidth: false, responsive: true, processing: true, serverSide: true,
            order: [0, 'desc'],
            ajax: {
                url: '{{ route('users.list') }}',
                dataType: 'json',
                type: 'GET',
                data: { _token: '{{csrf_token()}}' }
            },
            columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'mobile' },
            { data: 'role' },
            { data: 'status' },
            { data: 'action' }
        ]
            , aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
        });
    }
    function load_customer() {
        if ($.fn.dataTable.isDataTable('#customerTable')) {
            $('#customerTable').DataTable().clear().destroy();
            $('#customerTable tbody').empty();
        }
        $('#customerTable').DataTable({
            paging: true, lengthChange: false, searching: true, ordering: true, info: true,
            autoWidth: false, responsive: true, processing: true, serverSide: true,
            order: [0, 'desc'],
            ajax: {
                url: '{{ route('customers.list') }}',
                dataType: 'json',
                type: 'GET',
                data: { _token: '{{csrf_token()}}' }
            },
            columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'company_name' },
            { data: 'status' },
            { data: 'action' }
        ]
            , aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
        });
    }
    load_customer();

    function load_item() {
        $('#itemTable').DataTable({
            paging: true, lengthChange: false, searching: true, ordering: true, info: true,
            autoWidth: false, responsive: true, processing: true, serverSide: true,
            order: [0, 'desc'],
            ajax: {
                url: '{{ route('items.list') }}',
                dataType: 'json',
                type: 'GET',
                data: { _token: '{{csrf_token()}}' }
            },
            columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'sku' },
            { data: 'category' },
            { data: 'group' },
            { data: 'sizes' },
            { data: 'price' },
            { data: 'status' },
            { data: 'action' }
        ]
            , aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
        });
    }

    // Initialize users table if present on the page
    if ($('#userTable').length) {
        load_user();
    }
    if ($('#itemTable').length) {
        load_item();
    }
    if ($('#masterTab').length) {
 
        // Init all three tables on page load
        load_Country();
        load_State();
        load_City();
        load_Category();
        load_Group();
        load_Size();
 
        // Re-init table when tab is shown (DataTables needs this for hidden tabs)
        $('#masterTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            if (target === '#country') load_Country();
            if (target === '#state')   load_State();
            if (target === '#city')    load_City();
            if (target === '#category') load_Category();
            if (target === '#group')    load_Group();
            if (target === '#size')     load_Size();
        });
    }
 
    function load_Country() {
        if ($.fn.dataTable.isDataTable('#CountryTable')) {
            $('#CountryTable').DataTable().clear().destroy();
            $('#CountryTable tbody').empty();
        }
 
        $('#CountryTable').DataTable({
            paging:      true,
            lengthChange: true,
            searching:   true,
            ordering:    true,
            info:        true,
            autoWidth:   false,
            responsive:  true,
            processing:  true,
            serverSide:  true,
            order:       [[0, 'asc']],
            ajax: {
                url:      "{{ route('country.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            columns: [
                { data: 'id',     orderable: true  },
                { data: 'name',   orderable: true  },
                { data: 'action', orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    $(document).on('click', '.country-date-modal', function () {
        // Reset form for add mode
        $('#countryForm')[0].reset();
        $('#country_id').val('');
        $('#country_name').val('');
        $('.country-name-error').text('');
        $('input').removeClass('is-invalid');
 
        // Restore action to store route and remove any leftover _method=PUT
        $('#countryForm').attr('action', '{{ route("country.store") }}');
        $('#countryForm input[name="_method"]').remove();
 
        $('#CountryModal').modal('show');
    });

    $(document).on('click', '.edit-country-date-modal', function () {
        var countryId   = $(this).data('id');
        var countryName = $(this).data('name');
 
        // Reset errors
        $('.country-name-error').text('');
        $('input').removeClass('is-invalid');
 
        $('#country_id').val(countryId);
        $('#country_name').val(countryName);
 
        var updateUrl = '{{ route("country.update", ":id") }}'.replace(':id', countryId);
        $('#countryForm').attr('action', updateUrl);
 
        // Remove any existing _method field before adding a fresh one
        $('#countryForm input[name="_method"]').remove();
        $('#countryForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#CountryModal').modal('show');
    });
 
    $(document).on('click', '#saveCountry', function (e) {
        e.preventDefault();
 
        var name = $.trim($('#country_name').val());
 
        // Clear previous errors
        $('.error').text('');
        $('input').removeClass('is-invalid');
 
        if (!name) {
            $('#country_name').addClass('is-invalid');
            $('.country-name-error').text('Country Name is required.');
            return;
        }
 
        $('#countryForm').submit();
    });
 
    $('#CountryModal').on('hidden.bs.modal', function () {
        $('#countryForm input[name="_method"]').remove();
        $('#countryForm')[0].reset();
        $('.country-name-error').text('');
        $('input').removeClass('is-invalid');
    });
    
    function load_State() {
        if ($.fn.dataTable.isDataTable('#StateTable')) {
            $('#StateTable').DataTable().clear().destroy();
            $('#StateTable tbody').empty();
        }
 
        $('#StateTable').DataTable({
            paging:       true,
            lengthChange: true,
            searching:    true,
            ordering:     true,
            info:         true,
            autoWidth:    false,
            responsive:   true,
            processing:   true,
            serverSide:   true,
            order:        [[0, 'asc']],
            ajax: {
                url:      "{{ route('state.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            // Must match StateController@list keys: id, country, name, action
            columns: [
                { data: 'id',      orderable: true  },
                { data: 'country', orderable: false },
                { data: 'name',    orderable: true  },
                { data: 'action',  orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    // STATE — Add modal
    $(document).on('click', '.state-date-modal', function () {
        $('#stateForm')[0].reset();
        $('#state_id_hidden').val('');
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        $('#stateForm').attr('action', '{{ route("state.store") }}');
        $('#stateForm input[name="_method"]').remove();
 
        $('#StateModal').modal('show');
    });
 
    // STATE — Edit modal
    $(document).on('click', '.edit-state-date-modal', function () {
        var stateId    = $(this).data('id');
        var stateName  = $(this).data('name');
        var countryId  = $(this).data('country_id');
 
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        $('#state_id_hidden').val(stateId);
        $('#state_name').val(stateName);
        $('#state_country_id').val(countryId);
 
        var updateUrl = '{{ route("state.update", ":id") }}'.replace(':id', stateId);
        $('#stateForm').attr('action', updateUrl);
        $('#stateForm input[name="_method"]').remove();
        $('#stateForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#StateModal').modal('show');
    });
 
    // STATE — Save button
    $(document).on('click', '#saveState', function (e) {
        e.preventDefault();
 
        var name      = $.trim($('#state_name').val());
        var countryId = $('#state_country_id').val();
 
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        var hasError = false;
 
        if (!countryId) {
            $('#state_country_id').addClass('is-invalid');
            $('.state-country-error').text('Country is required.');
            hasError = true;
        }
        if (!name) {
            $('#state_name').addClass('is-invalid');
            $('.state-name-error').text('State Name is required.');
            hasError = true;
        }
 
        if (hasError) return;
 
        $('#stateForm').submit();
    });
 
    // STATE — Reset on modal close
    $('#StateModal').on('hidden.bs.modal', function () {
        $('#stateForm input[name="_method"]').remove();
        $('#stateForm')[0].reset();
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
    });
 
    function load_City() {
        if ($.fn.dataTable.isDataTable('#CityTable')) {
            $('#CityTable').DataTable().clear().destroy();
            $('#CityTable tbody').empty();
        }
 
        $('#CityTable').DataTable({
            paging:       true,
            lengthChange: true,
            searching:    true,
            ordering:     true,
            info:         true,
            autoWidth:    false,
            responsive:   true,
            processing:   true,
            serverSide:   true,
            order:        [[0, 'asc']],
            ajax: {
                url:      "{{ route('city.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            // Must match CityController@list keys: id, country, state, name, action
            columns: [
                { data: 'id',      orderable: true  },
                { data: 'country', orderable: false },
                { data: 'state',   orderable: false },
                { data: 'name',    orderable: true  },
                { data: 'action',  orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    // CITY — AJAX: load states when country changes in City modal
    $(document).on('change', '#city_country_id', function () {
        var countryId = $(this).val();
        var stateSelect = $('#city_state_id');
 
        stateSelect.html('<option value="">Loading...</option>');
 
        if (!countryId) {
            stateSelect.html('<option value="">Select State</option>');
            return;
        }
 
        $.ajax({
            url:      "{{ route('states.by.country') }}",
            type:     "GET",
            data:     { country_id: countryId, _token: "{{ csrf_token() }}" },
            success: function (data) {
                var options = '<option value="">Select State</option>';
                $.each(data, function (i, state) {
                    options += '<option value="' + state.id + '">' + state.name + '</option>';
                });
                stateSelect.html(options);
            },
            error: function () {
                stateSelect.html('<option value="">Select State</option>');
            }
        });
    });
 
    // CITY — Add modal
    $(document).on('click', '.city-date-modal', function () {
        $('#cityForm')[0].reset();
        $('#city_id_hidden').val('');
        $('#city_state_id').html('<option value="">Select State</option>');
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        $('#cityForm').attr('action', '{{ route("city.store") }}');
        $('#cityForm input[name="_method"]').remove();
 
        $('#CityModal').modal('show');
    });
 
    // CITY — Edit modal
    $(document).on('click', '.edit-city-date-modal', function () {
        var cityId    = $(this).data('id');
        var cityName  = $(this).data('name');
        var countryId = $(this).data('country_id');
        var stateId   = $(this).data('state_id');
 
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        $('#city_id_hidden').val(cityId);
        $('#city_name').val(cityName);
 
        // Set country first, then load states and set the state
        $('#city_country_id').val(countryId).trigger('change');
 
        // After states load via AJAX, set the correct state
        var stateCheck = setInterval(function () {
            if ($('#city_state_id option').length > 1) {
                $('#city_state_id').val(stateId);
                clearInterval(stateCheck);
            }
        }, 100);
 
        var updateUrl = '{{ route("city.update", ":id") }}'.replace(':id', cityId);
        $('#cityForm').attr('action', updateUrl);
        $('#cityForm input[name="_method"]').remove();
        $('#cityForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#CityModal').modal('show');
    });
 
    // CITY — Save button
    $(document).on('click', '#saveCity', function (e) {
        e.preventDefault();
 
        var name      = $.trim($('#city_name').val());
        var countryId = $('#city_country_id').val();
        var stateId   = $('#city_state_id').val();
 
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
 
        var hasError = false;
 
        if (!countryId) {
            $('#city_country_id').addClass('is-invalid');
            $('.city-country-error').text('Country is required.');
            hasError = true;
        }
        if (!stateId) {
            $('#city_state_id').addClass('is-invalid');
            $('.city-state-error').text('State is required.');
            hasError = true;
        }
        if (!name) {
            $('#city_name').addClass('is-invalid');
            $('.city-name-error').text('City Name is required.');
            hasError = true;
        }
 
        if (hasError) return;
 
        $('#cityForm').submit();
    });
 
    // CITY — Reset on modal close
    $('#CityModal').on('hidden.bs.modal', function () {
        $('#cityForm input[name="_method"]').remove();
        $('#cityForm')[0].reset();
        $('#city_state_id').html('<option value="">Select State</option>');
        $('.error').text('');
        $('input, select').removeClass('is-invalid');
    });

        function load_Category() {
        if ($.fn.dataTable.isDataTable('#CategoryTable')) {
            $('#CategoryTable').DataTable().clear().destroy();
            $('#CategoryTable tbody').empty();
        }
 
        $('#CategoryTable').DataTable({
            paging:      true,
            lengthChange: true,
            searching:   true,
            ordering:    true,
            info:        true,
            autoWidth:   false,
            responsive:  true,
            processing:  true,
            serverSide:  true,
            order:       [[0, 'asc']],
            ajax: {
                url:      "{{ route('category.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            columns: [
                { data: 'id',     orderable: true  },
                { data: 'name',   orderable: true  },
                { data: 'action', orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    $(document).on('click', '.category-date-modal', function () {
        // Reset form for add mode
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#category_name').val('');
        $('.category-name-error').text('');
        $('input').removeClass('is-invalid');
 
        // Restore action to store route and remove any leftover _method=PUT
        $('#categoryForm').attr('action', '{{ route("category.store") }}');
        $('#categoryForm input[name="_method"]').remove();

        $('#CategoryModal').modal('show');
    });

    $(document).on('click', '.edit-category-date-modal', function () {
        var categoryId   = $(this).data('id');
        var categoryName = $(this).data('name');
 
        // Reset errors
        $('.category-name-error').text('');
        $('input').removeClass('is-invalid');
 
        $('#category_id').val(categoryId);
        $('#category_name').val(categoryName);
 
        var updateUrl = '{{ route("category.update", ":id") }}'.replace(':id', categoryId);
        $('#categoryForm').attr('action', updateUrl);
 
        // Remove any existing _method field before adding a fresh one
        $('#categoryForm input[name="_method"]').remove();
        $('#categoryForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#CategoryModal').modal('show');
    });
 
    $(document).on('click', '#saveCategory', function (e) {
        e.preventDefault();
 
        var name = $.trim($('#category_name').val());
 
        // Clear previous errors
        $('.error').text('');
        $('input').removeClass('is-invalid');
 
        if (!name) {
            $('#category_name').addClass('is-invalid');
            $('.category-name-error').text('Category Name is required.');
            return;
        }
 
        $('#categoryForm').submit();
    });
 
    $('#CategoryModal').on('hidden.bs.modal', function () {
        $('#categoryForm input[name="_method"]').remove();
        $('#categoryForm')[0].reset();
        $('.category-name-error').text('');
        $('input').removeClass('is-invalid');
    });

    function load_Group() {
        if ($.fn.dataTable.isDataTable('#GroupTable')) {
            $('#GroupTable').DataTable().clear().destroy();
            $('#GroupTable tbody').empty();
        }
 
        $('#GroupTable').DataTable({
            paging:      true,
            lengthChange: true,
            searching:   true,
            ordering:    true,
            info:        true,
            autoWidth:   false,
            responsive:  true,
            processing:  true,
            serverSide:  true,
            order:       [[0, 'asc']],
            ajax: {
                url:      "{{ route('group.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            columns: [
                { data: 'id',     orderable: true  },
                { data: 'name',   orderable: true  },
                { data: 'action', orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    $(document).on('click', '.group-date-modal', function () {
        // Reset form for add mode
        $('#groupForm')[0].reset();
        $('#group_id').val('');
        $('#group_name').val('');
        $('.group-name-error').text('');
        $('input').removeClass('is-invalid');
 
        // Restore action to store route and remove any leftover _method=PUT
        $('#groupForm').attr('action', '{{ route("group.store") }}');
        $('#groupForm input[name="_method"]').remove();
 
        $('#GroupModal').modal('show');
    });

    $(document).on('click', '.edit-group-date-modal', function () {
        var groupId   = $(this).data('id');
        var groupName = $(this).data('name');
 
        // Reset errors
        $('.group-name-error').text('');
        $('input').removeClass('is-invalid');
 
        $('#group_id').val(groupId);
        $('#group_name').val(groupName);
 
        var updateUrl = '{{ route("group.update", ":id") }}'.replace(':id', groupId);
        $('#groupForm').attr('action', updateUrl);
 
        // Remove any existing _method field before adding a fresh one
        $('#groupForm input[name="_method"]').remove();
        $('#groupForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#GroupModal').modal('show');
    });
 
    $(document).on('click', '#saveGroup', function (e) {
        e.preventDefault();
 
        var name = $.trim($('#group_name').val());
 
        // Clear previous errors
        $('.error').text('');
        $('input').removeClass('is-invalid');
 
        if (!name) {
            $('#group_name').addClass('is-invalid');
            $('.group-name-error').text('Group Name is required.');
            return;
        }
 
        $('#groupForm').submit();
    });
 
    $('#GroupModal').on('hidden.bs.modal', function () {
        $('#groupForm input[name="_method"]').remove();
        $('#groupForm')[0].reset();
        $('.group-name-error').text('');
        $('input').removeClass('is-invalid');
    });

    function load_Size() {
        if ($.fn.dataTable.isDataTable('#SizeTable')) {
            $('#SizeTable').DataTable().clear().destroy();
            $('#SizeTable tbody').empty();
        }
 
        $('#SizeTable').DataTable({
            paging:      true,
            lengthChange: true,
            searching:   true,
            ordering:    true,
            info:        true,
            autoWidth:   false,
            responsive:  true,
            processing:  true,
            serverSide:  true,
            order:       [[0, 'asc']],
            ajax: {
                url:      "{{ route('size.list') }}",
                dataType: "json",
                type:     "GET",
                data:     { _token: "{{ csrf_token() }}" }
            },
            columns: [
                { data: 'id',     orderable: true  },
                { data: 'name',   orderable: true  },
                { data: 'action', orderable: false }
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next:     "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
 
    $(document).on('click', '.size-date-modal', function () {
        // Reset form for add mode
        $('#sizeForm')[0].reset();
        $('#size_id').val('');
        $('#size_name').val('');
        $('.size-name-error').text('');
        $('input').removeClass('is-invalid');
 
        // Restore action to store route and remove any leftover _method=PUT
        $('#sizeForm').attr('action', '{{ route("size.store") }}');
        $('#sizeForm input[name="_method"]').remove();

        $('#SizeModal').modal('show');
    });

    $(document).on('click', '.edit-size-date-modal', function () {
        var sizeId   = $(this).data('id');
        var sizeName = $(this).data('name');
 
        // Reset errors
        $('.size-name-error').text('');
        $('input').removeClass('is-invalid');
 
        $('#size_id').val(sizeId);
        $('#size_name').val(sizeName);
 
        var updateUrl = '{{ route("size.update", ":id") }}'.replace(':id', sizeId);
        $('#sizeForm').attr('action', updateUrl);
 
        // Remove any existing _method field before adding a fresh one
        $('#sizeForm input[name="_method"]').remove();
        $('#sizeForm').append('<input type="hidden" name="_method" value="PUT">');
 
        $('#SizeModal').modal('show');
    });
 
    $(document).on('click', '#saveSize', function (e) {
        e.preventDefault();
 
        var name = $.trim($('#size_name').val());
 
        // Clear previous errors
        $('.error').text('');
        $('input').removeClass('is-invalid');
 
        if (!name) {
            $('#size_name').addClass('is-invalid');
            $('.size-name-error').text('Size Name is required.');
            return;
        }
 
        $('#sizeForm').submit();
    });
 
    $('#SizeModal').on('hidden.bs.modal', function () {
        $('#sizeForm input[name="_method"]').remove();
        $('#sizeForm')[0].reset();
        $('.size-name-error').text('');
        $('input').removeClass('is-invalid');
    });



});
</script>

<script>
function togglePw(inputId, iconId) {
    var inp = document.getElementById(inputId);
    var ico = document.getElementById(iconId);

    if (!inp || !ico) {
        return;
    }

    if (inp.type === 'password') {
        inp.type = 'text';
        ico.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        ico.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function toggleGroup(btn) {
    const card = btn ? btn.closest('.permission-card') : null;
    const cardBody = card ? card.querySelector('.permission-card-body') : null;

    if (!cardBody) {
        return;
    }

    const checkboxes = cardBody.querySelectorAll('.perm-chk');
    const allChecked = [...checkboxes].every(c => c.checked);
    checkboxes.forEach(c => {
        c.checked = !allChecked;
    });

    if (btn) {
        btn.textContent = allChecked ? 'All' : 'None';
    }

    updateAssignedBadge();
}

function selectAll(state) {
    document.querySelectorAll('.perm-chk').forEach(c => {
        c.checked = state;
    });

    document.querySelectorAll('.group-toggle-btn').forEach(b => {
        b.textContent = state ? 'None' : 'All';
    });

    updateAssignedBadge();
}

function updateAssignedBadge() {
    const badge = document.getElementById('assignedBadge');

    if (!badge) {
        return;
    }

    const count = document.querySelectorAll('.perm-chk:checked').length;
    badge.textContent = count + ' assigned';
}

document.addEventListener('change', function (e) {
    if (!(e.target && e.target.classList.contains('perm-chk'))) {
        return;
    }

    updateAssignedBadge();

    const cardBody = e.target.closest('.permission-card-body');
    const card = e.target.closest('.permission-card');

    if (!cardBody || !card) {
        return;
    }

    const checkboxes = cardBody.querySelectorAll('.perm-chk');
    const allChecked = [...checkboxes].every(c => c.checked);
    const toggleBtn = card.querySelector('.group-toggle-btn');

    if (toggleBtn) {
        toggleBtn.textContent = allChecked ? 'None' : 'All';
    }
});
</script>
