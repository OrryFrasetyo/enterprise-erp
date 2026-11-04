import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Enterprise ERP';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        if (name.includes('::')) {
            const [module, page] = name.split('::');
            const kebabModule = module
                .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
                .toLowerCase();

            return resolvePageComponent(
                `../../app-modules/${kebabModule}/resources/js/Pages/${page}.jsx`,
                import.meta.glob('../../app-modules/*/resources/js/Pages/**/*.jsx'),
            );
        }

        return resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        );
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
