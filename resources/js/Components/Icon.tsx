import { IChildrenProps } from "@/types/children";

interface IIconProps extends IChildrenProps {
    onClick?: () => void;
    onMouseEnter?: () => void;
    onMouseLeave?: () => void;
}

export default function Icon({
    children,
    onClick,
    onMouseEnter,
    onMouseLeave,
}: IIconProps) {
    return (
        <div
            onMouseEnter={() => onMouseEnter && onMouseEnter()}
            onMouseLeave={() => onMouseLeave && onMouseLeave()}
            onClick={() => onClick && onClick()}
            className="transition-all duration-200 cursor-pointer hover:bg-gray-200 rounded-md p-1"
        >
            {children}
        </div>
    );
}
