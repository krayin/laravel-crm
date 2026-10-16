import { execFileSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { test, expect } from '@playwright/test';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PROJECT_ROOT = path.resolve(__dirname, '../../../../../../..');

const BASE_LANG = 'en';

const PACKAGES = ['Admin', 'GoogleContact', 'Installer', 'WebForm'];

// The file is PHP, so PHP reads it: a key is its full path through the nested
// arrays, which is what `trans()` is given and what tells one `title` from the
// next.
function getKeys(filePath: string): Record<string, string> {
    const script = `
        $flatten = function ($data, $prefix = '') use (&$flatten) {
            $keys = [];

            foreach ($data as $key => $value) {
                $path = $prefix === '' ? $key : $prefix . '.' . $key;

                if (is_array($value)) {
                    $keys += $flatten($value, $path);
                } else {
                    $keys[$path] = (string) $value;
                }
            }

            return $keys;
        };

        echo json_encode($flatten(require ${JSON.stringify(filePath)}));
    `;

    return JSON.parse(execFileSync('php', ['-r', script], { encoding: 'utf-8' }));
}

function langDir(pkg: string): string {
    return path.join(PROJECT_ROOT, 'packages/Webkul', pkg, 'src/Resources/lang');
}

function localesOf(dir: string): string[] {
    return fs
        .readdirSync(dir)
        .filter(
            (locale) =>
                locale !== BASE_LANG && fs.existsSync(path.join(dir, locale, 'app.php'))
        );
}

test('every locale carries the same keys as English', () => {
    const problems: string[] = [];

    for (const pkg of PACKAGES) {
        const dir = langDir(pkg);

        if (! fs.existsSync(path.join(dir, BASE_LANG, 'app.php'))) {
            continue;
        }

        const base = getKeys(path.join(dir, BASE_LANG, 'app.php'));

        for (const locale of localesOf(dir)) {
            const keys = getKeys(path.join(dir, locale, 'app.php'));

            const missing = Object.keys(base).filter((key) => ! (key in keys));
            const extra = Object.keys(keys).filter((key) => ! (key in base));

            if (missing.length) {
                problems.push(
                    `${pkg}/${locale}: ${missing.length} missing - ${missing.slice(0, 10).join(', ')}`
                );
            }

            if (extra.length) {
                problems.push(
                    `${pkg}/${locale}: ${extra.length} unknown - ${extra.slice(0, 10).join(', ')}`
                );
            }
        }
    }

    expect(problems, problems.join('\n')).toEqual([]);
});

test('no locale falls further behind English', () => {
    // These locales already carry phrases that were never translated. The count
    // is recorded so that it can only go down: a locale that gains one fails
    // here, and a locale that is caught up is removed from the list.
    const BACKLOG: Record<string, number> = {
        'Admin/ar': 1,
        'Admin/pt_BR': 46,
        'Admin/tr': 23,
        'Admin/vi': 41,
    };

    const SHARED = new Set([
        'sku', 'json', 'url', 'id', 'api', 'pdf', 'csv', 'xml', 'html', 'email',
        'general', 'total', 'subtotal', 'error', 'color', 'post', 'put', 'webhook',
        'webhooks', 'roles', 'global', 'individual', 'leads', 'png, jpeg, jpg',
        'mp4, webm, mkv', 'x-www-form-urlencoded',
    ]);

    // Names of products and services keep their spelling in every language.
    const PROPER_NOUNS = /^(magic ai|google contacts|deepseek|gemini|gpt-|grok|llama|claude|openai|euro \(eur\)|data transfer)/i;

    const problems: string[] = [];

    for (const pkg of PACKAGES) {
        const dir = langDir(pkg);

        if (! fs.existsSync(path.join(dir, BASE_LANG, 'app.php'))) {
            continue;
        }

        const base = getKeys(path.join(dir, BASE_LANG, 'app.php'));

        for (const locale of localesOf(dir)) {
            const keys = getKeys(path.join(dir, locale, 'app.php'));

            const untranslated = Object.keys(base).filter((key) => {
                const english = base[key];

                if (keys[key] !== english) {
                    return false;
                }

                if (
                    ! /[a-z]/i.test(english)
                    || SHARED.has(english.toLowerCase())
                    || PROPER_NOUNS.test(english)
                ) {
                    return false;
                }

                // A phrase is what has to be translated; a lone word often
                // carries across unchanged.
                return english.replace(/:\w+/g, '').trim().split(/\s+/).length > 1;
            });

            const name = `${pkg}/${locale}`;
            const allowed = BACKLOG[name] ?? 0;

            if (untranslated.length > allowed) {
                problems.push(
                    `${name}: ${untranslated.length} phrases still in English, ${allowed} allowed - ` +
                    untranslated.slice(0, 10).map((key) => `${key} ("${base[key]}")`).join(', ')
                );
            }

            if (untranslated.length < allowed) {
                problems.push(
                    `${name}: down to ${untranslated.length} phrases from ${allowed} - lower its entry in BACKLOG`
                );
            }
        }
    }

    expect(problems, problems.join('\n')).toEqual([]);
});
