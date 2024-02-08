import Icon from "@/Components/Icon";
import Sidebar from "@/Components/Sidebar/Sidebar";
import { IMonthData, IUser } from "@/Types/page";
import { Link } from "@inertiajs/react";
import { PropsWithChildren, ReactNode, useState } from "react";
import { RxClipboard, RxHamburgerMenu, RxHome } from "react-icons/rx";

export default function Authenticated({
    user,
    header,
    children,
    monthData,
}: PropsWithChildren<{
    user: IUser;
    header?: ReactNode;
    monthData: IMonthData[];
}>) {
    const [sidebar, setSidebar] = useState<boolean>(false);
    const [sidebarHover, setSidebarHover] = useState<boolean>(false);
    const handleClickSidebar = () => {
        setSidebar((prev) => !prev);
    };

    return (
        <div className="flex">
            <Sidebar
                monthData={monthData}
                hover={sidebarHover}
                open={sidebar}
                user={user}
            />
            <div className="dark:bg-gray-800 h-screen w-full">
                <div className="w-full center relative">
                    <div className="center gap-2 absolute left-2 top-2 text-gray-600">
                        <Icon
                            onMouseEnter={() => setSidebarHover(true)}
                            onMouseLeave={() => setSidebarHover(false)}
                            onClick={handleClickSidebar}
                        >
                            <RxHamburgerMenu className="w-4 h-4" />
                        </Icon>
                        <Link href="/">
                            <Icon>
                                <RxHome className="w-4 h-4" />
                            </Icon>
                        </Link>
                        <Link href="/note">
                            <Icon>
                                <RxClipboard className="w-4 h-4" />
                            </Icon>
                        </Link>
                    </div>
                    <div className="max-w-screen-xl w-full">
                        <main className="w-full p-10 ">{children}</main>
                    </div>
                </div>
            </div>
        </div>
    );
}
