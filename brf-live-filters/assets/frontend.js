(function () {
    function ajaxRequest(form) {
        var wrapper = form.closest('.brf-lf-wrapper');
        if (!wrapper) {
            return;
        }

        var results = wrapper.querySelector('.brf-lf-results');
        var formData = new FormData(form);
        formData.set('nonce', form.querySelector('input[name="nonce"]').value);
        formData.set('filterId', wrapper.getAttribute('data-filter-id'));

        wrapper.classList.add('brf-lf-loading');
        fetch(BRFLiveFilters.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    return;
                }
                results.classList.add('brf-lf-fade-out');
                setTimeout(function () {
                    results.innerHTML = data.data.html;
                    results.classList.remove('brf-lf-fade-out');
                    results.classList.add('brf-lf-fade-in');
                    setTimeout(function () {
                        results.classList.remove('brf-lf-fade-in');
                    }, 200);
                }, 200);
            })
            .catch(function () {
                wrapper.classList.remove('brf-lf-loading');
            })
            .finally(function () {
                wrapper.classList.remove('brf-lf-loading');
            });
    }

    function initForms() {
        document.querySelectorAll('.brf-lf-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                ajaxRequest(form);
            });

            form.querySelectorAll('input, select').forEach(function (input) {
                input.addEventListener('change', function () {
                    form.querySelector('input[name="page"]').value = '1';
                    ajaxRequest(form);
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initForms);
})();
