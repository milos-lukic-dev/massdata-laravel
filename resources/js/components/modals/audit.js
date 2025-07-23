import { Modal } from 'bootstrap';

$(document).ready(function () {
    $(document).on('click', '.btn-audits', function (e) {
        e.preventDefault();

        let $form = $(this).closest('form.audit-form');
        let route = $form.attr('action');
        let token = $form.find('input[name="_token"]').val();

        let modalEl = $('#auditModal');
        let $tbody = modalEl.find('tbody');

        $tbody.html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: route,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (data) {
                if (data.length === 0) {
                    $tbody.html('<tr><td colspan="7" class="text-center">No result.</td></tr>');
                    return;
                }

                let rows = '';
                data.forEach(log => {
                    rows += `<tr>
                        <td>${log.user_name ?? '-'}</td>
                        <td>${log.file_name ?? '-'}</td>
                        <td>${log.file_row ?? '-'}</td>
                        <td>${log.file_column ?? '-'}</td>
                        <td>${log.old_value ?? '-'}</td>
                        <td>${log.new_value ?? '-'}</td>
                        <td>${log.created_at}</td>
                    </tr>`;
                });

                $tbody.html(rows);
            },
            error: function () {
                $tbody.html('<tr><td colspan="7" class="text-center text-danger">Failed to load audits.</td></tr>');
            }
        });

        const modal = new Modal(modalEl[0]);
        modal.show();
    });
});
