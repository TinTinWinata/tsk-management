import Schedule from "@/Components/Kanban/Schedule";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { User } from "@/types";
import { IScheduleData } from "@/types/schedule-data";
// import Board from "@asseinfo/react-kanban";
import { Head } from "@inertiajs/react";

export interface IDashboardProps {
    auth: { user: User };
    token: string;
    data: IScheduleData;
}
export default function Dashboard({ auth, data, token }: IDashboardProps) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />
            <Schedule token={token} datas={data}></Schedule>
        </AuthenticatedLayout>
    );
}
