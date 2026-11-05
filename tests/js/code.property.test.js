import { test } from 'node:test';
import assert from 'node:assert/strict';
import fc from 'fast-check';
import { CODE_MAX_LENGTH, slugify } from '../../packages/Webkul/Admin/src/Resources/assets/js/utils/code.js';

const ALLOWED = /^[\wء-ي一-龥㐂-퟿-舘ぁ-゠゠-ㇿ-]*$/u;

const word = fc.stringMatching(/^[a-z0-9]{1,12}$/);

const longLabel = fc.array(word, { minLength: 25, maxLength: 60 }).map((words) => words.join(' '));

const anyText = fc.oneof(
    fc.string(),
    fc.string({ unit: 'grapheme' }),
    fc.string({ unit: 'binary', maxLength: 400 }),
    longLabel,
);

test('slugify only emits lowercase code characters within the column limit', () => {
    fc.assert(fc.property(anyText, (value) => {
        const code = slugify(value);

        assert.match(code, ALLOWED);
        assert.equal(code, code.toLowerCase());
        assert.ok(code.length <= CODE_MAX_LENGTH);
    }));
});

test('slugify never leaves leading, trailing or repeated dashes', () => {
    fc.assert(fc.property(anyText, (value) => {
        assert.doesNotMatch(slugify(value), /^-|-$|--/);
    }));
});

test('slugify is idempotent', () => {
    fc.assert(fc.property(anyText, (value) => {
        const code = slugify(value);

        assert.equal(slugify(code), code);
    }));
});

test('slugify joins words with a single dash however they are spaced', () => {
    const separator = fc.constantFrom(' ', '  ', ' - ', '--', ' -- ');

    fc.assert(fc.property(fc.array(word, { minLength: 1, maxLength: 10 }), separator, (words, glue) => {
        assert.equal(slugify(words.join(glue)), words.join('-'));
    }));
});

test('slugify accepts any value without throwing', () => {
    fc.assert(fc.property(fc.oneof(anyText, fc.integer(), fc.double(), fc.boolean(), fc.constant(null), fc.constant(undefined)), (value) => {
        assert.equal(typeof slugify(value), 'string');
    }));
});
