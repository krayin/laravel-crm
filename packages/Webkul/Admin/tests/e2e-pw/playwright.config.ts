import { defineConfig, devices } from "@playwright/test";
import dotenv from "dotenv";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export const TESTS_ROOT_PATH = __dirname;
export const STATE_DIR_PATH = `${ TESTS_ROOT_PATH }/.state/`;
export const ADMIN_AUTH_STATE_PATH = `${ STATE_DIR_PATH }/admin-auth.json`;

dotenv.config({ path: path.resolve(__dirname, "../../../../../.env") });

export default defineConfig({
    /**
     * Resolved from this file rather than from the directory the command was run in: a relative
     * path sent Playwright looking for specs beside whatever happened to be the working
     * directory, which is how an editor or a root-level invocation found none.
     */
    testDir: path.resolve(__dirname, "tests"),

    /**
     * A failing test should report quickly rather than hold the run for two minutes.
     *
     * The budget covers fixture setup as well as the test body: `adminPage` opens a browser
     * context, navigates to the dashboard and may sign in, which on its own can take several
     * seconds. Below about 30s that setup alone starts timing out, so healthy tests fail for a
     * reason that has nothing to do with what they assert.
     */
    timeout: 30 * 1000,

    /**
     * Kept well below the test timeout so a failed assertion surfaces its own message and locator
     * rather than being cut off by the test timeout, which reports nothing about what was expected.
     */
    expect: { timeout: 7 * 1000 },

    outputDir: "./test-results",

    fullyParallel: false,

    workers: 1,

    forbidOnly: !!process.env.CI,

    retries: 0,

    reportSlowTests: null,

    reporter: [
        [
            "html",
            {
                outputFolder: "./playwright-report",
            },
        ],
    ],

    use: {
        baseURL: `${process.env.APP_URL}/`.replace(/\/+$/, "/"),
        screenshot: { mode: "only-on-failure", fullPage: true },
        video: "retain-on-failure",
        trace: "retain-on-failure",
    },

    projects: [
        {
            name: "chromium",
            use: { ...devices["Desktop Chrome"] },
        },
    ],
});
