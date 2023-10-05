<div class="modal" tabindex="-1" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('main.file.upload') }}" method="POST" enctype="multipart/form-data" id="fileForm">
                <div class="modal-header">
                    @csrf
                    <h5 class="modal-title">Загрузка видео</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Название</label>
                        <input class="form-control" type="text" id="textInput" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Загрузите видеоролик (не более 15 сек.)</label>
                        <input class="form-control" type="file" id="fileInput" name="upload" accept="video/*"
                            required>
                        <small>
                            <p style="color: red" id="validationMessage"></p>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </div>

            </form>
        </div>
    </div>
</div>
