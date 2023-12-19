import {
    TextareaHTMLAttributes,
    forwardRef,
    useEffect,
    useImperativeHandle,
    useRef,
} from "react";

export default forwardRef(function TextAreaInput(
    {
        className = "",
        isFocused = false,
        ...props
    }: TextareaHTMLAttributes<HTMLTextAreaElement> & { isFocused?: boolean },
    ref
) {
    const localRef = useRef<HTMLTextAreaElement>(null);

    useImperativeHandle(ref, () => ({
        focus: () => localRef.current?.focus(),
    }));

    useEffect(() => {
        if (isFocused) {
            localRef.current?.focus();
        }
    }, []);

    return (
        <textarea
            {...props}
            className={
                "border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-yellow-500 dark:focus:border-yellow-600 focus:ring-yellow-500 dark:focus:ring-yellow-600 rounded-md shadow-sm " +
                className
            }
            ref={localRef}
        />
    );
});
