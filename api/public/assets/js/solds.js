$(function () {
    $('a.action-delete').on('click', function (e) {
        e.preventDefault();

        $('#modal-delete').modal({backdrop: true, keyboard: true})
                .off('click', '#modal-delete-button')
                .on('click', '#modal-delete-button', function () {
                    $('#delete-form').trigger('submit');
                });
    });
         $('a.action-list').html("Volver");
});
$(".embedded-list th a").click(function (event) {
    event.preventDefault();
    event.stopPropagation()
});


