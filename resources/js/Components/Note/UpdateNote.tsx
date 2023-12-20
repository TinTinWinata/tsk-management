import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { INote } from "@/Types/note";
import { Transition } from "@headlessui/react";
import { useForm } from "@inertiajs/react";
import { FormEventHandler } from "react";
import DangerButton from "../DangerButton";
import TextBox from "../TextBox";

export interface IUpdateNoteProps {
    note?: INote;
    onSuceeded?: () => void;
}

export default function UpdateNote({ note, onSuceeded }: IUpdateNoteProps) {
    const {
        data,
        setData,
        patch,
        errors,
        processing,
        recentlySuccessful,
        wasSuccessful,
        delete: destroy,
    } = useForm({
        title: note?.title,
        content: note?.content,
        id: note?.id,
    });

    const submit: FormEventHandler = async (e) => {
        e.preventDefault();
        patch(route("note.update", note?.id), {
            onSuccess: () => onSuceeded && onSuceeded(),
        });
    };
    const handleDelete = () => {
        destroy(route("note.destroy", note?.id), {
            onSuccess: () => onSuceeded && onSuceeded(),
        });
    };

    // destroy(route('profile.destroy'), {
    //     preserveScroll: true,
    //     onSuccess: () => closeModal(),
    //     onError: () => passwordInput.current?.focus(),
    //     onFinish: () => reset(),
    // });

    return (
        <section className="p-6">
            <header>
                <h2 className="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Update Note
                </h2>

                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Keeping notes is an effective way to ensure that we don't
                    forget important information.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="title" value="Title" />

                    <TextInput
                        id="title"
                        className="mt-1 block w-full"
                        value={data.title}
                        onChange={(e) => setData("title", e.target.value)}
                        required
                        isFocused
                        autoComplete="text"
                    />

                    <InputError className="mt-2" message={errors.title} />
                </div>

                <div>
                    <InputLabel htmlFor="content" value="Content" />

                    <TextBox
                        id="content"
                        rows={5}
                        className="mt-1 block w-full"
                        value={data.content}
                        onChange={(e) => setData("content", e.target.value)}
                        required
                        autoComplete="username"
                    />

                    <InputError className="mt-2" message={errors.content} />
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Update</PrimaryButton>
                    <DangerButton
                        disabled={processing}
                        type="button"
                        onClick={handleDelete}
                    >
                        Delete
                    </DangerButton>
                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            Success.
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}
