@props(['disabled' => false])

@php
    $name = $attributes->get('name');
@endphp

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'form-control' . ($name && $errors->has($name) ? ' is-invalid' : ''),
]) }}>
