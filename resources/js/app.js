import './bootstrap';
import '../css/app.css';
import { createApp, h, defineAsyncComponent } from 'vue'

// Heavy, route-specific components are loaded async so they stay out of the main bundle.
// Keep this list in sync with the negative glob patterns in the eager registration below.
const ASYNC_COMPONENTS = [
    'BookingForm', 'TripForm', 'DestinationForm', 'CampaignForm', 'ItineraryForm',
    'BlogPostForm', 'TripPricesManager', 'TripItemsTab',
    'DataTable', 'SortableBlocks', 'TipTap', 'ImageUploader',
    'BlockedDatesManager', 'LightBox', 'DatePicker',
]
import { createInertiaApp, router } from '@inertiajs/vue3'
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
import reveal from './Directives/reveal.js';

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
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return pages[`./Pages/${name}.vue`]()
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
            app.directive('reveal', reveal)
            // Register components globally; heavy admin components are loaded async
            const getName = (path) => path.split('/').pop().replace(/\.[^/.]+$/, '')

            // Eagerly register all non-async components. Heavy, route-specific components are
            // excluded from the eager glob so they are not pulled into the main bundle; they are
            // registered async below and code-split into their own chunks.
            const eagerGlobs = [
                import.meta.glob([
                    './Components/**/*.vue',
                    '!**/{BookingForm,TripForm,DestinationForm,CampaignForm,ItineraryForm,BlogPostForm,TripPricesManager,TripItemsTab,DataTable,SortableBlocks,TipTap,ImageUploader,BlockedDatesManager,LightBox,DatePicker}.vue',
                ], { eager: true }),
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
