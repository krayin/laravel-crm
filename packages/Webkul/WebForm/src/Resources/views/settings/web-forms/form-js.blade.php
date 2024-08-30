(function() {
    var version = parseInt(Math.random() * 10000);

    var webFormId = "{{ $webForm->id }}";

    var content = document.createElement('div');

    var scriptTag = document.currentScript || document.getElementById('krayin_' + webFormId);

    scriptTag.parentElement.appendChild(content);

    var formHTML = `{!! view('web_form::settings.web-forms.form-js.form', compact('webForm'))->render() !!}`;

    content.innerHTML = '<div id="krayin-content-container-' + webFormId + '" class="krayin-content-container krayin_'+webFormId+'">' + formHTML + '</div>';

    var script = document.createElement('script');

    script.src = '{{ asset('vendor/webkul/web-form/assets/js/web-form.js') }}';

    document.addEventListener('DOMContentLoaded', function() {
        {{-- flatpickr(".form-group.date input", {
                allowInput: true,
                altFormat: "Y-m-d",
                dateFormat: "Y-m-d",
                weekNumbers: true,
            });
        
            flatpickr(".form-group.datetime input", {
                allowInput: true,
                altFormat: "Y-m-d H:i:S",
                dateFormat: "Y-m-d H:i:S",
                enableTime: true,
                time_24hr: true,
                weekNumbers: true,
            });
        --}}
        let data = null;
    
        document.getElementById('krayinWebForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = document.getElementById("krayinWebForm");

            data = new FormData(form);
    
            if (validateForm(form)) {
                const formData = new FormData(document.querySelector('#krayinWebForm'));
                const data = new URLSearchParams(formData).toString();
                
                fetch("{{ route('admin.settings.web_forms.form_store', $webForm->id) }}", {
                    method: 'POST',
                    body: data,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#loaderDiv').classList.remove('loaderDiv');
                    document.querySelector('#imgSpinner').classList.remove('imgSpinner');
                
                    if (data.message) {
                        document.querySelector('.alert-wrapper .alert p').textContent = data.message;
                        document.querySelector('.alert-wrapper').style.display = 'block';
                    } else {
                        window.location.href = data.redirect;
                    }
                
                    document.querySelector('#krayinWebForm').reset();
                })
                .catch(error => {
                    console.log(error);
                
                    error.response.json().then(errorData => {
                        for (let key in errorData.errors) {
                            let inputName = key.split('.').map((chunk, index) => index ? `[${chunk}]` : chunk).join('');
                            showError(inputName, errorData.errors[key][0]);
                        }
                    });
                });
            }
        });
    });
    
    function validateForm(form) {
        // Implement validation logic or integrate with an existing validation library
        return true; // Replace with actual validation check
    }
    
    function showError(inputName, errorMessage) {
        const input = document.querySelector(`[name="${inputName}"]`);

        if (input) {
            const errorElement = document.createElement('span');

            errorElement.classList.add('error');

            errorElement.textContent = errorMessage;

            input.parentElement.appendChild(errorElement);
        }
    }

    content.appendChild(script);
})()