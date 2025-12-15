import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig(({ mode }) => {
    const plugins = [vue()];

    // Only load Laravel plugin when not in test mode
    if (mode !== "test") {
        plugins.unshift(
            laravel({
                input: ["resources/js/app.js"],
                refresh: true,
            })
        );
    }

    return {
        plugins,
        test: {
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
