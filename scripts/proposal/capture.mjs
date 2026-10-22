// Captures full-page screenshots of every screen (desktop + phone) for the client proposal.
// Usage: node scripts/proposal/capture.mjs  (env: BASE_URL, ADMIN_EMAIL/ADMIN_PASSWORD, APPLICANT_EMAIL/APPLICANT_PASSWORD, APPLICATION_REF, APPLICATION_ID)
import puppeteer from 'puppeteer-core';
import fs from 'node:fs';
import path from 'node:path';

const BASE = process.env.BASE_URL ?? 'http://127.0.0.1:8000';
const OUT = path.resolve('dist/proposal/shots');
const CHROME = process.env.CHROME_PATH ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const ADMIN = { email: process.env.ADMIN_EMAIL ?? 'admin@fortress.test', password: process.env.ADMIN_PASSWORD ?? 'password' };
const APPLICANT = { email: process.env.APPLICANT_EMAIL ?? 'test.applicant@fortress.test', password: process.env.APPLICANT_PASSWORD ?? 'portal-test-123' };
const REF = process.env.APPLICATION_REF ?? 'FSS-O5E13R';
const APP_ID = process.env.APPLICATION_ID ?? '57';

fs.mkdirSync(OUT, { recursive: true });

const PUBLIC = [
  ['home', '/'], ['about', '/about'], ['services', '/services'], ['service-cit', '/services/cash-in-transit'],
  ['training', '/training'], ['careers', '/careers'], ['job', '/careers/security-drivers'], ['apply', '/careers/security-drivers/apply'],
  ['contact', '/contact'], ['portal-login', '/portal/login'], ['portal-register', '/portal/register'], ['admin-login', '/login'],
];
const PORTAL = [['portal-dashboard', '/portal'], ['portal-application', `/portal/applications/${REF}`], ['portal-notifications', '/portal/notifications'], ['portal-profile', '/portal/profile']];
const ADMIN_PAGES = [
  ['admin-dashboard', '/admin'], ['admin-pipeline', '/admin/applications/pipeline'], ['admin-applicants', '/admin/applications'],
  ['admin-applicant', `/admin/applications/${APP_ID}`], ['admin-jobs', '/admin/jobs'], ['admin-job-edit', '/admin/jobs/security-drivers/edit'],
  ['admin-interviews', '/admin/interviews'], ['admin-enquiries', '/admin/enquiries'], ['admin-templates', '/admin/templates'],
  ['admin-settings', '/admin/settings'], ['admin-users', '/admin/users'],
];
const MOBILE = [['m-home', '/'], ['m-careers', '/careers'], ['m-job', '/careers/security-drivers'], ['m-portal', '/portal'], ['m-admin-pipeline', '/admin/applications/pipeline'], ['m-admin-applicant', `/admin/applications/${APP_ID}`]];

const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new', protocolTimeout: 60000, args: ['--hide-scrollbars', '--no-sandbox'] });
const page = await browser.newPage();
await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'reduce' }]);

async function settle() {
  await page.evaluate(async () => {
    document.querySelectorAll('[data-reveal], .reveal-clip').forEach(e => e.classList.add('is-visible'));
    document.querySelectorAll('video').forEach(v => { try { v.pause(); v.currentTime = 0.5; } catch {} });
    // trigger lazy images
    window.scrollTo(0, document.body.scrollHeight); await new Promise(r => setTimeout(r, 400)); window.scrollTo(0, 0);
    await Promise.race([
      Promise.all([...document.images].filter(i => !i.complete).map(i => new Promise(r => { i.onload = i.onerror = r; }))),
      new Promise(r => setTimeout(r, 1500)),
    ]);
  });
  await new Promise(r => setTimeout(r, 500));
}

async function shoot(name, url, { width = 1440, height = 900, fullPage = true, mobile = false } = {}) {
  await page.setViewport({ width, height, deviceScaleFactor: mobile ? 2 : 1.25, isMobile: mobile, hasTouch: mobile });
  await page.goto(BASE + url, { waitUntil: 'networkidle0' });
  await settle();
  const file = path.join(OUT, `${name}.jpg`);
  await page.screenshot({ path: file, type: 'jpeg', quality: 82, fullPage, captureBeyondViewport: true });
  const dims = await page.evaluate(() => ({ w: document.documentElement.clientWidth, h: document.documentElement.scrollHeight }));
  console.log(`✓ ${name} ${dims.w}x${dims.h}`);
  return { name, url, file, ...dims };
}

async function login(url, email, password) {
  await page.goto(BASE + url, { waitUntil: 'networkidle0' });
  await page.type('input[name=email]', email);
  await page.type('input[name=password]', password);
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type=submit], form button')]);
}

const manifest = { desktop: [], mobile: [] };
for (const [n, u] of PUBLIC) manifest.desktop.push(await shoot(n, u));
await login('/portal/login', APPLICANT.email, APPLICANT.password);
for (const [n, u] of PORTAL) manifest.desktop.push(await shoot(n, u));
await login('/login', ADMIN.email, ADMIN.password);
for (const [n, u] of ADMIN_PAGES) manifest.desktop.push(await shoot(n, u, { fullPage: !u.endsWith('/pipeline') }));
for (const [n, u] of MOBILE) manifest.mobile.push(await shoot(n, u, { width: 390, height: 844, mobile: true, fullPage: false }));
fs.writeFileSync(path.join(OUT, 'manifest.json'), JSON.stringify(manifest, null, 2));
await browser.close();
console.log('done');
