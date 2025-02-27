import '../css/app.css';
import './bootstrap';

import {createInertiaApp} from '@inertiajs/react';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createRoot} from 'react-dom/client';
import '@gravity-ui/uikit/styles/fonts.css';
import '@gravity-ui/uikit/styles/styles.css';
import {ThemeProvider, Toaster, ToasterComponent, ToasterProvider} from '@gravity-ui/uikit';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const toaster = new Toaster();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <ThemeProvider theme="light">
                <ToasterProvider toaster={toaster}>
            <App {...props} />
                    <ToasterComponent/>
                </ToasterProvider>
            </ThemeProvider>,
        );
    },
    progress: {
        color: '#b76133',
    },
});
