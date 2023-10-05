import { IUser } from "@/Types/page";
import { Link } from "@inertiajs/react";
import { PiDotsSixVerticalBold } from "react-icons/pi";
import Dropdown from "./Dropdown";
import Initial from "./Initial";

interface IProfileProps {
    user: IUser;
    onClick?: (val: boolean) => void;
    onClickOutside?: (val: boolean) => void;
}

export default function Profile({
    user,
    onClick,
    onClickOutside,
}: IProfileProps) {
    const initial = user.name.charAt(0).toUpperCase();
    return (
        <Dropdown onToggle={onClick}>
            <Dropdown.Trigger onToggle={onClickOutside}>
                <div className="hover:bg-gray-200 cursor-pointer transition-all duration-200 p-2 flex items-center gap-2">
                    <Initial initial={initial} />
                    <div className="">
                        <div className="font-semibold text-sm text-gray-600 dark:text-gray-200">
                            {user.name}
                        </div>
                        <div className="text-gray-400 font-light text-[10px]">
                            {user.email}
                        </div>
                    </div>
                </div>
            </Dropdown.Trigger>
            <Dropdown.Content onClickOutside={onClickOutside} width="80">
                <div className="pt-3 mx-2 w-full p-1 text-gray-500 text-xs font-semibold">
                    {user.email}
                </div>
                <Link
                    href={route("profile.edit")}
                    className="my-2 cursor-pointer transition-all duration-200 hover:bg-gray-50 flex items-center p-2 gap-2"
                >
                    <PiDotsSixVerticalBold className="text-gray-400" />
                    <Initial initial={user.name} size="xl" />
                    <div className="flex flex-cols items-center justify-center text-gray-700">
                        {user.name}
                    </div>
                </Link>
                <Dropdown.Border />
                <Dropdown.Link href={route("logout")} method="post" as="button">
                    Log Out
                </Dropdown.Link>
            </Dropdown.Content>
        </Dropdown>
    );
}
