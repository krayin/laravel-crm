const timeouts = new WeakMap();

export default {
    mounted(el, binding) {
        let handler = function (e) {
            clearTimeout(timeouts.get(el));

            timeouts.set(
                el,
                setTimeout(function () {
                    el.dispatchEvent(new Event('change'));
                }, binding.value || 500)
            );
        };

        el.addEventListener('input', handler);
    },

    unmounted(el) {
        clearTimeout(timeouts.get(el));

        timeouts.delete(el);
    },
};
