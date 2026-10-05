@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/layouts-guest.css')
@endpush

@section('content')
    <div class="guest-layout-wrapper">
        <div class="guest-card">
            {{ $slot }}
        </div>
    </div>
@endsection
