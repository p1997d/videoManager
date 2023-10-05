<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
        integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>

    <!-- jQuery PJAX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js"
        integrity="sha512-7G7ueVi8m7Ldo2APeWMCoGjs4EjXDhJ20DrPglDQqy8fnxsFQZeJNtuQlTT0xoBQJzWRFp4+ikyMdzDOcW36kQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- jQuery Cookie -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"
        integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- jQuery Form -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script src="{{ asset('js/theme.js') }}"></script>
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
</head>

<body>
    @include('layouts.header')
    <main>
        @yield('content')
    </main>
</body>

<script>
    $(function() {
        $(document).ready(function() {
            $('#fileForm').ajaxForm({
                dataType: 'json',
                beforeSubmit: function(arr, $form, options) {
                    const videoInput = $('#fileInput')[0];
                    if (!videoInput.files || videoInput.files.length === 0) {
                        $('#validationMessage').text('Пожалуйста, выберите видеофайл.');
                        return false;
                    }
                    const videoFile = videoInput.files[0];
                    const maxSizeMB = 10;
                    if (!(/video\/\w+/g).test(videoFile.type)) {
                        $('#validationMessage').text(
                            `Выбранный файл не является видеофайлом. Пожалуйста, выберите видеофайл.`
                        );
                        return false;
                    }
                    if (videoFile.size > maxSizeMB * 1024 * 1024) {
                        $('#validationMessage').text(
                            `Размер файла слишком большой (максимальный размер: ${maxSizeMB} MB).`
                        );
                        return false;
                    }
                },
                beforeSend: function() {
                    $('#addModal').modal('hide');
                    $('#forToast').append(`
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                            <div class="toast-header">
                                <strong class="me-auto toastTitle">${$('#textInput').val()}</strong>
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
                    `);
                    $('.toast').toast('show');
                    var percentage = '0';
                    $('#addModal').resetForm();
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentage = percentComplete;
                    $('.progress .progress-bar').css("width", percentage + '%', function() {
                        return $(this).attr("aria-valuenow", percentage) + "%";
                    })
                },
                error: function(xhr, status, error) {
                    var response = xhr.responseJSON;
                    $('.toastTitle').text('Ошибка');
                    $('.toast-close').replaceWith(
                        '<button type="button" class="btn-close removeToast" aria-label="Close"></button> '
                    );
                    $(".removeToast").off('click').on("click", function() {
                        $(this).closest('.toast').remove();
                    });
                    if (response && response.errors) {
                        $('.progress').replaceWith(response.errors['upload']);
                    } else {
                        $('.progress').replaceWith('Произошла ошибка: ' + error);
                    }
                    $('#validationMessage').text('');
                },
                success: function(xhr) {
                    $.pjax.reload({
                        container: '#videos'
                    });
                }
            });
        });

    });
</script>

</html>
