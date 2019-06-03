import "../../less/single-product.less";

$('.dokan-report-abuse-admin-single-product-delete-item').on('click', function (e) {
    e.preventDefault();

    const button = $(this);
    const id = button.data('id');
    const tr = button.parents('tr');
    const fieldset = button.parents('fieldset');

    if (! confirm('Are you sure you want to delete this report')) {
        return;
    }

    fieldset.prop('disabled', true);

    button.html(`<i class="fa fa-refresh fa-spin"></i> ${dokanReportAbuse.i18n.deleting}...`);

    $.ajax({
        url: `${dokanReportAbuse.rest.root}dokan/v1/abuse-reports/${id}`,
        method: 'post',
        beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', dokanReportAbuse.rest.nonce);
            xhr.setRequestHeader('X-HTTP-Method-Override', 'DELETE');
        },
    }).done((response) => {
        tr.remove();
        alert(dokanReportAbuse.i18n.deletedSuccessfully);
    }).fail((jqXHR) => {
        button.html(`<i class="fa fa-trash"></i> ${dokanReportAbuse.i18n.delete}`);

        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
            alert(jqXHR.responseJSON.message);
        }
    }).always(() => {
        fieldset.prop('disabled', false);
    });
});
