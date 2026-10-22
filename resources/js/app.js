import Alpine from 'alpinejs';

window.Alpine = Alpine;

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

// Kanban pipeline: drag applicants between stage columns, PATCH the new status.
Alpine.data('pipeline', () => ({
    dragging: null,
    over: null,
    toast: '',
    start(e, id) {
        this.dragging = id;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', String(id));
    },
    end() { this.dragging = null; this.over = null; },
    async drop(stage) {
        const id = this.dragging; this.over = null;
        if (!id) return;
        const card = document.getElementById('app-' + id);
        const from = card?.closest('[data-stage]')?.dataset.stage;
        if (!card || from === stage) { this.dragging = null; return; }
        const list = document.querySelector(`[data-stage="${stage}"] [data-list]`);
        if (!list) { this.dragging = null; return; }
        list.prepend(card);
        this.recount();
        try {
            const r = await fetch(card.dataset.url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ status: stage }),
            });
            if (!r.ok) throw new Error('Request failed');
            const d = await r.json();
            this.flash(`${card.dataset.name} moved to ${d.label}`);
        } catch (err) {
            document.querySelector(`[data-stage="${from}"] [data-list]`)?.prepend(card);
            this.recount();
            this.flash('Could not update – please refresh and try again.');
        }
        this.dragging = null;
    },
    recount() {
        document.querySelectorAll('[data-stage]').forEach(col => {
            const n = col.querySelectorAll('[data-list] > [id^="app-"]').length;
            col.querySelector('[data-count]').textContent = n;
            col.querySelector('[data-empty]')?.classList.toggle('hidden', n > 0);
        });
    },
    flash(msg) { this.toast = msg; clearTimeout(this._t); this._t = setTimeout(() => this.toast = '', 2600); },
}));

// Message composer on the applicant page: pick a template → fill subject/body/stage.
Alpine.data('composer', (templates, initial = {}) => ({
    templates,
    subject: initial.subject ?? '',
    body: initial.body ?? '',
    setStatus: initial.set_status ?? '',
    open: Boolean(initial.open),
    pick(id) {
        const t = this.templates.find(x => String(x.id) === String(id));
        if (!t) return;
        this.subject = t.subject; this.body = t.body; this.setStatus = t.set_status ?? '';
    },
}));

// Simple sparkline/bars chart for the dashboard.
Alpine.data('bars', (points) => ({
    points,
    max: Math.max(1, ...points.map(p => p.count)),
    h(p) { return Math.max(4, Math.round((p.count / this.max) * 100)) + '%'; },
}));


// ---------- Motion layer ----------
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Scroll reveal: add .is-visible once an element enters the viewport (staggered via --d set in markup).
const revealTargets = () => document.querySelectorAll('[data-reveal], .reveal-clip');
if ('IntersectionObserver' in window && !reduceMotion) {
    // Clip-path'd elements report an intersection ratio of 0, so they get their own threshold-0 observer.
    const make = (opts) => new IntersectionObserver((entries, obs) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); obs.unobserve(e.target); } });
    }, opts);
    const io = make({ rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    const ioClip = make({ rootMargin: '0px 0px -8% 0px', threshold: 0 });
    document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
    document.querySelectorAll('.reveal-clip').forEach(el => ioClip.observe(el));
} else {
    revealTargets().forEach(el => el.classList.add('is-visible'));
}

// Kinetic headline: cycles through words with an enter/leave transition.
Alpine.data('rotator', (words, interval = 2600) => ({
    words, i: 0, leaving: -1,
    init() {
        if (reduceMotion || words.length < 2) return;
        setInterval(() => {
            this.leaving = this.i;
            this.i = (this.i + 1) % this.words.length;
            setTimeout(() => this.leaving = -1, 500);
        }, interval);
    },
}));

// Count-up number when scrolled into view. Usage: x-data="counter(20)" x-text="shown" with optional suffix.
Alpine.data('counter', (target, duration = 1400) => ({
    shown: reduceMotion ? target : 0,
    init() {
        if (reduceMotion) return;
        const start = () => {
            const t0 = performance.now();
            const step = (now) => {
                const p = Math.min(1, (now - t0) / duration);
                this.shown = Math.round(target * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((es) => { if (es[0].isIntersecting) { start(); io.disconnect(); } }, { threshold: 0.4 });
            io.observe(this.$el);
        } else start();
    },
}));

// Hero video: only play when motion is allowed and the connection isn't flagged as data-saver.
document.querySelectorAll('video[data-hero]').forEach(v => {
    const saveData = navigator.connection?.saveData;
    if (reduceMotion || saveData) { v.removeAttribute('autoplay'); v.pause(); return; }
    v.play().catch(() => {});
});

Alpine.start();
