import { ISchedule } from "@/types/schedule";
import { Droppable } from "react-beautiful-dnd";
import { FcBiohazard, FcEngineering } from "react-icons/fc";
import { IHoveredData } from "./Schedule";
import ScheduleCardDetail from "./ScheduleCardDetail";

interface IScheduleCardProps {
    formatted: string;
    schedules: ISchedule[];
    index: number;
    date: string;
    hovered?: IHoveredData | undefined;
    handleNewTask?: (date: string) => void;
    onCheck?: (bool: boolean) => void;
    handleUnFocusText?: (text: string, date: string, index: number) => void;
}

export default function ScheduleCard({
    schedules,
    formatted,
    index,
    date,
    handleNewTask,
    handleUnFocusText,
    hovered,
    onCheck,
}: IScheduleCardProps) {
    const isOnLeft = index % 2 === 0;
    const isHovered = hovered && hovered.date === date;
    const isHoveredToNewDates = isHovered && hovered.sourceDate !== date;
    const top =
        isHovered &&
        (hovered.index / (schedules.length + (isHoveredToNewDates ? 1 : 0))) *
            100;

    return (
        <div className="flex flex-col gap-2 min-h-[250px]">
            <div
                className={`flex gap-2 p-1 ${
                    isOnLeft ? "bg-orange-50" : "bg-green-50"
                }`}
            >
                <div className="center">
                    {isOnLeft ? (
                        <FcEngineering className="w-5 h-5" />
                    ) : (
                        <FcBiohazard className="w-5 h-5" />
                    )}
                </div>
                <div className="text-xl font-semibold">{formatted}</div>
            </div>
            <hr />
            <div
                onClick={() => handleNewTask(date)}
                className="cursor-pointer  hover:bg-gray-50 transition-all p-2 border border-gray-300 border-opacity-30  text-sm"
            >
                New Task
            </div>

            <Droppable droppableId={date} type="COLUMN" direction="vertical">
                {(droppableProvided, snapshot) => {
                    return (
                        <div
                            className={`relative h-32 ${
                                snapshot.isDraggingOver ? "  " : ""
                            }`}
                            ref={droppableProvided.innerRef}
                            {...droppableProvided.droppableProps}
                        >
                            {schedules.map((schedule, index: number) => (
                                <ScheduleCardDetail
                                    onCheck={onCheck}
                                    handleUnFocusText={(
                                        text: string,
                                        index: number
                                    ) => handleUnFocusText(text, date, index)}
                                    index={index}
                                    position={index}
                                    key={index}
                                    schedule={schedule}
                                />
                            ))}
                            {isHovered && snapshot.isDraggingOver && (
                                <div
                                    style={{ top: `${top}%` }}
                                    className="absolute bg-sky-100 w-full h-1 left-0"
                                ></div>
                            )}
                            {droppableProvided.placeholder}
                        </div>
                    );
                }}
            </Droppable>
        </div>
    );
}
