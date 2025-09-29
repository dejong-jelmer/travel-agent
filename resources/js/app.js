import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js';
import VueTippy from 'vue-tippy'
import { Vue3Mq } from "vue3-mq";
import Vue3TouchEvents from "vue3-touch-events";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import toastOptions from './toastOptions.js';
import screens from './screens.js';
import '@vuepic/vue-datepicker/dist/main.css';


import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
            app.use(plugin)
            app.use(ZiggyVue)
            app.use(Vue3TouchEvents)
            app.use(Toast, toastOptions)
            app.use(Vue3Mq, {
                breakpoints: screens
            });
            app.use(VueTippy, {
                defaultProps: {
                    placement: 'right'
                }
            });
            // Register components globally
            function registerComponents(glob) {
                Object.entries(glob).forEach(([path, definition]) => {
                    const name = path.split('/').pop().replace(/\.[^/.]+$/, '')
                    app.component(name, definition.default)
                });
            }

            const components = import.meta.glob('./Components/**/*.vue', { eager: true })
            registerComponents(components)
            const templates = import.meta.glob('./Templates/*.vue', { eager: true })
            registerComponents(templates)
            const icons = import.meta.glob('./Icons/*.vue', { eager: true })
            registerComponents(icons)


            app.mount(el);
    },
})
