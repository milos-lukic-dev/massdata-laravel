import 'jquery-ui/dist/jquery-ui';

$(document).ready(function () {
    $("#permission_user_search").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: $('#permission_user_search').data('search-url'),
                dataType: "json",
                method: "POST",
                data: {
                    _token: $('#permission_search_form').find('input[name="_token"]').val(),
                    q: request.term
                },
                success: function (data) {
                    response(data.map(user => ({
                        label: user.email,
                        value: user.email,
                        id: user.id
                    })));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            $("#permission_user_search").val(ui.item.label);
            $("#permission_user_id").val(ui.item.id);
            return false;
        }
    });
});
