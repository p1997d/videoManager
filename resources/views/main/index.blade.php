@extends('layouts.main')

@section('title', 'Video Manager')

@section('content')
    <button class="position-fixed bottom-0 end-0 mb-4 me-4 btn btn-danger rounded-circle p-3 lh-1 z-3" type="button"
        data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i>
    </button>

    @include('main.modal')
    <div id="videoContainer">
        @include('main.video')
    </div>
    <div id="forToast" aria-live="polite" aria-atomic="true" class="w-100 position-fixed bottom-0 start-0 mb-4 ms-4 z-2">
        <div class="toast emptyToast my-3" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header">
                <strong class="me-auto toastTitle"></strong>
                <div class="toast-close"></div>
            </div>
            <div class="toast-body">
                <h5>Загрузка</h5>
                <div class="progress" role="progressbar" aria-label="Animated striped example" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
