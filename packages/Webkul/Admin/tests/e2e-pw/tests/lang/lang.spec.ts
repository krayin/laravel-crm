import { expect, test } from '@playwright/test';
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

/**
 * Resolve file paths relative to this test file
 */
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Absolute path to /lang directory
 */
const LANG_DIR = path.resolve(__dirname, '../../../../src/Resources/lang');

const BASE_LANG = 'en';

/**
 * Normalizes the keys in a PHP array file by converting it to a JSON object and flattening the keys.
 */
function getNormalizedKeys(filePath: string): string[] {
  const phpCode = `echo json_encode(include '${filePath}');`;
  
  const json = execSync(`php -r "${phpCode.replace(/"/g, '\\"')}"`).toString();

  const obj = JSON.parse(json);

  function flattenKeys(obj: any, prefix = ''): string[] {
    return Object.keys(obj).flatMap(key => {
      const fullKey = prefix ? `${prefix}.${key}` : key;

      if (typeof obj[key] === 'object' && obj[key] !== null) {
        return flattenKeys(obj[key], fullKey);
      }
    
      return [fullKey];
    });
  }

  return flattenKeys(obj);
}

test('All language files must match number of keys and key names with English app.php', () => {
  const baseFile = path.join(LANG_DIR, BASE_LANG, 'app.php');

  const baseKeys = getNormalizedKeys(baseFile);
  
  /**
   * All locales except the base one
   */
  const locales = fs
    .readdirSync(LANG_DIR)
    .filter(locale => locale !== BASE_LANG && fs.existsSync(path.join(LANG_DIR, locale, 'app.php')));

  /**
   * Array to collect any locales that have issues
   */
  let failedLocales: { locale: string; missingKeys: string[]; extraKeys: string[] }[] = [];

  for (const locale of locales) {
    const filePath = path.join(LANG_DIR, locale, 'app.php');

    const keys = getNormalizedKeys(filePath);
    
    const missingKeys = baseKeys.filter(key => !keys.includes(key));
    const extraKeys = keys.filter(key => !baseKeys.includes(key));

    if (missingKeys.length > 0 || extraKeys.length > 0) {
      failedLocales.push({ locale, missingKeys, extraKeys });
    }
  }

  if (failedLocales.length > 0) {
    for (const { locale, missingKeys, extraKeys } of failedLocales) {
      if (missingKeys.length) {
        for (const key of missingKeys) console.error(`    - ${key}`);
      }

      if (extraKeys.length) {
        for (const key of extraKeys) console.error(`    + ${key}`);
      }
    }

    /**
     * Fail the test if there are any locales with missing or extra keys, showing the details in the console.
     */
    expect(failedLocales).toEqual([]);
  } else {
    console.log('All language files have matching keys and counts with en/app.php');
  }
});
