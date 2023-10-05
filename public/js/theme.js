$(document).ready(function () {
    theme();
});

$(document).on('pjax:end', function () {
    theme();
});

function theme() {
    $('#btnSwitch').on("click", function () {
        if ($('html').attr('data-bs-theme') == 'light') {
            setTheme('dark');
        }
        else {
            setTheme('light');
        }
    })

    if (!$.cookie("theme")) {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            setTheme('dark');
        }
        else {
            setTheme('light');
        }
    } else {
        setTheme($.cookie("theme"));
    }
}

function setTheme(theme) {
    if (theme == 'dark') {
        $('html').attr('data-bs-theme', 'dark');
        $('#btnSwitch').html('<i class="bi bi-moon-stars-fill"></i>');
        $('.changeColorButton').addClass('btn-dark').removeClass('btn-light');
        $('.changeOutlineColorButton').addClass('btn-outline-light').removeClass('btn-outline-dark');
        $('.changeColorText').addClass('text-light').removeClass('text-dark');
        $('.changeColorLink').addClass('link-light').removeClass('link-dark');
    } else {
        $('html').attr('data-bs-theme', 'light');
        $('#btnSwitch').html('<i class="bi bi-sun-fill"></i>');
        $('.changeColorButton').addClass('btn-light').removeClass('btn-dark');
        $('.changeOutlineColorButton').addClass('btn-outline-dark').removeClass('btn-outline-light');
        $('.changeColorText').addClass('text-dark').removeClass('text-light');
        $('.changeColorLink').addClass('link-dark').removeClass('link-light');
    }
    $.cookie("theme", theme)
}
