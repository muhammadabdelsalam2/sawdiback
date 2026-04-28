(function ($) {
    'use strict';

    // =========================
    // Axios AJAX Form Submit
    // =========================
    $(document).on('submit', 'form[data-axios="true"]', function (e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.data('action');
        const method = $form.data('method') || 'POST';
        const $btn = $form.find('button[type="submit"]');
        const formData = new FormData(this);

        // Disable button while processing
        $btn.prop('disabled', true).text('Processing...');

        axios({
            method,
            url,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' }
        })
            .then(response => {
                const { redirect, message } = response.data;
                if (redirect) {
                    window.location.href = redirect;
                } else if (message) {
                    show_toastr(message, 'success');
                    // Real-time update: reload after short delay
                    setTimeout(() => location.reload(), 800);
                }
            })
            .catch(error => {
                const errors = error.response?.data?.errors || null;
                if (errors) {
                    $.each(errors, function (field, messages) {
                        const $input = $form.find(`[name="${field}"]`);
                        $input.addClass('is-invalid');
                        const $errorDiv = $input.siblings('.invalid-feedback');
                        if ($errorDiv.length) $errorDiv.text(messages[0]).show();
                    });
                } else {
                    console.error('Axios Error:', error);
                }
            })
            .finally(() => {
                $btn.prop('disabled', false).text('Success ✔');
            });
    });

    // =========================
    // Bootstrap Validation
    // =========================
    $('.needs-validation').each(function () {
        const $form = $(this);

        // Submit validation
        $form.on('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $form.addClass('was-validated');
        });

        // Real-time validation
        $form.find('input, select, textarea').on('input', function () {
            const $field = $(this);
            $field.toggleClass('is-valid', this.checkValidity())
                .toggleClass('is-invalid', !this.checkValidity());
        });
    });

})(jQuery);

// =========================
// Modal AJAX Loader
// =========================
$(document).on('click', '[data-ajax-popup="true"]', function (e) {
    e.preventDefault();

    const $trigger = $(this);
    const url = $trigger.data('url');
    const size = $trigger.data('size') || 'md';
    const title = $trigger.data('title') || $trigger.data('bs-original-title') ||
        $trigger.data('original-title') || 'Modal';

    const $modal = $('#commonModal');
    const $modalDialog = $modal.find('.modal-dialog');
    const $modalBody = $modal.find('.body');

    // Reset modal size and set title
    $modalDialog.removeClass('modal-md modal-lg modal-xl').addClass(`modal-${size}`);
    $modal.find('.modal-title').text(title);

    // Collect extra data if exists
    const extraData = {};
    ['vc_name', 'warehouse_name', 'discount', 'quotation_id'].forEach(field => {
        const $field = $(`#${field}_hidden`);
        if ($field.length) extraData[field] = $field.val();
    });

    // AJAX request for modal content
    $.ajax({
        url,
        data: extraData,
        beforeSend: function () {
            $modalBody.html('<div class="text-center py-3">Loading...</div>');
        },
        success: function (response) {
            $modalBody.html(response);
            $modal.modal('show');
            // show_toastr('Modal loaded successfully.', 'success');
            if (typeof taskCheckbox === 'function') taskCheckbox();
            if (typeof common_bind === 'function') common_bind('#commonModal');
            if (typeof commonLoader === 'function') commonLoader();
        },
        error: function (xhr) {
            const errorMsg = xhr.responseJSON?.error || 'An error occurred while loading the modal.';
            show_toastr(errorMsg, 'error');
        }
    });
});

// =========================
// Toast Notifications
// =========================
function show_toastr(message, type = 'success') {
    const toast = $(`<div class="custom-toast">${message}</div>`)
        .css({
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            background: type === 'success' ? '#28a745' : '#dc3545',
            color: '#fff',
            padding: '10px 20px',
            borderRadius: '6px',
            boxShadow: '0 2px 6px rgba(0,0,0,0.3)',
            zIndex: '9999'
        })
        .appendTo('body');

    setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
}

// =========================
// Select2 with Style
// =========================
$(document).ready(function () {
    $('.select-search').each(function () {
        const $select = $(this);
        const dynamicPlaceholder = $select.data('placeholder') || 'Select an option...';

        $select.select2({
            width: '100%',
            placeholder: dynamicPlaceholder,
            allowClear: true,
            templateResult: function (state) {
                if (!state.id) return state.text;
                let icon = '';
                if ($select.attr('id') === 'user_id') icon = '<i class="bi bi-person-circle me-2"></i>';
                if ($select.attr('id') === 'plan_id') icon = '<i class="bi bi-card-checklist me-2"></i>';
                return $('<span style="display:flex; align-items:center;">' + icon + state.text + '</span>');
            },
            templateSelection: function (state) {
                if (!state.id) return state.text;
                return $('<span style="display:flex; align-items:center;">' + state.text + '</span>');
            }
        });
    });

    $('.select2-selection').css({
        'border-radius': '12px',
        'border': '2px solid #6c5ce7',
        'padding': '6px 12px',
        'background-color': '#f8f9fa',
        'transition': 'all 0.3s'
    });

    $('.select2-selection').hover(function () {
        $(this).css({
            'border-color': '#00b894',
            'box-shadow': '0 0 10px rgba(0, 184, 148, 0.3)'
        });
    }, function () {
        $(this).css({
            'border-color': '#6c5ce7',
            'box-shadow': 'none'
        });
    });
});

// =========================
// Select2 Initialization
// =========================
function select_search() {
    $(".select2").each(function () {
        var $select = $(this);

        $select.select2({
            theme: "bootstrap-5",
            width: '100%',
            overflow: 'scroll',
            placeholder: $select.attr('data-placeholder') || "Select an option",
            allowClear: true,
            dropdownParent: $select.closest('#commonModal')
        });

        $select.on('change', function () {
            if ($select.val()) {
                $select.removeClass('is-invalid').addClass('is-valid');
            } else {
                $select.removeClass('is-valid').addClass('is-invalid');
            }
        });

        if ($select.val()) {
            $select.addClass('is-valid').removeClass('is-invalid');
        } else {
            $select.addClass('is-invalid').removeClass('is-valid');
        }
    });
}



(function ($) {
    // Form validation using jQuery
    $('.needs-validation').each(function () {
        let $form = $(this);

        $form.on('submit', function (e) {
            if (this.checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            }
            $form.addClass('was-validated');
        });

        // Real-time validation
        $form.find('input, select, textarea').on('input', function () {
            let $field = $(this);
            if (this.checkValidity()) {
                $field.addClass('is-valid').removeClass('is-invalid');
            } else {
                $field.addClass('is-invalid').removeClass('is-valid');
            }
        });
    });

    // Axios AJAX submit
    // $(document).on('submit', 'form[data-axios="true"]', function (e) {
    //     e.preventDefault();

    //     let $form = $(this);
    //     let url = $form.data('action');
    //     let method = $form.data('method') || 'POST';
    //     let formData = new FormData(this);

    //     // Disable button while processing
    //     let $btn = $form.find('button[type="submit"]');
    //     $btn.prop('disabled', true).text('Processing...');

    //     axios({
    //         method: method,
    //         url: url,
    //         data: formData,
    //         headers: { 'Content-Type': 'multipart/form-data' }
    //     })
    //         .then(response => {
    //             if (response.data.redirect) {
    //                 window.location.href = response.data.redirect;
    //             } else {
    //                 alert(response.data.message);
    //                 console.log(response.data);
    //             }
    //         })
    //         .catch(error => {
    //             if (error.response && error.response.data.errors) {
    //                 let errors = error.response.data.errors;

    //                 // Show errors below each input
    //                 $.each(errors, function (field, messages) {
    //                     let $input = $form.find(`[name="${field}"]`);
    //                     $input.addClass('is-invalid');
    //                     let $errorDiv = $input.siblings('.invalid-feedback');
    //                     if ($errorDiv.length) {
    //                         $errorDiv.text(messages[0]).show();
    //                     }
    //                 });
    //             }
    //         })
    //         .finally(() => {
    //             $btn.prop('disabled', false).text('Success ✔');
    //         });
    // });

})(jQuery);

function showToast(message, type = 'success') {
    const toast = $(`
                <div class="custom-toast">${message}</div>
            `).css({
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        background: type === 'success' ? '#28a745' : '#dc3545',
        color: '#fff',
        padding: '10px 20px',
        borderRadius: '6px',
        boxShadow: '0 2px 6px rgba(0,0,0,0.3)',
        zIndex: '9999'
    }).appendTo('body');

    setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
}



$(document).ready(function () {
    let deleteUrl = '';
    let deleteId = '';
    const modalEl = $('#deleteModal');
    const planNameEl = $('#planName');
    const confirmDeleteBtn = $('#confirmDelete');

    // Open Delete Modal
    $('.itemDelete').on('click', function () {
        console.log('Delete button clicked');

        deleteUrl = $(this).data('url');
        deleteId = $(this).data('item_id');
        const planName = $(this).data('name') || 'this plan';

        planNameEl.text(planName);
        modalEl.modal('show');
    });

    // Confirm Delete
    confirmDeleteBtn.on('click', function () {

        axios.delete(deleteUrl, {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                if (response.status === 201 || response.data.success) {
                    // Remove the card from the UI
                    $(`button[data-item_id="${deleteId}"]`).closest('.col-lg-4').remove();

                    showToast('Plan deleted successfully!', 'success');
                    modalEl.modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                } else {
                    showToast('Failed to delete the plan.', 'danger');
                }
            })
            .catch(error => {
                console.error(error);
                showToast('Something went wrong while deleting.', 'danger');
            });
    });

    // Toast notification helper
    function showToast(message, type = 'success') {
        const toast = $(`
                <div class="custom-toast">${message}</div>
            `).css({
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            background: type === 'success' ? '#28a745' : '#dc3545',
            color: '#fff',
            padding: '10px 20px',
            borderRadius: '6px',
            boxShadow: '0 2px 6px rgba(0,0,0,0.3)',
            zIndex: '9999'
        }).appendTo('body');

        setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
    }
});