import DOMPurify from 'dompurify';

/**
 * Mail bodies come from the compose editor and from inbound mail, so they are rendered with a
 * stricter profile than other rich text: elements that could pose as part of the admin panel
 * (media players, form controls, global stylesheets) are dropped, and positioning is removed
 * from inline styles so a body cannot lay a fixed overlay over the page.
 *
 * Usage: v-safe-html:mail="email.reply"
 */
const mailPurifier = DOMPurify(window);

const MAIL_FORBIDDEN_TAGS = [
    'audio',
    'video',
    'source',
    'track',
    'form',
    'input',
    'button',
    'select',
    'option',
    'optgroup',
    'textarea',
    'datalist',
    'fieldset',
    'output',
    'style',
];

mailPurifier.addHook('afterSanitizeAttributes', (node) => {
    if (! node.style?.position) {
        return;
    }

    node.style.removeProperty('position');

    if (! node.getAttribute('style')?.trim()) {
        node.removeAttribute('style');
    }
});

const sanitize = ({ arg, value }) => {
    if (arg === 'mail') {
        return mailPurifier.sanitize(value ?? '', { FORBID_TAGS: MAIL_FORBIDDEN_TAGS });
    }

    return DOMPurify.sanitize(value);
};

export default {
    beforeMount(el, binding) {
        el.innerHTML = sanitize(binding);
    },
    updated(el, binding) {
        el.innerHTML = sanitize(binding);
    }
};
