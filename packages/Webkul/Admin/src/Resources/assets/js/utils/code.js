export const CODE_MAX_LENGTH = 255;

const DISALLOWED = /[^\wء-ي一-龥㐂-퟿-舘ぁ-゠゠-ㇿ -]+/gu;

/**
 * Turn a label into a lowercase, dash separated code that fits the `code` column.
 */
export function slugify(value) {
    const slug = String(value ?? '')
        .toLowerCase()
        .replace(DISALLOWED, '')
        .replace(/[ -]+/g, '-')
        .replace(/^-|-$/g, '');

    return slug.slice(0, CODE_MAX_LENGTH).replace(/-$/, '');
}
