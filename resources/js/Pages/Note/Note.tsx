import NoteList from "@/Components/Note/NoteList";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { INote } from "@/Types/note";
import { PageProps } from "@/Types/page";
import { useState } from "react";
// import Board from "@asseinfo/react-kanban";
import Modal from "@/Components/Modal";
import InsertNote from "@/Components/Note/InsertNote";
import UpdateNote from "@/Components/Note/UpdateNote";
import { Head } from "@inertiajs/react";

export default function Note({
    auth,
    monthData,
    notes,
}: PageProps<{ notes: INote[] }>) {
    const [modal, setModal] = useState<boolean>(false);
    const [selectedNote, setSelectedNote] = useState<INote | null>(null);
    function handleSelect(idx: number) {
        if (notes[idx]) {
            setSelectedNote(notes[idx]);
        }
    }

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
                <div className="flex">  
                    <h1 className="font-bold text-3xl">Notes</h1>
                    <div
                        onClick={() => setModal(true)}
                        className="center ml-3 py-1 cursor-pointer px-3 border border-gray-300"
                    >
                        Insert
                    </div>
                    <Modal show={modal} onClose={() => setModal(false)}>
                        <InsertNote />
                    </Modal>
                    <Modal
                        show={selectedNote !== null}
                        onClose={() => setSelectedNote(null)}
                    >
                        <UpdateNote note={selectedNote} />
                    </Modal>
                </div>
                <div className="h-4"></div>
                <NoteList notes={notes} handleSelect={handleSelect} />
            </div>
        </AuthenticatedLayout>
    );
}
