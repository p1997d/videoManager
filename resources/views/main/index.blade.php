@extends('layouts.main')

@section('title', 'Video Manager')

@section('content')
    <button class="position-fixed bottom-0 end-0 mb-4 me-4 btn btn-danger rounded-circle p-3 lh-1 z-3" type="button"
        data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i>
    </button>

    @include('main.modal')
    @include('main.video')
    <div id="forToast" aria-live="polite" aria-atomic="true" class="w-100 position-fixed bottom-0 start-0 mb-4 ms-4 z-2"></div>

@endsection
