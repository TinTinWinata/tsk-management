import Icon from "@/Components/Icon";
import Sidebar from "@/Components/Sidebar/Sidebar";
import { IMonthData, IUser } from "@/Types/page";
import { Link } from "@inertiajs/react";
import { PropsWithChildren, ReactNode, useState } from "react";
import { RxHamburgerMenu, RxLayers } from "react-icons/rx";

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
            <div className="h-screen w-full overflow-y-scroll">
                <div className="w-full center relative">
                    <div className="center gap-2 absolute left-2 top-2 text-gray-600">
                        <Icon
                            onMouseEnter={() => setSidebarHover(true)}
                            onMouseLeave={() => setSidebarHover(false)}
                            onClick={handleClickSidebar}
                        >
                            <RxHamburgerMenu className="w-5 h-5" />
                        </Icon>
                        <Link href="/">
                            <Icon>
                                <RxLayers className="w-5 h-5" />
                            </Icon>
                        </Link>
                    </div>
                    <div className="max-w-screen-xl w-full">
                        <main className="w-full p-10">{children}</main>
                    </div>
                </div>
            </div>
        </div>
    );
}
