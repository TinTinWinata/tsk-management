import { INote } from "@/Types/note";
import toast from "react-hot-toast";
import { IoCopyOutline } from "react-icons/io5";
import { RxPencil2 } from "react-icons/rx";

interface INoteListProps {
    notes: INote[];
    handleSelect: (idx: number) => void;
}

export default function NoteList({ notes, handleSelect }: INoteListProps) {
    function handleCopy(idx: number) {
        if (notes[idx]) {
            console.log(notes[idx].content);
            navigator.clipboard.writeText(notes[idx].content);
            toast("Succesfully copy to clipboard!", {
                duration: 4000,
                icon: "😊",
            });
        }
    }

    return (
        <div className="flex flex-col gap-3">
            {notes.length > 0 ? (
                notes.map((note, idx: number) => (
                    <div
                        key={note.id}
                        className="p-3 border border-gray-800 border-opacity-30"
                    >
                        <div className="flex justify-between items-center gap-3">
                            <div className="font-semibold text-lg">
                                {note.title}
                            </div>
                            <div className="text-gray-600 flex gap-1">
                                <IoCopyOutline
                                    onClick={() => handleCopy(idx)}
                                    className="cursor-pointer"
                                />
                                <RxPencil2
                                    onClick={() => handleSelect(idx)}
                                    className="cursor-pointer"
                                />
                            </div>
                        </div>
                        <div className="text-sm py-2 whitespace-pre-line">
                            {note.content}
                        </div>
                    </div>
                ))
            ) : (
                <div>You don't have available notes, Create a new One!</div>
            )}
        </div>
    );
}
