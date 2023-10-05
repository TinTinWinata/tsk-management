import { IMonthData } from "@/Types/page";
import { Link } from "@inertiajs/react";
import { RxCaretRight } from "react-icons/rx";

interface ISidebarMonthMenuProps {
    data: IMonthData;
}

export default function SidebarMonthMenu({ data }: ISidebarMonthMenuProps) {
    const link = data.month.replace(/\s/g, "");
    return (
        <Link href={route("schedule", { date: link })} className="w-full">
            <div className="pl-1 rounded-md transition-all duration-200 py-[3px] my-[0.25px] cursor-pointer items-center gap-0.5a flex  hover:bg-hover m-1 text-[13px] text-gray-500">
                <RxCaretRight className="w-5 h-5 text-gray-400 " />
                <div className="font-medium">{data.month}</div>
            </div>
        </Link>
    );
}
