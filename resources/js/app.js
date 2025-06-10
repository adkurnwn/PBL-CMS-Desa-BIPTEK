import "../css/app.css";
import "../css/icons.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

createInertiaApp({
    title: (title) => {
        // Use dynamic app_name from props, fallback to "Laravel"
        const appName = window.Laravel?.appName || "Laravel";
        return `${title} - ${appName}`;
    },
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        // Make app_name available globally for title function
        window.Laravel = window.Laravel || {};
        window.Laravel.appName = props.initialPage.props.app_name || "Laravel";

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
