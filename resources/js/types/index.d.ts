import {ToastProps} from "@gravity-ui/uikit";

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface OfficeData {
    id: string | number;
    name: string;
    timezone: string;
    location: string | null;
    description: string | null;
    rooms_count?: number;
}

export type BreadcrumbItem = {
    url?: string;
    text: string;
}
export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    toast?: ToastProps;
    breadcrumbs: BreadcrumbItem[];
};


export interface DashboardPageProps extends PageProps{
    url: string;
    href: (path?: string) => string;
    offices: OfficeData[];
    selectedOffice: OfficeData | null;
    data: { data: string };
    rooms: RoomData[];
}

export interface RoomData {
    id: number;
    name: string;
    capacity: number;
    description?: string | null;
    office_id: number;
    booked_events: number;
    workload: number;
    status: string;
}
