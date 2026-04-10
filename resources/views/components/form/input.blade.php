<div class="form-group {{ $class ?? '' }}">
    @if(! empty($label))
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ old($name, $value ?? '') }}" class="form-control">
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
