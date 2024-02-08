import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";
import { Toaster } from "react-hot-toast";

createInertiaApp({
    title: (title) => `${title} - Tsk`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob("./Pages/**/*.tsx")
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <>
                <App {...props} />
                <Toaster />
            </>
        );
    },
    progress: {
        color: "#4B5563",
    },
});
