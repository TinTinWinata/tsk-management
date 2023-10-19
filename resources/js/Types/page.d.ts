export interface IUser {
    id: number;
    name: string;
    token: string | null;
    email: string;
    email_verified_at: string;
}

export interface IMonthData {
    month: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    monthData: IMonthData[];
    auth: {
        user: IUser;
    };
};
