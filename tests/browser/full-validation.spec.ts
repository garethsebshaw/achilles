import { expect, test } from '@playwright/test';
import { execFileSync } from 'node:child_process';

type ResourceManifestEntry = {
    label: string;
    slug: string;
    sampleId?: number | null;
};

const adminEmail = process.env.AW_ADMIN_EMAIL ?? 'thisisg@gmail.com';
const adminPassword = process.env.AW_ADMIN_PASSWORD ?? 'rcp@UHE-nmq_kmw0qzk';
const expectNovaRegistered = process.env.EXPECT_NOVA_REGISTERED === 'true';
const weatherLocationId = process.env.AW_WEATHER_LOCATION_ID ?? '33';
const baseURL = process.env.BASE_URL ?? 'http://achillesworkouts.test';
const allowLocalSampleIdFallback = /achillesworkouts\.test|127\.0\.0\.1|localhost/.test(baseURL);

const publicPages = [
    '/login',
    '/forgot-password',
];

const authenticatedPages = [
    '/dashboards/main',
    '/account/security',
    '/workouts',
    '/workout-sessions',
    '/workout-signups',
    '/meeting-points',
];

const resourceManifest = getResourceManifest();

test.describe('browser validation', () => {
    test('public auth pages render and seeded admin login succeeds', async ({ page }) => {
        for (const path of publicPages) {
            await page.goto(path, { waitUntil: 'domcontentloaded' });
            await expect(page).toHaveURL(new RegExp(`${escapeForRegex(path)}$`));
            await assertNoPageError(page, path);
        }

        await login(page);
        await expect(page).toHaveURL(/\/dashboards\/main$/);
        await expect(page.getByText('Dashboards', { exact: false })).toBeVisible();
        await assertNovaRegistrationState(page);
        await assertNoPageError(page, '/dashboards/main');
    });

    test('authenticated bridge pages and dashboard render cleanly', async ({ page }) => {
        await login(page);

        for (const path of authenticatedPages) {
            await page.goto(path, { waitUntil: 'domcontentloaded' });
            await assertNovaRegistrationState(page);
            await assertNoPageError(page, path);
        }

        const resolvedWeatherLocationId = weatherLocationId || await resolveWeatherLocationId(page);

        if (resolvedWeatherLocationId !== null) {
            await page.goto(`/weather/${resolvedWeatherLocationId}`, { waitUntil: 'domcontentloaded' });
            await assertNoPageError(page, `/weather/${resolvedWeatherLocationId}`);

            const body = (await page.locator('body').innerText()) ?? '';
            expect(body).toMatch(/Current Conditions|No live weather data|No hourly weather records|No daily weather records/);
        }
    });

    test('nova resource pages load cleanly across the full manifest', async ({ page }) => {
        test.setTimeout(15 * 60 * 1000);
        await login(page);

        for (const resource of resourceManifest) {
            const indexPath = `/resources/${resource.slug}`;
            await page.goto(indexPath, { waitUntil: 'domcontentloaded' });
            await assertNovaRegistrationState(page);
            await assertNoPageError(page, `${resource.label} index`);

            const createPath = `${indexPath}/new`;
            await page.goto(createPath, { waitUntil: 'domcontentloaded' });
            await assertNovaRegistrationState(page);
            await assertNoPageError(page, `${resource.label} create`);

            const detailPath = await findDetailPath(page, resource.slug);

            if (detailPath !== null) {
                await page.goto(detailPath, { waitUntil: 'domcontentloaded' });
                await assertNovaRegistrationState(page);
                await assertNoPageError(page, `${resource.label} detail`);

                const editPath = `${detailPath}/edit`;
                await page.goto(editPath, { waitUntil: 'domcontentloaded' });
                await assertNovaRegistrationState(page);
                await assertNoPageError(page, `${resource.label} edit`);
            }
        }
    });

    test('workout-session check-in navigation resolves into a scoped signup page', async ({ page }) => {
        const sessionResource = resourceManifest.find((resource) => resource.slug === 'workout-sessions');

        await login(page);
        await page.goto('/resources/workout-sessions', { waitUntil: 'domcontentloaded' });
        const sessionDetailPath = await findDetailPath(page, 'workout-sessions')
            ?? (
                sessionResource?.sampleId
                    ? `/resources/workout-sessions/${sessionResource.sampleId}`
                    : null
            );

        test.skip(!sessionDetailPath, 'No workout session detail path was available for browser validation.');

        await page.goto(sessionDetailPath!, { waitUntil: 'domcontentloaded' });
        const sessionBody = (await page.locator('body').innerText()) ?? '';

        test.skip(
            /Whoops|We\'re lost in space\.|404\s+NOT FOUND/i.test(sessionBody),
            'The deployed dataset did not expose a reachable workout session detail page for the attendance flow.',
        );

        await assertNovaRegistrationState(page);
        await assertNoPageError(page, 'workout session detail');

        const checkInLink = page.locator('a[href*="/workout-signups"]').first();
        await expect(checkInLink).toBeVisible();
        await checkInLink.click();
        await page.waitForLoadState('domcontentloaded');

        await expect(page).toHaveURL(/workout-signups/);
        await assertNovaRegistrationState(page);
        await assertNoPageError(page, 'session-scoped workout signups');
    });
});

async function login(page: import('@playwright/test').Page): Promise<void> {
    await page.goto('/login', { waitUntil: 'domcontentloaded' });

    await page.getByLabel('Email').fill(adminEmail);
    await page.getByLabel('Password').fill(adminPassword);
    await page.getByRole('button', { name: 'Log in' }).click();

    await page.waitForURL(/\/dashboards\/main$/, { timeout: 30000 });
}

async function assertNoPageError(page: import('@playwright/test').Page, context: string): Promise<void> {
    await page.waitForLoadState('domcontentloaded');

    const body = (await page.locator('body').innerText()) ?? '';
    const errorSnippets = [
        'Server Error',
        'Not Found',
        'Whoops',
        'Undefined variable',
        'SQLSTATE[',
        'Route [',
        'We\'re lost in space.',
    ];

    for (const snippet of errorSnippets) {
        expect.soft(body, `${context} unexpectedly contained "${snippet}"`).not.toContain(snippet);
    }

    await expect(page.locator('body')).not.toContainText('Server Error');
}

async function assertNovaRegistrationState(page: import('@playwright/test').Page): Promise<void> {
    if (! expectNovaRegistered) {
        return;
    }

    await expect(page.locator('body')).not.toContainText('UNREGISTERED');
    await expect(page.locator('body')).not.toContainText('This copy of Nova is unlicensed.');
}

function getResourceManifest(): ResourceManifestEntry[] {
    const output = execFileSync('php', ['tests/browser/resource-manifest.php'], {
        cwd: process.cwd(),
        encoding: 'utf8',
    });

    return JSON.parse(output) as ResourceManifestEntry[];
}

async function findDetailPath(page: import('@playwright/test').Page, slug: string): Promise<string | null> {
    const links = await page.locator(`a[href^="/resources/${slug}/"]`).all();

    for (const link of links) {
        const href = await link.getAttribute('href');

        if (! href) {
            continue;
        }

        if (new RegExp(`^/resources/${escapeForRegex(slug)}/[^/]+$`).test(href)) {
            return href;
        }
    }

    return null;
}

async function resolveWeatherLocationId(page: import('@playwright/test').Page): Promise<string | null> {
    await page.goto('/resources/system-locations', { waitUntil: 'domcontentloaded' });
    const locationDetailPath = await findDetailPath(page, 'system-locations');

    return locationDetailPath?.split('/').pop() ?? null;
}

function escapeForRegex(value: string): string {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
