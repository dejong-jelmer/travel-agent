import './bootstrap';
import '../css/app.css';
import { createApp, createSSRApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import "vue-toastification/dist/index.css";
import '@vuepic/vue-datepicker/dist/main.css';
import { resolvePage, setupApp } from './setupApp.js';

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

// Inertia resets the scroll to the top on each page visit. Because <html> has
// `scroll-smooth`, that reset animates and the window visibly scrolls up on every
// navigation. Disable smooth scrolling for the duration of a visit so the reset is
// instant; in-page anchor links keep their smooth behaviour outside of visits.
router.on('start', () => {
    document.documentElement.style.scrollBehavior = 'auto'
})
router.on('finish', () => {
    document.documentElement.style.scrollBehavior = ''
})

createInertiaApp({
    resolve: resolvePage,
    setup({ el, App, props, plugin }) {
        // Hydrate the HTML rendered by ssr.js. Admin pages, and every page while the SSR server is down, arrive with an
        // empty element and are rendered in the browser instead.
        const app = (el.hasChildNodes() ? createSSRApp : createApp)({ render: () => h(App, props) });
        app.use(plugin)
        setupApp(app, props)
        app.mount(el);
    },
})
