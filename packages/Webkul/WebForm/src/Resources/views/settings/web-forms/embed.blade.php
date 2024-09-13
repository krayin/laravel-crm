(function() {
    document.write(`{!! view('web_form::settings.web-forms.preview', compact('webForm'))->render() !!}`.replaceAll('$', '\$'));
})();