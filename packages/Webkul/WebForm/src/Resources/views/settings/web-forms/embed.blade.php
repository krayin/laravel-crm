(function() {
    /**
     * The preview HTML is handed to document.write as a JSON-encoded string rather than
     * interpolated into a template literal. Blade escapes the form's stored fields for HTML, which
     * leaves dollar-brace sequences untouched - inert in markup, but evaluated as code by the
     * engine once that markup sits between backticks. The replaceAll this replaces could never
     * help, since interpolation resolves before the call runs.
     */
    document.write(@json(view('web_form::settings.web-forms.preview', compact('webForm'))->render()));
})();
