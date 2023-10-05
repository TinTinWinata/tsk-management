export interface ISidebarMenuProps {
    name: string;
    onClick?: () => void;
    icon: JSX.Element;
}

export default function SidebarMenu({
    name,
    onClick,
    icon,
}: ISidebarMenuProps) {
    return (
        <div className="w-full center text-gray-500">
            <div
                className="m-1 flex items-center w-full rounded-md  px-1 hover:bg-hover cursor-pointer transition-all duration-100"
                onClick={() => onClick && onClick()}
            >
                <div className="center w-8 h-8">{icon}</div>
                <div className="font-medium text-sm">{name}</div>
            </div>
        </div>
    );
}
