import { ISchedule } from "@/Types/schedule";
import { ChangeEvent, useEffect, useState } from "react";
import { Draggable } from "react-beautiful-dnd";
import { GoMultiSelect } from "react-icons/go";
import Checkbox from "../Checkbox";

export interface IScheduleCardDetailProps {
    schedule: ISchedule;
    position: number;
    handleUnFocusText?: (text: string, index: number) => void;
    index: number;
    onCheck?: (val: boolean) => void;
}

export default function ScheduleCardDetail({
    handleUnFocusText,
    schedule,
    position,
    index,
    onCheck,
}: IScheduleCardDetailProps) {
    const [checked, setState] = useState<boolean>(false);
    const [hover, setHover] = useState<boolean>(false);

    useEffect(() => {
        setState(schedule.is_done);
    }, [schedule]);

    const handleOnChange = (e: ChangeEvent<HTMLInputElement>) => {
        schedule.is_done = e.currentTarget.checked;
        onCheck && onCheck(e.currentTarget.checked);
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
            {(provided, snapshot) => {
                return (
                    <div
                        onMouseEnter={() => setHover(true)}
                        onMouseLeave={() => setHover(false)}
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                        className={`  pt-2 gap-1  text-sm pl-1 flex items-center ${
                            snapshot.isDragging && "opacity-30"
                        }`}
                    >
                        <div className="center">
                            <GoMultiSelect
                                className={`text-gray-400 opacity-400 w-5 h-5 mr-1 transition-all  ${
                                    hover ? "opacity-100" : "opacity-0"
                                }`}
                            />
                        </div>
                        <div className="">
                            <Checkbox
                                checked={checked}
                                onChange={handleOnChange}
                            />
                        </div>
                        <input
                            onKeyDown={(e) => {
                                if (e.key === "Enter") {
                                    handleUnFocusText(
                                        e.currentTarget.value,
                                        index
                                    );
                                }
                            }}
                            onBlur={(e) =>
                                handleUnFocusText(e.currentTarget.value, index)
                            }
                            placeholder="To-do"
                            defaultValue={schedule.title}
                            className={` placeholder-gray-300 w-full focus:ring-0 border-none focus:shadow-none focus:outline-none focus:border-none mt-1 ${getCheckedClass()}`}
                        ></input>
                    </div>
                );
            }}
        </Draggable>
    );
}
