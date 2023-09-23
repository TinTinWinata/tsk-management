import { IScheduleData } from "@/types/schedule-data";
import { useEffect, useState } from "react";
import { DragDropContext, OnDragEndResponder } from "react-beautiful-dnd";
import ScheduleCard from "./ScheduleCard";

export interface IScheduleProps {
    datas: IScheduleData;
}

export default function Schedule({ datas: defaultSchedules }: IScheduleProps) {
    const [datas, setDatas] = useState<IScheduleData>(defaultSchedules);

    const cloneDatas = () => {
        return JSON.parse(JSON.stringify(datas)) as IScheduleData;
    };

    useEffect(() => {
        // Scroll to the center of the page when the component mounts
        window.scrollTo({
            top: window.innerHeight / 2,
            behavior: "smooth", // This adds smooth scrolling animation
        });
    }, []);

    const saveDatas = (datas: IScheduleData) => {
        setDatas(datas);
    };

    const onDragEnd: OnDragEndResponder = async (e) => {
        if (!e.destination) {
            return;
        }

        if (
            e.destination.droppableId === e.source.droppableId &&
            e.destination.index === e.source.index
        ) {
            return;
        }

        const descDate = e.destination.droppableId;
        const descIndex = e.destination.index;

        const sourceIndex = e.source.index;
        const sourceDate = e.source.droppableId;

        const newData = cloneDatas();

        // Add new data to destination
        if (newData[descDate].schedules.length > 0) {
            newData[descDate].schedules = newData[descDate].schedules.splice(
                descIndex,
                0,
                newData[sourceDate].schedules[sourceIndex]
            );
        } else {
            newData[descDate].schedules.push(
                newData[sourceDate].schedules[sourceIndex]
            );
        }

        // Remove old data from source
        newData[sourceDate].schedules.splice(sourceIndex, 1);

        saveDatas(newData);
    };

    return (
        <DragDropContext onDragEnd={onDragEnd}>
            <div className="center min-h-screen w-full">
                <div className="w-full grid gap-x-5 gap-y-10 grid-cols-2">
                    {Object.keys(datas).map((val, index: number) => {
                        const data = datas[val];
                        return (
                            <ScheduleCard
                                date={val}
                                index={index}
                                key={index}
                                formatted={data.formatted}
                                schedules={data.schedules}
                            />
                        );
                    })}
                </div>
            </div>
        </DragDropContext>
    );
}
