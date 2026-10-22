// Builds the client proposal (letter-size PDF) from the captured screenshots.
// Usage: npm run proposal
import puppeteer from 'puppeteer-core';
import fs from 'node:fs';
import path from 'node:path';

const CHROME = process.env.CHROME_PATH ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SHOTS = path.resolve('dist/proposal/shots');
const chunks = JSON.parse(fs.readFileSync(path.join(SHOTS, 'chunks.json'), 'utf8'));
const OUT_HTML = path.resolve('dist/proposal/proposal.html');
const OUT_PDF = path.resolve('dist/proposal/Fortress-Website-and-Recruitment-Platform-Proposal.pdf');
const DATE = new Date().toLocaleDateString('en-TT', { day: 'numeric', month: 'long', year: 'numeric' });
const img = (f) => 'file://' + f;
const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;');

const LOGO = `<svg viewBox="0 0 48 56" width="42" height="49" aria-hidden="true"><path d="M24 2 44 9v17c0 13-8.5 22.5-20 28C12.5 48.5 4 39 4 26V9z" fill="#1f6fe5"/><path d="M24 7 39 12.5V26c0 10.5-6.6 18.3-15 23C15.6 44.3 9 36.5 9 26V12.5z" fill="#0a1a33"/><path d="m24 14 2.6 5.6 6.1.7-4.5 4.2 1.2 6L24 27.5l-5.4 3 1.2-6-4.5-4.2 6.1-.7z" fill="#fff"/><text x="24" y="43" text-anchor="middle" font-family="Helvetica, Arial" font-weight="800" font-size="11" fill="#fff">FSS</text></svg>`;

/** Screen definitions: section, key, title, bullets. */
const SECTIONS = [
  { id: 'public', eyebrow: 'Part 1', title: 'The public website', intro: 'A modern, mobile-first replacement for fortress.tt. Every page is built from the content on the current site and your Facebook page, re-written for clarity and search engines, with licensed photography and a looping control-room video in the hero.', screens: [
    ['home', 'Home page', ['Looping monitoring-room video hero with an animated radar graphic and a kinetic headline (Protection / Prevention / Response / Vigilance).', 'Live credentials strip: IFPO member, STOW certified, Ministry of National Security registration.', 'Featured services with photography, "Why Fortress" with the training story and counters, industries served, and current vacancies pulled live from the recruitment system.', 'Click-to-call and the customer vehicle-tracking link are always one tap away.']],
    ['about', 'About Fortress', ['History timeline from Personnel Protection Services (2005) to today.', 'Mission, vision, commitments and certifications presented as a credibility page for tenders and corporate clients.', 'Both offices (St. Augustine and Marabella) with map links and hours.']],
    ['services', 'Services catalogue', ['Ten services, each with its own photo card: protection officers, cash in transit, VIP protection, CCTV & alarms, 24-hour monitoring, GPS tracking, courier, investigations, event security and valet.', 'Three-step "how we work" (survey, proposal, deploy) sets client expectations.']],
    ['service-cit', 'Service detail page', ['Each service has a dedicated page with photography, what is included, who it is for and a quote call-to-action that pre-selects the service on the contact form.', 'Content is editable by your team in the admin area without a developer.']],
    ['training', 'Officer training', ['The five-level training ladder (aptitude, Level 1, Level 2, Response Officer, Level 3 CIT) as an interactive timeline.', 'Reinforces the recruitment message and the quality story for clients.']],
    ['careers', 'Careers', ['All open vacancies in one searchable, filterable page replacing the Facebook poster workflow.', 'Benefits and minimum requirements taken from your current careers page.', 'Five-step "how it works" and a general application for candidates when no role fits.']],
    ['job', 'Job posting', ['Responsibilities, minimum requirements and benefits in a scannable layout.', 'Google for Jobs structured data so vacancies appear in Google job searches.', 'Prominent Apply call-to-action and "before you apply" document checklist.']],
    ['apply', 'Online application form', ['Five-minute form: personal details, driver\'s permit class, police certificate, precept and firearm licence, experience, CV upload (PDF/Word) and consent.', 'Duplicate protection, spam protection and an automatic confirmation email with a reference number.', 'Signed-in applicants get a pre-filled form.']],
    ['contact', 'Contact & quotes', ['Quote-request form with service selector; enquiries land in the admin CRM and alert the operations inbox.', 'Both offices, WhatsApp button (optional), emails and the 24/7 monitoring note.']],
  ]},
  { id: 'portal', eyebrow: 'Part 2', title: 'The applicant portal', intro: 'Candidates get their own secure area to follow every application, book interviews and keep their CV current. It removes the "has anyone seen my application?" phone calls and gives Fortress a professional edge with recruits.', screens: [
    ['portal-login', 'Applicant sign-in', ['Separate from staff login. Password reset by email.', 'Existing applications submitted with the same email are linked automatically when the candidate registers.']],
    ['portal-register', 'Create account', ['One-step registration; the thank-you page after applying invites candidates to create their account.']],
    ['portal-dashboard', 'Applicant dashboard', ['Every application with a plain-language status: Sent, In review, Interview stage, Accepted or Not successful.', 'Upcoming interview card, CV status and the latest notifications.', 'Suggested open roles the candidate has not applied for.']],
    ['portal-application', 'Application tracker & interview booking', ['Four-step progress bar and a timeline of updates (internal recruiter notes stay private).', 'When invited to interview the candidate picks a time from the slots you publish, or sees the date you set, with a "please bring" checklist.', 'Position details and the CV used on that application.']],
    ['portal-notifications', 'Notifications', ['Every status change, interview booking and recruiter message is recorded here and emailed to the candidate.']],
    ['portal-profile', 'CV & profile', ['Upload, replace or remove a CV; the latest version is attached to the candidate\'s active applications so recruiters always see the current one.', 'Contact details and password management.']],
  ]},
  { id: 'admin', eyebrow: 'Part 3', title: 'The recruitment & administration back office', intro: 'A private, role-based portal for your recruitment officers and management. It replaces email inboxes and paper files with a single pipeline, and keeps the public site content under your control.', screens: [
    ['admin-login', 'Staff sign-in', ['Separate staff accounts with two roles: Administrator (everything) and Recruiter (jobs, applicants, interviews, templates, enquiries).', 'Accounts can be deactivated instantly.']],
    ['admin-dashboard', 'Dashboard', ['New applicants to review, pipeline totals, week-over-week applications, hires this month and live vacancies.', '14-day application chart, upcoming interviews, jobs ranked by applicants and the latest quote enquiries.']],
    ['admin-pipeline', 'Hiring pipeline (drag & drop)', ['Kanban columns from New through Screening, Interview, Assessment & Vetting and Offer.', 'Drag a card to change stage; the candidate is notified automatically.', 'Filter by job or recruiter.']],
    ['admin-applicants', 'Applicant list', ['Search by name, phone, reference or area; filter by job, recruiter, permit class, police certificate, precept and rating.', 'One-click CSV export for management reporting.']],
    ['admin-applicant', 'Applicant profile', ['Qualifications at a glance, cover note and CV download.', 'Decision panel: start review, interview stage, accept, hire, reject. Set or let the candidate self-book the interview.', 'Templated emails (interview invitation, document request, offer, regret) that can move the stage automatically, plus a full notes and activity timeline.', 'Portal status shows whether the candidate has registered and their current CV.']],
    ['admin-jobs', 'Job postings', ['Open, pause, close or duplicate vacancies; closing dates auto-expire listings.', 'Applicant counts with new-applicant badges per job.']],
    ['admin-job-edit', 'Job editor', ['Title, department, location, type, summary, description, responsibilities, requirements and benefits.', 'Urgent flag, permit/precept requirements, publish scheduling and closing date.']],
    ['admin-interviews', 'Interviews & slots', ['Publish interview slots (date, first time, length, how many in a row, seats, location) in one click.', 'See who has booked, who is scheduled and who is still waiting for a time.']],
    ['admin-enquiries', 'Quote enquiries (CRM)', ['Website quote requests with status (new, contacted, quoted, won, closed), owner and internal notes.']],
    ['admin-templates', 'Email templates', ['Edit the wording of every candidate email; placeholders fill in names, roles, references and interview dates.']],
    ['admin-settings', 'Site settings', ['Phone numbers, emails, WhatsApp, social links, notification addresses, careers page intro, home-page announcement banner.', 'Switch general applications and automatic confirmations on or off.']],
    ['admin-users', 'Users & roles', ['Add recruiters and administrators, reset passwords, deactivate leavers.']],
  ]},
];

const FEATURES = [
  ['Public website', ['Responsive on phones, tablets and desktops', 'Licensed photography and video', 'Search-engine ready (sitemap, structured data)', 'Quote-request form with alerts', 'Vehicle-tracking customer link', 'Editable services and settings']],
  ['Recruitment', ['Online applications with CV upload', 'Drag-and-drop hiring pipeline', 'Interview slots and self-booking', 'Templated candidate emails', 'Ratings, notes and recruiter assignment', 'CSV export and dashboard reporting']],
  ['Applicant portal', ['Application status tracking', 'Interview booking or viewing', 'CV storage and updates', 'Email and in-portal notifications', 'Password reset', 'Automatic linking of past applications']],
  ['Platform', ['Laravel 13 on PHP 8.3', 'Runs on Hostinger shared hosting (no extra servers)', 'Private CV storage outside the web root', 'Role-based staff access', 'HTTPS enforced, forms rate-limited', '53 automated tests']],
];

function screenPages(sec, [key, title, bullets]) {
  const c = chunks[key];
  if (!c) return `<section class="page"><h2>${esc(title)}</h2><p class="muted">Screenshot missing: ${key}</p></section>`;
  return c.chunks.map((file, i) => `
    <section class="page shot">
      <header class="shot-head">
        <div><p class="eyebrow">${esc(sec.title)}${i ? ' · continued' : ''}</p><h2>${esc(title)}</h2></div>
        <p class="url">${esc(c.url)}</p>
      </header>
      ${i === 0 ? `<ul class="bullets">${bullets.map(b => `<li>${esc(b)}</li>`).join('')}</ul>` : ''}
      <figure class="${i === 0 && bullets.length > 3 ? 'short' : ''}"><img src="${img(file)}" alt="${esc(title)}"></figure>
    </section>`).join('');
}

const html = `<!doctype html><html><head><meta charset="utf-8"><title>Fortress Security Services – Website & Recruitment Platform Proposal</title>
<style>
  @page { size: Letter; margin: 0.55in 0.6in 0.6in; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; font-family: Inter, -apple-system, "Helvetica Neue", Helvetica, Arial, sans-serif; color: #1f2937; font-size: 10.5pt; line-height: 1.45; }
  h1, h2, h3 { font-family: "Barlow Condensed", "Arial Narrow", Impact, "Helvetica Neue", Arial, sans-serif; text-transform: uppercase; letter-spacing: .01em; color: #0a1a33; margin: 0; line-height: 1; }
  .page { page-break-after: always; break-after: page; min-height: 9.6in; display: flex; flex-direction: column; }
  .page:last-child { page-break-after: auto; }
  .eyebrow { font-size: 8.5pt; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: #1f6fe5; margin: 0 0 4px; }
  .muted { color: #6b7a90; }
  /* cover */
  .cover { background: #050d1a; color: #fff; border-radius: 14px; padding: 0.7in 0.65in; min-height: 9.8in; display: flex; flex-direction: column; justify-content: space-between; background-image: radial-gradient(60% 50% at 80% 20%, rgba(31,111,229,.55), transparent 70%), linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: auto, 44px 44px, 44px 44px; }
  .cover h1 { color: #fff; font-size: 54pt; line-height: .95; margin: 18px 0 14px; }
  .cover .accent { color: #3b8bff; }
  .cover .eyebrow { color: #7fb2ff; }
  .cover p.lead { color: rgba(227,238,255,.85); font-size: 13pt; max-width: 5.6in; }
  .cover .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; border-top: 1px solid rgba(255,255,255,.15); padding-top: 18px; font-size: 9.5pt; color: rgba(227,238,255,.8); }
  .cover .meta b { display: block; color: #fff; font-size: 11pt; }
  .brand { display: flex; align-items: center; gap: 10px; } .brand .n { font-family: "Barlow Condensed", "Arial Narrow", Arial; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; font-size: 20pt; color: #fff; line-height: 1; } .brand .s { display: block; font-size: 8pt; letter-spacing: .28em; color: #7fb2ff; }
  /* text pages */
  .prose h1 { font-size: 30pt; margin-bottom: 10px; }
  .prose h2 { font-size: 18pt; margin: 18px 0 6px; }
  .prose p { margin: 0 0 9px; }
  .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .card { border: 1px solid #dde4ee; border-radius: 10px; padding: 12px 14px; background: #fff; }
  .card h3 { font-size: 12.5pt; margin-bottom: 6px; }
  .card ul { margin: 0; padding-left: 16px; } .card li { margin: 2px 0; }
  .pill-row { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 0; } .pill { border: 1px solid #bcd5ff; background: #f2f7ff; color: #1f6fe5; border-radius: 999px; padding: 2px 9px; font-size: 8.5pt; font-weight: 600; }
  .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 12px 0; } .stat { background: #0a1a33; color: #fff; border-radius: 10px; padding: 12px; } .stat b { display: block; font-family: "Barlow Condensed", "Arial Narrow", Arial; font-size: 24pt; line-height: 1; color: #7fb2ff; } .stat span { font-size: 8.5pt; color: rgba(227,238,255,.8); }
  table.feat { width: 100%; border-collapse: collapse; font-size: 9.5pt; } table.feat th, table.feat td { border-bottom: 1px solid #dde4ee; padding: 6px 8px; text-align: left; vertical-align: top; } table.feat th { background: #f6f8fb; font-size: 8.5pt; text-transform: uppercase; letter-spacing: .08em; color: #6b7a90; }
  .section-divider { background: #0a1a33; color: #fff; border-radius: 14px; padding: 0.8in 0.7in; min-height: 9.8in; display: flex; flex-direction: column; justify-content: center; }
  .section-divider h1 { color: #fff; font-size: 46pt; line-height: .95; margin: 10px 0 18px; } .section-divider p { color: rgba(227,238,255,.85); font-size: 12.5pt; max-width: 5.8in; } .section-divider .eyebrow { color: #7fb2ff; }
  .section-divider ol { color: rgba(227,238,255,.85); columns: 2; font-size: 10pt; margin-top: 22px; padding-left: 18px; } .section-divider li { break-inside: avoid; margin: 2px 0; }
  /* screenshot pages */
  .shot-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; border-bottom: 2px solid #0a1a33; padding-bottom: 6px; margin-bottom: 8px; }
  .shot-head h2 { font-size: 20pt; } .url { font-family: Menlo, Consolas, monospace; font-size: 8.5pt; color: #6b7a90; margin: 0; white-space: nowrap; }
  .bullets { margin: 0 0 8px; padding-left: 16px; font-size: 9.5pt; } .bullets li { margin: 1px 0; }
  figure { margin: 0; flex: 1; display: flex; align-items: flex-start; justify-content: center; min-height: 0; }
  figure img { width: 100%; max-height: 8.5in; object-fit: contain; object-position: top; border: 1px solid #dde4ee; border-radius: 6px; }
  figure.short img { max-height: 7.7in; }
  .phones { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 10px; } .phones figure { display: block; } .phones img { width: 100%; max-height: 7.6in; object-fit: cover; object-position: top; border-radius: 18px; border: 6px solid #0a1a33; } .phones figcaption { text-align: center; font-size: 9pt; color: #6b7a90; margin-top: 6px; }
  .next li { margin: 4px 0; }
  footer.foot { margin-top: auto; padding-top: 8px; font-size: 8pt; color: #94a3b8; display: flex; justify-content: space-between; }
</style></head><body>

<section class="page cover">
  <div class="brand">${LOGO}<div class="n">Fortress<span class="s">Security Services</span></div></div>
  <div>
    <p class="eyebrow">Website proposal · ${esc(DATE)}</p>
    <h1>A modern website <span class="accent">and recruitment platform</span> for Fortress</h1>
    <p class="lead">A complete rebuild of fortress.tt with online applications, an applicant portal and a recruitment back office – built to run on your existing Hostinger hosting.</p>
  </div>
  <div class="meta">
    <div><b>Prepared for</b>Fortress Security Services Ltd<br>8 Desaine Street, St. Augustine, Trinidad &amp; Tobago</div>
    <div><b>Prepared by</b>Keron Lewis<br>Web &amp; application developer</div>
  </div>
</section>

<section class="page prose">
  <p class="eyebrow">Executive summary</p>
  <h1>Why rebuild now</h1>
  <p>The current fortress.tt was last updated in 2017. It is not mobile-friendly, it lists no vacancies, and hiring runs through Facebook posters that ask candidates to email <b>recruitment@fortress.tt</b>. Every application then has to be tracked by hand, interviews are arranged by phone, and candidates have no way to see where they stand.</p>
  <p>This proposal delivers three things in one system: a modern public website that presents Fortress as the professional, certified firm it is; a careers section with online applications that feed a drag-and-drop hiring pipeline for your recruitment officers; and an applicant portal where candidates track progress, book their interview and keep their CV current – with automatic email and in-portal notifications at every step.</p>
  <div class="stat-grid">
    <div class="stat"><b>24</b><span>screens designed, built and tested</span></div>
    <div class="stat"><b>10</b><span>services with photography and detail pages</span></div>
    <div class="stat"><b>5</b><span>minutes to apply online, from any phone</span></div>
    <div class="stat"><b>0</b><span>new servers – runs on Hostinger PHP 8.3</span></div>
  </div>
  <h2>What Fortress gains</h2>
  <div class="cols">
    <div class="card"><h3>For clients &amp; tenders</h3><ul><li>Credibility: IFPO, STOW and Ministry registration front and centre</li><li>Clear service catalogue with quote requests routed to your inbox</li><li>Mobile-first, fast, secure (HTTPS) and search-engine ready</li></ul></div>
    <div class="card"><h3>For recruitment</h3><ul><li>One pipeline for every applicant instead of email threads</li><li>Interview slots that candidates book themselves</li><li>Templated emails, ratings, notes and CSV reporting</li></ul></div>
    <div class="card"><h3>For candidates</h3><ul><li>Apply in five minutes with CV upload and instant reference</li><li>Portal to track status, book interviews and update CVs</li><li>Notifications by email and in the portal</li></ul></div>
    <div class="card"><h3>For management</h3><ul><li>Dashboard: new applicants, hires, vacancies, enquiries</li><li>Role-based access for recruiters and administrators</li><li>Website content editable without a developer</li></ul></div>
  </div>
</section>

<section class="page prose">
  <p class="eyebrow">At a glance</p>
  <h1>What is included</h1>
  <table class="feat"><thead><tr><th style="width:22%">Area</th><th>Features</th></tr></thead><tbody>
    ${FEATURES.map(([a, f]) => `<tr><td><b>${esc(a)}</b></td><td><div class="pill-row">${f.map(x => `<span class="pill">${esc(x)}</span>`).join('')}</div></td></tr>`).join('')}
  </tbody></table>
  <h2>How the pieces connect</h2>
  <p>A candidate finds a vacancy on the <b>careers page</b> (or from a Facebook post linking to it), applies online and receives a reference by email. The application appears instantly in the <b>recruitment back office</b>, where a recruiter screens it, rates it and moves it through the pipeline. When the candidate is invited to interview they log in to the <b>applicant portal</b> and choose a published slot – or see the date the recruiter set. Acceptance, rejection and every message are recorded on the candidate's timeline and sent by email. Website quote requests follow the same path into a simple enquiry CRM.</p>
  <h2>Content and imagery</h2>
  <p>All copy was sourced from the current fortress.tt pages (services, training programme, benefits, requirements, offices) and validated against the Fortress Facebook page and business directory listings. The seven starter vacancies mirror the roles advertised on Facebook in 2026 (security drivers, baton officers for the East-West Corridor and Point Lisas, Tobago officers, EMT officers, social media manager, web developer). Photography and the hero video are licensed stock imagery under a standard licence; they can be replaced with Fortress's own photography at any time.</p>
  <footer class="foot"><span>Fortress Security Services – Website &amp; Recruitment Platform Proposal</span><span>${esc(DATE)}</span></footer>
</section>

${SECTIONS.map(sec => `
<section class="page section-divider">
  <p class="eyebrow">${esc(sec.eyebrow)}</p>
  <h1>${esc(sec.title)}</h1>
  <p>${esc(sec.intro)}</p>
  <ol>${sec.screens.map(s => `<li>${esc(s[1])}</li>`).join('')}</ol>
</section>
${sec.screens.map(s => screenPages(sec, s)).join('')}
`).join('')}

<section class="page">
  <header class="shot-head"><div><p class="eyebrow">Responsive design</p><h2>Built for phones first</h2></div></header>
  <p style="margin:0 0 6px">Most candidates will apply from a phone, often from a Facebook link. Every public page adapts to small screens: the hero, service cards, vacancies and the application form.</p>
  <div class="phones">
    ${[['m-home', 'Home'], ['m-careers', 'Careers'], ['m-job', 'Job posting']].map(([k, c]) => `<figure><img src="${img(chunks[k].chunks[0])}" alt="${c}"><figcaption>${c}</figcaption></figure>`).join('')}
  </div>
</section>
<section class="page">
  <header class="shot-head"><div><p class="eyebrow">Responsive design · continued</p><h2>Portal and back office on a phone</h2></div></header>
  <p style="margin:0 0 6px">Recruiters can work the pipeline and review applicants from the field, and candidates manage everything from their phone.</p>
  <div class="phones">
    ${[['m-portal', 'Applicant portal'], ['m-admin-pipeline', 'Pipeline (staff)'], ['m-admin-applicant', 'Applicant profile (staff)']].map(([k, c]) => `<figure><img src="${img(chunks[k].chunks[0])}" alt="${c}"><figcaption>${c}</figcaption></figure>`).join('')}
  </div>
</section>

<section class="page prose">
  <p class="eyebrow">Technology, hosting &amp; security</p>
  <h1>Built to run on what you have</h1>
  <div class="cols">
    <div class="card"><h3>Platform</h3><ul><li>Laravel 13 (PHP 8.3), MySQL – the most widely supported PHP stack</li><li>Packaged for Hostinger shared hosting: upload one zip, run a guided installer</li><li>No background workers or extra services required</li><li>Automated test suite (53 tests) protects every feature</li></ul></div>
    <div class="card"><h3>Security &amp; privacy</h3><ul><li>HTTPS enforced; forms rate-limited and spam-protected</li><li>CVs stored outside the public web folder, downloadable only by signed-in staff</li><li>Separate staff and applicant logins with role-based permissions</li><li>Candidate consent captured; privacy policy page included</li></ul></div>
    <div class="card"><h3>Performance &amp; reach</h3><ul><li>Optimised WebP imagery (all photos under 2 MB combined) and a 0.7 MB video loop</li><li>Reduced-motion and data-saver aware</li><li>Sitemap, Open Graph and Google for Jobs structured data</li><li>Phone-width layouts tested on every page</li></ul></div>
    <div class="card"><h3>Ownership</h3><ul><li>All content, vacancies, templates and settings editable in the back office</li><li>Admin seeding and documented installation guide</li><li>Source code delivered with the project</li><li>Facebook hiring posts simply link to fortress.tt/careers</li></ul></div>
  </div>
  <h2>Email notifications included</h2>
  <p>Application received (candidate), new applicant alert (recruitment inbox), application in review, invited to interview, interview scheduled / booked, accepted, not successful, recruiter messages from templates, quote-enquiry alert (operations inbox) and applicant password reset – all branded and sent from your Hostinger mailbox.</p>
  <footer class="foot"><span>Fortress Security Services – Website &amp; Recruitment Platform Proposal</span><span>${esc(DATE)}</span></footer>
</section>

<section class="page prose">
  <p class="eyebrow">Next steps</p>
  <h1>From proposal to launch</h1>
  <ol class="next">
    <li><b>Review this proposal</b> and the working demo together; agree on any changes to pages, wording or workflow.</li>
    <li><b>Content sign-off:</b> confirm services copy, offices, contact details and the vacancies to publish at launch. Supply Fortress photography if preferred over the licensed imagery.</li>
    <li><b>Hosting:</b> point fortress.tt at the Hostinger plan, install the free SSL certificate and create the recruitment@ and support@ mailboxes (or confirm the existing ones).</li>
    <li><b>Install and configure:</b> upload the package, run the installer, create administrator and recruiter accounts, set notification addresses and interview locations.</li>
    <li><b>Training (one session):</b> posting jobs, working the pipeline, publishing interview slots, sending templated emails, editing site content.</li>
    <li><b>Launch:</b> switch the domain, submit the sitemap to Google, and update the Facebook "We're hiring" posts to link to fortress.tt/careers.</li>
    <li><b>After launch:</b> monthly check-ins on applicant volumes and content updates; optional additions such as WhatsApp notifications, SMS interview reminders or a client portal for GPS/alarm customers.</li>
  </ol>
  <h2>Questions</h2>
  <p>Keron Lewis · <a href="mailto:strawz32@gmail.com">strawz32@gmail.com</a></p>
  <footer class="foot"><span>Fortress Security Services – Website &amp; Recruitment Platform Proposal</span><span>${esc(DATE)}</span></footer>
</section>
</body></html>`;

fs.writeFileSync(OUT_HTML, html);
const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new', args: ['--no-sandbox', '--allow-file-access-from-files'] });
const page = await browser.newPage();
await page.goto('file://' + OUT_HTML, { waitUntil: 'networkidle0' });
await page.pdf({ path: OUT_PDF, format: 'Letter', printBackground: true, preferCSSPageSize: true, displayHeaderFooter: false });
await browser.close();
console.log('PDF written:', OUT_PDF, Math.round(fs.statSync(OUT_PDF).size / 1024 / 1024 * 10) / 10 + ' MB');
