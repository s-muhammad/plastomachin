import 'basecoat-css/all';

const onReady = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
};

onReady(() => {
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

    // Confirmation for delete buttons
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'این مورد حذف شود؟')) {
                event.preventDefault();
            }
        });
    });
});
