import '../css/app.css';
import '../css/transitions.css';
import './bootstrap';

import {createInertiaApp} from '@inertiajs/react';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createRoot} from 'react-dom/client';
import '@gravity-ui/uikit/styles/fonts.css';
import '@gravity-ui/uikit/styles/styles.css';
import {Toaster, ToasterComponent, ToasterProvider} from '@gravity-ui/uikit';
import {Wrapper} from '@/Components/Wrapper';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const toaster = new Toaster();

// Get initial theme from localStorage or default to light
const initialTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', initialTheme);

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
            <Wrapper>
                <ToasterProvider toaster={toaster}>
                    <App {...props} />
                    <ToasterComponent/>
                </ToasterProvider>
            </Wrapper>,
        );
    },
    progress: {
        color: '#b76133',
    },
});
