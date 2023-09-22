import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { User } from "@/types";
// import Board from "@asseinfo/react-kanban";
import { Head } from "@inertiajs/react";

export interface IDashboardProps {
    auth: { user: User };
    dates: string[];
}
export default function Dashboard({ auth, dates }: IDashboardProps) {
    const board = {
        columns: dates.map((date, index: number) => ({
            id: index,
            title: date,
        })),
    };

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
            {/* <Board initialBoard={board} />a */}
        </AuthenticatedLayout>
    );
}
