import "../css/app.css";
import "../css/icons.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import HomeView from "./Pages/FrontEnd/HomeView.vue";
import ProfileView from "./Pages/FrontEnd/ProfileView.vue";
import InformasiView from "./Pages/FrontEnd/InformasiView.vue";
import LayananView from "./Pages/FrontEnd/LayananView.vue";

// Mapping ID div dengan komponen Vue yang relevan
// const pages = {
//     'home-page': HomeView,
//     'profildesa-page': ProfileView,
//     'informasi-page': InformasiView,
//     'layanan-page': LayananView,
// };

// Object.entries(pages).forEach(([id, component]) => {
//     const el = document.getElementById(id);
//     if (el) {
//         createApp(component).mount(el);
//     }
// });

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
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
