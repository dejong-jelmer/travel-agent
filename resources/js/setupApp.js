// Shared by app.js (browser) and ssr.js (server), so both build the same app and the server-rendered HTML hydrates
// without mismatches.
import { defineAsyncComponent } from 'vue'
import { ZiggyVue } from 'ziggy-js'
import Vue3TouchEvents from 'vue3-touch-events'
import Toast from 'vue-toastification'
import VueHoneypot from 'vue-honeypot'
import toastOptions from './toastOptions.js'
import i18n from './plugins/i18n'
import reveal from './Directives/reveal.js'

// The components the public pages render are registered eagerly, so the server renders them and the browser hydrates
// them at once. All others (the admin, the booking steps, heavy editors such as TipTap and the LightBox) are loaded
// when they are first rendered, which keeps them out of the bundle every visitor downloads. A component that a public
// page starts to use belongs in this list: left out, it still works, but hydrates only once its own chunk has loaded.
const EAGER_COMPONENTS = import.meta.glob([
    './Components/Atoms/{Button,CallButton,DecorativeLine,DefaultLink,FormFeedback,HighlightIcon,KeyFactIcon,Label,MailButton,Pill,ResponsiveImage,ScrollIndicator,SectionHeader}.vue',
    './Components/Molecules/{BaseCard,Card,Checkbox,Input,MobileMenu,Modal,NavDropdown,NavLink,Newsletter,PageBreadcrumbs,PriceBadge,PullQuote,QuoteBlock,Select,TextArea,USP}.vue',
    './Components/Organisms/{Footer,HomeHero,HomeTestimonials,HorizontalSlider,Nav,PageHero,TestimonialSlider,Topbar,TripCard,TripHero,TripHighlights,TripItems,TripItinerary,TripKeyFacts,TripPlanCard,TripStorySection,TripTestimonials,TripTravelInfo,Trust}.vue',
    './Components/Organisms/Forms/{ContactForm,TripRequestForm}.vue',
    './Templates/{Layout,NewsletterTemplate}.vue',
    './Icons/{Logo,LogoWhite,Sto,StoSmall}.vue',
], { eager: true })

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

    app.directive('reveal', reveal)

    // Register every component globally by its file name: the eager ones as they are, the others async
    const getName = (path) => path.split('/').pop().replace(/\.[^/.]+$/, '')
    const loaders = import.meta.glob(['./Components/**/*.vue', './Templates/*.vue', './Icons/*.vue'])

    for (const [path, loader] of Object.entries(loaders)) {
        const module = EAGER_COMPONENTS[path]
        app.component(getName(path), module ? (module.default ?? module) : defineAsyncComponent(loader))
    }

    return app
}

/**
 * Installs what only the admin uses, loaded on demand so it stays out of the bundle of the public pages. Admin pages
 * are never rendered on the server (HandleInertiaRequests::ssrEnabled), so app.js installs these in the browser before
 * it mounts an admin page. Links from the public pages into the admin are plain links, so the first page decides.
 *
 * @param {import('vue').App} app
 */
export async function setupAdmin(app) {
    const [{ default: VueTippy }] = await Promise.all([
        import('vue-tippy'),
        import('tippy.js/dist/tippy.css'),
    ])

    app.use(VueTippy, {
        defaultProps: {
            placement: 'right'
        }
    })
}
