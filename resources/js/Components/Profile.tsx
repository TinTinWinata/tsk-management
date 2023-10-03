import { User } from "@/types";
import Initial from "./Initial";

interface IProfileProps {
    user: User;
}

export default function Profile({ user }: IProfileProps) {
    const initial = user.name.charAt(0).toUpperCase();
    return (
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
    );
}
