import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Transition } from "@headlessui/react";
import { useForm } from "@inertiajs/react";
import { FormEventHandler } from "react";

export default function InsertNote({ className = "" }: { className?: string }) {
    const { data, setData, post, errors, processing, recentlySuccessful } =
        useForm({
            title: "",
            content: "",
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route("note.insert"));
    };

    return (
        <section className={className}>
            <header>
                <h2 className="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Insert Note
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

                    <TextInput
                        id="content"
                        type="text"
                        className="mt-1 block w-full"
                        value={data.content}
                        onChange={(e) => setData("content", e.target.value)}
                        required
                        autoComplete="username"
                    />

                    <InputError className="mt-2" message={errors.content} />
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Insert</PrimaryButton>
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
