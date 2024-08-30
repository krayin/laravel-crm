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
        document.getElementById('krayinWebForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = document.getElementById("krayinWebForm");

            const formData = new FormData(document.querySelector('#krayinWebForm'));
            const data = new URLSearchParams(formData).toString();
            
            fetch("{{ route('admin.settings.web_forms.form_store', $webForm->id) }}", {
                method: 'POST',
                body: data,
                headers: {
                    'Accept': 'application/json, text/javascript, */*; q=0.01',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                }
            })
                .then(response => response.json())
                .then(response => {
                    document.querySelectorAll('.error-message').forEach(function(errorElement) {
                        errorElement.textContent = '';
                    });

                    if (response.message) {
                        let messageElement = document.querySelector('#message');

                        messageElement.style.display = 'block';

                        messageElement.textContent = response.message;

                        setTimeout(function() {
                            messageElement.style.display = 'none';
                        }, 5000);

                        document.querySelector('#krayinWebForm').reset();
                    }

                    if (response.errors) {
                        showError(response.errors);
                    }
                })
                .catch(error => {});
        });
    });
    
    function showError(errors) {
        for (var key in errors) {
            var inputNames = [];

            key.split('.').forEach(function(chunk, index) {
                if(index) {
                    inputNames.push('[' + chunk + ']')
                } else {
                    inputNames.push(chunk)
                }
            })


            var inputName = inputNames.join('');

            const input = document.querySelector(`[name="${inputName}"]`);

            if (input) {
                const inputError = document.querySelector(`[id="${inputName}-error"]`);

                inputError.textContent = errors[key][0];
            }
        }
    };
})()