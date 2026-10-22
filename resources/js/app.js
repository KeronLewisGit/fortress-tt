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
Alpine.data('composer', (templates) => ({
    templates, subject: '', body: '', setStatus: '', open: false,
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

Alpine.start();
