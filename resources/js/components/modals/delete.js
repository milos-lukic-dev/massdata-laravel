import { Modal } from 'bootstrap';

$(document).ready(function() {
    let formToSubmit;
    const modalEl = document.getElementById('confirmDeleteModal');
    const modal = new Modal(modalEl);

    $('.btn-delete').on('click', function() {
        formToSubmit = $(this).closest('form');
        modal.show();
    });

    $('#confirmDeleteBtn').on('click', function() {
        if (formToSubmit) {
            formToSubmit.submit();
        }
    });
});
