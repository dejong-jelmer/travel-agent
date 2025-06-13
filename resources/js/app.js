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
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Vue3TouchEvents)
            .use(Toast, toastOptions)
            .use(Vue3Mq, {
                breakpoints: screens
            })
            .use(VueTippy, {
                defaultProps: {
                    placement: 'right'
                }
            })
            .mount(el)
    },
})
