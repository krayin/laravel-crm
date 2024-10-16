import DOMPurify from 'dompurify';

export default {
    beforeMount(el, binding) {
        el.innerHTML = DOMPurify.sanitize(binding.value);
    },
    updated(el, binding) {
        el.innerHTML = DOMPurify.sanitize(binding.value);
    }
};
