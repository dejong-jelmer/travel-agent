import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig(({ mode, isSsrBuild }) => {
    const plugins = [vue()];

    // Only load Laravel plugin when not in test mode
    if (mode !== "test") {
        plugins.unshift(
            laravel({
                input: ["resources/js/app.js"],
                ssr: "resources/js/ssr.js",
                refresh: true,
            })
        );
    }

    return {
        plugins,
        ...(isSsrBuild && {
            // Bundle the dependencies into the SSR build (`vite build --ssr`) as well: Node cannot resolve named
            // imports from CommonJS packages such as lodash and vue-toastification on its own
            ssr: {
                noExternal: true,
            },
            // Vue and vue-i18n read NODE_ENV at runtime in Node. The SSR server is started without it, which would run
            // them in development mode: slower, and with development warnings in the log.
            define: {
                "process.env.NODE_ENV": JSON.stringify("production"),
            },
        }),
        test: {
            // Only scan the app's own sources, so copies in git worktrees under .claude/ are not picked up
            dir: "resources/js",
            globals: true,
            environment: "happy-dom",
            pool: "vmThreads",
            setupFiles: ["./resources/js/__tests__/setup.js"],
        },
        resolve: {
            alias: {
                "@": path.resolve(__dirname, "resources/js"),
            },
        },
    };
});
