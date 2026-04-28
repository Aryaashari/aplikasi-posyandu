import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true });
        const page = pages[`./Pages/${name}.svelte`];
        if (!page) throw new Error(`Page not found: ./Pages/${name}.svelte`);

        // Sesuai dokumentasi Inertia v3: attach layout ke default export, lalu return modul penuh
        page.default.layout = page.default.layout || page.layout;
        return page;
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});
