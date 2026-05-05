import { expect, test } from '@playwright/test';
import { execFileSync } from 'node:child_process';

const adminEmail = process.env.AW_ADMIN_EMAIL ?? 'thisisg@gmail.com';
const adminPassword = process.env.AW_ADMIN_PASSWORD ?? 'rcp@UHE-nmq_kmw0qzk';
const baseURL = process.env.BASE_URL ?? 'http://achillesworkouts.test';
const fixture = getAttendanceFixture();

test.describe('attendance flow', () => {
    test('session relation view, active user search, and reassignment page load cleanly', async ({ page }) => {
        await login(page);

        await page.goto(
            `/resources/workout-signups?viaResource=workout-sessions&viaResourceId=${fixture.sessionId}&viaRelationship=signups`,
            { waitUntil: 'domcontentloaded' },
        );
        await assertNoPageError(page, 'session signups relation');
        await expect(page.locator('body')).toContainText('Check-in/out for');

        await page.goto(`/attendance/sessions/${fixture.sessionId}/activate`, { waitUntil: 'domcontentloaded' });
        await expect(page).toHaveURL(/workout-signups/);
        await assertNoPageError(page, 'activated check-in session');

        await page.goto(`/resources/users?search=${encodeURIComponent(fixture.userSearch)}`, { waitUntil: 'domcontentloaded' });
        await assertNoPageError(page, 'scoped users search');

        await page.goto(`/attendance/users/${fixture.userId}/check-in`, { waitUntil: 'domcontentloaded' });
        await assertNoPageError(page, 'attendance user check in');

        await page.goto(`/resources/workout-signups?resourceId=${fixture.sessionId}&search=${encodeURIComponent(fixture.userSearch)}`, {
            waitUntil: 'domcontentloaded',
        });
        await assertNoPageError(page, 'session signup search after user check in');
        await expect(page.locator('body')).toContainText(fixture.userSearch);

        await page.goto(`/resources/workout-signups/${fixture.signupId}/edit`, { waitUntil: 'domcontentloaded' });
        await assertNoPageError(page, 'workout signup edit');
        await expect(page.locator('body')).toContainText('Assigned Athlete');
    });
});

async function login(page: import('@playwright/test').Page): Promise<void> {
    await page.goto(`${baseURL}/login`, { waitUntil: 'domcontentloaded' });
    await page.getByLabel('Email').fill(adminEmail);
    await page.getByLabel('Password').fill(adminPassword);
    await page.getByRole('button', { name: 'Log in' }).click();
    await page.waitForURL(/\/dashboards\/main$/, { timeout: 30000 });
}

async function assertNoPageError(page: import('@playwright/test').Page, context: string): Promise<void> {
    const body = (await page.locator('body').innerText()) ?? '';
    for (const snippet of ['Server Error', 'Not Found', 'Whoops', 'SQLSTATE[', 'We\'re lost in space.']) {
        expect.soft(body, `${context} unexpectedly contained "${snippet}"`).not.toContain(snippet);
    }
}

function getAttendanceFixture(): { sessionId: number; signupId: number; userId: number; userSearch: string } {
    const output = execFileSync('php', ['tests/browser/attendance-fixture.php'], {
        cwd: process.cwd(),
        encoding: 'utf8',
    });

    return JSON.parse(output) as { sessionId: number; signupId: number; userId: number; userSearch: string };
}
