import { IMonthData, IUser } from "@/Types/page";
import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { RxCalendar, RxClipboard, RxHome, RxRocket } from "react-icons/rx";
import LineTutorial from "../LineTutorial";
import Modal from "../Modal";
import Profile from "../Profile";
import SidebarMenu from "./SidebarMenu";
import SidebarMonthMenu from "./SidebarMonthMenu";

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
    const [lineModal, setLineModal] = useState<boolean>(false);
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
            <Modal maxWidth="sm" show={lineModal} onClose={setLineModal}>
                <LineTutorial user={user} />
            </Modal>
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
                <div className="h-1"></div>
                <SidebarMenu
                    onClick={() => setLineModal(true)}
                    icon={<RxRocket />}
                    name="Line"
                />
                <Link href="/">
                    <SidebarMenu icon={<RxHome />} name="Home" />
                </Link>
                <Link href="/list">
                    <SidebarMenu icon={<RxCalendar />} name="Schedule" />
                </Link>
                <Link href="/note">
                    <SidebarMenu icon={<RxClipboard />} name="Note" />
                </Link>
                {monthData.length > 0 && (
                    <div className="mt-5">
                        <div className="ml-3 mb-1 text-xs font-medium text-gray-500  opacity-75">
                            Schedule
                        </div>
                        {monthData.map((data, index: number) => (
                            <SidebarMonthMenu data={data} key={index} />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
