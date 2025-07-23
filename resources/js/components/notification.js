$(document).ready(function() {
    $('#custom-notification').on('click', function() {
        $(this).addClass('hide');
        setTimeout(() => { $(this).remove(); }, 500);
    });

    setTimeout(() => {
        $('#custom-notification').addClass('hide');
        setTimeout(() => { $('#custom-notification').remove(); }, 500);
    }, 5000);
});
