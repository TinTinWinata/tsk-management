import { INote } from "@/Types/note";

interface INoteListProps {
    notes: INote[];
}

export default function NoteList({ notes }: INoteListProps) {
    return (
        <div>
            {notes.map((note) => (
                <div
                    key={note.id}
                    className="p-3 border border-gray-800 border-opacity-30"
                >
                    <div className="font-semibold text-lg">{note.title}</div>
                    <div className="text-sm py-2">{note.content}</div>
                </div>
            ))}
        </div>
    );
}
