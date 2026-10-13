import { createSSRApp, h } from 'vue'
import { renderToString } from 'vue/server-renderer'
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { route } from 'ziggy-js'
import { resolvePage, setupApp } from './setupApp.js'

// Renders the first visit of a page to HTML; Laravel posts the page to this server and app.js hydrates the result.
// Start it with `php artisan inertia:start-ssr` after `npm run build:ssr`.
//
// One Node process renders the requests of every visitor, so anything kept on module level is shared between them.
// The i18n instance is: that is safe because the public site always renders in Dutch (SetLocale) and admin pages,
// where the locale can change, are not rendered here (HandleInertiaRequests::ssrEnabled).
createServer(page =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: resolvePage,
        setup({ App, props, plugin }) {
            // There is no @routes script on the server, so the route list comes from the ziggy prop
            const ziggy = {
                ...page.props.ziggy,
                location: new URL(page.props.ziggy.location),
            }

            // Script blocks call the global route() that @routes defines in the browser. Being global, it is shared with
            // the requests rendered at the same time; that is harmless as long as no script block on a public page
            // calls route().current(), as the route list is the same for every request and only the location differs.
            globalThis.route = (name, params, absolute, config = ziggy) => route(name, params, absolute, config)

            const app = createSSRApp({ render: () => h(App, props) })
            app.use(plugin)

            return setupApp(app, props, ziggy)
        },
    })
)
