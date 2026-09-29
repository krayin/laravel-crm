import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Absolute path to /lang directory
const LANG_DIR = path.resolve(
  __dirname,
  '../../../../../src/Resources/lang/'
);

  export const fetchConfig= async () => {
    const res = await fetch(LANG_DIR);
    const appConfig = await res.json();
    return appConfig;
    };
