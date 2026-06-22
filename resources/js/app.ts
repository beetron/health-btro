import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/src/js";
import type { DefineComponent } from "vue";

// Extend the global window interface to allow runtime application naming parameters from Blade templates
declare global {
    interface Window {
        Laravel?: {
            appName: string;
        };
    }
}

// Fall back to the globally declared runtime variable injected by the Blade layout to support dynamic names in immutable environments
const appName = window.Laravel?.appName || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            // Add <DefineComponent> to strongly type the glob imports
            import.meta.glob<DefineComponent>("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
