import { User } from "@/types";
import { useEffect, useState } from "react";
import Profile from "./Profile";

interface ISidebarProps {
    user: User;
    open: boolean;
    hover: boolean;
}

export default function Sidebar({ user, open, hover }: ISidebarProps) {
    const [stay, setStay] = useState<boolean>(false);
    const [profile, setProfile] = useState<boolean>(false);

    const width = "min-w-[200px]";
    const timeToClose = 500;

    useEffect(() => {
        if (open === false) {
            setStay(true);
            setTimeout(() => {
                setStay(false);
            }, timeToClose);
        }
    }, [open]);

    const handleOnMouseLeave = () => {
        // if (!profile) setStay(false);
    };

    const handleOnProfileClick = (val: boolean) => {
        // setProfile(val);
    };

    const handleOnProfileClickOutside = (val: boolean) => {
        // setStay(false);
    };

    return (
        <div
            className={`${
                open ? width : "w-0 overflow-hidden "
            } duration-300 transition-all min-h-screen bg-gray-100`}
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
                    onClick={handleOnProfileClick}
                    user={user}
                />
            </div>
        </div>
    );
}
