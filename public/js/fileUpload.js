$(document).ready(function () {
    startAjaxForm();
});

$(document).on('changePage', function () {
    startAjaxForm();
});

function startAjaxForm() {
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
            $('.emptyToast').clone().appendTo("#forToast").removeClass('emptyToast').attr('id', $(this)[0].extraData.id).toast('show').find('.toastTitle').text($('#textInput').val());
            $('#addModal').modal('hide');
            var percentage = '0';
            $('#addModal').resetForm();
            startAjaxForm()
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

            removeToastClickHandler($(this));
        },
        success: function (xhr) {
            $('#videoContainer').html(xhr.videos).trigger('changePage');
            $('#' + $(this)[0].extraData.id).find('.progress').replaceWith(xhr.message);

            removeToastClickHandler($(this));
        }
    });
    $('.fileRemoveForm').ajaxForm({
        success: function (xhr) {
            $('#videoContainer').html(xhr.videos).trigger('changePage');
        }
    });
}

function removeToastClickHandler(element) {
    $('#' + element[0].extraData.id).find('.toast-close').replaceWith(
        '<button type="button" class="btn-close removeToast" aria-label="Close"></button> '
    );
    $(".removeToast").off('click').on("click", function () {
        $(this).closest('.toast').hide('slow', function(){ $(this).closest('.toast').remove(); });

    });
    setTimeout(() => {
        $('#' + element[0].extraData.id).hide('slow', function () { $('#' + element[0].extraData.id).remove(); });
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
