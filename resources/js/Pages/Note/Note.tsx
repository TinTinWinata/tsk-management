import NoteList from "@/Components/Note/NoteList";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { INote } from "@/Types/note";
import { PageProps } from "@/Types/page";
// import Board from "@asseinfo/react-kanban";
import { Head } from "@inertiajs/react";

export default function Note({
    auth,
    monthData,
    notes,
}: PageProps<{ notes: INote[] }>) {
    return (
        <AuthenticatedLayout
            monthData={monthData}
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl texat-gray-800 dark:text-gray-200 leading-tight">
                    Note
                </h2>
            }
        >
            <Head title="Note" />
            <div className="min-h-screen p-20 flex-col">
                <h1 className="font-bold text-3xl">Notes</h1>
                <div className="h-4"></div>
                <NoteList notes={notes} />
            </div>
        </AuthenticatedLayout>
    );
}
