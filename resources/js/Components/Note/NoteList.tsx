import { INote } from "@/Types/note";
import NoteCard from "./NoteCard";

interface INoteListProps {
    notes: INote[];
    handleSelect: (idx: number) => void;
}

export default function NoteList({ notes, handleSelect }: INoteListProps) {
    return (
        <div className="flex flex-col gap-3">
            {notes.length > 0 ? (
                notes.map((note, idx: number) => (
                    <NoteCard
                        key={idx}
                        note={note}
                        idx={idx}
                        handleSelect={handleSelect}
                    />
                ))
            ) : (
                <div className="text-gray-600">
                    You don't have available notes, Create a new One!
                </div>
            )}
        </div>
    );
}
