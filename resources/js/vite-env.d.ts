/// <reference types="vite/client" />

import { DefineComponent } from "vue";

declare module "*.vue" {
    const component: DefineComponent<{}, {}, any>;
    export default component;
}

// Teach TypeScript about Ziggy's route() function in Vue templates
declare module "vue" {
    interface ComponentCustomProperties {
        route: {
            (name: string, params?: any, absolute?: boolean): string;
            (): {
                current(name: string): boolean;
            };
        };
    }
}

// Teach TypeScript about Inertia's globally injected $page.props
declare module "@inertiajs/core" {
    import { PageProps as DefaultPageProps } from "@inertiajs/core";

    interface PageProps extends DefaultPageProps {
        auth: {
            user: {
                id: number;
                name: string;
                email: string;
                email_verified_at?: string;
            };
        };
    }
}

declare global {
    function route(name?: string, params?: any, absolute?: boolean): any;
}
