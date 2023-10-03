import { IChildrenProps } from "@/types/children";

interface IIconProps extends IChildrenProps {
    onClick?: () => void;
}

export default function Icon({ children, onClick }: IIconProps) {
    return (
        <div
            onClick={() => {
                onClick && onClick();
            }}
            className="transition-all duration-200 cursor-pointer hover:bg-gray-200 rounded-md p-1"
        >
            {children}
        </div>
    );
}
