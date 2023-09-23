import { ISchedule } from "@/types/schedule";
import { ChangeEvent, useState } from "react";
import { Draggable } from "react-beautiful-dnd";
import Checkbox from "../Checkbox";

export interface IScheduleCardDetailProps {
    schedule: ISchedule;
    position: number;
}

export default function ScheduleCardDetail({
    schedule,
    position,
}: IScheduleCardDetailProps) {
    const [checked, setState] = useState<boolean>(schedule.is_done);
    const handleOnChange = (e: ChangeEvent<HTMLInputElement>) => {
        setState(e.currentTarget.checked);
    };
    const getCheckedClass = () => {
        if (checked) return "line-through text-gray-400";
    };
    return (
        <Draggable
            key={schedule.id + ""}
            draggableId={schedule.id + ""}
            index={position}
        >
            {(provided, snapshot) => (
                <div
                    ref={provided.innerRef}
                    {...provided.draggableProps}
                    {...provided.dragHandleProps}
                    className="mt-2 gap-3 text-sm ml-1 flex items-center"
                >
                    <div className="">
                        <Checkbox onChange={handleOnChange} />
                    </div>
                    <div className={`mt-1 ${getCheckedClass()}`}>
                        {schedule.title}
                    </div>
                </div>
            )}
        </Draggable>
    );
}
