import { User } from "./user";

export interface DailyStat {
    id: number;
    user_id: number;
    log_date: string;
    body_weight: number;
    body_fat: number;
    sleep_duration: number;
    mood: number;
    workout: boolean;
    cardio: boolean;
    notes: string | null;
    created_at?: string;
    updated_at?: string;

    // Optional loaded relationship to easily pull owner context if needed
    user?: User;
}
