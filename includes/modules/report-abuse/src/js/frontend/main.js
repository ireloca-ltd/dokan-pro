dokan.reportAbuse = {
    button: null,
    form_html: '',
    flashMessage: '',

    init() {
        const self = this;

        $('.dokan-report-abuse-button').on('click', function (e) {
            e.preventDefault();

            self.button = this;

            if (dokanReportAbuse.reported_by_logged_in_users_only === 'on' && ! dokanReportAbuse.is_user_logged_in) {
                return $('body').trigger('dokan:login_form_popup:show');
            }

            self.getForm();
        });

        $('body').on('dokan:login_form_popup:fetching_form', () => {
            self.showLoadingAnim();
        });

        $('body').on('dokan:login_form_popup:fetched_form', () => {
            self.stopLoadingAnim();
        });

        $('body').on('dokan:login_form_popup:logged_in', (e, response) => {
            dokanReportAbuse.is_user_logged_in = true;
            dokanReportAbuse.nonce = response.data.dokan_report_abuse_nonce;
            self.getForm();
        });

        $('body').on( 'submit', '#dokan-report-abuse-form-popup form', function(e) {
            e.preventDefault();
            self.submitForm(this);
        });
    },

    showLoadingAnim() {
        $(this.button)
            .addClass('working')
            .children('i')
            .removeClass('fa-flag')
            .addClass('fa-spin fa-refresh');
    },

    stopLoadingAnim() {
        $(this.button)
            .removeClass('working')
            .children('i')
            .removeClass('fa-spin fa-refresh')
            .addClass('fa-flag');
    },

    submittingForm() {
        $('#dokan-report-abuse-form-popup fieldset').prop('disabled', true);
        $('#dokan-report-abuse-form-submit-btn').addClass('dokan-hide');
        $('#dokan-report-abuse-form-working-btn').removeClass('dokan-hide');
    },

    submittedForm() {
        $('#dokan-report-abuse-form-popup fieldset').prop('disabled', false);
        $('#dokan-report-abuse-form-submit-btn').removeClass('dokan-hide');
        $('#dokan-report-abuse-form-working-btn').addClass('dokan-hide');
    },

    getForm() {
        const self = this;

        if (self.form_html) {
            self.showPopup();
            return;
        }

        self.showLoadingAnim();

        $.ajax({
            url: dokan.ajaxurl,
            method: 'get',
            dataType: 'json',
            data: {
                _wpnonce: dokanReportAbuse.nonce,
                action: 'dokan_report_abuse_get_form'
            }
        }).done((response) => {
            self.form_html = response.data;
            self.showPopup();
        }).always(() => {
            self.stopLoadingAnim();
        });
    },

    showPopup() {
        const self = this;

        $.magnificPopup.open({
            items: {
                src: self.form_html,
                type: 'inline'
            },

            callbacks: {
                afterClose: () => {
                    self.afterPopupClose();
                }
            }
        });
    },

    afterPopupClose() {
        if (this.flashMessage) {
            alert(this.flashMessage);
            this.flashMessage = '';
        }
    },

    submitForm(form) {
        const self = this;

        const form_data = $(form).serialize();
        const error_section = $('.dokan-popup-error', '#dokan-report-abuse-form-popup');

        error_section.removeClass('has-error').text('');

        self.submittingForm();

        $.ajax({
            url: dokan.ajaxurl,
            method: 'post',
            dataType: 'json',
            data: {
                _wpnonce: dokanReportAbuse.nonce,
                action: 'dokan_report_abuse_submit_form',
                form_data: {
                    reason: $(form).find('[name="reason"]:checked').val(),
                    product_id: dokanReportAbuse.product_id,
                    customer_name: $(form).find('[name="customer_name"]').val(),
                    customer_email: $(form).find('[name="customer_email"]').val(),
                    description: $(form).find('[name="description"]').val(),
                }
            }
        }).done((response) => {
            self.flashMessage = response.data.message;
            $.magnificPopup.close();
        }).always(() => {
            self.submittedForm();
        }).fail((jqXHR) => {
            if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
                error_section.addClass('has-error').text(jqXHR.responseJSON.data.message);
            }
        });
    }
}

dokan.reportAbuse.init();
