import './bootstrap';
import '../css/app.css';
import { createApp, h, defineAsyncComponent } from 'vue'

// Only heavy admin-only components are loaded async
const ASYNC_COMPONENTS = [
    'BookingForm', 'TripForm', 'DestinationForm', 'CampaignForm', 'ItineraryForm',
    'DataTable', 'SortableBlocks', 'ImageUploader', 'TripPricesManager',
    'TripItemsTab', 'BlockedDatesManager', 'LightBox',
]
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js';
import VueTippy from 'vue-tippy'
import { Vue3Mq } from "vue3-mq";
import Vue3TouchEvents from "vue3-touch-events";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import toastOptions from './toastOptions.js';
import screens from './screens.js';
import VueHoneypot from 'vue-honeypot'
import '@vuepic/vue-datepicker/dist/main.css';
import i18n from './plugins/i18n';

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
            app.use(VueHoneypot)
            app.use(i18n)

            // Sync i18n locale with server-side locale
            if (props.initialPage.props.locale) {
                i18n.global.locale.value = props.initialPage.props.locale;
            }

            app.use(Vue3Mq, {
                breakpoints: screens
            });
            app.use(VueTippy, {
                defaultProps: {
                    placement: 'right'
                }
            });
            // Register components globally; heavy admin components are loaded async
<<<<<<< HEAD
            const getName = (path) => path.split('/').pop().replace(/\.[^/.]+$/, '')

            // Eagerly register all non-async components
            const eagerGlobs = [
                import.meta.glob('./Components/**/*.vue', { eager: true }),
                import.meta.glob('./Templates/*.vue', { eager: true }),
                import.meta.glob('./Icons/*.vue', { eager: true }),
            ]
            for (const glob of eagerGlobs) {
                for (const [path, module] of Object.entries(glob)) {
                    const name = getName(path)
                    if (!ASYNC_COMPONENTS.includes(name)) {
                        app.component(name, module.default ?? module)
                    }
                }
=======
            function registerComponents(glob) {
                Object.entries(glob).forEach(([path, load]) => {
                    const name = path.split('/').pop().replace(/\.[^/.]+$/, '')
                    if (ASYNC_COMPONENTS.includes(name)) {
                        app.component(name, defineAsyncComponent(load))
                    } else {
                        app.component(name, load.default ?? load)
                    }
                });
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
            }

            // Lazily register async components (loader is a function → defineAsyncComponent works correctly)
            for (const [path, loader] of Object.entries(import.meta.glob('./Components/**/*.vue'))) {
                const name = getName(path)
                if (ASYNC_COMPONENTS.includes(name)) {
                    app.component(name, defineAsyncComponent(loader))
                }
            }


            app.mount(el);
    },
})
