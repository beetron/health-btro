export interface User {
    id: number;
    name: string;
    username: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<T extends Record<string, any> = Record<string, any>> =
    T & {
        auth: {
            user: User;
        };
    };
