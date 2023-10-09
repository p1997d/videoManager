$(document).ready(function () {
    ajaxFormHandler();
});

$(document).on('changePage', function () {
    ajaxFormHandler();
});

function ajaxFormHandler() {
    $('.fileUploadForm').ajaxForm({
        dataType: 'json',
        data: { 'id': generateID() },
        beforeSubmit: function (arr, form, options) {
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
        beforeSend: function () {
            $('.emptyToast')
                .clone()
                .appendTo("#forToast")
                .removeClass('emptyToast')
                .attr('id', $(this)[0].extraData.id)
                .toast('show')
                .find('.toastTitle')
                .text($('#textInput').val())
                .end()
                .find('.toastAction')
                .text('Загрузка');

            $('#addModal').modal('hide');
            var percentage = '0';
            $('#addModal').resetForm();
            ajaxFormHandler();
        },
        uploadProgress: function (event, position, total, percentComplete) {
            var percentage = percentComplete;
            $('#' + $(this)[0].extraData.id).find('.progress .progress-bar').css("width", percentage + '%', function () {
                return $(this).attr("aria-valuenow", percentage) + "%";
            })
        },
        error: function (xhr, status, error) {
            var response = xhr.responseJSON;
            $('#' + $(this)[0].extraData.id).find('.toastTitle').text('Ошибка');

            if (response && response.errors) {
                $('#' + $(this)[0].extraData.id).find('.progress').replaceWith(response.errors['video']);
            } else {
                $('#' + $(this)[0].extraData.id).find('.progress').replaceWith('Произошла ошибка: ' + error);
            }

            $('#validationMessage').text('');

            removeToastClickHandler($(this)[0].extraData.id);
        },
        success: function (xhr) {
            $('#videoContainer').html(xhr.videos).trigger('changePage');
            $('#' + $(this)[0].extraData.id).find('.progress').replaceWith(xhr.message);

            removeToastClickHandler($(this)[0].extraData.id);
        }
    });
    $('.fileRemoveForm').ajaxForm({
        beforeSend: function () {
            ajaxFormHandler();
        },
        success: function (xhr) {
            $('.emptyToast')
                .clone()
                .appendTo("#forToast")
                .removeClass('emptyToast')
                .attr('id', xhr.id)
                .toast('show')
                .find('.toastTitle')
                .text(xhr.title)
                .end()
                .find('.toastAction')
                .text('Удаление')
                .end()
                .find('.progress')
                .replaceWith(xhr.message);

            $('#videoContainer').html(xhr.videos).trigger('changePage');
            removeToastClickHandler(xhr.id)
        }
    });
}

function removeToastClickHandler(element) {
    $('#' + element).find('.toast-close').replaceWith(
        '<button type="button" class="btn-close removeToast" aria-label="Close"></button> '
    );
    $(".removeToast").off('click').on("click", function () {
        $(this).closest('.toast').hide('slow', function () { $(this).closest('.toast').remove(); });

    });
    setTimeout(() => {
        $('#' + element).hide('slow', function () { $('#' + element).remove(); });
    }, 10000)
}

function generateID() {
    var length = 10,
        charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    res = '';
    for (var i = 0, n = charset.length; i < length; ++i) {
        res += charset.charAt(Math.floor(Math.random() * n));
    }
    return res;
}
