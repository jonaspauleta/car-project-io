import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    flash?: {
        message?: string;
        token?: string;
    };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Car {
    id: number;
    user_id: number;
    make: string;
    model: string;
    year: number;
    nickname?: string;
    vin?: string;
    image_url?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
    user?: User;
    modifications?: Modification[];
    modifications_count?: number;
}

export interface Modification {
    id: number;
    car_id: number;
    name: string;
    category: string;
    notes?: string;
    brand?: string;
    vendor?: string;
    installation_date?: string;
    cost?: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    car?: Car;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev?: string;
        next?: string;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}
