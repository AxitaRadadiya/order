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
    // load_Dealstage();
    function load_Country(){

        var table = $('#CountryTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
        "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('country.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'country.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
             { "data": "action", "orderable": false }
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
    
    $(document).on('click', '.country-date-modal', function () {
        $('#countryForm').attr('action', '{{ route("country.store") }}');
        $('#CountryModal').modal('show');
    });

    $(document).on('click', '.edit-country-date-modal', function () {
        let countryId = $(this).data('id');
        let countryName = $(this).data('name');

        $("#country_id").val(countryId);
        $('#country_name').val(countryName);
        
        let updateUrl = '{{ route("country.update", ":id") }}'.replace(':id', countryId);
        $('#countryForm').attr('action', updateUrl);

        $('#countryForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#CountryModal').modal('show');

    });

    // Save the Country
    $(document).on('click', '#saveCountry', function (e) {
        e.preventDefault();

        // Get input value
        let country = $('#country_name').val();
        
        // Clear previous error messages
        $('.error').text('');
        $('input').removeClass('is-invalid');

        let errors = {};

        // Validate the input
        if (!country) {
            errors.country = "Country Name is required.";
        }
        
        // If errors exist, show them
        if (Object.keys(errors).length > 0) {
            if (errors.country) {
                $('#country_name').addClass('is-invalid');
                $('.country-name-error').text(errors.country);
            }
            return;
        }

        // Submit the form if no errors
        $('#countryForm').submit();
    });
    function load_State(){

        var table = $('#StateTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
        "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('state.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'state.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
             { "data": "action", "orderable": false }
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
    
    $(document).on('click', '.state-date-modal', function () {
        $('#stateForm').attr('action', '{{ route("state.store") }}');
        $('#StateModal').modal('show');
    });

    $(document).on('click', '.edit-state-date-modal', function () {
        let stateId = $(this).data('id');
        let stateName = $(this).data('name');

        $("#state_id").val(stateId);
        $('#state_name').val(stateName);
        
        let updateUrl = '{{ route("state.update", ":id") }}'.replace(':id', stateId);
        $('#stateForm').attr('action', updateUrl);

        $('#stateForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#StateModal').modal('show');

    });

    // Save the State
    $(document).on('click', '#saveState', function (e) {
        e.preventDefault();

        // Get input value
        let state = $('#state_name').val();
        
        // Clear previous error messages
        $('.error').text('');
        $('input').removeClass('is-invalid');

        let errors = {};

        // Validate the input
        if (!state) {
            errors.state = "State Name is required.";
        }
        
        // If errors exist, show them
        if (Object.keys(errors).length > 0) {
            if (errors.state) {
                $('#state_name').addClass('is-invalid');
                $('.state-name-error').text(errors.state);
            }
            return;
        }

        // Submit the form if no errors
        $('#stateForm').submit();
    });
     function load_City(){

        var table = $('#CityTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
        "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('city.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'city.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
             { "data": "action", "orderable": false }
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
    
    $(document).on('click', '.city-date-modal', function () {
        $('#cityForm').attr('action', '{{ route("city.store") }}');
        $('#CityModal').modal('show');
    });

    $(document).on('click', '.edit-city-date-modal', function () {
        let cityId = $(this).data('id');
        let cityName = $(this).data('name');

        $("#city_id").val(cityId);
        $('#city_name').val(cityName);
        
        let updateUrl = '{{ route("city.update", ":id") }}'.replace(':id', cityId);
        $('#cityForm').attr('action', updateUrl);

        $('#cityForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#CityModal').modal('show');

    });

    // Save the City
    $(document).on('click', '#saveCity', function (e) {
        e.preventDefault();

        // Get input value
        let city = $('#city_name').val();
        
        // Clear previous error messages
        $('.error').text('');
        $('input').removeClass('is-invalid');

        let errors = {};

        // Validate the input
        if (!city) {
            errors.city = "City Name is required.";
        }
        
        // If errors exist, show them
        if (Object.keys(errors).length > 0) {
            if (errors.city) {
                $('#city_name').addClass('is-invalid');
                $('.city-name-error').text(errors.city);
            }
            return;
        }

        // Submit the form if no errors
        $('#cityForm').submit();
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
