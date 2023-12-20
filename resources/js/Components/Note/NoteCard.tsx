import { INote } from "@/types/note";
import { createRef } from "react";
import toast from "react-hot-toast";
import { IoCopyOutline } from "react-icons/io5";
import { RxPencil2 } from "react-icons/rx";

interface INoteCardProps {
    note: INote;
    handleSelect: (idx: number) => void;
    idx: number;
}

export default function NoteCard({ note, handleSelect, idx }: INoteCardProps) {
    const contentRef = createRef<HTMLDivElement>();
    function handleCopy() {
        if (contentRef && contentRef.current) {
            const range = document.createRange();
            range.selectNode(contentRef.current);
            window.getSelection().removeAllRanges();

            window.getSelection().addRange(range);
            document.execCommand("copy");

            window.getSelection().removeAllRanges();

            toast("Succesfully copy to clipboard!", {
                duration: 4000,
                icon: "😊",
            });
        }
    }

    return (
        <div
            key={note.id}
            className="p-3 border border-gray-800 border-opacity-20"
        >
            <div className="flex justify-between items-center gap-3">
                <div className="font-semibold text-lg">{note.title}</div>
                <div className="text-gray-600 flex gap-1">
                    <IoCopyOutline
                        onClick={() => handleCopy()}
                        className="cursor-pointer"
                    />
                    <RxPencil2
                        onClick={() => handleSelect(idx)}
                        className="cursor-pointer"
                    />
                </div>
            </div>
            <div ref={contentRef} className="text-sm py-2 whitespace-pre-line">
                {note.content}
            </div>
        </div>
    );
}
