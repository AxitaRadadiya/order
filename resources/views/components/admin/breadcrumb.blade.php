@if (isset($breadcrumb) && is_array($breadcrumb))
    <nav class="breadcrumb">
        <ol>
            @foreach($breadcrumb as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    </nav>
@endif
