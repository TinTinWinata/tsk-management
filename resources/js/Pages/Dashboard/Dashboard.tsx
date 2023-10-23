import Schedule from "@/Components/Kanban/Schedule";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps } from "@/Types/page";
import { IScheduleData } from "@/Types/schedule-data";
// import Board from "@asseinfo/react-kanban";
import { Head } from "@inertiajs/react";

export default function Dashboard({
    auth,
    data,
    monthData,
}: PageProps<{ data: IScheduleData }>) {
    return (
        <AuthenticatedLayout
            monthData={monthData}
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl texat-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />
            <Schedule token={auth.user.token} datas={data}></Schedule>
        </AuthenticatedLayout>
    );
}
