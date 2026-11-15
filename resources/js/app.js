import 'basecoat-css/all';

const onReady = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
};

onReady(() => {
    // Track contact intent clicks without delaying the user's navigation.
    if (!window.location.pathname.startsWith('/admin')) {
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href') || '';
            let type = null;
            if (/^https?:/i.test(href) && (href.includes('wa.me/') || href.includes('whatsapp.com/'))) type = 'whatsapp';

            if (/^tel:/i.test(href)) {
                type = 'phone';
            } else if (/^whatsapp:/i.test(href) || /(^|\\.)wa\\.me$/i.test((() => {
                try { return new URL(href, window.location.href).hostname; } catch { return ''; }
            })()) || /(^|\\.)whatsapp\\.com$/i.test((() => {
                try { return new URL(href, window.location.href).hostname; } catch { return ''; }
            })())) {
                type = 'whatsapp';
            }

            if (!type) return;

            const query = new URLSearchParams(window.location.search);
            let linkUrl = href;
            if (type === 'whatsapp') {
                try {
                    if (/^whatsapp:/i.test(href)) {
                        linkUrl = 'https://wa.me';
                    } else {
                        const target = new URL(href, window.location.href);
                        linkUrl = target.origin + target.pathname;
                    }
                } catch {
                    linkUrl = 'https://wa.me';
                }
            }
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const endpoint = document.querySelector('meta[name="contact-click-endpoint"]')?.content;
            if (!csrf || !endpoint) return;

            const payload = {
                type,
                page_url: window.location.origin + window.location.pathname,
                link_url: linkUrl.slice(0, 2048),
                referrer: document.referrer ? (() => {
                    try {
                        const ref = new URL(document.referrer);
                        return ref.origin + ref.pathname;
                    } catch { return ''; }
                })() : null,
                utm_source: query.get('utm_source'),
                utm_medium: query.get('utm_medium'),
                utm_campaign: query.get('utm_campaign'),
            };

            fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            }).catch(() => {});
        }, true);
    }

    // Mobile navigation
    const menu = document.querySelector('[data-mobile-menu]');
    const openButton = document.querySelector('[data-mobile-menu-open]');
    const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');

    const closeMenu = () => {
        menu?.classList.add('hidden');
        openButton?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
    };

    openButton?.addEventListener('click', () => {
        menu?.classList.remove('hidden');
        openButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
        menu?.querySelector('button[data-mobile-menu-close]')?.focus();
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeMenu);
    });

    menu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !menu || menu.classList.contains('hidden')) return;

        closeMenu();
        openButton?.focus();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) closeMenu();
    });

    // Admin sidebar (Basecoat initializes the component and exposes toggle/close)
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const sidebar = document.getElementById(button.dataset.sidebarToggle || 'sidebar');

            if (sidebar && typeof sidebar.toggle === 'function') {
                sidebar.toggle();
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        const sidebar = document.getElementById('sidebar');
        const breakpoint = Number(sidebar?.dataset.breakpoint) || 768;

        if (window.innerWidth < breakpoint && sidebar && typeof sidebar.close === 'function' && sidebar.getAttribute('aria-hidden') === 'false') {
            sidebar.close();
        }
    });

    // Repeating FAQ fields in the admin panel
    document.querySelectorAll('[data-faq-repeater]').forEach((repeater) => {
        const list = repeater.querySelector('[data-faq-rows]');
        const template = repeater.querySelector('template');
        const addButton = repeater.querySelector('[data-faq-add]');
        let index = Number(repeater.dataset.nextIndex || 0);

        addButton?.addEventListener('click', () => {
            if (!template || !list) return;
            const html = template.innerHTML.replaceAll('__INDEX__', String(index++));
            list.insertAdjacentHTML('beforeend', html);
            repeater.querySelector('[data-faq-empty]')?.remove();
        });

        repeater.addEventListener('click', (event) => {
            const remove = event.target.closest('[data-faq-remove]');
            if (!remove) return;
            remove.closest('[data-faq-row]')?.remove();
        });
    });

    // Rich text editor (only loaded on admin pages that use it)
    if (document.querySelector('textarea[data-editor]')) {
        import('./editor')
            .then(({ initEditors }) => initEditors())
            .catch((error) => console.error('CKEditor bundle failed to load:', error));
    }

    // SEO keyword graph (only loaded on the graph tab)
    if (document.querySelector('[data-seo-graph]')) {
        import('./seo-graph')
            .then(({ initSeoGraph }) => initSeoGraph())
            .catch((error) => console.error('SEO graph bundle failed to load:', error));
    }

    // Confirmation for delete buttons
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'این مورد حذف شود؟')) {
                event.preventDefault();
            }
        });
    });

    // Character counters used by the SEO screens
    document.querySelectorAll('[data-char-counter]').forEach((counter) => {
        const input = document.getElementById(counter.dataset.charCounter);
        if (!input) return;

        const max = Number(counter.dataset.max) || 0;
        const update = () => {
            const length = Array.from(input.value).length;
            counter.textContent = `${length.toLocaleString('fa-IR')} / ${max.toLocaleString('fa-IR')} نویسه`;
            counter.classList.toggle('text-brand-dark', max > 0 && length > max);
            counter.classList.toggle('font-bold', max > 0 && length > max);
        };

        input.addEventListener('input', update);
        update();
    });

    // Shared create dialogs: reset fields each time they are opened.
    document.querySelectorAll('[data-create-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.createDialog);
            if (!dialog) return;

            dialog.querySelector('form')?.reset();
            dialog.showModal();
        });
    });

    // Shared edit dialogs: fill the form from the clicked row and open it.
    document.querySelectorAll('[data-edit-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.editDialog);
            const form = dialog?.querySelector('form');
            if (!dialog || !form) return;

            if (button.dataset.action) {
                form.action = button.dataset.action;
            }

            let record = {};
            try {
                record = JSON.parse(button.dataset.record || '{}');
            } catch (error) {
                record = {};
            }

            Object.entries(record).forEach(([name, value]) => {
                const field = form.elements.namedItem(name);
                if (!field) return;
                field.value = value ?? '';
            });

            dialog.showModal();
        });
    });
});
