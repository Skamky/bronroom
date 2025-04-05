import {ToastProps} from "@gravity-ui/uikit";

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    toast?: ToastProps
};


export interface DashboardPageProps extends PageProps{
    url: string;
    href: (path?: string) => string;
    offices: { id: string; name: string; timezone:string; location: string|null; description: string|null }[];
    selectedOffice: {
        id: string;
        name: string;
        timezone: string;
        location: string | null;
        description: string | null
    };
    data: { data: string };
}
