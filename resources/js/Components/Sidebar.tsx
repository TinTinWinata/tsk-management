import { IMonthData, IUser } from "@/Types/page";
import { useEffect, useState } from "react";
import { RxCaretRight, RxRocket } from "react-icons/rx";
import Profile from "./Profile";
import SidebarMenu from "./SidebarMenu";

interface ISidebarProps {
    user: IUser;
    open: boolean;
    hover: boolean;
    monthData: IMonthData[];
}

export default function Sidebar({
    user,
    open,
    hover,
    monthData,
}: ISidebarProps) {
    const [stay, setStay] = useState<boolean>(false);
    const [first, setFirst] = useState<boolean>(true);

    const width = "min-w-[240px]";
    const timeToClose = 500;

    useEffect(() => {
        if (open === false && !first) {
            setStay(true);
            setTimeout(() => {
                setStay(false);
            }, timeToClose);
        }
        if (first) setFirst(false);
    }, [open]);

    const closeStay = (time: number = 500) => {
        setTimeout(() => {
            if (stay) setStay(false);
        }, time);
    };

    const handleOnMouseLeave = () => {
        closeStay(350);
    };

    const handleOnProfileClickOutside = (val: boolean) => {
        closeStay();
    };

    return (
        <div
            className={`${
                open ? width : "w-0 overflow-hidden "
            } duration-300 transition-all min-h-screen bg-sidebar border border-r`}
        >
            <div
                onMouseEnter={() => setStay(true)}
                onMouseLeave={handleOnMouseLeave}
                className={`${width} ${
                    open
                        ? " top-0 "
                        : ` bg-white sidebar-background ${
                              hover || stay
                                  ? "translate-x-[0%]"
                                  : "translate-x-[-100%]"
                          } shadow-xl top-[50px] h-[60%]`
                } transition-all fixed z-50 duration-300`}
            >
                <Profile
                    onClickOutside={handleOnProfileClickOutside}
                    user={user}
                />
                <SidebarMenu icon={<RxRocket />} name="Line" />
                <div className="mt-5">
                    <div className="ml-3 mb-1 text-xs font-medium text-gray-500  opacity-75">
                        Schedule
                    </div>
                    {monthData.map((data, index: number) => (
                        <div className="w-full">
                            <div
                                className="pl-1 rounded-md transition-all duration-200 py-[3px] my-[0.25px] cursor-pointer items-center gap-0.5 flex  hover:bg-hover m-1 text-[13px] text-gray-500"
                                key={index}
                            >
                                <RxCaretRight className="w-5 h-5 text-gray-400 " />
                                <div className="font-medium">{data.month}</div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
