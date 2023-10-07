@php
    use Carbon\Carbon;
    Carbon::setLocale('ru');
@endphp
<div class="container pt-3 videos">
    @if (count($videos) != 0)
        <div class="d-flex flex-wrap">
            @foreach ($videos as $video)
                <div class="card m-2" style="width: 18rem;">
                    <div class="card-body">
                        <img src="{{ asset("upload/preview/$video->id.jpg") }}" class="card-img-top" alt="preview">
                    </div>
                    <div class="card-footer">
                        <h5 class="card-title">{{ $video->title }}</h5>
                        <small>
                            <span class="card-subtitle text-body-secondary fw-light">
                                {{ Carbon::create()->seconds($video->duration)->format('H:i:s') }}
                            </span>
                            <span class="card-subtitle text-body-secondary fw-light">|</span>
                            <span class="card-subtitle text-body-secondary fw-light">
                                {{ round($video->size / 1048576, 2) }} МБ
                            </span>
                            <span class="card-subtitle text-body-secondary fw-light">|</span>
                            <span class="card-subtitle text-body-secondary fw-light">
                                {{ Carbon::parse($video->created_at)->diffForHumans() }}
                            </span>
                        </small>
                        <hr>
                        <div class="d-flex justify-content-evenly pb-2">
                            <a href="{{ asset("upload/480p/$video->id.mp4") }}"
                                class="btn btn-outline-light btn-sm changeColorButton changeOutlineColorButton">480p</a>
                            <a href="{{ asset("upload/1080p/$video->id.mp4") }}"
                                class="btn btn-outline-light btn-sm changeColorButton changeOutlineColorButton">1080p</a>
                            <form action="{{ route('main.file.remove', $video->id) }}" method="POST" class="fileRemoveForm">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger changeColorButton btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $videos->links('partials.pagination') }}
    @else
        <div class="card text-center">
            <div class="card-body">
                Здесь пока ничего нет.
            </div>
        </div>
    @endif
</div>
