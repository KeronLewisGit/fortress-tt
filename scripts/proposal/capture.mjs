// Captures full-page screenshots of every screen (desktop + phone) for the client proposal.
// Slices follow the page's own sections so a screenshot never breaks mid-block. Usage: node scripts/proposal/capture.mjs  (env: BASE_URL, ADMIN_EMAIL/ADMIN_PASSWORD, APPLICANT_EMAIL/APPLICANT_PASSWORD, APPLICATION_REF, APPLICATION_ID)
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

/**
 * Measure natural content blocks (header, each section, footer). Blocks taller than the
 * page budget are replaced by their children, recursively, so slices follow real section
 * boundaries instead of cutting wherever the pixel budget runs out.
 */
async function measureBlocks(maxH) {
  return page.evaluate((maxH) => {
    const rect = (el) => { const r = el.getBoundingClientRect(); return { top: Math.round(r.top + window.scrollY), bottom: Math.round(r.bottom + window.scrollY) }; };
    const visible = (el) => { const cs = getComputedStyle(el); const r = el.getBoundingClientRect(); return cs.display !== 'none' && cs.visibility !== 'hidden' && r.height > 24 && cs.position !== 'fixed'; };
    const skip = (el) => el.matches('script, style, template, [x-cloak], [aria-hidden="true"]:not(section)');
    const label = (el) => {
      const hs = [...el.querySelectorAll('h1, h2, h3')].filter(h => visible(h) && h.closest('nav, footer, [aria-hidden="true"]') === null);
      const seen = new Set();
      const text = (h) => { const c = h.cloneNode(true); c.querySelectorAll('.kinetic > span:not(.is-active), [aria-hidden="true"], template').forEach(e => e.remove()); return c.textContent.replace(/\s+/g, ' ').trim(); };
      return hs.map(text).filter(t => t && !seen.has(t.toLowerCase()) && seen.add(t.toLowerCase())).slice(0, 4);
    };
    const explode = (el) => {
      const r = { ...rect(el), labels: label(el) };
      if (r.bottom - r.top <= maxH) return [r];
      const kids = [...el.children].filter(k => !skip(k) && visible(k));
      if (kids.length === 0) return [{ top: r.top, bottom: r.bottom, labels: r.labels }];
      if (kids.length === 1) return explode(kids[0]); // wrapper div – look inside it
      return kids.flatMap(explode);
    };
    // Admin layout: the content column is <main>; public layout: body-level header/main/footer.
    const adminMain = document.querySelector('body > div.flex > div.flex > main');
    const roots = adminMain
      ? [...adminMain.children].map(el => ({ el, trailing: false }))
      : [...document.body.children].filter(el => ['HEADER', 'MAIN', 'FOOTER', 'DIV', 'SECTION'].includes(el.tagName) && !skip(el) && visible(el))
          .flatMap(el => el.tagName === 'MAIN' ? [...el.children].map(k => ({ el: k, trailing: false })) : [{ el, trailing: el.tagName === 'FOOTER' || (el.tagName === 'SECTION' && el.previousElementSibling?.tagName === 'MAIN') }]);
    const blocks = roots.filter(r => visible(r.el)).flatMap(r => explode(r.el).map(b => ({ ...b, trailing: r.trailing }))).filter(b => b.bottom > b.top).sort((a, b) => a.top - b.top);
    // merge overlaps / fill gaps so nothing is lost between blocks
    const merged = [];
    for (const b of blocks) {
      const last = merged[merged.length - 1];
      if (last && b.top < last.bottom - 2) { last.bottom = Math.max(last.bottom, b.bottom); last.trailing = last.trailing && b.trailing; last.labels = [...new Set([...last.labels, ...b.labels])]; } else merged.push({ ...b, labels: [...b.labels] }); // merge true overlaps only
    }
    const docH = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
    if (merged.length) { merged[0].top = 0; merged[merged.length - 1].bottom = Math.max(merged[merged.length - 1].bottom, docH); for (let i = 1; i < merged.length; i++) merged[i].top = merged[i - 1].bottom; }
    return { blocks: merged, docH, docW: document.documentElement.clientWidth };
  }, maxH);
}

/** Greedily pack consecutive blocks into page-sized groups; a lone oversize block is hard-cut as a last resort. */
function packBlocks(blocks, maxH, docH) {
  const groups = [];
  let cur = null;
  for (const b of blocks) {
    const h = b.bottom - b.top;
    if (cur && b.bottom - cur.top <= maxH) { cur.bottom = b.bottom; cur.labels = [...new Set([...cur.labels, ...(b.labels ?? [])])]; continue; }
    if (cur) groups.push(cur);
    if (h > maxH) {
      for (let y = b.top; y < b.bottom; y += maxH) groups.push({ top: y, bottom: Math.min(y + maxH, b.bottom), labels: b.labels ?? [] });
      cur = null;
    } else cur = { top: b.top, bottom: b.bottom, labels: [...(b.labels ?? [])] };
  }
  if (cur) groups.push(cur);
  // swallow a tiny trailing sliver (e.g. a thin footer strip) into the previous group when it fits
  if (groups.length > 1) {
    const last = groups[groups.length - 1], prev = groups[groups.length - 2];
    if (last.bottom - last.top < maxH * 0.15 && last.bottom - prev.top <= maxH * 1.05) { prev.bottom = last.bottom; prev.labels = [...new Set([...prev.labels, ...last.labels])]; groups.pop(); }
  }
  groups[groups.length - 1].bottom = Math.min(groups[groups.length - 1].bottom, docH);
  return groups;
}

const RATIO = 1.08; // slice height ÷ width that fits a letter page beneath the heading and bullets

async function shoot(name, url, { width = 1440, height = 900, fullPage = true, mobile = false } = {}) {
  await page.setViewport({ width, height, deviceScaleFactor: mobile ? 2 : 1.25, isMobile: mobile, hasTouch: mobile });
  await page.goto(BASE + url, { waitUntil: 'networkidle0' });
  await settle();
  const files = []; const labels = [];
  if (!fullPage) {
    const file = path.join(OUT, `${name}.jpg`);
    await page.screenshot({ path: file, type: 'jpeg', quality: 82 });
    files.push(file);
    console.log(`✓ ${name} viewport`);
    return { name, url, chunks: files.map(f => path.resolve(f)), labels: [[]], mobile };
  }
  const maxH = Math.round(width * RATIO);
  let { blocks, docH } = await measureBlocks(maxH);
  if (name !== 'home') { blocks = blocks.filter(b => !b.trailing); docH = blocks[blocks.length - 1].bottom; } // CTA band + footer are shown once (home)
  const groups = packBlocks(blocks, maxH, docH);
  for (const [i, g] of groups.entries()) {
    const file = path.join(OUT, groups.length > 1 ? `${name}-${i}.jpg` : `${name}.jpg`);
    await page.screenshot({ path: file, type: 'jpeg', quality: 82, captureBeyondViewport: true, clip: { x: 0, y: g.top, width, height: g.bottom - g.top } });
    files.push(file); labels.push(g.labels ?? []);
  }
  console.log(`✓ ${name} → ${groups.length} slice(s): ${groups.map(g => (g.labels ?? []).join(' | ') || '(no heading)').join('  ·  ')}`);
  return { name, url, chunks: files.map(f => path.resolve(f)), labels, w: width, h: docH };
}

async function login(url, email, password) {
  await page.goto(BASE + url, { waitUntil: 'networkidle0' });
  await page.type('input[name=email]', email);
  await page.type('input[name=password]', password);
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle0' }), page.click('button[type=submit], form button')]);
}

const shots = [];
for (const [n, u] of PUBLIC) shots.push(await shoot(n, u));
await login('/portal/login', APPLICANT.email, APPLICANT.password);
for (const [n, u] of PORTAL) shots.push(await shoot(n, u));
await login('/login', ADMIN.email, ADMIN.password);
for (const [n, u] of ADMIN_PAGES) shots.push(await shoot(n, u, { fullPage: !u.endsWith('/pipeline') }));
for (const [n, u] of MOBILE) shots.push(await shoot(n, u, { width: 390, height: 844, mobile: true, fullPage: false }));
fs.writeFileSync(path.join(OUT, 'chunks.json'), JSON.stringify(Object.fromEntries(shots.map(s => [s.name, s])), null, 1));
await browser.close();
console.log('done');
