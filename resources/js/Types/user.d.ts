export interface User {
    id: number;
    username: string;
    email: string;
    avatar_path?: string | null;
    email_verified_at?: string | null;
    timezone?: string | null;
    clock_format: string;
    created_at?: string;
    updated_at?: string;
}

export type PageProps<T extends Record<string, any> = Record<string, any>> =
    T & {
        auth: {
            user: User;
        };
    };
