import { User } from "@/types";
import { Link } from "@inertiajs/react";
import { PiDotsSixVerticalBold } from "react-icons/pi";
import Dropdown from "./Dropdown";
import Initial from "./Initial";
import Profile from "./Profile";

interface ISidebarProps {
    user: User;
    open: boolean;
}

export default function Sidebar({ user, open }: ISidebarProps) {
    return (
        <div
            className={`${
                open ? "w-[250px]" : "w-0 overflow-hidden "
            } duration-300 transition-all min-h-screen bg-gray-100`}
        >
            <Dropdown>
                <Dropdown.Trigger>
                    <Profile user={user} />
                </Dropdown.Trigger>

                <Dropdown.Content width="80">
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
                    <Dropdown.Link
                        href={route("logout")}
                        method="post"
                        as="button"
                    >
                        Log Out
                    </Dropdown.Link>
                </Dropdown.Content>
            </Dropdown>
            {/* <div className="p-3">Search</div> */}
        </div>
    );
}
