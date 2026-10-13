// Shared by app.js (browser) and ssr.js (server), so both build the same app and the server-rendered HTML hydrates
// without mismatches.
import { defineAsyncComponent } from 'vue'
import { ZiggyVue } from 'ziggy-js'
import VueTippy from 'vue-tippy'
import { Vue3Mq } from 'vue3-mq'
import Vue3TouchEvents from 'vue3-touch-events'
import Toast from 'vue-toastification'
import VueHoneypot from 'vue-honeypot'
import toastOptions from './toastOptions.js'
import screens from './screens.js'
import i18n from './plugins/i18n'
import reveal from './Directives/reveal.js'

// Heavy, route-specific components are loaded async so they stay out of the main bundle.
// Keep this list in sync with the negative glob patterns in the eager registration below.
const ASYNC_COMPONENTS = [
    'BookingForm', 'TripForm', 'DestinationForm', 'CampaignForm', 'ItineraryForm',
    'BlogPostForm', 'TestimonialForm', 'TripPricesManager', 'TripItemsTab',
    'DataTable', 'SortableBlocks', 'TipTap', 'ImageUploader',
    'BlockedDatesManager', 'LightBox', 'DatePicker',
]

export const resolvePage = name => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    return pages[`./Pages/${name}.vue`]()
}

/**
 * Registers the plugins, directives and global components on the app.
 *
 * @param {import('vue').App} app
 * @param {Object} props The props Inertia passes to setup()
 * @param {Object} [ziggy] The Ziggy config; the browser leaves it out and uses the one from @routes
 */
export function setupApp(app, props, ziggy) {
    app.use(ZiggyVue, ziggy)
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
            '!**/{BookingForm,TripForm,DestinationForm,CampaignForm,ItineraryForm,BlogPostForm,TestimonialForm,TripPricesManager,TripItemsTab,DataTable,SortableBlocks,TipTap,ImageUploader,BlockedDatesManager,LightBox,DatePicker}.vue',
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

    return app
}
