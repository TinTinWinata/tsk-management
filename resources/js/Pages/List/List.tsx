import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps } from "@/Types/page";
import { ISchedule } from "@/Types/schedule";
import { formatDate } from "@/Util/Date";
import { IoBookmarks, IoCalendarSharp, IoText } from "react-icons/io5";
// import Board from "@asseinfo/react-kanban";
import HeaderTable from "@/Components/List/HeaderTable";
import { Head } from "@inertiajs/react";

export default function List({
    auth,
    data,
    monthData,
}: PageProps<{ data: ISchedule[] }>) {
    return (
        <AuthenticatedLayout
            monthData={monthData}
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    List
                </h2>
            }
        >
            <Head title="Dashboard" />
            <div className="min-h-screen p-20 flex-col">
                <h1 className="font-bold text-3xl">Schedule List</h1>
                {data.length > 0 ? (
                    <table className="text-sm w-full mt-5">
                        <tbody>
                            <tr className="text-gray-400 ">
                                <HeaderTable
                                    className="border-r"
                                    icon={<IoText />}
                                    name="Title"
                                />
                                <HeaderTable
                                    className="border-r"
                                    icon={<IoCalendarSharp />}
                                    name="Date"
                                />
                                <HeaderTable
                                    icon={<IoBookmarks />}
                                    name="Is Done"
                                />
                            </tr>
                            {data.map((data: ISchedule, index: number) => (
                                <tr className="text-gray-800" key={index}>
                                    <td className="font-semibold border-b border-r p-2">
                                        {data.title}
                                    </td>
                                    <td className="border-b border-r p-2">
                                        {formatDate(data.date)}
                                    </td>

                                    <td className="border-b  p-2 ">
                                        <div
                                            className={
                                                "text-xs  font-semibold bg-opacity-75 w-fit px-2 py-1 rounded-xl " +
                                                (data.is_done
                                                    ? "bg-green-200"
                                                    : "bg-red-200")
                                            }
                                        >
                                            {data.is_done ? "Done" : "Not Yet"}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <div className="text-gray-600">
                        You don't have available tasks, Create a new One!
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
