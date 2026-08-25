import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const dataFile = path.resolve(
  __dirname,
  "../test-data/runtime.json"
);

function readJson(): Record<string, any> {
  if (!fs.existsSync(dataFile)) {
    return {};
  }

  try {
    return JSON.parse(fs.readFileSync(dataFile, "utf-8"));
  } catch {
    // Corrupt or partial write protection
    return {};
  }
}

function writeJson(data: Record<string, any>) {
  const dir = path.dirname(dataFile);

  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }

  fs.writeFileSync(dataFile, JSON.stringify(data, null, 2));
}

export function saveData(key: string, value: unknown): void {
  const data = readJson();
  data[key] = value;
  writeJson(data);
}

export function getData<T>(key: string): T {
  const data = readJson();

  if (!(key in data)) {
    throw new Error(`❌ Runtime data key not found: "${key}"`);
  }

  return data[key] as T;
}
