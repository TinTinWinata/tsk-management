import { InputHTMLAttributes } from "react";

export default function Checkbox({
    className = "",
    ...props
}: InputHTMLAttributes<HTMLInputElement>) {
    return (
        <input
            {...props}
            type="checkbox"
            className={
                " cursor-pointer rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-yellow-600 shadow-sm focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:focus:`ring-o`ffset-gray-800 " +
                className
            }
        />
    );
}
