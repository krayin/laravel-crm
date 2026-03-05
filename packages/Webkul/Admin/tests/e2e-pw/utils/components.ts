/**
 * Confirm the modal dialog.
 */
export function confirmModal(message, page) {
    return new Promise(async (resolve, reject) => {
        await page.waitForSelector("text=" + message);
        const agreeButton = await page.locator(
            'button.primary-button:has-text("Agree")'
        );

        if (await agreeButton.isVisible()) {
            await agreeButton.click();
            resolve(true);
        } else {
            reject("Agree button not found or not visible.");
        }
    });
}