import { Transition } from "@headlessui/react";
import { InertiaLinkProps, Link } from "@inertiajs/react";
import {
    Dispatch,
    Fragment,
    PropsWithChildren,
    SetStateAction,
    createContext,
    useContext,
    useState,
} from "react";

export interface IDropdownProps extends PropsWithChildren {
    onToggle?: (val: boolean) => void;
}

const DropDownContext = createContext<{
    open: boolean;
    setOpen: Dispatch<SetStateAction<boolean>>;
    toggleOpen: () => void;
}>({
    open: false,
    setOpen: () => {},
    toggleOpen: () => {},
});

const Dropdown = ({ children, onToggle }: IDropdownProps) => {
    const [open, setOpen] = useState(false);

    const toggleOpen = () => {
        setOpen((previousState) => {
            onToggle(!previousState);
            return !previousState;
        });
    };

    return (
        <DropDownContext.Provider value={{ open, setOpen, toggleOpen }}>
            <div className="relative">{children}</div>
        </DropDownContext.Provider>
    );
};

const Trigger = ({ children, onToggle }: IDropdownProps) => {
    const { open, setOpen, toggleOpen } = useContext(DropDownContext);

    return (
        <>
            <div onClick={toggleOpen}>{children}</div>

            {open && (
                <div
                    className="cursor-pointer fixed inset-0 z-40"
                    onClick={() => {
                        onToggle && onToggle(false);
                        setOpen(false);
                    }}
                ></div>
            )}
        </>
    );
};

const Content = ({
    align = "right",
    width = "48",
    contentClasses = "overflow-hidden bg-white dark:bg-gray-700",
    children,
    onClickOutside,
}: PropsWithChildren<{
    align?: "left" | "right";
    width?: "48" | "60" | "80";
    contentClasses?: string;
    onClickOutside?: (val: boolean) => void;
}>) => {
    const { open, setOpen } = useContext(DropDownContext);

    let alignmentClasses = "origin-top";

    if (align === "left") {
        alignmentClasses = "origin-top-left left-0";
    } else if (align === "right") {
        alignmentClasses = "origin-top-right right-0";
    }

    let widthClasses = "";

    if (width === "48") {
        widthClasses = "w-48";
    }
    if (width === "60") {
        widthClasses = "w-60";
    }
    if (width === "80") {
        widthClasses = "w-80";
    }

    const handleClick = () => {
        setOpen(false);
        onClickOutside && onClickOutside(false);
    };

    return (
        <>
            <Transition
                as={Fragment}
                show={open}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 scale-95"
                enterTo="opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="opacity-100 scale-100"
                leaveTo="opacity-0 scale-95"
            >
                <div
                    className={`left-2 absolute z-50 mt-2 rounded-md shadow-lg ${alignmentClasses} ${widthClasses}`}
                    onClick={handleClick}
                >
                    <div
                        className={
                            `rounded-md ring-1 ring-black ring-opacity-5 ` +
                            contentClasses
                        }
                    >
                        {children}
                    </div>
                </div>
            </Transition>
        </>
    );
};

const DropdownLink = ({
    className = "",
    children,
    ...props
}: InertiaLinkProps) => {
    return (
        <Link
            {...props}
            className={
                "block w-full bg-gray-100 px-4  text-left text-xs leading-5 py-2 text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out " +
                className
            }
        >
            {children}
        </Link>
    );
};

const DropdownBorder = () => {
    return <hr className="w-full bg-gray-300" />;
};

Dropdown.Trigger = Trigger;
Dropdown.Content = Content;
Dropdown.Link = DropdownLink;
Dropdown.Border = DropdownBorder;

export default Dropdown;
