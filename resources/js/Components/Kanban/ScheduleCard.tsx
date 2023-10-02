import { ISchedule } from "@/types/schedule";
import { Droppable } from "react-beautiful-dnd";
import { FcBiohazard, FcEngineering } from "react-icons/fc";
import ScheduleCardDetail from "./ScheduleCardDetail";

interface IScheduleCardProps {
    formatted: string;
    schedules: ISchedule[];
    index: number;
    date: string;
    handleNewTask?: (date: string) => void;
    handleUnFocusText?: (text: string, date: string, index: number) => void;
}

export default function ScheduleCard({
    schedules,
    formatted,
    index,
    date,
    handleNewTask,
    handleUnFocusText,
}: IScheduleCardProps) {
    const isOnLeft = index % 2 === 0;
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
                className="cursor-pointer hover:bg-gray-50 transition-all p-2 border border-gray-300 border-opacity-30  text-sm"
            >
                New Task
            </div>
            <Droppable droppableId={date}>
                {(droppableProvided, snapshot) => (
                    <div
                        className={
                            snapshot.isDraggingOver
                                ? "relative transition-all div-is-dragging"
                                : ""
                        }
                        ref={droppableProvided.innerRef}
                        {...droppableProvided.droppableProps}
                    >
                        {droppableProvided.placeholder}
                        {schedules.map((schedule, index: number) => (
                            <ScheduleCardDetail
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
                    </div>
                )}
            </Droppable>
        </div>
    );
}
